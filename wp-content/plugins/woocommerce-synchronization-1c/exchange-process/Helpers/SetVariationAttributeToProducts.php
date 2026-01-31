<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class SetVariationAttributeToProducts
{
    public static function process()
    {
        if (empty($_SESSION['IMPORT_1C']['setTerms'])) {
            return true;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        foreach ($_SESSION['IMPORT_1C']['setTerms'] as $productID => $tax) {
            if (!HeartBeat::nextTerm()) {
                Logger::logProtocol('SetVariationAttributeToProducts - progress');

                return false;
            }

            if (isset($_SESSION['IMPORT_1C']['setTerms'][$productID]['is_visible'])) {
                Product::show($productID, true);
            } else {
                Product::hide($productID, true);
            }

            // ignore set attributes if only visible status
            if (count($tax) === 1 && isset($tax['is_visible'])) {
                unset($_SESSION['IMPORT_1C']['setTerms'][$productID]);

                continue;
            }

            $productAttributes = get_post_meta($productID, '_product_attributes', true);

            if (!is_array($productAttributes)) {
                $productAttributes = [];
            }

            $allCurrentVariableTaxes = [];

            foreach ($tax as $taxonomy => $ids) {
                if ($taxonomy === 'is_visible') {
                    continue;
                }

                $allCurrentVariableTaxes[] = $taxonomy;

                $productAttributes[$taxonomy] = [
                    'name' => \wc_clean($taxonomy),
                    'value' => '',
                    'position' => 0,
                    'is_visible' => 0,
                    'is_variation' => 1,
                    'is_taxonomy' => 1
                ];

                \wp_set_object_terms(
                    $productID,
                    array_map('intval', $ids),
                    $taxonomy
                );

                Logger::logChanges('set_variation_attributes', 'product', $productID, $taxonomy);
            }

            $resolvedAttributes = $productAttributes;

            // remove non exists variation attributes
            foreach ($productAttributes as $key => $value) {
                if (empty($key)) {
                    unset($resolvedAttributes[$key]);

                    continue;
                }

                if ($value['is_variation'] && !in_array($key, $allCurrentVariableTaxes)) {
                    unset($resolvedAttributes[$key]);

                    \wp_set_object_terms(
                        $productID,
                        [],
                        $key
                    );
                }
            }

            // clean up missing product variations
            if (
                !empty($settings['remove_missing_variation']) &&
                !empty($_SESSION['IMPORT_1C']['productVariations']) &&
                !empty($_SESSION['IMPORT_1C']['productVariations'][$productID])
            ) {
                $variationIds = wp_parse_id_list(
                    get_posts(
                        [
                            'post_parent' => $productID,
                            'post_type' => 'product_variation',
                            'fields' => 'ids',
                            'post_status' => ['any', 'trash', 'auto-draft'],
                            'numberposts' => -1,
                        ]
                    )
                );

                Logger::logChanges(
                    'current_exchange_variation_list',
                    'product',
                    $productID,
                    json_encode($_SESSION['IMPORT_1C']['productVariations'][$productID])
                );

                if (!empty($variationIds)) {
                    foreach ($variationIds as $variationId) {
                        if (!in_array($variationId, $_SESSION['IMPORT_1C']['productVariations'][$productID])) {
                            Logger::logChanges(
                                'remove_variation',
                                'product',
                                $variationId
                            );

                            wp_delete_post($variationId, true);
                        }
                    }
                }

                delete_transient('wc_product_children_' . $productID);
            }

            update_post_meta($productID, '_product_attributes', $resolvedAttributes);

            unset($_SESSION['IMPORT_1C']['setTerms'][$productID]);
        }

        unset($_SESSION['IMPORT_1C']['setTerms']);

        return true;
    }
}
