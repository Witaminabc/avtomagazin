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

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class Parser1cXml31
{
    private $rate = 1;

    // true or false
    private $onlyChanges = '';

    public function __construct()
    {
        HeartBeat::start();
    }

    public function parce($filename)
    {
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

        if ($exclude1cCategories = trim(get_option('exclude_1c_categories'))) {
            $exclude1cCategories = explode(';', get_option('exclude_1c_categories'));
        } else {
            $exclude1cCategories = false;
        }

        $valid = false;

        $reader = new \XMLReader();
        $reader->open($filename);

        while ($reader->read()) {
            if ($reader->name == 'Каталог' && $this->onlyChanges == '') {
                $this->onlyChanges = $reader->getAttribute('СодержитТолькоИзменения');
            }

            if ($reader->name == 'Классификатор') {
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
                        && str_replace(' ', '', $reader->readOuterXml()) !== '<' . Bootstrap::XML_TAGS['options'] .'/>'
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

                delete_option('product_cat_children');
                wp_cache_flush();
            } // 'Классификатор'


            if ($reader->name == 'Товары') {
                $valid = true;

                $all1cProducts = (array) get_option('all1cProducts');

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

                while ($reader->read() &&
                    !($reader->name == 'Товары' &&
                        $reader->nodeType == \XMLReader::END_ELEMENT)
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
                            $all1cProducts[] = $productEntry['ID'];

                            update_option('all1cProducts', $all1cProducts);

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

                        $all1cProducts[] = $productEntry['ID'];

                        update_option('all1cProducts', $all1cProducts);

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

                delete_option('product_cat_children');
                wp_cache_flush();
            }

            if ($reader->name == 'ПакетПредложений') {
                $this->onlyChanges = $reader->getAttribute('СодержитТолькоИзменения');
                $valid = true;

                if (!isset($_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'])) {
                    $_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'] = [];
                }

                if (!isset($_SESSION['IMPORT_1C']['offers_parse'])) {
                    while ($reader->read() &&
                        !($reader->name == 'ПакетПредложений' &&
                            $reader->nodeType == \XMLReader::END_ELEMENT)) {
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

                                    if (isset($element->Остатки)) {
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

                                    if (isset($element->Остатки)) {
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
            } // end 'Предложения'
        } // end parce

        \wp_defer_term_counting(false);

        return $valid;
    }
}
