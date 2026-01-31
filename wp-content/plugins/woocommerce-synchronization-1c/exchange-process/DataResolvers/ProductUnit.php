<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

class ProductUnit
{
    public static function process($element)
    {
        if (isset($element->БазоваяЕдиница)) {
            return [
                'code' => (string) $element->БазоваяЕдиница['Код'],
                'nameFull' => (string) $element->БазоваяЕдиница['НаименованиеПолное'],
                'internationalAcronym' => (string) $element->БазоваяЕдиница['МеждународноеСокращение'],
                'value' => (string) $element->БазоваяЕдиница
            ];
        }

        return [];
    }
}
