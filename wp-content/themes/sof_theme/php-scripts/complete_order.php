<?php


if(!empty($_GET['order_id'])) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	global $user_ID;
	global $woocommerce;

	$order = wc_get_order( $_GET['order_id']);
	$note = 'Статус заказа изменён по ссылке';
	$orderData = $order->get_data();
	if($orderData['status'] == 'cancelled') {
		echo '<h1>Заказ уже в статусе - Отменено</h1>';
	} else {
		if($orderData['status'] == 'completed') {
			echo '<h1>Заказ уже в статусе - Выполнен</h1>';
		} else {
			if ($order->update_status( "completed", $note ) ) {
				echo '<h1>Заказ '.$_GET['order_id'].' переведён в статус - Выполнен</h1>';
			} else {
				echo '<h1>Не удалось изменить статус заказа '.$_GET['order_id'].'</h1>';
			}
		}
	}
}
