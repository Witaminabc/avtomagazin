<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

class Stocks
{
    public static function process(&$reader)
    {
        // run once per exchange
        if (isset($_SESSION['IMPORT_1C']['stocks_parse'])) {
            return;
        }

        $stocks = [];

        while ($reader->read() &&
            !($reader->name == 'Склады' &&
                $reader->nodeType == \XMLReader::END_ELEMENT)
        ) {
            if (
                $reader->name == 'Склад' &&
                $reader->nodeType == \XMLReader::ELEMENT
            ) {
                $element = $reader->readOuterXml();
                $element = simplexml_load_string(trim($element));

                $stocks[(string) $element->Ид] = [
                    'Наименование' => (string) $element->Наименование
                ];

                if (
                    isset($element->Адрес) &&
                    isset($element->Адрес->АдресноеПоле)
                ) {
                    $stocks[(string) $element->Ид]['Адрес'] = [];

                    foreach ($element->Адрес->АдресноеПоле as $requisite) {
                        $stocks[(string) $element->Ид]['Адрес'][(string) $requisite->Тип]
                            = (string) $requisite->Значение;
                    }
                }

                if (
                    isset($element->Контакты) &&
                    isset($element->Контакты->Контакт)
                ) {
                    $stocks[(string) $element->Ид]['Контакты'] = [];

                    foreach ($element->Контакты->Контакт as $contact) {
                        $stocks[(string) $element->Ид]['Контакты'][]
                            = [(string) $contact->Тип => (string) $contact->Значение];
                    }
                }
            }
        }

        if (count($stocks)) {
            update_option('all_1c_stocks', $stocks);
        }

        $_SESSION['IMPORT_1C']['stocks_parse'] = true;
    }
}
