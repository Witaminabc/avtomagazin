<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess;

use Itgalaxy\Wc\Exchange1c\Includes\Helper;
use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class SaleProcessStarter
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if ($_GET['mode'] == 'checkauth') {
            $sessionId = session_id();

            echo "success\n"
                . session_name()
                . "\n"
                . $sessionId
                . "\n";
            // 1c response does not require escape

            $_SESSION['logSynchronizeProcessFile'] = $_SESSION['synchronization1cPathLogs']
                . '/orders_get_send_'
                . date_i18n('Y.m.d_H-i-s')
                . '.log1c';

            Logger::clearOldLogs();
            Logger::logProtocol('success', $sessionId);
        } else {
            $baseName = basename($_SESSION['1cExchangefilename']);

            if ($_GET['mode'] == 'init') {
                if (isset($_GET['version'])) {
                    $_SESSION['version'] = $_GET['version'];
                }

                if (!is_dir($_SESSION['synchronization1cPathTemp'])) {
                    echo "failure\n " . Helper::convertMessage(esc_html__('Initialization Error!', 'itgalaxy-woocommerce-1c'));
                    // 1c response does not require escape

                    $message = 'failure&emsp;' . esc_html__('Initialization Error!', 'itgalaxy-woocommerce-1c');
                } else {
                    if (isset($_SESSION['version'])) {
                        echo "zip=no\n"
                            . "file_limit=" . Helper::getFileSizeLimit() . "\n"
                            . "sessid=\n"
                            . "version=2.08";
                    } else {
                        echo "zip=no\n"
                            . "file_limit=" . Helper::getFileSizeLimit() . "\n";
                    }
                    // 1c response does not require escape

                    $message = 'zip=no, file_limit=' . Helper::getFileSizeLimit();
                }

                Logger::logProtocol($message);
            } else {
                if ($_GET['mode'] === 'query') {
                    $version = $this->resolveVersion();

                    // if exchange order not enabled
                    if (empty($settings['send_orders'])) {
                        $this->notEnabled($version);
                    }

                    $dom = new \DOMDocument;
                    $dom->loadXML(
                        "<?xml version='1.0' encoding='utf-8'?><КоммерческаяИнформация></КоммерческаяИнформация>"
                    );
                    $xml = simplexml_import_dom($dom);
                    unset($dom);

                    $xml->addAttribute('ВерсияСхемы', $version);
                    $xml->addAttribute('ДатаФормирования', date('Y-m-d H:i', current_time('timestamp', 0)));

                    $orders = $this->getOrders();

                    Logger::logProtocol('count orders', count($orders));

                    if (count($orders) > 0) {
                        $currency = $this->getCurrency();

                        foreach ($orders as $orderID) {
                            $order = \wc_get_order($orderID);

                            if (!$order) {
                                Logger::logProtocol('wrong order', $orderID);

                                continue;
                            }

                            $orderData = $order->get_data();
                            $shippingAddress = $order->get_formatted_shipping_address();
                            $billingAddress = $order->get_formatted_billing_address();

                            $document = $xml->addChild('Документ');

                            $document->addChild('Ид', $order->get_id());
                            $document->addChild('Номер', $order->get_order_number());
                            $document->addChild('Дата', $order->get_date_created()->date_i18n('Y-m-d'));
                            $document->addChild('Время', $order->get_date_created()->date_i18n('H:i'));
                            $document->addChild('ХозОперация', 'Заказ товара');
                            $document->addChild('Роль', 'Продавец');
                            $document->addChild('Валюта', $currency);
                            $document->addChild('Курс', 1);
                            $document->addChild('Сумма', $orderData['total']);
                            $document->addChild(
                                'Комментарий',
                                apply_filters(
                                    'itglx_wc1c_xml_order_comment',
                                    htmlspecialchars($order->get_customer_note()),
                                    $order
                                )
                            );

                            // can be used if you want to transfer custom data
                            $moreOrderInfo = apply_filters('itglx_wc1c_xml_order_info_custom', [], $orderID);

                            if ($moreOrderInfo) {
                                foreach ($moreOrderInfo as $key => $moreOrderInfoValue) {
                                    $document->addChild($key, $moreOrderInfoValue);
                                }
                            }

                            $contragents = $document->addChild('Контрагенты');

                            if (!function_exists('itglx_wc1c_xml_order_contragent_data')) {
                                $contragent = $contragents->addChild('Контрагент');
                                $contragent->addChild('Ид', $order->get_customer_id());
                                $contragent->addChild(
                                    'Наименование',
                                    htmlspecialchars(
                                        $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
                                    )
                                );
                                $contragent->addChild('Роль', 'Покупатель');
                                $contragent->addChild(
                                    'ПолноеНаименование',
                                    htmlspecialchars(
                                        $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
                                    )
                                );
                                $contragent->addChild('Фамилия', htmlspecialchars($order->get_billing_last_name()));
                                $contragent->addChild('Имя', htmlspecialchars($order->get_billing_first_name()));

                                $address = $contragent->addChild('АдресРегистрации');
                                $address->addChild('Вид', 'Адрес доставки');
                                $address->addChild('Представление', htmlspecialchars($shippingAddress));

                                $addressField = $address->addChild('АдресноеПоле');
                                $addressField->addChild('Тип', 'Почтовый индекс');
                                $addressField->addChild('Значение', htmlspecialchars($order->get_shipping_postcode()));

                                $addressField = $address->addChild('АдресноеПоле');
                                $addressField->addChild('Тип', 'Регион');
                                $addressField->addChild('Значение', htmlspecialchars($order->get_shipping_state()));

                                $addressField = $address->addChild('АдресноеПоле');
                                $addressField->addChild('Тип', 'Город');
                                $addressField->addChild('Значение', htmlspecialchars($order->get_shipping_city()));

                                $addressField = $address->addChild('АдресноеПоле');
                                $addressField->addChild('Тип', 'Улица');
                                $addressField->addChild(
                                    'Значение',
                                    htmlspecialchars($order->get_formatted_shipping_address())
                                );

                                $contacts = $contragent->addChild('Контакты');

                                $contact = $contacts->addChild('Контакт');
                                $contact->addChild('Тип', 'Почта');
                                $contact->addChild('Значение', $order->get_billing_email());

                                if ($order->get_billing_phone()) {
                                    $phone = htmlspecialchars($order->get_billing_phone());
                                    $contact = $contacts->addChild('Контакт');
                                    $contact->addChild('Тип', 'ТелефонРабочий');
                                    $contact->addChild('Представление', $phone);
                                    $contact->addChild('Значение', $phone);
                                }
                            } else {
                                itglx_wc1c_xml_order_contragent_data($contragents, $order);
                            }

                            if ($order->get_discount_total() > 0) {
                                $discounts = $document->addChild('Скидки');
                                $discount = $discounts->addChild('Скидка');
                                $discount->addChild('Наименование', 'Скидка');
                                $discount->addChild('Сумма', $order->get_discount_total());
                                $discount->addChild('УчтеноВСумме', 'true');
                            }

                            $productsXml = $document->addChild('Товары');

                            $products = [];

                            foreach ($order->get_items() as $item) {
                                $products[] = [
                                    'id' => $item['variation_id'] ? $item['variation_id'] : $item['product_id'],
                                    'productId' => $item['product_id'],
                                    'variationId' => $item['variation_id'],
                                    '_id_1c' => get_post_meta(
                                        $item['variation_id'] ? $item['variation_id'] : $item['product_id'],
                                        '_id_1c',
                                        true
                                    ),
                                    'quantity' => $item['qty'],
                                    'name' => htmlspecialchars($item['name']),
                                    'priceInOrder' => $item['line_subtotal'] / $item['qty'],
                                    'lineSubtotal' => $item['line_subtotal']
                                ];
                            }

                            foreach ($products as $product) {
                                $productXml = $productsXml->addChild('Товар');

                                if (!empty($product['_id_1c'])) {
                                    $productXml->addChild('Ид', $product['_id_1c']);
                                }

                                $productXml->addChild('Наименование', htmlspecialchars($product['name']));

                                if ($unit = get_post_meta($product['id'], '_unit', true)) {
                                    $base = $productXml->addChild('БазоваяЕдиница', $unit['value']);
                                    $base->addAttribute('Код', $unit['code']);
                                    $base->addAttribute('НаименованиеПолное', $unit['nameFull']);
                                    $base->addAttribute('МеждународноеСокращение', $unit['internationalAcronym']);
                                } else {
                                    $base = $productXml->addChild('БазоваяЕдиница', 'шт');
                                    $base->addAttribute('Код', 796);
                                    $base->addAttribute('НаименованиеПолное', 'Штука');
                                    $base->addAttribute('МеждународноеСокращение', 'PCE');
                                }

                                $productXml->addChild('ЦенаЗаЕдиницу', $product['priceInOrder']);
                                $productXml->addChild('Количество', $product['quantity']);
                                $productXml->addChild('Сумма', $product['lineSubtotal']);

                                $details = $productXml->addChild('ЗначенияРеквизитов');

                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'ВидНоменклатуры');
                                $detail->addChild('Значение', 'Товар');

                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'ТипНоменклатуры');
                                $detail->addChild('Значение', 'Товар');

                                // can be used if you want to transfer custom data
                                $moreProductInfo = apply_filters(
                                    'itglx_wc1c_xml_product_info_custom',
                                    [],
                                    $product['productId'],
                                    $product['variationId']
                                );

                                if ($moreProductInfo) {
                                    foreach ($moreProductInfo as $key => $moreProductInfoValue) {
                                        $productXml->addChild($key, $moreProductInfoValue);
                                    }
                                }
                            }

                            if ($order->get_shipping_total() > 0) {
                                $productXml = $productsXml->addChild('Товар');
                                $productXml->addChild('Ид', 'ORDER_DELIVERY');
                                $productXml->addChild('Наименование', htmlspecialchars($order->get_shipping_method()));

                                $base = $productXml->addChild('БазоваяЕдиница', 'шт');
                                $base->addAttribute('Код', 796);
                                $base->addAttribute('НаименованиеПолное', 'Штука');
                                $base->addAttribute('МеждународноеСокращение', 'PCE');

                                $productXml->addChild('ЦенаЗаЕдиницу', $order->get_shipping_total());
                                $productXml->addChild('Количество', '1');
                                $productXml->addChild('Сумма', $order->get_shipping_total());

                                $details = $productXml->addChild('ЗначенияРеквизитов');

                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'ВидНоменклатуры');
                                $detail->addChild('Значение', 'Услуга');

                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'ТипНоменклатуры');
                                $detail->addChild('Значение', 'Услуга');
                            }

                            $details = $document->addChild('ЗначенияРеквизитов');

                            if (wc_get_payment_gateway_by_order($order)) {
                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'Способ оплаты');
                                $detail->addChild(
                                    'Значение',
                                    htmlspecialchars(wc_get_payment_gateway_by_order($order)->title)
                                );
                            }

                            // order status
                            $detail = $details->addChild('ЗначениеРеквизита');

                            if (isset($_SESSION['version']) && (float) $_SESSION['version'] > 3) {
                                $detail->addChild('Наименование', 'Статус заказа ИД');
                            } else {
                                $detail->addChild('Наименование', 'Статус заказа');
                            }

                            $detail->addChild('Значение', htmlspecialchars($order->get_status()));


                            $detail = $details->addChild('ЗначениеРеквизита');
                            $detail->addChild('Наименование', 'Дата изменения статуса');
                            $detail->addChild('Значение', $order->get_date_modified()->date_i18n('Y-m-d H:i'));

                            if ($order->get_shipping_method()) {
                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'Способ доставки');
                                $detail->addChild('Значение', htmlspecialchars($order->get_shipping_method()));

                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'Доставка разрешена');
                                $detail->addChild('Значение', 'true');
                            }

                            $detail = $details->addChild('ЗначениеРеквизита');
                            $detail->addChild('Наименование', 'Адрес доставки');
                            $detail->addChild('Значение', htmlspecialchars($shippingAddress));

                            $detail = $details->addChild('ЗначениеРеквизита');
                            $detail->addChild('Наименование', 'Адрес плательщика');
                            $detail->addChild('Значение', htmlspecialchars($billingAddress));

                            if ($order->get_status() == 'cancelled') {
                                $detail = $details->addChild('ЗначениеРеквизита');
                                $detail->addChild('Наименование', 'ПометкаУдаления');
                                $detail->addChild('Значение', 'true');
                            }
                        }
                    }

                    $this->sendResponse($xml);

                    Logger::logProtocol('order query send result');
                } elseif ($_GET['mode'] == 'success') {
                    $settings['send_orders_last_success_export'] = str_replace(' ', 'T', date_i18n('Y-m-d H:i'));
                    update_option(Bootstrap::OPTIONS_KEY, $settings);

                    echo "success\n";
                    // 1c response does not require escape

                    Logger::logProtocol('1c send success');
                } elseif ($_GET['mode'] == 'file') {
                    if (!get_option('get_orders_synchronization')) {
                        echo "success\n";
                        // 1c response does not require escape

                        Logger::logProtocol('success');

                        exit();
                    }

                    if (function_exists('file_get_contents')) {
                        $data = file_get_contents('php://input');
                    } elseif (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                        $data = &$GLOBALS['HTTP_RAW_POST_DATA'];
                    } else {
                        $data = false;
                    }

                    if ($data !== false) {
                        if (!is_writable(dirname($_SESSION['1cExchangefilename']))
                            || (
                                file_exists($_SESSION['1cExchangefilename'])
                                && !is_writable($_SESSION['1cExchangefilename'])
                            )
                        ) {
                            echo "failure\n"
                                . Helper::convertMessage(
                                    esc_html__('The directory / file is not writable', 'itgalaxy-woocommerce-1c')
                                    . ": {$baseName}"
                                );
                            // 1c response does not require escape

                            $message = 'failure&emsp;'
                                . esc_html__('The directory / file is not writable', 'itgalaxy-woocommerce-1c')
                                . ": {$baseName}";
                        } else {
                            $fp = fopen($_SESSION['1cExchangefilename'], 'ab');
                            $result = fwrite($fp, $data);

                            if ($result === mb_strlen($data, 'latin1')) {
                                echo "success\n";
                                // 1c response does not require escape

                                $message = 'success';
                            } else {
                                echo "failure\n "
                                    . Helper::convertMessage(esc_html__('Error writing file!', 'itgalaxy-woocommerce-1c'));
                                // 1c response does not require escape

                                $message = 'failure&emsp;' . esc_html__('Error writing file!', 'itgalaxy-woocommerce-1c');
                            }
                        }
                    } else {
                        echo "failure\n "
                            . Helper::convertMessage(esc_html__('Error reading http stream!', 'itgalaxy-woocommerce-1c'));
                        // 1c response does not require escape

                        $message = 'failure&emsp;'
                            . esc_html__('Error reading http stream!', 'itgalaxy-woocommerce-1c');
                    }

                    Logger::logProtocol($message);
                }
            }

            exit();
        }
    }

    private function resolveVersion()
    {
        $version = '2.05';

        if (isset($_SESSION['version']) && (float) $_SESSION['version'] > 2.08) {
            $version = '2.08';
        }

        return $version;
    }

    private function getOrders()
    {
        global $wpdb;

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['send_orders_last_success_export'])) {
            $lastTime = $settings['send_orders_last_success_export'];
        } else {
            $lastTime = '2019-10-01 00:00:00';
        }

        $lastTime = date_i18n('Y-m-d H:i:s', strtotime($lastTime));

        Logger::logProtocol('start orders date', $lastTime);

        $statuses = [];

        foreach (wc_get_order_statuses() as $status => $_) {
            $statuses[] = $status;
        }

        $placeholders = array_fill(0, count($statuses), '%s');
        $format = implode(', ', $placeholders);
        $params = $statuses;

        array_unshift($params, $lastTime);

        $orders = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT `ID` FROM `{$wpdb->posts}`
                              WHERE `post_modified` >= '%s'
                              AND `post_type` = 'shop_order'
                              AND `post_status` IN ({$format})
                              ORDER BY `post_modified`",
                $params
            )
        );

        return $orders;
    }

    private function getCurrency()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $basePriceType = isset($settings['price_type_1'])
            ? $settings['price_type_1']
            : '';
        $allPriceTypes = get_option('all_prices_types');

        // if empty, then use the first
        if (empty($basePriceType) && $allPriceTypes) {
            $value = reset($allPriceTypes);
            $basePriceType = $value['id'];
        }

        $currency = 'руб';

        if (!empty($basePriceType) && !empty($allPriceTypes[$basePriceType])) {
            $currency = $allPriceTypes[$basePriceType]['currency'];
        }

        return $currency;
    }

    private function sendResponse($xml)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['send_orders_response_encoding'])) {
            $resultEncoding = $settings['send_orders_response_encoding'];
        } else {
            if (isset($_SESSION['version'])) {
                $resultEncoding = 'utf-8';
            } else {
                $resultEncoding = 'windows-1251';
            }
        }

        switch ($resultEncoding) {
            case 'utf-8':
                header("Content-Type: text/xml; charset=utf-8");

                echo $xml->asXML();
                // 1c response does not require escape
                break;
            default:
                header("Content-Type: text/xml; charset=windows-1251");

                echo mb_convert_encoding(
                    str_replace('encoding="utf-8"', 'encoding="windows-1251"', $xml->asXML()),
                    'cp1251',
                    'utf-8'
                );
                // 1c response does not require escape
                break;
        }
    }

    private function notEnabled($version)
    {
        $dom = new \DOMDocument;
        $dom->loadXML(
            "<?xml version='1.0' encoding='utf-8'?><КоммерческаяИнформация></КоммерческаяИнформация>"
        );
        $xml = simplexml_import_dom($dom);
        unset($dom);

        $xml->addAttribute('ВерсияСхемы', $version);
        $xml->addAttribute('ДатаФормирования', date('Y-m-d H:i', current_time('timestamp', 0)));

        $this->sendResponse($xml);

        Logger::logProtocol('order unload not enabled');

        exit();
    }
}
