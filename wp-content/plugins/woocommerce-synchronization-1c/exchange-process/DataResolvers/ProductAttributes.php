<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Term;
use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class ProductAttributes
{
    public static function process($element, $productId)
    {
        if (
            isset($element->ЗначенияСвойств) &&
            isset($element->ЗначенияСвойств->ЗначенияСвойства)
        ) {
            $productOptions = get_option('all_product_options');
            $productAttributes = get_post_meta($productId, '_product_attributes', true);

            if (empty($productAttributes)) {
                $productAttributes = [];
            }

            $currentAttributes = [];

            foreach ($element->ЗначенияСвойств->ЗначенияСвойства as $property) {
                if (
                    !empty($property->{Bootstrap::XML_TAGS['value']}) &&
                    !empty($productOptions[(string) $property->{Bootstrap::XML_TAGS['id']}])
                ) {
                    /*
                     * ignore attribute with full null value
                     * Example
                     *
                     <ЗначенияСвойства>
                        <Ид>5ff7fc04-d7d8-4c80-b6c6-46fe8bf9ceb2</Ид>
                        <Значение>00000000-0000-0000-0000-000000000000</Значение>
                     </ЗначенияСвойства>
                     */
                    if ((string) $property->{Bootstrap::XML_TAGS['value']} === '00000000-0000-0000-0000-000000000000') {
                        continue;
                    }

                    $attribute = $productOptions[(string) $property->{Bootstrap::XML_TAGS['id']}];

                    $optionTermID = false;

                    if (
                        $attribute['type'] === 'Справочник' &&
                        isset($attribute['values'][(string) $property->{Bootstrap::XML_TAGS['value']}]) &&
                        $attribute['values'][(string) $property->{Bootstrap::XML_TAGS['value']}] !== ''
                    ) {
                        $optionTermID = $attribute['values'][(string) $property->{Bootstrap::XML_TAGS['value']}];
                    } else {
                        $optionTermSlug = md5($attribute['taxName']
                            . (string) $property->{Bootstrap::XML_TAGS['value']});
                        $term = get_term_by('slug', $optionTermSlug, $attribute['taxName']);

                        if ($term) {
                            $optionTermID = $term->term_id;
                        } else {
                            $term =
                                wp_insert_term(
                                    (string) $property->{Bootstrap::XML_TAGS['value']},
                                    $attribute['taxName'],
                                    [
                                        'slug' => $optionTermSlug,
                                        'description' => '',
                                        'parent' => 0
                                    ]
                                );

                            if (!is_wp_error($term)) {
                                $optionTermID = $term['term_id'];
                            }
                        }
                    }

                    if ($optionTermID) {
                        if (!isset($productAttributes[$attribute['taxName']])) {
                            $productAttributes[$attribute['taxName']] = [
                                'name' => \wc_clean($attribute['taxName']),
                                'value' => '',
                                'position' => 0,
                                'is_visible' => 1,
                                'is_variation' => 0,
                                'is_taxonomy' => 1
                            ];
                        }

                        $currentAttributes[] = $attribute['taxName'];

                        Term::setObjectTerms(
                            $productId,
                            (int) $optionTermID,
                            $attribute['taxName']
                        );
                    }
                }
            }

            // remove non exists attributes
            $resolvedAttributes = $productAttributes;
            $allAttributeTaxes = \array_column($productOptions, 'taxName');

            foreach ($productAttributes as $key => $value) {
                if (empty($key)) {
                    unset($resolvedAttributes[$key]);

                    continue;
                }

                // not check variation attribute
                if ($value['is_variation']) {
                    continue;
                }

                // if not in current set and attribute was getting from 1C
                if (!in_array($key, $currentAttributes) && in_array($key, $allAttributeTaxes)) {
                    unset($resolvedAttributes[$key]);

                    \wp_set_object_terms(
                        $productId,
                        [],
                        $key
                    );
                }
            }

            update_post_meta($productId, '_product_attributes', $resolvedAttributes);
        }
    }
}
