<?php

namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\PriceTypes;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\ProductAndVariationPrices;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\ProductImages;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\ProductAndVariationStock;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\GlobalProductAttributes;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Groups;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Stocks;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\VariationCharacteristicsToGlobalProductAttributes;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\SetVariationAttributeToProducts;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\HeartBeat;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Term;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Product;

use Itgalaxy\Wc\Exchange1c\Includes\Cron;
use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class Parser1cXml
{
    private $rate = 1;

    private $postAuthor = 0;

    // true or false
    private $onlyChanges = '';

    public function __construct()
    {
        HeartBeat::start();
    }

    public function parce($filename)
    {
        global $wpdb;

        wp_defer_term_counting(true);

        if (class_exists('\\WPSEO_Sitemaps_Cache')) {
            add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!$postAuthor = get_option('synchronization_post_author')) {
            if ($users = get_users(['role' => 'administrator'])) {
                $postAuthor = array_shift($users)->ID;
            } else {
                $postAuthor = 1;
            }
        }

        $this->postAuthor = $postAuthor;

        if ($exclude1cCategories = trim(get_option('exclude_1c_categories'))) {
            $exclude1cCategories = explode(';', get_option('exclude_1c_categories'));
        } else {
            $exclude1cCategories = false;
        }

        if (!isset($_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'])) {
            $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'] = [];
        }

        $valid = false;

        $reader = new \XMLReader();
        $reader->open($filename);

        while ($reader->read()) {
            if ($reader->name == 'Каталог' && $this->onlyChanges == '') {
                $this->onlyChanges = $reader->getAttribute('СодержитТолькоИзменения');
            }

            if ((!isset($_SESSION['IMPORT_1C']['categoryIsParse'])
                    || !isset($_SESSION['IMPORT_1C']['optionsIsParse']))
                && $reader->name == 'Классификатор'
            ) {
                $valid = true;

                $processDataGroups = [
                    'numberOfCategories' => 0,
                    'currentCategoryId' => isset($_SESSION['IMPORT_1C']['currentCategoryId'])
                        ? $_SESSION['IMPORT_1C']['currentCategoryId']
                        : 0,
                    'categoryIdStack' => isset($_SESSION['IMPORT_1C']['categoryIdStack'])
                        ? $_SESSION['IMPORT_1C']['categoryIdStack']
                        : [],
                    'exclude1cCategories' => $exclude1cCategories
                ];

                $reader->read();

                while ($reader->read()
                    && !($reader->name == 'Классификатор'
                        && $reader->nodeType == \XMLReader::END_ELEMENT)
                ) {
                    // resolve attributes
                    if (!isset($_SESSION['IMPORT_1C']['optionsIsParse'])
                        && $reader->name == Bootstrap::XML_TAGS['options']
                        && $reader->nodeType == \XMLReader::ELEMENT
                        && str_replace(' ', '', $reader->readOuterXml()) !== '<' . Bootstrap::XML_TAGS['options'] . '/>'
                    ) {
                        GlobalProductAttributes::process($reader);

                        return false;
                    }

                    // resolve groups
                    if (empty($settings['skip_categories']) && in_array($reader->name, ['Группы', 'Группа'])) {
                        $processDataGroups = Groups::process($reader, $processDataGroups);

                        // time limit check
                        if ($processDataGroups === false) {
                            return false;
                        }
                    }

                    // resolve price types
                    if ($reader->name == 'ТипыЦен' && $reader->nodeType !== \XMLReader::END_ELEMENT) {
                        PriceTypes::process($reader);
                    }

                    // resolve stocks
                    if ($reader->name == 'Склады' && $reader->nodeType !== \XMLReader::END_ELEMENT) {
                        Stocks::process($reader);
                    }
                }

                $_SESSION['IMPORT_1C']['categoryIsParse'] = 'yes';
                delete_option('product_cat_children');
                wp_cache_flush();
            } // 'Классификатор'

            if ($reader->name == 'Товары') {
                $valid = true;

                if (!isset($_SESSION['IMPORT_1C']['products_parse'])) {
                    if (empty($settings['skip_categories'])) {
                        if (!isset($_SESSION['IMPORT_1C']['categoryIds'])) {
                            $_SESSION['IMPORT_1C']['categoryIds'] = Term::getProductCatIDs();
                            $categoryIds = $_SESSION['IMPORT_1C']['categoryIds'];
                        } else {
                            $categoryIds = $_SESSION['IMPORT_1C']['categoryIds'];
                        }
                    } else {
                        $categoryIds = [];
                    }

                    while ($reader->read()
                        && !($reader->name == 'Товары' && $reader->nodeType == \XMLReader::END_ELEMENT)
                    ) {
                        if ($reader->name == 'Товар' && $reader->nodeType == \XMLReader::ELEMENT) {
                            if (!HeartBeat::next('Товар', $reader)) {
                                return false;
                            }

                            $element = $reader->readOuterXml();
                            $element = simplexml_load_string(trim($element));

                            if (apply_filters('itglx_wc1c_skip_product_by_xml', false, $element)) {
                                unset($element);

                                continue;
                            }

                            // resolve xml id
                            $xmlID = explode('#', (string) $element->{Bootstrap::XML_TAGS['id']});
                            $resolveOldVariation = !empty($xmlID[1]);
                            $xmlID = $xmlID[0];

                            $product = Product::getProductIdByMeta($xmlID);

                            // maybe removed
                            if ((string) $element->ПометкаУдаления &&
                                (string) $element->ПометкаУдаления === 'true'
                            ) {
                                if ($product) {
                                    Product::removeProduct($product);
                                }

                                unset($element);
                                continue;
                            }

                            // prevent search product if not exists
                            if (!$product) {
                                $product = apply_filters('itglx_wc1c_find_product_id', $product, $element);

                                if ($product) {
                                    update_post_meta($product, '_id_1c', (string) $element->{Bootstrap::XML_TAGS['id']});
                                }
                            } else {
                                // if duplicate product
                                if (in_array($product, $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'])) {
                                    if ($_SESSION['xmlVersion'] === 2.04 && $resolveOldVariation) {
                                        $this->resolveOldVariation($element);
                                    }

                                    continue;
                                }
                            }

                            $productEntry = [
                                'ID' => $product
                            ];

                            $isNewProduct = true;

                            if (!empty($productEntry['ID'])) {
                                $isNewProduct = false;
                                do_action('itglx_wc1c_before_exists_product_info_resolve', $productEntry['ID'], $element);
                            } else {
                                do_action('itglx_wc1c_before_new_product_info_resolve', $element);
                            }

                            $productHash = md5(json_encode((array) $element));

                            if (!empty($productEntry['ID'])
                                && empty($settings['force_update_product'])
                                && $productHash == get_post_meta($productEntry['ID'], '_md5', true)
                            ) {
                                $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'][] = $productEntry['ID'];

                                if (
                                    !empty($settings['more_check_image_changed']) &&
                                    empty($settings['skip_post_images'])
                                ) {
                                    // it is necessary to check the change of images,
                                    // since the photo can be changed without changing the file name,
                                    // which means the hash matches
                                    $stop = ProductImages::process($element, $productEntry);

                                    if ($stop) {
                                        return false;
                                    }
                                }

                                unset($productEntry, $element);

                                continue;
                            }

                            $productEntry = Product::mainProductData(
                                $element,
                                $productEntry,
                                trim(strip_tags((string) $element->{Bootstrap::XML_TAGS['name']})),
                                $categoryIds,
                                $productHash,
                                $postAuthor
                            );

                            if ($_SESSION['xmlVersion'] === 2.04 && $resolveOldVariation) {
                                $this->resolveOldVariation($element);
                            }

                            $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'][] = $productEntry['ID'];

                            // is new or not disabled image data procesing
                            if ($isNewProduct || empty($settings['skip_post_images'])) {
                                $stop = ProductImages::process($element, $productEntry);
                            } else {
                                $stop = false;
                            }

                            do_action('itglx_wc1c_after_product_info_resolve', $productEntry['ID'], $element);

                            if ($stop) {
                                return false;
                            }

                            unset($productEntry, $element);
                        }
                    }

                    $_SESSION['IMPORT_1C']['products_parse'] = true;
                }

                if (
                    isset($_SESSION['IMPORT_1C']['products_parse']) &&
                    !empty($settings['remove_missing_products']) &&
                    $this->onlyChanges == 'false'
                ) {
                    /*------------------REMOVAL OF THE PRODUCTS OUT OF FULL EXCHANGE--------------------------*/
                    if (!isset($_SESSION['IMPORT_1C_PROCESS']['missingProductsIsRemove'])
                        && !empty($_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'])
                    ) {
                        $productIds = [];
                        $posts =
                            $wpdb->get_results(
                                "SELECT `meta_value`, `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_id_1c'"
                            );

                        foreach ($posts as $post) {
                            $productIds[$post->meta_value] = $post->post_id;
                        }

                        unset($posts);

                        if (!isset($_SESSION['IMPORT_1C_PROCESS']['countProductRemove'])) {
                            $_SESSION['IMPORT_1C_PROCESS']['countProductRemove'] = 0;
                        }

                        $kol = 0;

                        foreach ($productIds as $productID) {
                            if (!HeartBeat::nextTerm()) {
                                return false;
                            }

                            $kol++;

                            if ($kol <= $_SESSION['IMPORT_1C_PROCESS']['countProductRemove']) {
                                continue;
                            }

                            if (!in_array($productID, $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'])) {
                                Product::removeProduct($productID);
                                $kol--;
                            }

                            $_SESSION['IMPORT_1C_PROCESS']['countProductRemove'] = $kol;
                        }

                        $_SESSION['IMPORT_1C_PROCESS']['missingProductsIsRemove'] = true;
                    }
                    /*------------------REMOVAL OF THE PRODUCTS OUT OF FULL EXCHANGE--------------------------*/

                    /*------------------REMOVAL OF THE CATEGORIES OUT OF FULL EXCHANGE--------------------------*/
                    if (!isset($_SESSION['IMPORT_1C_PROCESS']['missingTermsIsRemove'])
                        && !empty($_SESSION['IMPORT_1C_PROCESS']['currentCategorys1c'])
                    ) {
                        if (!isset($_SESSION['IMPORT_1C_PROCESS']['countTermRemove'])) {
                            $_SESSION['IMPORT_1C_PROCESS']['countTermRemove'] = 0;
                        }

                        $kol = 0;

                        foreach (Term::getProductCatIDs() as $id => $category) {
                            if (!HeartBeat::nextTerm()) {
                                return false;
                            }

                            $kol++;

                            if ($kol <= $_SESSION['IMPORT_1C_PROCESS']['countTermRemove']) {
                                continue;
                            }

                            if (\get_term($category, 'product_cat')
                                && !in_array($id, $_SESSION['IMPORT_1C_PROCESS']['currentCategorys1c'])
                            ) {
                                \wp_delete_term($category, 'product_cat');

                                $kol--;
                            }

                            $_SESSION['IMPORT_1C_PROCESS']['countTermRemove'] = $kol;
                        }

                        global $wp_object_cache;

                        if ($wp_object_cache) {
                            $wp_object_cache->flush();
                        }

                        $_SESSION['IMPORT_1C_PROCESS']['missingTermsIsRemove'] = true;
                    }
                    /*------------------REMOVAL OF THE CATEGORIES OUT OF FULL EXCHANGE--------------------------*/
                }

                delete_option('product_cat_children');
                wp_cache_flush();

                $ready = SetVariationAttributeToProducts::process();

                if (!$ready) {
                    return false;
                }
            }


            if (in_array($reader->name, ['ПакетПредложений', 'ИзмененияПакетаПредложений'])) {
                $valid = true;

                if (!isset($_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'])) {
                    $_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'] = [];
                }

                if (!isset($_SESSION['IMPORT_1C']['offers_parse'])) {
                    while ($reader->read() &&
                        !(in_array($reader->name, ['ПакетПредложений', 'ИзмененияПакетаПредложений']) &&
                            $reader->nodeType == \XMLReader::END_ELEMENT)
                    ) {
                        // resolve price types
                        if ($reader->name == 'ТипыЦен' && $reader->nodeType !== \XMLReader::END_ELEMENT) {
                            PriceTypes::process($reader);
                        }

                        // resolve stocks
                        if ($reader->name == 'Склады' && $reader->nodeType !== \XMLReader::END_ELEMENT) {
                            Stocks::process($reader);
                        }

                        if ($reader->name == 'Предложение' && $reader->nodeType == \XMLReader::ELEMENT) {
                            if (!HeartBeat::next('Предложение', $reader)) {
                                return false;
                            }

                            $element = $reader->readOuterXml();
                            $element = simplexml_load_string(trim($element));

                            if (!isset($element->{Bootstrap::XML_TAGS['id']})) {
                                continue;
                            }

                            // if duplicate offer
                            if (in_array((string) $element->{Bootstrap::XML_TAGS['id']}, $_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'])) {
                                continue;
                            }

                            $productEntry = [];
                            $parseID = explode('#', (string) $element->{Bootstrap::XML_TAGS['id']});

                            // not empty variation hash
                            if (!empty($parseID[1])) {
                                $productEntry['ID'] = Product::getProductIdByMeta((string) $element->{Bootstrap::XML_TAGS['id']});
                                $productEntry['post_parent'] = Product::getProductIdByMeta($parseID[0]);

                                if (empty($productEntry['post_parent'])) {
                                    Logger::logChanges(
                                        'get_parent',
                                        'not_exists_parent_product',
                                        0,
                                        (string) $element->{Bootstrap::XML_TAGS['id']}
                                    );

                                    continue;
                                }

                                // resolve main variation data
                                if (
                                    isset($element->ЗначенияСвойств) &&
                                    isset($element->ЗначенияСвойств->ЗначенияСвойства)
                                ) {
                                    $productEntry = Product::mainVariationData(
                                        $element,
                                        $productEntry,
                                        $postAuthor
                                    );
                                // simple variant without ids
                                } elseif (
                                    isset($element->ХарактеристикиТовара) &&
                                    isset($element->ХарактеристикиТовара->ХарактеристикаТовара)
                                ) {
                                    VariationCharacteristicsToGlobalProductAttributes::process($element);

                                    $productEntry = Product::mainVariationData(
                                        $element,
                                        $productEntry,
                                        $postAuthor
                                    );
                                }

                                if (!empty($productEntry['ID'])) {
                                    if (isset($element->Цены)) {
                                        ProductAndVariationPrices::setPrices(
                                            ProductAndVariationPrices::resolvePrices(
                                                $element,
                                                $this->rate
                                            ),
                                            $productEntry['ID']
                                        );

                                        \WC_Product_Variable::sync($productEntry['post_parent']);
                                    }

                                    if (isset($element->Остатки)
                                        || isset($element->КоличествоНаСкладах)
                                        || isset($element->{Bootstrap::XML_TAGS['stock']})
                                        // the old exchange may not contain a stock node when the value is 0
                                        || !isset($_GET['version'])
                                    ) {
                                        ProductAndVariationStock::set(
                                            $productEntry['ID'],
                                            ProductAndVariationStock::resolve($element),
                                            $productEntry['post_parent']
                                        );

                                        \WC_Product_Variable::sync($productEntry['post_parent']);
                                    }

                                    do_action(
                                        'itglx_wc1c_after_variation_offer_resolve',
                                        $productEntry['ID'],
                                        $productEntry['post_parent'],
                                        $element
                                    );
                                }
                            } else {
                                $productId = Product::getProductIdByMeta((string) $element->{Bootstrap::XML_TAGS['id']});

                                if ($productId) {
                                    if (isset($element->Цены)) {
                                        ProductAndVariationPrices::setPrices(
                                            ProductAndVariationPrices::resolvePrices(
                                                $element,
                                                $this->rate
                                            ),
                                            $productId
                                        );
                                    }

                                    if (isset($element->Остатки) ||
                                        isset($element->КоличествоНаСкладах) ||
                                        isset($element->{Bootstrap::XML_TAGS['stock']}) ||
                                        // the old exchange may not contain a stock node when the value is 0
                                        !isset($_GET['version'])
                                    ) {
                                        ProductAndVariationStock::set(
                                            $productId,
                                            ProductAndVariationStock::resolve($element)
                                        );
                                    }

                                    do_action('itglx_wc1c_after_product_offer_resolve', $productId, $element);
                                }
                            }

                            $_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'][] = (string) $element->{Bootstrap::XML_TAGS['id']};

                            unset($element, $productEntry);
                        }
                    }

                    $_SESSION['IMPORT_1C']['offers_parse'] = true;
                }

                $ready = SetVariationAttributeToProducts::process();

                if (!$ready) {
                    return false;
                }

                // recalculate product cat counts
                $cron = Cron::getInstance();
                $cron->createCronTermRecount();

                // clear sitemap cache
                if (class_exists('\\WPSEO_Sitemaps_Cache')) {
                    remove_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');
                    \WPSEO_Sitemaps_Cache::clear();
                }
            } // end 'Предложения'
        } // end parce

        \wp_defer_term_counting(false);

        return $valid;
    }

    private function resolveOldVariation($element)
    {
        $parseID = explode('#', (string) $element->{Bootstrap::XML_TAGS['id']});

        // old format - resolve main variation data
        if (
            !empty($parseID[1]) && // not empty variation hash
            isset($element->ХарактеристикиТовара) &&
            isset($element->ХарактеристикиТовара->ХарактеристикаТовара)
        ) {
            VariationCharacteristicsToGlobalProductAttributes::process($element);

            $variationEntry['ID'] = Product::getProductIdByMeta((string) $element->{Bootstrap::XML_TAGS['id']});
            $variationEntry['post_parent'] = Product::getProductIdByMeta($parseID[0]);

            if (empty($variationEntry['post_parent'])) {
                Logger::logChanges('get_parent', 'not_exists_parent_product', 0, $parseID[0]);
            }

            Product::mainVariationData(
                $element,
                $variationEntry,
                $this->postAuthor
            );
        }
    }
}
