<?php
$modelcount = file_get_contents("/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/modelcount.csv");
if ($modelcount > 37801) {exit;}
unlink("/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/modelcount.csv");
$fp = fopen("/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/modelcount.csv", "a");
fwrite($fp, $modelcount+200);
fclose($fp);
//Загружаем каталог изображений
$fileimg = file_get_contents('/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/modeltemp.csv');
//Распарсиваем
$fileimg = explode (PHP_EOL,$fileimg);
unset($fileimg[count($fileimg)-1]);
foreach ($fileimg as $fikey => $fivalue) {
    $fileimg[$fikey]=explode (';',$fivalue);
    $fileimglist[0][$fikey] = $fileimg[$fikey][0];
    $fileimglist[1][$fikey] = $fileimg[$fikey][1];
}
//Загружаем марки-модели
$filemark = file_get_contents('/home/c18551/xn-----8kcfbcq1fuaq.xn--p1ai/www/mark.csv');
$filemark = explode (PHP_EOL,$filemark);
$countfile = count($filemark);
//Подгружаем библиотеки wordpress
require_once(dirname(__FILE__) . '/wp-load.php');
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

//По каждой паре марка-модель-год-двигатель создаем пост
foreach ($filemark as $fkey => $fvalue) {
    if ($fkey < $modelcount) {
        continue;
    }
    if ($fkey > $modelcount+200-1) {exit;}
	$filemark[$fkey]=explode (';',$fvalue);

	$my_postarr = array(
		'post_title'    => $filemark[$fkey][1],
		'post_status'   => 'publish',
		'post_type'     => 'cars'
	);

	// добавляем пост и получаем его ID
	$my_post_id = wp_insert_post( $my_postarr );
	update_post_meta($my_post_id, 'model', $filemark[$fkey][2]);
	update_post_meta($my_post_id, 'year', $filemark[$fkey][4]);
	update_post_meta($my_post_id, 'year_end', $filemark[$fkey][5]);
	update_post_meta($my_post_id, 'engine', $filemark[$fkey][3]);
	update_post_meta($my_post_id, 'article', $filemark[$fkey][6]);
    $keymark = array_search($filemark[$fkey][1], $fileimglist[0]);
	update_field( 'field_5e0765c234188', $fileimg[$keymark][1] , $my_post_id );
}
?>