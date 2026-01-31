<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

class PriceTypes
{
    public static function process(&$reader)
    {
        // run once per exchange
        if (isset($_SESSION['IMPORT_1C']['price_types_parse'])) {
            return;
        }

        $prices = [];

        while ($reader->read() &&
            !($reader->name == 'ТипыЦен' &&
                $reader->nodeType == \XMLReader::END_ELEMENT)
        ) {
            if (
                $reader->name == 'ТипЦены' &&
                $reader->nodeType == \XMLReader::ELEMENT
            ) {
                $element = $reader->readOuterXml();
                $element = simplexml_load_string(trim($element));

                $prices[(string) $element->Ид] = [
                    'id' => (string) $element->Ид,
                    'name' => (string) $element->Наименование,
                    'currency' => (string) $element->Валюта
                ];
            }
        }

        if (count($prices)) {
            update_option('all_prices_types', $prices);
        }

        $_SESSION['IMPORT_1C']['price_types_parse'] = true;
    }
}
