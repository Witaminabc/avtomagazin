<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Product;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class ProductAndVariationStock
{
    public static function resolve($element)
    {
        $stock = 0;

        if (
            isset($element->КоличествоНаСкладах) &&
            isset($element->КоличествоНаСкладах->КоличествоНаСкладе)
        ) {
            foreach ($element->КоличествоНаСкладах->КоличествоНаСкладе as $store) {
                $stock += (float) $store->{Bootstrap::XML_TAGS['stock']};
            }
            // schema 3.1
        } elseif (
            isset($element->Остатки) &&
            isset($element->Остатки->Остаток)
        ) {
            foreach ($element->Остатки->Остаток as $stockElement) {
                if (isset($stockElement->Склад)) {
                    $stock += (float) $stockElement->Склад->Количество;
                } elseif (isset($stockElement->Количество)) {
                    $stock += (float) $stockElement->Количество;
                }
            }
        } else {
            $stock = (string) $element->{Bootstrap::XML_TAGS['stock']}
                ? (float) $element->{Bootstrap::XML_TAGS['stock']}
                : 0;
        }

        return [
            '_stock' => $stock,
            '_separate_warehouse_stock' => self::resolveSeparate($element)
        ];
    }

    public static function resolveSeparate($element)
    {
        $stocks = [];

        if (isset($element->Склад)) {
            foreach ($element->Склад as $store) {
                if (!isset($stocks[(string) $store['ИдСклада']])) {
                    $stocks[(string) $store['ИдСклада']] = 0;
                }

                $stocks[(string) $store['ИдСклада']] += (float) $store['КоличествоНаСкладе'];
            }
            // schema 3.1
        } elseif (
            isset($element->Остатки) &&
            isset($element->Остатки->Остаток) &&
            isset($element->Остатки->Остаток->Склад)
        ) {
            foreach ($element->Остатки->Остаток->Склад as $stockElement) {
                if (!isset($stocks[(string) $stockElement->Ид])) {
                    $stocks[(string) $stockElement->Ид] = 0;
                }

                $stocks[(string) $stockElement->Ид] += (float) $stockElement->Количество;
            }
        } elseif (
            isset($element->КоличествоНаСкладах) &&
            isset($element->КоличествоНаСкладах->КоличествоНаСкладе)
        ) {
            foreach ($element->КоличествоНаСкладах->КоличествоНаСкладе as $stockElement) {
                if (!isset($stocks[(string) $stockElement->Ид])) {
                    $stocks[(string) $stockElement->Ид] = 0;
                }

                $stocks[(string) $stockElement->Ид] += (float) $stockElement->Количество;
            }
        }

        return $stocks;
    }

    public static function set($productId, $stockData, $parentProductID = false)
    {
        global $wpdb;

        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $products1cStockNull = isset($settings['products_stock_null_rule'])
            ? (int) $settings['products_stock_null_rule']
            : '';

        update_post_meta($productId, '_stock', $stockData['_stock']);
        update_post_meta($productId, '_separate_warehouse_stock', $stockData['_separate_warehouse_stock']);
        Logger::logChanges('update', 'product_stock', $productId, $stockData['_stock']);

        // resolve stock status
        if (get_post_meta($productId, '_price', true) &&
            ($stockData['_stock'] > 0 || $products1cStockNull === 1)
        ) {
            if ($stockData['_stock'] <= 0 && $products1cStockNull === 1) {
                \update_post_meta($productId, '_manage_stock', 'no');
            } else {
                \update_post_meta(
                    $productId,
                    '_manage_stock',
                    get_option('woocommerce_manage_stock')
                );
            }

            // enable variable
            if ($parentProductID && get_option('woocommerce_manage_stock') === 'yes') {
                $wpdb->update(
                    $wpdb->posts,
                    ['post_status' => 'publish'],
                    ['ID' => $productId]
                );
            }

            Product::show($productId, true);

            // set stock variation
            if ($parentProductID) {
                $_SESSION['IMPORT_1C']['setTerms'][$parentProductID]['is_visible'] = true;
            }
        } else {
            if ($parentProductID && get_option('woocommerce_manage_stock') === 'yes') {
                // disable variable
                $wpdb->update(
                    $wpdb->posts,
                    ['post_status' => 'private'],
                    ['ID' => $productId]
                );
            }

            Product::hide($productId, true);
        }
    }
}
