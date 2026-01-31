<?php
/*
 * Develop by icmark.ru 2021
 * (c) vafonin@icmark.ru
 *
 */

function true_show_profile_fields( $user ) {
	echo '<h3>Информация о менеджере</h3>';
	echo '<table class="form-table">';
	$legal_user_id = get_the_author_meta( 'legal-user-id', $user->ID );
	echo '<tr><th><label for="legal-user-id">ID Главного менеджера</label></th>
 	<td><input type="number" name="legal-user-id" id="legal-user-id" value="' . esc_attr( $legal_user_id ) . '" class="regular-text" /></td>
	</tr>';
	echo '</table>';
}
add_action( 'edit_user_profile', 'true_show_profile_fields' );

function true_save_profile_fields( $user_id ) {
	update_user_meta( $user_id, 'legal-user-id', sanitize_text_field( $_POST[ 'legal-user-id' ] ) );
}
add_action( 'edit_user_profile_update', 'true_save_profile_fields' );


//Запрос списка пользователей в статусе Магазин привязанных к Юр. лицу
function get_my_manager() {
	$message = array(
		'status' => 'error'
	);
	$users = get_users( [
		'role'         => 'shop_manager',
		'meta_key'     => 'legal-user-id',
		'meta_value'   => get_current_user_id(),
	] );

	foreach( $users as $user ){
		$myManager = '<div class="my-manager-item">
<div><span>ID:</span> '.$user->ID.'</div>
<div><span>Логин:</span> '.$user->data->user_login.'</div>
<div><span>Email:</span> '.$user->data->user_email.'</div>
<div><span>Имя:</span> '.$user->data->display_name.'</div>
<div><span>Телефон:</span> '.get_user_meta($user->ID,'phone')[0].'</div>
</div>';
		$message['manager'][] = $myManager;
	}
	if(!empty($message['manager'])) {
		$message['status'] = 'success';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_get_my_manager', 'get_my_manager');
add_action('wp_ajax_nopriv_get_my_manager', 'get_my_manager');

//Регистрация нового менеджера
function add_new_manager() {
	$message = array(
		'status' => 'error'
	);
	if(!empty($_POST['login']) && $_POST['email'] && $_POST['password'] && $_POST['confirm_password']) {
		if($_POST['password'] === $_POST['confirm_password']) {
			$user_id = wp_create_user( $_POST['login'], $_POST['password'], $_POST['email'] );

			if ( is_wp_error( $user_id ) ) {
				$message['text'] = $user_id->get_error_message();
			}
			else {
				$message['status'] = 'success';
				$args = array(
					'ID' => $user_id,
					'role'   => 'shop_manager',
				);
				wp_update_user( $args );
				update_user_meta( $user_id, 'legal-user-id', get_current_user_id() );
				if(!empty($_POST['phone'])) {
					update_user_meta( $user_id, 'phone', $_POST['phone'] );
				}
				$message['text'] = 'Пользователь успешно зарегестрирован';

				$legalUser = wp_get_current_user();
				$legalEmail = $legalUser->user_email;
				if(!empty($legalEmail)) {
					$emailText = 'Вы зарегистрировали нового менеджера<br>
Доступы для входа:<br>
Логин: '.$_POST['login'].'<br>
Email: '.$_POST['email'].'<br>
Пароль: '.$_POST['password'].'<br>';
					sendMail($emailText,$legalEmail,'Данные нового менеджера');
					$message['text'] .= '<br>Доступы для входа отправлены на вашу почту.';
				}
			}
		} else {
			$message['text'] = 'Пароли не совпадают';
		}
	} else {
		$message['text'] = 'Некоректные данные';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_add_new_manager', 'add_new_manager');
add_action('wp_ajax_nopriv_add_new_manager', 'add_new_manager');