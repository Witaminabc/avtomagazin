<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class VariationCharacteristicsToGlobalProductAttributes
{
    public static function process($element)
    {
        global $wpdb;

        $options = get_option('all_product_options', []);

        if (
            isset($element->ХарактеристикиТовара) &&
            isset($element->ХарактеристикиТовара->ХарактеристикаТовара)
        ) {
            foreach ($element->ХарактеристикиТовара->ХарактеристикаТовара as $property) {
                $label = (string) $property->{Bootstrap::XML_TAGS['name']};
                $taxByLabel = trim(strtolower($label));
                $taxByLabel = hash('crc32', $taxByLabel);

                $attributeName = 'simple_' . $taxByLabel;
                $attributeTaxName = 'pa_' . $attributeName;

                $attribute = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM `{$wpdb->prefix}woocommerce_attribute_taxonomies` WHERE `id_1c` = %s",
                        $attributeTaxName
                    )
                );

                // exists
                if ($attribute && isset($options[$attributeName])) {
                    continue;
                }

                $options[$attributeName] = [
                    'taxName' => $attributeTaxName,
                    'type' => 'simple',
                    'values' => []
                ];

                $attributeCreate = [
                    'attribute_label' => $label,
                    'attribute_name' => $attributeName,
                    'attribute_type' => 'select',
                    'attribute_public' => 0,
                    'attribute_orderby' => 'menu_order',
                    'id_1c' => $attributeTaxName
                ];

                $wpdb->insert(
                    $wpdb->prefix . 'woocommerce_attribute_taxonomies',
                    $attributeCreate
                );

                do_action('woocommerce_attribute_added', $wpdb->insert_id, $attributeCreate);

                flush_rewrite_rules();
                delete_transient('wc_attribute_taxonomies');

                delete_option($attributeTaxName . '_children');

                register_taxonomy($attributeTaxName, null);
            }

            if (count($options)) {
                update_option('all_product_options', $options);
            }
        }
    }
}
