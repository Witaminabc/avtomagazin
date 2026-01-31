<?php
define('FPDF_FONTPATH',__DIR__."/fpdf/font/");
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
global $user_ID;

require_once( __DIR__."/fpdf/fpdf.php" );
//начало конфигурации
$textColour = array( 0, 0, 0 );
$headerColour = array( 100, 100, 100 );
$tableHeaderTopTextColour = array( 255, 255, 255 );
$tableHeaderTopFillColour = array( 125, 152, 179 );
$tableHeaderTopProductTextColour = array( 0, 0, 0 );
$tableHeaderTopProductFillColour = array( 143, 173, 204 );
$tableHeaderLeftTextColour = array( 99, 42, 57 );
$tableHeaderLeftFillColour = array( 184, 207, 229 );
$tableBorderColour = array( 50, 50, 50 );
$tableRowFillColour = array( 213, 170, 170 );
$reportName = "Данные вашего заказа";
$reportNameYPos = 20;
$logoFile = "basket-img.png";
$logoXPos = 50;
$logoYPos = 108;
$logoWidth = 110;
//конец конфигурации
global $woocommerce;
					$total = 0;
					if(empty($_GET['new_order'])) return;
					$new_order = intval($_GET['new_order']);
                  if(wc_get_order($new_order) == false) return;
$order = wc_get_order( $new_order );
$order_id = $order->get_id();
$user_id = $order->get_user_id();
if(!is_user_logged_in()) return;
if($user_ID != $user_id) return;
 //получить товары заказа
  $order_items = $order->get_items();
   $data = $order->get_data();
$pdf = new FPDF( 'P', 'mm', 'A4' );
$pdf->SetTextColor( $textColour[0], $textColour[1], $textColour[2] );
$pdf->AddPage();
$pdf->SetDisplayMode(real,'default');
$pdf->AddFont("georgia");
    $pdf->SetFont("georgia");
	   $pdf->SetFontSize(16);
    $pdf->SetXY(10,10);
	$pdf->SetTitle(iconv("utf-8", "windows-1251", "Report data ".$order_id));
$pdf->Ln( $reportNameYPos );
$pdf->Cell( 0, 15, iconv("utf-8", "windows-1251", $reportName), 0, 0, 'C' );
//калантитул
//товары
foreach($order_items as $key_item => $item){
	$item_data = $item->get_data();
		$order_product = $item->get_product();
$pdf->Ln( 16 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Название товара: ".strip_tags($item_data['name'])));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Кол-во товара: ".$item_data['quantity']));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Цена: ".($item_data['quantity'] * $order_product->get_price())." р"));
$total += ($order_product->get_price() * $item_data['quantity']);
} //endforeach
$pdf->Ln( 16 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Сумма: ".$total." р"));
//ваши данные
$pdf->Ln( 16 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Имя, Фамилия: ".$data['billing']['first_name']." ".$data['billing']['last_name']));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Адрес: ".$data['billing']['address_1']));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Телефон: ".$data['billing']['phone']));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Почта: ".$data['billing']['email']));
//доставка
//$pdf->SetFont( 'georgia', '', 20 );
//$pdf->Write( 19, iconv("utf-8", "windows-1251", "Доставка:") );
$pdf->Ln( 12 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Доставка : " . get_post_meta($order_id, 'shipping_delivery', true).", ". get_post_meta($order_id, 'shipping_pickup', true)));
$pdf->Ln( 16 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251","Номер заказа: " . $order_id));
//$pdf->Write( 6, iconv("utf-8", "windows-1251", get_post_meta($order_id, 'shipping_pickup', true)));
//оплата
$pdf->SetFont( 'georgia', '', 20 );
//$pdf->Write( 19, iconv("utf-8", "windows-1251", "Оплата : ") );
$pdf->Ln( 16 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Оплата : " . get_post_meta($order_id, 'billing_payment', true)));


$pdf->Output( "report_order.pdf", "I" );
?>