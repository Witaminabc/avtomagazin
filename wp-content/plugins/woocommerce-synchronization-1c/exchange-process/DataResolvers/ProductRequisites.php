<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class ProductRequisites
{
    public static function process($element)
    {
        $requisites = [
            'fullName' => '',
            'weight' => 0,
            'htmlPostContent' => '',
            'allRequisites' => []
        ];

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (
            isset($element->ЗначенияРеквизитов) &&
            isset($element->ЗначенияРеквизитов->ЗначениеРеквизита)
        ) {
            foreach ($element->ЗначенияРеквизитов->ЗначениеРеквизита as $requisite) {
                $requisiteName = trim((string) $requisite->{Bootstrap::XML_TAGS['name']});
                $requisites['allRequisites'][$requisiteName] = (string) $requisite->{Bootstrap::XML_TAGS['value']};

                switch ($requisiteName) {
                    case 'Полное наименование':
                    case 'Повне найменування':
                        $fullName = (string) $requisite->{Bootstrap::XML_TAGS['value']};

                        if (!empty($fullName) && !empty($settings['product_use_full_name'])) {
                            $requisites['fullName'] = $fullName;
                        }

                        break;
                    case 'ОписаниеВФорматеHTML':
                        $htmlPostContent = html_entity_decode((string) $requisite->{Bootstrap::XML_TAGS['value']});

                        if (!empty($htmlPostContent) && !empty($settings['use_html_description'])) {
                            $requisites['htmlPostContent'] = $htmlPostContent;
                        }

                        break;
                    case 'Вес':
                        $weight = (float) $requisite->{Bootstrap::XML_TAGS['value']};

                        if ($weight > 0) {
                            $requisites['weight'] = $weight;
                        }

                        break;
                    case 'Длина':
                        $value = (float) $requisite->{Bootstrap::XML_TAGS['value']};

                        if ($value > 0) {
                            $requisites['length'] = $value;
                        }

                        break;
                    case 'Ширина':
                        $value = (float) $requisite->{Bootstrap::XML_TAGS['value']};

                        if ($value > 0) {
                            $requisites['width'] = $value;
                        }

                        break;
                    case 'Высота':
                        $value = (float) $requisite->{Bootstrap::XML_TAGS['value']};

                        if ($value > 0) {
                            $requisites['height'] = $value;
                        }

                        break;
                    default:
                        // Nothing
                        break;
                }
            }
        }

        return $requisites;
    }
}
