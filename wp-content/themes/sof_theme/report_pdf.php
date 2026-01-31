<?php
define('FPDF_FONTPATH',__DIR__."/fpdf/font/");
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

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
$reportName = "Данные вашей корзины";
$reportNameYPos = 160;
$logoFile = "basket-img.png";
$logoXPos = 50;
$logoYPos = 108;
$logoWidth = 110;
$columnLabels = array( "Q1", "Q2", "Q3", "Q4" );
$rowLabels = array( "SupaWidget", "WonderWidget", "MegaWidget", "HyperWidget" );
$data = array(
array( 9940, 10100, 9490, 11730 ),
array( 19310, 21140, 20560, 22590 ),
array( 25110, 26260, 25210, 28370 ),
array( 27650, 24550, 30040, 31980 ),
);
//конец конфигурации
global $woocommerce;
					$total = 0;
if(count($woocommerce->cart->get_cart()) > 0){
	
$pdf = new FPDF( 'P', 'mm', 'A4' );
$pdf->SetTextColor( $textColour[0], $textColour[1], $textColour[2] );
$pdf->AddPage();
$pdf->SetDisplayMode(real,'default');
$pdf->AddFont("georgia");
    $pdf->SetFont("georgia");
	   $pdf->SetFontSize(10);
//    $pdf->SetXY(10,10);
	$pdf->SetTitle(iconv("utf-8", "windows-1251", "Basket data"));
//$pdf->Ln( $reportNameYPos );
//$pdf->Cell( 0, 15, iconv("utf-8", "windows-1251", $reportName), 0, 0, 'C' );
//калантитул
//$pdf->AddPage();
$pdf->SetTextColor( $headerColour[0], $headerColour[1], $headerColour[2] );
$pdf->SetFont( 'georgia', '', 17 );
$pdf->Cell( 0, 15, iconv("utf-8", "windows-1251", $reportName), 0, 0, 'C' );
$pdf->SetTextColor( $textColour[0], $textColour[1], $textColour[2] );
$pdf->SetFont( 'georgia', '', 20 );
$pdf->Write( 19, iconv("utf-8", "windows-1251", "В вашей корзине следующие товары:") );
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
$_product = $values['data'];
$total += ($_product->get_price() * $values['quantity']);
$pdf->Ln( 16 );
$pdf->SetFont( 'georgia', '', 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Название товара: ".strip_tags($_product->name)));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Кол-во товара: ".$values['quantity']));
$pdf->Ln( 12 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "Цена: ".($values['quantity'] * $_product->get_price())." р"));
	} //endforeach
$pdf->Ln( 16 );
$pdf->Write( 6, iconv("utf-8", "windows-1251", "К оплате: ".$total." р"));

$pdf->Output( "report.pdf", "I" );
} //endif count cart
else{
echo'В корзине нет товаров';
} //endelse
?>