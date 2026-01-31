<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\HeartBeat;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Term;

class GlobalProductAttributes
{
    public static function process(&$reader)
    {
        global $wpdb;

        $numberOfOptions = 0;

        if (!isset($_SESSION['IMPORT_1C']['numberOfOptions'])) {
            $_SESSION['IMPORT_1C']['numberOfOptions'] = 0;
        }

        $options = get_option('all_product_options');

        if (!is_array($options)) {
            $options = [];
        }

        while ($reader->read()
            && !($reader->name == Bootstrap::XML_TAGS['options']
                && $reader->nodeType == \XMLReader::END_ELEMENT)
        ) {
            if ($reader->name == Bootstrap::XML_TAGS['option']
                && $reader->nodeType == \XMLReader::ELEMENT
            ) {
                if (!HeartBeat::nextTerm()) {
                    return false;
                }

                $numberOfOptions++;

                if ($numberOfOptions < $_SESSION['IMPORT_1C']['numberOfOptions']) {
                    continue;
                }

                $element = $reader->readOuterXml();
                $element = simplexml_load_string(trim($element));

                if (!isset($element->{Bootstrap::XML_TAGS['id']})) {
                    unset($element);
                    $_SESSION['IMPORT_1C']['numberOfOptions'] = $numberOfOptions;
                    continue;
                }

                $attribute = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM `{$wpdb->prefix}woocommerce_attribute_taxonomies` WHERE `id_1c` = '%s'",
                        (string) $element->{Bootstrap::XML_TAGS['id']}
                    )
                );

                if ($attribute) {
                    $attributeTaxName = 'pa_' . $attribute->attribute_name;

                    $attributeUpdate = [
                        'attribute_label' => (string) $element->{Bootstrap::XML_TAGS['name']}
                    ];

                    $wpdb->update(
                        $wpdb->prefix . 'woocommerce_attribute_taxonomies',
                        $attributeUpdate,
                        [
                            'attribute_id' => $attribute->attribute_id
                        ]
                    );
                } else {
                    $attributeTaxName = uniqid();

                    $attributeCreate = [
                        'attribute_label' => (string) $element->{Bootstrap::XML_TAGS['name']},
                        'attribute_name' => $attributeTaxName,
                        'attribute_type' => 'select',
                        'attribute_public' => 0,
                        'attribute_orderby' => 'menu_order',
                        'id_1c' => (string) $element->{Bootstrap::XML_TAGS['id']}
                    ];

                    $wpdb->insert(
                        $wpdb->prefix . 'woocommerce_attribute_taxonomies',
                        $attributeCreate
                    );

                    do_action('woocommerce_attribute_added', $wpdb->insert_id, $attributeCreate);

                    flush_rewrite_rules();
                    delete_transient('wc_attribute_taxonomies');

                    return false;
                }

                $type = (string) $element->ТипЗначений;

                $options[(string) $element->{Bootstrap::XML_TAGS['id']}] = [
                    'taxName' => $attributeTaxName,
                    'type' => $type,
                    'values' => []
                ];

                if (isset($element->ВариантыЗначений)
                    && isset($element->ВариантыЗначений->$type)
                ) {
                    foreach ($element->ВариантыЗначений->$type as $variant) {
                        if (empty((string) $variant->{Bootstrap::XML_TAGS['value']})) {
                            continue;
                        }

                        $uniqId1c = md5((string) $variant->ИдЗначения . $attributeTaxName);
                        $variantTerm = Term::getTermIdByMeta($uniqId1c);

                        if (!$variantTerm) {
                            $variantTerm = Term::getTermIdByMeta((string) $variant->ИдЗначения);
                        }

                        if ($variantTerm) {
                            $realTerm = get_term($variantTerm, $attributeTaxName);

                            if (!$realTerm) {
                                $variantTerm = false;
                            }
                        }

                        if ($variantTerm) {
                            wp_update_term(
                                $variantTerm,
                                $attributeTaxName,
                                [
                                    'name' => (string) $variant->{Bootstrap::XML_TAGS['value']},
                                    'parent' => 0
                                ]
                            );
                        } else {
                            $variantTerm =
                                wp_insert_term(
                                    (string) $variant->{Bootstrap::XML_TAGS['value']},
                                    $attributeTaxName,
                                    [
                                        'slug' => uniqid(),
                                        'description' => '',
                                        'parent' => 0
                                    ]
                                );

                            if (is_wp_error($variantTerm)) {
                                print_r($variantTerm);
                                // 1c response does not require escape

                                exit();
                            }

                            $variantTerm = $variantTerm['term_id'];

                            // default meta value by ordering
                            update_term_meta($variantTerm, 'order_' . $attributeTaxName, 0);

                            Term::update1cId($variantTerm, $uniqId1c);
                        }

                        $options[(string) $element->{Bootstrap::XML_TAGS['id']}]['values'][(string) $variant->ИдЗначения] = $variantTerm;
                    }
                }

                if (count($options)) {
                    update_option('all_product_options', $options);
                }

                $_SESSION['IMPORT_1C']['numberOfOptions'] = $numberOfOptions;
                delete_option($attributeTaxName . '_children');
                unset($element);
            }
        }

        if (count($options)) {
            update_option('all_product_options', $options);
        }

        $_SESSION['IMPORT_1C']['optionsIsParse'] = true;

        return false;
    }
}
