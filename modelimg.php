<?php
//Загружаем каталог марок-моделей
$filemark = file_get_contents('/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/mark.csv');
//Распарсиваем
$filemark = explode (PHP_EOL,$filemark);
$countfile = count($filemark);
//Подгружаем библиотеки wordpress
require_once(dirname(__FILE__) . '/wp-load.php');
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

//По каждой паре марка-модель-год-двигатель создаем пост
$fp = fopen("/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/modeltemp.csv", "a");
foreach ($filemark as $fkey => $fvalue) {
    /*if ($fkey <= 747 || $fkey > 2412) {
        continue;
    }*/
	$filemark[$fkey]=explode (';',$fvalue);

	// добавляем изображение и получаем его ID
	$img1='https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-markimage-jpg/'.$filemark[$fkey][0].'.jpg';
	if ($img0 !== $img1) {
		$img0='https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-markimage-jpg/'.$filemark[$fkey][0].'.jpg';
		$thumbid = media_sideload_image($img0, 0, $desc = $filemark[$fkey][1], 'id');
		$text = $filemark[$fkey][1].";".$thumbid."\n";
		fwrite($fp, $text);
	}
}
fclose($fp);
?>