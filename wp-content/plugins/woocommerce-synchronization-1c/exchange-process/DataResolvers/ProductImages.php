<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class ProductImages
{
    public static function process($element, $productEntry)
    {
        $dirName = $_SESSION['synchronization1cPathTemp'] . '/';
        $oldImages = get_post_meta($productEntry['ID'], '_old_images', true);

        if (!is_array($oldImages)) {
            $oldImages = [];
        }

        if (isset($element->Картинка) && (string) $element->Картинка) {
            $settings = get_option(Bootstrap::OPTIONS_KEY);
            $images = [];
            $countNew = 0;

            foreach ($element->Картинка as $image) {
                $images[] = (string) $image;
            }

            update_post_meta($productEntry['ID'], '_old_images', $images);

            // delete images that do not exist in the current set
            foreach ($oldImages as $oldImage) {
                $attachID = self::findImageByMeta($oldImage);
                $imageSrcPath = str_replace('//', '/', $dirName . $oldImage);
                $removeImage = false;

                if ($attachID && !in_array($oldImage, $images)) {
                    $removeImage = true;
                } elseif (
                    $attachID &&
                    file_exists($imageSrcPath) &&
                    sha1_file($imageSrcPath) !== get_post_meta($attachID, '_1c_image_sha_hash', true)
                ) {
                    $removeImage = true;
                }

                if ($removeImage) {
                    // clean product thumbnail if removed
                    if ($attachID == get_post_meta($productEntry['ID'], '_thumbnail_id', true)) {
                        update_post_meta($productEntry['ID'], '_thumbnail_id', '');
                    }

                    // clean category thumbnail if removed
                    if (!empty($settings['set_category_thumbnail_by_product'])
                        && get_term_meta((int) $productEntry['categoryID'], 'thumbnail_id', true) == $attachID
                    ) {
                        update_term_meta((int) $productEntry['categoryID'], 'thumbnail_id', '');
                    }

                    wp_delete_attachment($attachID, true);
                }
            }

            $attachmentIds = [];

            foreach ($images as $image) {
                $attachID = self::findImageByMeta($image);

                if ($attachID && !is_wp_error($attachID)) {
                    $attachmentIds[] = $attachID;
                } else {
                    $imageSrcPath = str_replace('//', '/', $dirName . $image);

                    if (file_exists($imageSrcPath)) {
                        $attachID = self::resolveImage($image, $imageSrcPath, $productEntry['ID']);

                        if ($attachID) {
                            $attachmentIds[] = $attachID;
                            $countNew++;
                        }
                    }
                }
            }

            if (empty($attachmentIds)) {
                return false;
            }

            $gallery = [];
            $hasThumbnail = false;

            // distribution of the current set of images
            foreach ($attachmentIds as $attachID) {
                if (!empty($settings['set_category_thumbnail_by_product'])
                    && !get_term_meta((int) $productEntry['categoryID'], 'thumbnail_id', true)
                ) {
                    update_term_meta((int) $productEntry['categoryID'], 'thumbnail_id', $attachID);
                }

                if (!$hasThumbnail) {
                    update_post_meta($productEntry['ID'], '_thumbnail_id', $attachID);
                    $hasThumbnail = true;
                } else {
                    $gallery[] = $attachID;
                }
            }

            // setting gallery images
            if (!empty($gallery)) {
                update_post_meta($productEntry['ID'], '_product_image_gallery', implode(',', $gallery));

                // solves the problem of a large number of pictures of consecutive products
                if ($countNew > 1) {
                    return true;
                }

                return false;
            // if in the current set there are no images for the gallery, but before they were - delete
            } elseif (count($oldImages) > 1) {
                update_post_meta($productEntry['ID'], '_product_image_gallery', '');
            }
        // the current data does not contain information about the image, but it was before, so it should be deleted
        } elseif ($oldImages) {
            self::removeImages($oldImages, $productEntry['ID']);
        }

        return false;
    }

    private static function resolveImage($image, $imageSrcPath, $productID)
    {
        $imageSha = sha1_file($imageSrcPath);
        $wpFiletype = wp_check_filetype(basename($imageSrcPath), null);

        $_FILES['async-upload'] = [
            'name' => trim(str_replace(' ', '', basename($imageSrcPath))),
            'type' => $wpFiletype['type'],
            'tmp_name' => $imageSrcPath,
            'error' => 0,
            'size' => filesize($imageSrcPath)
        ];

        $attachID = media_handle_upload(
            'async-upload',
            $productID,
            [],
            [
                'test_form' => false,
                'action' => '1c-sync-upload'
            ]
        );

        if (!$attachID || is_wp_error($attachID)) {
            return false;
        }

        update_post_meta($attachID, '_1c_image_path', $image);
        update_post_meta($attachID, '_1c_image_sha_hash', $imageSha);

        return $attachID;
    }

    private static function removeImages($oldImages, $productID)
    {
        update_post_meta($productID, '_old_images', []);
        update_post_meta($productID, '_thumbnail_id', '');

        if (count($oldImages) > 1) {
            update_post_meta($productID, '_product_image_gallery', '');
        }

        // delete a known set of images
        foreach ($oldImages as $oldImage) {
            $attachID = self::findImageByMeta($oldImage);

            if ($attachID) {
                wp_delete_attachment($attachID, true);
            }
        }
    }

    private static function findImageByMeta($value, $key = '_1c_image_path')
    {
        global $wpdb;

        $attachID = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT `post_id` FROM `{$wpdb->postmeta}`
                      WHERE `meta_value` = '%s' AND `meta_key` = '%s'",
                (string) $value,
                $key
            )
        );

        return $attachID;
    }
}
