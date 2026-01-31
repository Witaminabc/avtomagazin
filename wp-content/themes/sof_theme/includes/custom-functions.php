<?php
/**
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: Факеев Станислав
 * Author e-mail: stas-fakeyev@ya.ru
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */
//show_admin_bar(false);
//добавление настройки кол-во адресов
function add_option_field_to_general_admin_page(){
	$option_name = 'my_address_limit';
	register_setting( 'general', $option_name );
	add_settings_field(
		'plemya_setting-id',
		'Кол-во адресов в профиле пользователя',
		'plemya_setting_callback_function',
		'general',
		'default',
		array(
			'id' => 'plemya_setting-id',
			'option_name' => 'my_address_limit'
		)
	);
} //endfunction
add_action('admin_menu', 'add_option_field_to_general_admin_page');
function plemya_setting_callback_function( $val ){
	$id = $val['id'];
	$option_name = $val['option_name'];
	?>
    <input
            type="text"
            name="<? echo $option_name ?>"
            id="<? echo $id ?>"
            value="<? echo esc_attr( get_option($option_name) ) ?>"
    />
	<?
} //endfunction
//добавление настройки лимит данных заказа в личном кабинете
function add_option_order_field_to_general_admin_page(){
	$option_name = 'my_order_limit';
	register_setting( 'general', $option_name );
	add_settings_field(
		'plemya_order_setting-id',
		'Кол-во строк таблици заказов  в личном кабинете',
		'plemya_order_setting_callback_function',
		'general',
		'default',
		array(
			'id' => 'plemya_order_setting-id',
			'option_name' => 'my_order_limit'
		)
	);
} //endfunction
add_action('admin_menu', 'add_option_order_field_to_general_admin_page');
function plemya_order_setting_callback_function( $val ){
	$id = $val['id'];
	$option_name = $val['option_name'];
	?>
    <input
            type="text"
            name="<? echo $option_name ?>"
            id="<? echo $id ?>"
            value="<? echo esc_attr( get_option($option_name) ) ?>"
    />
	<?
} //endfunction
//добавление настройки лимита товаров на странице каталога
function add_option_product_field_to_general_admin_page(){
	$option_name = 'my_product_limit';
	register_setting( 'general', $option_name );
	add_settings_field(
		'plemya_product_setting-id',
		'Кол-во товаров на странице каталога',
		'plemya_product_setting_callback_function',
		'general',
		'default',
		array(
			'id' => 'plemya_product_setting-id',
			'option_name' => 'my_product_limit'
		)
	);
} //endfunction
add_action('admin_menu', 'add_option_product_field_to_general_admin_page');
function plemya_product_setting_callback_function( $val ){
	$id = $val['id'];
	$option_name = $val['option_name'];
	?>
    <input
            type="text"
            name="<? echo $option_name ?>"
            id="<? echo $id ?>"
            value="<? echo esc_attr( get_option($option_name) ) ?>"
    />
	<?
} //endfunction

//дополнительные поля профиля юзера
add_filter( 'user_contactmethods', 'add_user_contact_method' );

function add_user_contact_method( $method ) {
	$limit = get_option('my_address_limit');
	$custom_contact = [
		'name_manager' => __( 'Персональный менеджер' ),
		'email_manager' => __( 'Email менеджера' ),
		'phone_manager'  => __( 'Телефон менеджера' ),
		'vk_manager'  => __( 'Ссылка на vk-аккаунт  менеджера' ),
		'instagram_manager'  => __( 'Ссылка на instagram-аккаунт менеджера' ),
		'phone' => __( 'Телефон пользователя' ),
		'type_customer' => __( 'Тип пользователя' ),

	];
	for($x = 1; $x <= $limit; $x++){
		$custom_contact['address'.$x] = __( 'Адрес доставки '.$x );
	} //endfor
	$method = array_merge( $method, $custom_contact );

	return $method;

} //endfunction

//регистрация кастомных записей Авто
add_action('init','cars_post');
function cars_post() {
	register_post_type('cars',array(

		'public'=>true,
		'show_ui' => true, // показывать интерфейс в админке
		'supports' => array('title', 'thumbnail'),
		'menu_position' =>120,
		'menu_icon' => admin_url().'images/media-button-other.gif',
		'has_archive'         => true,
        'rewrite'             => ['slug'=> 'cars' ],
		'labels' => array(
			'name' => 'Авто',
			'all_items' => 'Все авто',
			'add_new' => 'Добавить новое авто',
			'add_new_item' => 'Новое авто'
		)
	));
} //endfunction

//регистрация Фин. документов
add_action('init','icm_finance');
function icm_finance() {
    register_post_type('icm_finance',array(
        'public'=>true,
        'show_ui' => true, // показывать интерфейс в админке
        'supports' => array('title', 'thumbnail'),
        'menu_position' =>120,
        'menu_icon' => admin_url().'images/media-button-other.gif',
        'labels' => array(
            'name' => 'Фин. документ',
            'all_items' => 'Все Фин. документы',
            'add_new' => 'Добавить новый Фин. документ',
            'add_new_item' => 'Новый Фин. документ'
        )
    ));
} //endfunction

//регистрация групп клиентов
add_action('init','icm_client_group');
function icm_client_group() {
    register_post_type('icm_client_group',array(
        'public'=>true,
        'show_ui' => true, // показывать интерфейс в админке
        'supports' => array('title', 'thumbnail'),
        'menu_position' =>120,
        'menu_icon' => admin_url().'images/media-button-other.gif',
        'labels' => array(
            'name' => 'Группы клиентов',
            'all_items' => 'Все группы',
            'add_new' => 'Добавить новую группу',
            'add_new_item' => 'Новая группа'
        )
    ));
} //endfunction

//методы доставки
add_action('init','delivery_post');
function delivery_post() {
	register_post_type('delivery',array(

		'public'=>true,
		'show_ui' => true, // показывать интерфейс в админке
		'supports' => array('title', 'thumbnail'),
		'menu_position' =>120,
		'menu_icon' => admin_url().'images/media-button-other.gif',
		'labels' => array(
			'name' => 'Способы доставки',
			'all_items' => 'Все способы',
			'add_new' => 'Добавить новый способ',
			'add_new_item' => 'Новый способ'
		)
	));
} //endfunction

//регистрация групп клиентов
add_action('init','icm_available');
function icm_available() {
    register_post_type('icm_available',array(
        'public'=>true,
        'show_ui' => true, // показывать интерфейс в админке
        'supports' => array('title', 'thumbnail'),
        'menu_position' =>120,
        'menu_icon' => admin_url().'images/media-button-other.gif',
        'labels' => array(
            'name' => 'Доступность товаров',
            'all_items' => 'Все отгрузки',
            'add_new' => 'Добавить новую отгрузку',
            'add_new_item' => 'Новая отгрузка'
        )
    ));
} //endfunction

//способы оплаты
add_action('init','payment_post');
function payment_post() {
	register_post_type('payment',array(

		'public'=>true,
		'show_ui' => true, // показывать интерфейс в админке
		'supports' => array('title', 'editor'),
		'menu_position' =>120,
		'menu_icon' => admin_url().'images/media-button-other.gif',
		'labels' => array(
			'name' => 'Способы оплаты',
			'all_items' => 'Все способы',
			'add_new' => 'Добавить новый способ',
			'add_new_item' => 'Новый способ'
		)
	));
} //endfunction
//точки самовывоза
add_action('init','pickup_post');
function pickup_post() {
	register_post_type('pickup',array(

		'public'=>true,
		'show_ui' => true, // показывать интерфейс в админке
		'supports' => array('title'),
		'menu_position' =>120,
		'menu_icon' => admin_url().'images/media-button-other.gif',
		'labels' => array(
			'name' => 'Точки самовывоза',
			'all_items' => 'Все точки',
			'add_new' => 'Добавить новую точку',
			'add_new_item' => 'Новая точка'
		)
	));
} //endfunction

//создание кастомных полей заказа
add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_fields' );

add_action( 'show_user_profile', 'add_extra_fields_icm' );
add_action( 'edit_user_profile', 'add_extra_fields_icm' );

//Дополнительные данные пользователя
function add_extra_fields_icm( $user )
{
    ?>
    <h3>Дополнительные данные пользователя</h3>
    <table class="form-table">
        <tr>
            <th><label for="guid_1c">GUID из 1с</label></th>
            <td><input type="text" name="guid_1c" value="<?php echo esc_attr(get_the_author_meta( 'guid_1c', $user->ID )); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><label for="guid_group_1c">GUID группы из 1с</label></th>
            <td><input type="text" name="guid_group_1c" value="<?php echo esc_attr(get_the_author_meta( 'guid_group_1c', $user->ID )); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><label for="user_inn">ИНН</label></th>
            <td><input type="text" name="user_inn" value="<?php echo esc_attr(get_the_author_meta( 'user_inn', $user->ID )); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><label for="user_kpp">КПП</label></th>
            <td><input type="text" name="user_kpp" value="<?php echo esc_attr(get_the_author_meta( 'user_kpp', $user->ID )); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

add_action( 'personal_options_update', 'save_extra_fields_icm' );
add_action( 'edit_user_profile_update', 'save_extra_fields_icm' );

function save_extra_fields_icm( $user_id )
{
    update_user_meta( $user_id,'guid_1c', sanitize_text_field( $_POST['guid_1c'] ) );
    update_user_meta( $user_id,'guid_group_1c', sanitize_text_field( $_POST['guid_group_1c'] ) );
    update_user_meta( $user_id,'user_inn', sanitize_text_field( $_POST['user_inn'] ) );
    update_user_meta( $user_id,'user_kpp', sanitize_text_field( $_POST['user_kpp'] ) );
}

function custom_checkout_fields( $fields ) {
	$fields['billing']['billing_payment'] = array(
		'type' => 'text',
		'label' => __('Способ оплаты', 'woocommerce'),
		'placeholder' => _x('способ оплаты', 'placeholder', 'woocommerce'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	);
	$fields['shipping']['shipping_delivery'] = array(
		'type' => 'text',
		'label' => __('Способ доставки', 'woocommerce'),
		'placeholder' => _x('способ доставки', 'placeholder', 'woocommerce'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	);
	$fields['shipping']['shipping_pickup'] = array(
		'type' => 'text',
		'label' => __('Точка самовывоза', 'woocommerce'),
		'placeholder' => _x('Точка самовывоза', 'placeholder', 'woocommerce'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	);

	return $fields;
} //endfunction
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_field_display_admin_order_meta', 10, 1 );

function custom_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Метод оплаты').':</strong> ' . get_post_meta( $order->id, 'billing_payment', true ) . '</p>';
} //endfunction
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_field_display_admin_order_meta_shipping', 10, 1 );

function custom_field_display_admin_order_meta_shipping($order){
	echo '<p><strong>'.__('Метод доставки').':</strong> ' . get_post_meta( $order->id, 'shipping_delivery', true ) . '</p>';
	echo '<p><strong>'.__('Точка самовывоза').':</strong> ' . get_post_meta( $order->id, 'shipping_pickup', true ) . '</p>';

} //endfunction
//разрешить редактирование заказа
add_filter( 'wc_order_is_editable', 'lets_make_processing_orders_editable', 10, 2 );
function lets_make_processing_orders_editable( $is_editable, $order ) {
	if ( $order->get_status() == 'processing' ) {
		$is_editable = true;
	} //endif

	return $is_editable;
} //endfuncton
//добавление кастомных полей заказа в email
add_filter('woocommerce_email_order_meta_keys', 'email_checkout_field_order_meta_keys');

function email_checkout_field_order_meta_keys( $keys ) {

	$keys['Способ оплаты'] = 'billing_payment';
	$keys['Способ доставки'] = 'shipping_delivery';
	$keys['Точка самовывоза'] = 'shipping_pickup';

	return $keys;
} //endfunction

function get_cars(){
	$str = '';
	if(!empty($_GET['more'])) $more = intval($_GET['more']);
	else $more = 8;

	$new_title_id = unserialize($_GET['count']);
	$c = get_posts(array(
		'numberposts' => -1,
		'post_type'   => 'cars',
		'orderby' => 'date',
		'order'       => 'DESC',
	));

	$count = count($c);
	if($more >= $count) $more = $count;
	$str.='<div class="all-stamps">';
	$QueryArgs = array(
		'post_type' => 'cars',
		'posts_per_page' => $more,
		'post_status' => 'publish',
		'post__in'  => $new_title_id,
		'orderby' => 'date',
		'order' => 'DESC',
	);
	$pc = new WP_Query($QueryArgs);
	if($pc->have_posts()){
		while ($pc->have_posts()) {
			$pc->the_post();
			unset($new_title_id[get_the_ID()]);
			$image = get_field('photo');
			$str.='<a href="/каталог/?categories=0&car='.get_the_title().'" class="stamp"><img src="'.$image['url'].'" alt="'.$image['alt'].'"><p>'.get_the_title().'</p></a>';
		}
		$pc->reset_postdata();
	}
	$str.='</div>';
	/*if($more >= $count){
		$str.='<a href="javascript:void(0)" data-count = "'.serialize($new_title_id).'" data-car="'.$count.'" class="but-red">Больше авто</a>';
	} else {
		$more = $more + 4;
		$str.='<a href="javascript:void(0)" data-count = "'.serialize($new_title_id).'" data-car="'.$more.'" class="but-red">Больше авто</a>';
	}*/
	echo $str;
	wp_die();
} //endfunction
add_action('wp_ajax_cars', 'get_cars');
add_action('wp_ajax_nopriv_cars', 'get_cars');
//ресайз списков параметров авто на странице каталога
function resize_cars(){
	$result = array('status' => '', 'models' => '', 'years' => '', 'engines' => '');
	try{
		if(empty($_GET['car'])) throw new Exception('error empty car');
		$car = trim(strip_tags(htmlspecialchars($_GET['car'], ENT_QUOTES)));
		$models = array();
		$years = array();
		$engines = array();
		$QueryArgs = array(
			'post_type' => 'cars',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'title' => $car,
			'orderby'        => 'date',
			'order' => 'DESC',
		);
		$pc = new WP_Query($QueryArgs);
		if($pc->have_posts()){
//				$models.='<select id="my_models"><option value=""></option>';

			while ($pc->have_posts()) {
				$pc->the_post();
//if(get_the_title() == $car){
				$models[] = get_field('model');
				$years[] = get_field('year');
				$engines[] = get_field('engine');
//} //endif car
			} //endwhile
			$pc->reset_postdata();
//						   $models.='</select>';
		} //endif
		if(count($years) > 0){
			asort($years);
			$unique_years = array_unique($years);
			$year = '<select id="my_years"><option value=""></option>';
			foreach($unique_years as $value){
				$year.='<option value="'.$value.'">'.$value.'</option>';
			} //endforeach
			$year.='</select>';
		} //endif count years
		if(count($engines) > 0){
			asort($engines);
			$unique_engines = array_unique($engines);
			$engine = '<select id="my_engines"><option value=""></option>';
			foreach($unique_engines as $value){
				$engine.='<option value="'.$value.'">'.$value.' л</option>';
			} //endforeach
			$engine.='</select>';
		} //endif count engines
		if(count($models) > 0){
			asort($models);
			$unique_models = array_unique($models);
			$model = '<select id="my_models"><option value=""></option>';
			foreach($unique_models as $value){
				$model.='<option value="'.$value.'">'.$value.'</option>';
			} //endforeach
			$model.='</select>';
		} //endif count engines

		$result['status'] = 'success';
		$result['models'] = $model;
		$result['years'] = $year;
		$result['engines'] = $engine;
	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
	} //endcatch
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_resizecars', 'resize_cars');
add_action('wp_ajax_nopriv_resizecars', 'resize_cars');








add_action('wp_ajax_filterest_new', 'filterest_new');
add_action('wp_ajax_nopriv_filterest_new', 'filterest_new');
function filterest_new(){
	$result = array();
	$tax_query = array();

	$per_page_new =	$_POST['per_page_new'];
	$number_page_new = (int)$_POST['number_page_new'];
	$cat_id_new = $_POST['cat_id_new'];
	$pickup_new = $_POST['pickup_new'];
	$price_new = $_POST['price_new'];
	$car = $_POST['car'];
	$models = $_POST['models'];
	$years = $_POST['years'];
	$engines = $_POST['engines'];


	/*if($cat_id_new != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $cat_id_new,
		];
	}*/	

	if($_POST['motoroil_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['motoroil_new'],
		];
	}

	if($_POST['antifreeze_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['antifreeze_new'],
		];
	}

	

	if($_POST['liquid_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['liquid_new'],
		];
	}

	if($_POST['transoil_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['transoil_new'],
		];
	}

	if($_POST['stop_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['stop_new'],
		];
	}

	if($_POST['filter_new'] != ''){
		$tax_query[] = [
			'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['filter_new'],
		];
	}


	if($price_new == 1){
		$order_by = 'meta_value_num';
		$order_ab = 'DESC';
		$meta_key = '_price';
	}elseif($price_new == 2){
		$order_by = 'meta_value_num';
		$order_ab = 'ASC';
		$meta_key = '_price';
	}else{
		$order_by = 'date';
		$order_ab = 'DESC';
		$meta_key = '';
	}



	if($pickup_new){
		$QueryArgs_dostypno = array(
	        'post_type' => 'icm_available',
	        'posts_per_page' => -1,
	        'post_status' => 'publish',
	        'orderby'        => 'date',
	        'order' => 'ASC',
	    );
	    $pc = new WP_Query($QueryArgs_dostypno);
	    if($pc->have_posts()){
	    	$available_products = array();
	        while ($pc->have_posts()){
	            $pc->the_post();
	            $name_pick = get_field('available_address', get_the_id());
	            if($pickup_new == $name_pick){
	            	$available_product = get_field('available_product', get_the_id());
	            	$available_products[] = $available_product;
	            }
	        }
	        $pc->reset_postdata();
	    }
    }                       




    if($car){

    	if($models and $years and $engines){
    		$filter = 'all';
    	}elseif($models and $years){
    		$filter = 'modelandyear';
    	}elseif($models and $engines){
    		$filter = 'modelsandengines';
    	}elseif($models){
    		$filter = 'model';
    	}else{
    		$filter = 'cars';
    	}
    	
	    $QueryArgs_cars = array(
			'post_type' => 'cars',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			's' => $car,
		);
		$pc = new WP_Query($QueryArgs_cars);
		if ($pc->have_posts()) {
			$products_ = array();
			while ($pc->have_posts()) {
				$pc->the_post();
				switch($filter){
				    case 'all':
				        $models_ = get_field('model', get_the_id());
						$years_ot = get_field('year', get_the_id());
						$years_do = get_field('year_end', get_the_id());
						$engines_ = get_field('engine', get_the_id());
						if($models == $models_ and $years <= $years_do and $years >= $years_ot and $engines == $engines_){
							$products_[] = get_field('article', get_the_id());
						}
				        break;
				    case 'modelandyear':
				        $models_ = get_field('model', get_the_id());
						$years_ot = get_field('year', get_the_id());
						$years_do = get_field('year_end', get_the_id());
						if($models == $models_ and $years <= $years_do and $years >= $years_ot){
							$products_[] = get_field('article', get_the_id());
						}
				        break;
				    case 'modelsandengines':
				        $models_ = get_field('model', get_the_id());
						$engines_ = get_field('engine', get_the_id());
						if($models == $models_ and $engines == $engines_){
							$products_[] = get_field('article', get_the_id());
						}
				        break;
				    case 'cars':
						$products_[] = get_field('article', get_the_id());
				        break;
				    case 'model':
				    	$models_ = get_field('model', get_the_id());
				    	if($models == $models_){
				    		$products_[] = get_field('article', get_the_id());
				    	}
				        break;
				}
			}
			$pc->reset_postdata();
		}
	}

	if(!$products_){
		$products_ = '';
	}

	if($tax_query){
		$tax_query = array(
        	$tax_query
        );
	}else{
		$tax_query = '';
	}

	$result['pickup_new'] = $pickup_new;
	$result['per_page_new'] = $per_page_new;
	$result['number_page_new'] = $number_page_new;
	$result['cat_id_new'] = $cat_id_new;
	
	$result['products_'] = $products_;
	$result['car'] = $car;
	$result['order_by'] = $order_by;
	$result['order_ab'] = $order_ab;
	$result['meta_key'] = $meta_key;

	$result['resp'] = 'rabotaet';

	//$result['available_products'] = $available_products;



	$result['stop'] = 'no';
	if($pickup_new and count($available_products) === 0){
		$result['stop'] = 'yes';
	}
	if($car and count($products_) === 0){
		$result['stop'] = 'yes';
	}
	if($car and $products_ == ''){
		$result['stop'] = 'yes';
	}







	$pc = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => $per_page_new,
        'paged' => $number_page_new,
        'post__in'=> $available_products,
        'post_name__in'  => $products_,
        'meta_key' => $meta_key,
        'orderby' => $order_by,
    	'order' => $order_ab,
        'tax_query' => $tax_query,
        'meta_query' => array(
        	/*'relation' => 'AND',
			array(
				'key'     => 'title',
				'value'   => $products_,
				'compare' => 'LIKE',
			),
            array(
                'key' => '_stock_status',
                'value' => 'instock'
            )*/
        ),
    ));
	$result['products'] = '';
	if($result['stop'] == 'no'){
	    if ($pc->have_posts()) {
	    	while ($pc->have_posts()) {
	            $pc->the_post();

	            $result['products'] .= '
	            <div class="one-product-catalog">';
		                
	            $terms = wp_get_post_terms(get_the_ID(), 'product_tag');
	            if (count($terms) > 0) {
	                foreach ($terms as $term) {
	                    $result['products'] .= '<div class="';
	                    if ($term->name == 'Новый') {
	                        $result['products'] .= 'new popularity';
	                    } elseif ($term->name == 'Акция') {
	                        $result['products'] .= 'action popularity';
	                    } elseif ($term->name == 'Популярный') {
	                        $result['products'] .= 'popular popularity';
	                    }

	                    $result['products'] .= '">';
	                    $result['products'] .= '<p>' . $term->name . '</p>';
	                    $result['products'] .= '</div>';
	                } //endforeach
	            } //endif tag
		                
		        $result['products'] .= '
		        	<div class="image" onclick="document.location.href=\'' . get_the_permalink() . '\'">';
		        
		        $_product = wc_get_product( get_the_ID() );
		        $attachment_ids = $_product->get_gallery_image_ids();
		        if(count($attachment_ids) > 0){
		            foreach( array_slice( $attachment_ids, 0,3 ) as $attachment_id ) {
		                $thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
		                $result['products'] .= '<img src="'.$thumbnail_url.'" alt="'.strip_tags(get_the_title()).'">';
		            } //endforeach
		        } //endif count
		        elseif(get_the_post_thumbnail_url( $_product->id )){
		            $result['products'] .= '<img src="'.get_the_post_thumbnail_url( $_product->id, "full").'" alt="'.strip_tags(get_the_title()).'">';
		        }
		        else{
		            $img = get_option( 'woocommerce_placeholder_image', 0 );
					$result['products'] .= '<img src="'.$img.'" alt="'.strip_tags(get_the_title()).'">';
		        }
				
					$result['products'] .= '</div>
		            <h3 onclick="document.location.href=\'' . get_the_permalink() . '\'">' . get_the_title() . '</h3>
		            <div class="more-info">
		                <p>подробнее</p>';

		                
		                $_product = wc_get_product(get_the_ID());
		                $result['products'] .= '<h3 id="price' . get_the_ID() . '" data-price="' . $_product->get_price() . '">' . $_product->get_price() . ' р.</h3>';

		            $result['products'] .= '
		            </div>
		            <div class="buy-bask">
		                <input type="number" class="pp-number" id="list_input_product' . get_the_ID() . '" data-product="' . get_the_ID() . '" data-quantity="' . $_product->get_stock_quantity() . '" value="1">
		                <div class="button">
		                    <a href="javascript:void(0)" data-add="' . get_the_ID() . '"><img src="' . get_template_directory_uri() . '/img/red-basket.png" alt="В корзину">В корзину</a>
		                </div>
		            </div>
		        </div>';









	        }
	        wp_reset_postdata();
	    }
   	}




	echo json_encode($result, 320);
	wp_die();
}

add_filter( 'posts_where', 'title_like_posts_where', 10, 2 );
function title_like_posts_where( $where, $wp_query ) {
    global $wpdb;
    if ( $post_title_like = $wp_query->get( 'post_title_like' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $post_title_like ) ) . '%\'';
    }
    return $where;
}



function woocommerce_category_description() {
	if (is_product_category()) {
		global $wp_query;
		$cat = $wp_query->get_queried_object();
		return $cat->term_id;
	}
}
//Точка самовывоза авторизованого магазина
function get_shop_pickup($shopID) {
	$result = array();
	$pickupByShopQuery = array(
		'post_type' => 'pickup',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby'        => 'date',
		'order' => 'ASC',
		'meta_query' => [
			'relation' => 'OR',
			[
				'key' => 'magazin',
				'value' => $shopID
			],
		]
	);
	$pickupByAuthor = new WP_Query($pickupByShopQuery);
	if($pickupByAuthor->have_posts()){
		while ($pickupByAuthor->have_posts()) {
			$pickupByAuthor->the_post();
			$result[] = get_post();
		} //endwhile
		wp_reset_query();
	} //endif
	return $result;
}
//точки самовывоза
function get_all_pickup($autor = ''){
	$result = array();
	$QueryPickup = array(
		'post_type' => 'pickup',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby'        => 'date',
		'order' => 'ASC',
	);
	if(!empty($autor)) {
		$QueryPickup['autor'] = $autor;
	}
	$pickup = new WP_Query($QueryPickup);
	if($pickup->have_posts()){
		while ($pickup->have_posts()) {
			$pickup->the_post();
			$result[] = get_the_title();
		} //endwhile
		wp_reset_query();
	} //endif
	return $result;
} //endfunction
//точки самовывоза c координатами
function get_all_pickup_with_coord($author = ''){
	$result = array();
	$QueryPickup = array(
		'post_type' => 'pickup',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby'        => 'date',
		'order' => 'ASC',
	);
	if(!empty($author)) {
		$QueryPickup['author'] = $author;
	}
	$pickup = new WP_Query($QueryPickup);
	while ($pickup->have_posts()) {
		$pickup->the_post();
		$result[] = $pickup;
	} //endwhile
	wp_reset_query();
	return $result;
} //endfunction

//Добавление точки самовывоза
function set_new_pickup() {
	if(!empty($_POST['pickupAddress']) && !empty($_POST['pickupCoords'])) {
		$post_data = array(
			'post_type' => 'pickup',
			'post_title'    => sanitize_text_field( $_POST['pickupAddress'] ),
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'meta_input'    => [
				'active_point' => 1,
				'coords'=>$_POST['pickupCoords'],
				'magazin' => $_POST['pickupShop'],
				'fio-yuridicheskogo-lica' => $_POST['pickupYlName'],
				'telefon-yuridicheskogo-lica' => $_POST['pickupYlPhone'],
                'dadata' => normalizeAddress($_POST['pickupAddress']),
			],
		);
		// Вставляем запись в базу данных
		$post_id = wp_insert_post( $post_data );
		$message = array('status' => 'success','post_id' => $post_id);
	} else {
		$message = array('status' => 'error');
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_new_pickup', 'set_new_pickup');
add_action('wp_ajax_nopriv_new_pickup', 'set_new_pickup');

//Удаление точки самовывоза
function remove_pickup() {
	if(!empty($_POST['pickupID'])) {
		wp_delete_post( $_POST['pickupID']);
		$message = array('status' => 'success');
	} else {
		$message = array('status' => 'error');
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_remove_pickup', 'remove_pickup');
add_action('wp_ajax_nopriv_remove_pickup', 'remove_pickup');

//Обновление достпности у точки самовывоза
function change_active_pickup() {
	if(!empty($_POST['pickupId']) && !empty($_POST['active'])) {
		if($_POST['active'] == 'true') {
			$active = 1;
		} else {
			$active = 0;
		}
		if(update_post_meta( $_POST['pickupId'], 'active_point', $active )) {
			$message = array('status' => 'success');
		} else {
			$message = array('status' => 'error');
		}
	} else {
		$message = array('status' => 'error');
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_change_active_pickup', 'change_active_pickup');
add_action('wp_ajax_nopriv_change_active_pickup', 'change_active_pickup');

function woocommerce_category_data() {
	if (is_product()) {
		global $wp_query;
		$query = $wp_query->get_queried_object();
		$terms = get_terms( array(
			'taxonomy'     => 'product_cat',
			'object_ids' => $query->ID,
			'hide_empty'   => 0,
			'exclude'      => '',
			'number'       => 1,
		));
		if( $terms ){
			foreach( $terms as $cat ){

				return $cat->term_id;
			} //endforeach
		} //endif terms
	}
}
function filter_url($stranica = null){
	$categories = !empty($_GET['categories']) ? htmlspecialchars($_GET['categories'], ENT_QUOTES) : null;
	$car = !empty($_GET['car']) ? htmlspecialchars($_GET['car'], ENT_QUOTES) : null;
	$models = !empty($_GET['models']) ? htmlspecialchars($_GET['models'], ENT_QUOTES) : null;
	$years = !empty($_GET['years']) ? htmlspecialchars($_GET['years'], ENT_QUOTES) : null;
	$engines = !empty($_GET['engines']) ? htmlspecialchars($_GET['engines'], ENT_QUOTES) : null;
	$pickup = !empty($_GET['pickup']) ? htmlspecialchars($_GET['pickup'], ENT_QUOTES) : null;
	$price = !empty($_GET['price']) ? intval($_GET['price']) : null;
	$url = http_build_query(array('categories' => $categories, 'car' => $car, 'models' => $models, 'years' => $years, 'engines' => $engines, 'pickup' => $pickup, 'price' => $price, 'stranica' => $stranica));
	return $url;
} //endfunction
/**
 *  Отключаем оборачивание в <div class="textwidget">
 */
add_action('widgets_init', 'register_my_widgets');

function register_my_widgets()
{
	register_widget('MyTextWidget');
}

class MyTextWidget extends WP_Widget_Text
{
	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);
		echo $before_widget;
		if (!empty($title)) {
			echo $before_title . $title . $after_title;
		} ?>
		<?php echo !empty($instance['filter']) ? wpautop($text) : $text; ?>
		<?php
		echo $after_widget;
	}
} //end

function call_form(){

	$result = array('status' => '', 'data' => '');
	try{
		if(!isset($_POST['name'])) throw new Exception('Заполните имя');
		if(empty($_POST['name'])) throw new Exception('Заполните имя');
		$name = trim(strip_tags(htmlspecialchars($_POST['name'], ENT_QUOTES)));
		if(!isset($_POST['tel'])) throw new Exception('Укажите ваш телефон');
		if(empty($_POST['tel'])) throw new Exception('Укажите ваш телефон');
		$tel = trim(strip_tags(htmlspecialchars($_POST['tel'], ENT_QUOTES)));
		if(!isset($_POST['email'])) throw new Exception('Укажите email');
		if(empty($_POST['email'])) throw new Exception('Укажите email');
		$email = trim(strip_tags(htmlspecialchars($_POST['email'], ENT_QUOTES)));
		if(!isset($_POST['sale'])) throw new Exception('error isset');
		if(empty($_POST['sale'])) throw new Exception('error empty');
		$sale = trim(strip_tags(htmlspecialchars($_POST['sale'], ENT_QUOTES)));

		$admin_email = get_bloginfo( 'admin_email' );

		$headers = array(
			'From: '.get_bloginfo('name').' <'.$admin_email.'>',
			'content-type: text/html; charset=utf-8',
			'X-Mailer: PHP mail script',
			'MIME-Version: 1.0',
		);
		$subject = get_bloginfo('name').' - Узнать об акции '.$sale;
		$msg .= '<h2>Узнать об акции '.$sale.'</h2>';
		$msg .= '<table>';
		$msg .= '<tr><td>Имя: '.$name.'</td></tr>';
		$msg .= '<tr><td>Телефон: '.$tel.'</td></tr>';
		$msg .= '<tr><td>email: '.$email.'</td></tr>';
		$msg .= '</table>';

		if(wp_mail($admin_email, $subject, $msg, $headers)){
			$result['status'] = 'success';
			$result['data'] = 'Отправлено. мы с вами свяжемся';
		}
		else throw new Exception('Неудалось отправить письмо');



	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_call', 'call_form');
add_action('wp_ajax_nopriv_call', 'call_form');
function addtocart(){
	global $woocommerce;
	$result = array('status' => '', 'link' => '', 'quantity' => '', 'unique_quantity' => '', 'product' => '', 'price' => '');
	$str = '';
	try{
		if(empty($_GET['id'])) throw new Exception('error empty id');
		if(intval($_GET['id']) <= 0) throw new Exception('error id 0');
		$id = intval($_GET['id']);
		if(empty($_GET['q'])) throw new Exception('error empty q');
		if(intval($_GET['q']) <= 0) throw new Exception('error q 0');
		$q = intval($_GET['q']);
		$woocommerce->cart->add_to_cart( $id, $q );
		$result['status'] = 'success';
		$result['link'] = wc_get_cart_url();

	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
	} //endcatch
	$result['quantity'] = $woocommerce->cart->get_cart_contents_count();
	$total = 0;
	$qt = 0;
	if(count($woocommerce->cart->get_cart()) > 0){
//подсчитать кол-во уникальных товаров
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$qt++;
		} //endforeach
	} //endif count
	$result['unique_quantity'] = 'В корзине '.$qt.' товара.';
	if(count($woocommerce->cart->get_cart()) > 0){

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			$total += ($_product->get_price() * $values['quantity']);
			if(get_the_post_thumbnail_url( $_product->id)){
				$img = get_the_post_thumbnail_url( $_product->id, 'full' );
			} else{
				$imgall = get_field( "img_default", 6 );
				$img = $imgall["url"];
			} 
			$str.='<div class="products">
			<div class="image">
			<img src="'.$img.'" alt="'.strip_tags($_product->name).'">
			</div><!-- end image -->
			<div class="title">
			<a href="'.get_permalink($_product->id).'">'.$_product->name.'</a>
			</div>
			<div class="quantity">
			<p>'.$values['quantity'].'шт</p>
			</div><!-- end quantity -->
			<div>
			<p>'.$_product->get_price().'р</p>
			</div>
			</div><!-- end product -->';
		} //endforeach
		$result['product'] = $str;
		$result['price'] = $total.' р.';
	} //endif count
	else{
		$result['product'] = '<p>нет товаров</p>';
	} //endelse
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_addtocart', 'addtocart');
add_action('wp_ajax_nopriv_addtocart', 'addtocart');
//купить в один клик
function buy_one_click(){
	global $woocommerce;
	$result = array('status' => '', 'data' => '');
	try{
		if(empty($_GET['id'])) throw new Exception('error empty id');
		if(intval($_GET['id']) <= 0) throw new Exception('error id 0');
		$id = intval($_GET['id']);
		if(empty($_GET['q'])) throw new Exception('error empty q');
		if(intval($_GET['q']) <= 0) throw new Exception('error q 0');
		$q = intval($_GET['q']);
		$woocommerce->cart->add_to_cart( $id, $q );
		$result['status'] = 'success';
		$result['data'] = wc_get_cart_url();
	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
	} //endcatch
	echo json_encode($result);
	wp_die();
} //endfunction
add_action('wp_ajax_buyoneclick', 'buy_one_click');
add_action('wp_ajax_nopriv_buyoneclick', 'buy_one_click');

//обновление корзины
function update_basket(){
	global $woocommerce;
	$result = array('data' => '', 'sum' => '', 'quantity' => '', 'unique_quantity' => '', 'product' => '', 'price' => '');
	$str = '';
	try{
		if(empty($_GET['id'])) throw new Exception('error empty id');
		$id = htmlspecialchars($_GET['id'], ENT_QUOTES);
		$item = $woocommerce->cart->cart_contents[ $id ];
		$found = $woocommerce->cart->find_product_in_cart( $id );
		if($found == '') throw new Exception('error');
		if(empty($_GET['q'])) throw new Exception('error empty q');
		if(intval($_GET['q']) <= 0) throw new Exception('error q 0');
		$q = intval($_GET['q']);
		$woocommerce->cart->set_quantity( $id, $q );
	} //endtry
	catch(Exception $e){
//c	$result['data'] = $e->getMessage();
	} //endcatch
	$result['quantity'] = $woocommerce->cart->get_cart_contents_count();
	$total = 0;
	$qt = 0;
	if(count($woocommerce->cart->get_cart()) > 0){
//подсчитать кол-во уникальных товаров
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$qt++;
		} //endforeach
	} //endif count
	$result['unique_quantity'] = 'В корзине '.$qt.' товара.';
	if(count($woocommerce->cart->get_cart()) > 0){

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			$total += ($_product->get_price() * $values['quantity']);
			if(get_the_post_thumbnail_url( $_product->id)){
				$img = get_the_post_thumbnail_url( $_product->id, 'full' );
			} else{
				$imgall = get_field( "img_default", 6 );
				$img = $imgall;
			}
			$str.='<div class="products">
			<div class="image">
			<img src="'.$img.'" alt="'.strip_tags($_product->name).'">
			</div><!-- end image -->
			<div class="title">
			<a href="'.get_permalink($_product->id).'">'.$_product->name.'</a>
			</div>
			<div class="quantity">
			<p>'.$values['quantity'].'шт</p>
			</div><!-- end quantity -->
			<div>
			<p>'.$_product->get_price().'р</p>
			</div>
			</div><!-- end product -->';
		} //endforeach
		$result['product'] = $str;
		$result['price'] = $total.' р.';
		$result['sum'] = '<span>К оплате:</span>'.$total.' р.';
	} //endif count

	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_updatebasket', 'update_basket');
add_action('wp_ajax_nopriv_updatebasket', 'update_basket');
//удаление элемента из корзины
function delete_basket(){
	global $woocommerce;
	$result = array('data' => '', 'sum' => '', 'quantity' => '', 'unique_quantity' => '', 'product' => '', 'price' => '', 'status' => '', 'url' => '');
	$str = '';
	try{
		if(empty($_GET['id'])) throw new Exception('error empty id');
		$id = htmlspecialchars($_GET['id'], ENT_QUOTES);
		$item = $woocommerce->cart->cart_contents[ $id ];
		$found = $woocommerce->cart->find_product_in_cart( $id );
		if($found == '') throw new Exception('error');
		$woocommerce->cart->remove_cart_item($id);
	} //endtry
	catch(Exception $e){
//c	$result['data'] = $e->getMessage();
	} //endcatch
	$result['quantity'] = $woocommerce->cart->get_cart_contents_count();
	$total = 0;
	$qt = 0;
	if(count($woocommerce->cart->get_cart()) > 0){
//подсчитать кол-во уникальных товаров
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$qt++;
		} //endforeach
	} //endif count
	$result['unique_quantity'] = 'В корзине '.$qt.' товара.';
	if(count($woocommerce->cart->get_cart()) > 0){

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			$total += ($_product->get_price() * $values['quantity']);
			if(get_the_post_thumbnail_url( $_product->id)){
				$img = get_the_post_thumbnail_url( $_product->id, 'full' );
			} else{
				$imgall = get_field( "img_default", 6 );
				$img = $imgall;
			}
			$str.='<div class="products">
			<div class="image">
			<img src="'.$img.'" alt="'.strip_tags($_product->name).'">
			</div><!-- end image -->
			<div class="title">
			<a href="'.get_permalink($_product->id).'">'.$_product->name.'</a>
			</div>
			<div class="quantity">
			<p>'.$values['quantity'].'шт</p>
			</div><!-- end quantity -->
			<div>
			<p>'.$_product->get_price().'р</p>
			</div>
			</div><!-- end product -->';
		} //endforeach
		$result['product'] = $str;
		$result['price'] = $total.' р.';
		$result['sum'] = '<span>К оплате:</span>'.$total.' р.';
	} //endif count
	else{
		$result['url'] = get_home_url();
		$result['unique_quantity'] = 0;
	} //endelse
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_deletebasket', 'delete_basket');
add_action('wp_ajax_nopriv_deletebasket', 'delete_basket');
//создание заказа
function make_order(){
	$result = array('data' => '');
	global $woocommerce, $user_ID;
	$adress = '';
	$pickup = '';
	$str = '';
	try{
		if(empty($_GET['typ'])) throw new Exception('error empty type');
		if($_GET['typ'] != 'courier' && $_GET['typ'] != 'pickup') throw new Exception('error courier type');
//celseif($_GET['typ'] != 'pickup') throw new Exception('error pickup type');
		$type = $_GET['typ'];
		if($type == 'courier'){
			if(empty($_GET['adress'])) throw new Exception('error adress');
			$adress = trim(strip_tags(htmlspecialchars($_GET['adress'], ENT_QUOTES)));
		} //endif
		if($type == 'pickup'){
			if(empty($_GET['pickup'])) throw new Exception('error pickup');
			$pickup = trim(strip_tags(htmlspecialchars($_GET['pickup'], ENT_QUOTES)));
		} //endif

		if(empty($_GET['delivery'])) throw new Exception('error delivery');
		$delivery = trim(strip_tags(htmlspecialchars($_GET['delivery'], ENT_QUOTES)));
		if(empty($_GET['pay'])) throw new Exception('error pay');
		$pay = trim(strip_tags(htmlspecialchars($_GET['pay'], ENT_QUOTES)));
		if(empty($_GET['name'])) throw new Exception('error name');
		$name = trim(strip_tags(htmlspecialchars($_GET['name'], ENT_QUOTES)));
		if(empty($_GET['surname'])) throw new Exception('error surname');
		$surname = trim(strip_tags(htmlspecialchars($_GET['surname'], ENT_QUOTES)));
		if(empty($_GET['phone'])) throw new Exception('error phone');
		$phone = trim(strip_tags(htmlspecialchars($_GET['phone'], ENT_QUOTES)));
		if(empty($_GET['email'])) throw new Exception('error email');
		$email = trim(strip_tags(htmlspecialchars($_GET['email'], ENT_QUOTES)));
//проверить авторезирован ли пользователь
//если нет то искать пользователя по введённному мылу
		if(!is_user_logged_in()){
			if($user = get_user_by('email', $email)) $u_id = $user->ID; //пользователь найден
			else{
//создать пользователя
				$random_password = wp_generate_password();
				$u_id = wp_create_user( $email, $random_password, $email );
				update_user_meta($u_id, 'type_customer', 'Физическое лицо');

				$home_url = get_home_url();
				$admin_email = get_bloginfo( 'admin_email' );

				mail(
					$email,
					'Оформление заказа - '.get_bloginfo('name'),
					'<html>
            <head>
              <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
            </head>
            <body>
            <table style="">
                 <tr>
                    <td><h1>Для просмотра деталей заказа пожалуйста войдите в личный кабинет '.$home_url.'</h1></td>
                </tr>
				<tr>
				<td>Ваш логин: '.$email.'</td>
				</tr>
				<tr>
				<td>Ваш пароль: '.$random_password.'</td>
				</tr>
                <tr>
                    <td>Спасибо за заказ</a></td>
                </tr>
            </table>
            </body></html>',
					"From: ".get_bloginfo("name")." <".$admin_email.">\r\n"
					."Content-type: text/html; charset=utf-8\r\n"
					."X-Mailer: PHP mail script"
				);
			} //endelse
		} //endif logged
		else $u_id = $user_ID;
//обновить мета поля пользователя
		update_user_meta($u_id, 'first_name', $name);
		update_user_meta($u_id, 'last_name', $surname);
		update_user_meta($u_id, 'user_email', $email);
		update_user_meta($u_id, 'phone', $phone);
		update_user_meta($u_id, 'address1', $adress);

		$address = array(
			'first_name' => $name,
			'last_name'  => $surname,
			'company'    => '',
			'email'      => $email,
			'phone'      => $phone,
			'address_1'  => $adress,
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => ''
		);

//получить товары из корзины
		if(count($woocommerce->cart->get_cart()) == 0) throw new Exception('error cart');
		$order_data = array(
			'status' => apply_filters('woocommerce_default_order_status', 'processing'),
			'customer_id' => $u_id
		);
		$new_order = wc_create_order($order_data);
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$item_id = $new_order->add_product(
				$values['data'], $values['quantity'], array(
					'variation' => $values['variation'],
					'totals' => array(
						'subtotal' => $values['line_subtotal'],
						'subtotal_tax' => $values['line_subtotal_tax'],
						'total' => $values['line_total'],
						'tax' => $values['line_tax'],
						'tax_data' => $values['line_tax_data'] // Since 2.2
					)
				)
			);
		} //endforach
		$new_order->set_address($address, 'billing');
		$new_order->set_address($address, 'shipping');
		update_post_meta($new_order->id, 'billing_payment', $pay);
		update_post_meta($new_order->id, 'shipping_delivery', $delivery);
		update_post_meta($new_order->id, 'shipping_pickup', $pickup);
		$new_order->calculate_totals();
		$woocommerce->cart->empty_cart();
		//засечка
		if(wc_get_order($new_order->id) == false) throw new Exception('error add order');
		$order = wc_get_order($new_order->id);
		$total = 0;
		//получить товары заказа
		$order_items = $order->get_items();
		$data = $order->get_data();

		$str.='<h3>Спасибо за заказ!</h3>
<h4>Наш менеджер свяжется с вами в ближайшее время</h4>
<div class="all-buy-products">';
		foreach($order_items as $key_item => $item){
			$item_data = $item->get_data();
			$order_product = $item->get_product();
			if(get_the_post_thumbnail_url($item_data['product_id'])){
				$img = get_the_post_thumbnail_url($item_data['product_id'], 'full');
			} else{
				$imgall= get_field( "img_default", 6 );
				$img= $imgall['url'];
			}
			$str.='<div class="one-product-basket">
<div class="image">
<img src="'.$img.'" alt="'.strip_tags($item_data['name']).'">
</div>
<div class="title">
<h5 onclick="document.location.href=';
			$str.="'";
			$str.= get_permalink($item_data['product_id']);
			$str.="'";
			$str.='">'.$item_data['name'].'</h5>
</div>
<p>'.$order_product->get_price().' р.</p>
<p>'.$item_data['quantity'].' шт.</p>
<div class="price">
<p>'.($order_product->get_price() * $item_data['quantity']).' р.</p>
<p>'.($order_product->get_regular_price() * $item_data['quantity']).' р.</p>
</div>
</div><!-- end one product -->';
			$total += ($order_product->get_price() * $item_data['quantity']);
		} //endforeach
		$str.='</div><!-- end all buy product -->
<div class="bot">
<div class="all-sum">
<h3><span>К оплате:</span> '.$total.' р.</h3>
</div>
<div class="links">
<a href="/wp-content/themes/sof_theme/report_order.php?new_order='.$new_order->id.'">Скачать pdf</a>
<a href="javascript:void(0)" data-order-id="'.$new_order->id.'" class="complete-order-basket">Посмотреть QR-код</a>
</div>
</div><!-- end bot -->
<div class="main-info">
<div class="left-part">
<h4>Ваши данные </h4>
<div>
<p>Имя, Фамилия:</p>
<p>'.$data['billing']['first_name'].' '.$data['billing']['last_name'].'</p>
</div><!-- end div -->
<div>
<p>Адрес:</p>
<p>';
		if($type == 'pickup'){
			$str.=$pickup;
		}else{
			$str.=$data['billing']['address_1'];
		}

		$str.='</p>
</div>
<div>
<p>Телефон:</p>
                                    <p>'.$data['billing']['phone'].'</p>
</div>
<div>
<p>Почта:</p>
<p>'.$data['billing']['email'].'</p>
</div>
</div><!-- end left part -->
<div class="center-part">
<h4>Доставка:</h4>
<div>
<h5>'.get_post_meta($new_order->id, 'shipping_delivery', true).'</h5>
</div>
</div><!-- end center part -->
<div class="right-part">
<h4>Оплата:</h4>
<p>'.get_post_meta($new_order->id, 'billing_payment', true).'</p>
</div><!-- end right part -->
</div><!-- end main info -->
<div class="buttons">
<div class="button">
<a href="'.get_permalink(9).'">В личный кабинет</a>
</div>
<div class="button-red">
<a href="'.get_permalink(244).'">Продолжить покупки</a>
</div>
</div><!-- end buttons -->';
		$result['data'] = $str;

        $shopManagerEmail = '';
        $pickupByOrderPickup = array(
            'post_type' => 'pickup',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby'        => 'date',
            'order' => 'ASC',
            'title' => $pickup,
        );
        $pickupByOrderPickupQuery = new WP_Query($pickupByOrderPickup);
        if($pickupByOrderPickupQuery->have_posts()) {
            while ($pickupByOrderPickupQuery->have_posts()) {
                $pickupByOrderPickupQuery->the_post();
                $shopManagerId = get_post_meta(get_the_ID(), 'magazin', true);
                $shopManager = get_user_by( 'ID', $shopManagerId );
                $shopManagerEmail = $shopManager->user_email;
            }
        }
        wp_reset_query();

        if(!empty($shopManagerEmail)) {
            sendMail($str,$shopManager->user_email,'Новый заказ в ваш магазин');
        }

        $current_user = wp_get_current_user();
        $currentUserEmail = $current_user->user_email;
        if(!empty($currentUserEmail) && $currentUserEmail != $currentUserEmail) {
            sendMail($str,$currentUserEmail,'Ваш заказ');
        }

	} //endtry
	catch(Exception $e){
		$result['data'] = $e->getMessage();
//$result['data'] = '<p>Чтото пошло нетак. Попробуйте еще раз или обратитесь к менеджеру магазина</p>';
	} //endcatch
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_makeorder', 'make_order');
add_action('wp_ajax_nopriv_makeorder', 'make_order');
//перевод статусов заказа
function translate_order_status($status){
	$statuses = array('pending' => 'В ожидании оплаты', 'processing' => 'В обработке', 'completed' => 'Выполнен', 'on-hold' => 'На удержании', 'cancelled' => 'Отменено', 'refunded' => 'Возвращено');
	return $statuses[$status];
} //endfunction
//авторизация
function log_form(){
	$url = get_permalink(9);
	$result = array('status' => '', 'data' => '', 'page' => '');
	$user = wp_signon();

	if ( is_wp_error($user) ) {
		$result['status'] = 'error';
		$result['data'] = 'Некорректные данные';
	}
	else{
		$result['status'] = 'success';
		$result['data'] = $url;
	}
	echo json_encode($result);
	wp_die();
} //endfunction
add_action('wp_ajax_logi', 'log_form');
add_action('wp_ajax_nopriv_logi', 'log_form');
//регистрация физического лица
function reg_form(){
	$result = array('status' => '', 'data' => '');
	try{
		if(!isset($_POST['email'])) throw new Exception('Заполните эл-почту');
		if(empty($_POST['email'])) throw new Exception('Заполните эл-почту');
		$email = trim(strip_tags(htmlspecialchars($_POST['email'], ENT_QUOTES)));

		if(!isset($_POST['password'])) throw new Exception('Заполните пароль');
		if(empty($_POST['password'])) throw new Exception('Заполните пароль');
		$password = trim(strip_tags(htmlspecialchars($_POST['password'], ENT_QUOTES)));

		if(!isset($_POST['password2'])) throw new Exception('подтвердите пароль');
		if(empty($_POST['password2'])) throw new Exception('подтвердите пароль');
		$password2 = trim(strip_tags(htmlspecialchars($_POST['password2'], ENT_QUOTES)));

		if($password != $password2) throw new Exception('Не верное подтверждение пароля');
		$random_password = $password;
		$user_login = $email;
		$user_id = wp_create_user( $user_login, $random_password, $email );
		if ( is_wp_error( $user_id ) ) throw new Exception('error create user');
		update_user_meta($user_id, 'type_customer', 'Физическое лицо');

		$result['status'] = 'success';
		$result['data'] = 'регистрация успешна';
	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_regphiz', 'reg_form');
add_action('wp_ajax_nopriv_regphiz', 'reg_form');
//регистрация юр-лица
function entity_form(){

	$result = array('status' => '', 'data' => '');
	try{
		if(!isset($_POST['name'])) throw new Exception('Заполните имя');
		if(empty($_POST['name'])) throw new Exception('Заполните имя');
		$name = trim(strip_tags(htmlspecialchars($_POST['name'], ENT_QUOTES)));
		if(!isset($_POST['phone'])) throw new Exception('Укажите ваш телефон');
		if(empty($_POST['phone'])) throw new Exception('Укажите ваш телефон');
		$phone = trim(strip_tags(htmlspecialchars($_POST['phone'], ENT_QUOTES)));
		if(!isset($_POST['email'])) throw new Exception('Укажите email');
		if(empty($_POST['email'])) throw new Exception('Укажите email');
		$email = trim(strip_tags(htmlspecialchars($_POST['email'], ENT_QUOTES)));
		if(isset($_POST['message'])){
			$message = trim(strip_tags(htmlspecialchars($_POST['message'], ENT_QUOTES)));
		}
		else $message = '';
		$admin_email = get_bloginfo( 'admin_email' );
		$password = wp_generate_password();
		wp_create_user($name, $password, $email);
		$headers = array(
			'From: '.get_bloginfo('name').' <'.$admin_email.'>',
			'content-type: text/html; charset=utf-8',
			'X-Mailer: PHP mail script',
			'MIME-Version: 1.0',
		);
		$subject = get_bloginfo('name').' -  Заявка на регистрацию юр-лица';
		$msg .= '<h2>Регистрация юр-лица</h2>';
		$msg .= '<table>';
		$msg .= '<tr><td>Имя: '.$name.'</td></tr>';
		$msg .= '<tr><td>Телефон: '.$phone.'</td></tr>';
		$msg .= '<tr><td>email: '.$email.'</td></tr>';
		$msg .= '<tr><td>Пароль: '.$password.'</td></tr>';
		$msg .= '<tr><td>Комментарий: '.$message.'</td></tr>';

		$msg .= '</table>';

		if(wp_mail($admin_email, $subject, $msg, $headers)){
			$result['status'] = 'success';
			$result['data'] = 'Отправлено. мы с вами свяжемся';
		}
		else throw new Exception('Неудалось отправить письмо');



	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_regentity', 'entity_form');
add_action('wp_ajax_nopriv_regentity', 'entity_form');
//изменение пароля
function get_change(){
	global $user_ID;
	$result = array('status' => '', 'data' => '');
	try{
		if($user_ID == 0) throw new Exception('error user');
		if(!isset($_POST['old_password'])) throw new Exception('Заполните пароль');
		if(empty($_POST['old_password'])) throw new Exception('Заполните пароль');
		$old_password = trim(strip_tags(htmlspecialchars($_POST['old_password'], ENT_QUOTES)));

		if(!isset($_POST['new_password'])) throw new Exception('подтвердите пароль');
		if(empty($_POST['new_password'])) throw new Exception('подтвердите пароль');
		$new_password = trim(strip_tags(htmlspecialchars($_POST['new_password'], ENT_QUOTES)));
		$user = get_userdata( $user_ID );
		$hash     = $user->data->user_pass;
		if ( !wp_check_password( $old_password, $hash ) ) throw new Exception('error password check');
		$user_id = wp_update_user(array(
			'ID' => $user_ID,
			'user_pass' => $new_password,
		));

		if ( is_wp_error( $user_id ) ) throw new Exception($user_id->get_error_code());

		$result['status'] = 'success';

		$result['data'] = 'смена пароля успешна';

	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_changepassword', 'get_change');
add_action('wp_ajax_nopriv_changepassword', 'get_change');
//изменение мыла
function reset_email(){
	global $wpdb, $user_ID;
	$result = array('status' => '', 'data' => '');
	try{
		if($user_ID == 0) throw new Exception('error user');
		if(!isset($_POST['email'])) throw new Exception('error isset email');
		if(empty($_POST['email'])) throw new Exception('error empty email');
		$email = trim(strip_tags(htmlspecialchars($_POST['email'], ENT_QUOTES)));
		$user = get_userdata( $user_ID );
		$q = $wpdb->get_row("SELECT `user_email` FROM `confirm_email` WHERE `user_id` = {$user->data->ID} and `user_email` = '{$email}' ", ARRAY_A);
		if(count($q) > 0) throw new Exception('пПодтвердите регистрацию по ссылке на вашей почте');
		$code = wp_generate_password(12, false, false);
		$wpdb->insert( 'confirm_email', array('user_id' => $user->data->ID, 'user_email' => $email, 'code' => $code));
		$url = get_home_url();
		$admin_email = get_bloginfo( 'admin_email' );
		if(            mail(
			$email,
			'Подтверждение изменения email '.get_bloginfo('name'),
			'<html>
            <head>
              <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
            </head>
            <body>


            <table style="">
                 <tr>
                    <td><h1"Подтверждение изменения email"</h1></td>
                </tr>
                <tr>
                    <td>Для подтверждения изменения email перейдите по <a href="'.$url.'?confirm='.$code.'">ссылке</a></td>
                </tr>
            </table>
            </body></html>',
			"From: ".get_bloginfo("name")." <".$admin_email.">\r\n"
			."Content-type: text/html; charset=utf-8\r\n"
			."X-Mailer: PHP mail script"
		)){

			$result['status'] = 'success';
			$result['data'] = 'Изменение email - успешно. на вашу почту отправлено письмо подтверждения '.$url.'?confirm='.$code;
		}
		else throw new Exception('Неудалось отправить письмо');

	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_resetemail', 'reset_email');
add_action('wp_ajax_nopriv_resetemail', 'reset_email');
//подтверждение изменения email
function confirm_email(){
	global $wpdb, $user_ID;
	/*c
		$r = $wpdb->query("delete from `confirm_users`");
		if($r){
			echo'<p>yes</p>';
		}

	c*/
	if(isset($_GET['confirm'])){
		$user = get_userdata( $user_ID );

		$code = trim(strip_tags(htmlspecialchars($_GET['confirm'], ENT_QUOTES)));
		$q = $wpdb->get_row("SELECT * FROM `confirm_email` WHERE `code` = '{$code}' and `user_id` = {$user->data->ID}", ARRAY_A);
		if($q){
			$user_id = wp_update_user(array(
				'ID' => $user->data->ID,
				'user_email' => $q['user_email'],
			));
			update_user_meta($user->data->ID, 'billing_email', $q['user_email']);
			$r = $wpdb->query("delete from `confirm_email` where `code` = '{$code}' ");
			echo'<div class="successEmail mainPop-up oneOfPopUp" style="display: block;">
<h4 class="headline-popUp">Обновление адреса электронной почты</h4>
<p>Ваш e-mail успешно изменён.</p>
<button class="but-red close-thank-sale">ОК</button>
<img src="'.get_template_directory_uri().'/img/x.png" alt="Close" class="close-oneOfPopUp">
</div>';
		} //endif q
	} //endif confirm
	return;
} //endfunction
//изменение телефона
function change_phone(){
	global $user_ID;
	$result = array('status' => '', 'data' => '');
	try{
		if($user_ID == 0) throw new Exception('error user');
		if(!isset($_POST['phone'])) throw new Exception('Заполните телефон');
		if(empty($_POST['phone'])) throw new Exception('Заполните телефон');
		$phone = trim(strip_tags(htmlspecialchars($_POST['phone'], ENT_QUOTES)));

		$user = get_userdata( $user_ID );
		update_user_meta($user->data->ID, 'phone', $phone);
		$result['status'] = 'success';
		$result['data'] = $phone;

	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_resetphone', 'change_phone');
add_action('wp_ajax_nopriv_resetphone', 'change_phone');
//обновление адресов
function save_address(){
	global $user_ID;
	$result = array('status' => '', 'data' => '');
	try{
		if($user_ID == 0) throw new Exception('error user');
		if(empty($_POST['x'])) throw new Exception('error x');
		$x = trim(strip_tags(htmlspecialchars($_POST['x'], ENT_QUOTES)));
		$user = get_userdata( $user_ID );

		$ar = explode(' ', $x);
		$str = '';
		foreach($ar as $value){
			$value = intval($value);
			$p = trim(strip_tags(htmlspecialchars($_POST[$value])));

			update_user_meta($user->ID, 'address'.$value, $p);
		} //endforeach
		$result['data'] = get_permalink(9);

		$result['status'] = 'success';

	} //endtry
	catch(Exception $e){
		$result['status'] = 'error';
		$result['data'] = $e->getMessage();
	}
	echo json_encode($result, 320);
	wp_die();
} //endfunction
add_action('wp_ajax_addaddress', 'save_address');
add_action('wp_ajax_nopriv_addaddress', 'save_address');

function get_portfolio($id){
	$count = -1;
	$q = 0;
	$QueryArgs = array(
		'post_type' => 'post',
		'cat' => $id,
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
	);
	$pc = new WP_Query($QueryArgs);
	if($pc->have_posts()){
		while ($pc->have_posts()) {
			$pc->the_post();
			$q++;
			if($count + 3 == $q){
				echo'<div class="work middle-work">';
				$count = $q;
			}
			else{
				echo'<div class="work">';
			}
			?>
            <img src="<?php

			if(get_the_post_thumbnail_url( get_the_ID())){
				$img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
			} else{
				$imgall= get_field( "img_default", 6 );
				$img= $imgall['url'];
			}
			echo $img;
			?>" alt="<?php the_title(); ?>">
            <div class="bot-info">
                <h5><?php the_title(); ?></h5>
				<?php the_content(); ?>
            </div>
            </div>
			<?php
		} //endwhile
		wp_reset_query();
	} //endif
} //endfunction
// Получаем путь к логотипу сайта
function logo_url(){
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
	return $image[0];
}
//Дефолтные изображенние

add_action( 'init', 'custom_fix_thumbnail' );

function custom_fix_thumbnail() {
	add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');

	function custom_woocommerce_placeholder_img_src( $src ) {
		$upload_dir = wp_get_upload_dir();
		$uploads = untrailingslashit( $upload_dir['baseurl'] );
		$src = $uploads . '/2020/01/one-product.png';

		return $src;
	}
}

add_shortcode( 'marc_cars', 'marc_cars_func');
function marc_cars_func(){
	$str='';
	$QueryArgs = array(
		'post_type' => 'cars',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby'        => 'date',
		'order' => 'ASC',
	);
	$pc = new WP_Query($QueryArgs);
	if ($pc->have_posts()) {
		$cars = array();

		while ($pc->have_posts()) {
			$pc->the_post();

//                                $image = get_field('photo');
			$cars[] = get_the_title();
		} //endwhile
		$pc->reset_postdata();
		$unique_cars = array_unique($cars);
		foreach ($unique_cars as $key => $value) :
			$str.='<option value="'.$value.'">'.$value.'</option>';
		endforeach;
	}
	return $str;
}

//Получение списка заказов по дате
function get_orders_by_date() {
	$message = array(
		'status' => 'error'
	);
	if(!empty($_POST['date'])) {
		$dateParam = explode(' - ',$_POST['date']);
		$params = array(
			'numberposts' => -1,
			'customer_id' => get_current_user_id(),
			'date_before' => $dateParam[1],
			'date_after' => $dateParam[0]
		);
		$result = wc_get_orders($params);
		if(count($result) > 0) {
			$orders = array();
			foreach ($result as $item) {
				$order = wc_get_order($item->id);
				$data = $order->get_data();
				array_push($orders, array(
					'date' => $data['date_modified']->date('d.m.Y'),
					'orderId' => $item->id,
					'status' => translate_order_status($data['status']),
					'total' => $order->get_total().' руб.',
				));
			}
            $ordersRender = '';
			foreach ($orders as $order) {
				$ordersRender .= '<tr class="account-user-order" data-order-id="'.$order['orderId'].'">';
				$ordersRender .=
                '<td>'.$order['date'].'</td>
                <td>'.$order['orderId'].'</td>
                <td>'.$order['total'].'</td>
                <td><a href="#" class="show-order-detail" data-order-id="'.$order['orderId'].'">Просмотр</a></td>
                <td><a href="/wp-content/themes/sof_theme/report_order_account.php?new_order='.$order['orderId'].'" target="_blank">Скачать pdf</a></td>
                <td><a href="" class="open-order-qr">Посмотреть QR-код</a></td>
                <td>'.$order['status'].'</td>';
				if($order['status'] == 'В обработке') {
					$ordersRender .= '<td><a class="user-order-canceled" href="">Отменить заказ</a></td>';
				}
                $ordersRender .= '</tr>';
            }
			$message['status'] = 'success';
			$message['orders'] = $ordersRender;
		} else {
			$message['text'] = 'За указанный период заказов не найдено';
		}
	} else {
		$message['text'] = 'Не указана дата';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_get_orders_by_date', 'get_orders_by_date');
add_action('wp_ajax_nopriv_get_orders_by_date', 'get_orders_by_date');

//Получение списка заказов по дате
function get_customer_orders_by_date() {

	$current_user = wp_get_current_user();
	// Собираем роли текущего пользователя
	$current_user_roles = array();
	foreach ($current_user->roles as $user_role) {
		$current_user_roles[] = $user_role;
	}

	$ordersPickupPoints = array();

	//Точки самовывоза для Менеджера магазина
	if(in_array('shop_manager',$current_user_roles)) {
		$shopPickupPoints = get_shop_pickup(get_current_user_id());
		foreach ($shopPickupPoints as $shopPickupPoint) {
			$ordersPickupPoints[] = $shopPickupPoint->post_title;
		}
	}

	//Точки самовывоза для Юридического лица
	if(in_array('user_magazin',$current_user_roles)) {
		$legalPickupPoints = get_all_pickup_with_coord(get_current_user_id());
		foreach ($legalPickupPoints as $legalPickupPoint) {
			$legalPickupPoint->the_post();
			$ordersPickupPoints[] = get_the_title();
		}
	}
//    var_dump($ordersPickupPoints);
	$message = array(
		'status' => 'error'
	);
	if(!empty($_POST['date'])) {
		$dateParam = explode(' - ',$_POST['date']);
		$params = array(
			'numberposts' => -1,
			'date_before' => $dateParam[1],
			'date_after' => $dateParam[0],
			'meta_key'     => 'shipping_pickup',
			'meta_compare' => 'IN',
			'meta_value' => $ordersPickupPoints,
		);
		$result = wc_get_orders($params);
		if(count($result) > 0) {
			$orders = array();
			foreach ($result as $item) {
				$order = wc_get_order($item->id);
				$orderData = $order->get_data();
				//Товары

				$orderItems = $order->get_items();
				$orderProduct = array();
				foreach($orderItems as $orderItem) {
					$itemData = $orderItem->get_data();
					$order_product = $orderItem->get_product();
					array_push($orderProduct,array(
						'id' => $itemData['product_id'],
						'price'=> $order_product->get_price(),
						'regPrice' => $order_product->get_regular_price(),
						'link' => get_permalink($itemData['product_id']),
						'title' => $itemData['name'],
						'quantity' => $itemData['quantity']
					));
				}
				array_push($orders, array(
					'date' => $orderData['date_modified']->date('d.m.Y'),
					'orderId' => $item->id,
					'status' => translate_order_status($orderData['status']),
					'total' => $order->get_total().' руб.',
					'client' => array(
						'name' => $orderData['billing']['last_name'].' '.$orderData['billing']['first_name'],
						'phone' => $orderData['billing']['phone'],
						'email' => $orderData['billing']['email'],
					),
					'payment' => get_post_meta($itemData['order_id'], 'billing_payment', true),
					'delivery' => get_post_meta($itemData['order_id'], 'shipping_delivery', true),
					'shipping_pickup' => get_post_meta($itemData['order_id'], 'shipping_pickup', true),
					'products' => $orderProduct,
				));
			}
			$message['orders'] = '';
			foreach ($orders as $order) {
				$productRender = '<table class="full-width account-orders-item-product"><thead><tr><th>Товар</th><th>Цена</th><th>Количество</th></tr></thead><tbody>';
				foreach ($order['products'] as $product) {
					$productRender .=
						'<tr>
<td><a target="_blank" href="'.$product['link'].'">'.$product["title"].'</a></td>
<td>'.$product["price"].'</td>
<td>'.$product["quantity"].'</td>
</tr>';
				}
				$productRender .= '</tbody></table>';
				$orderRender .=
					'<div class="account-orders-item" data-order-id="'.$order['orderId'].'"><h5>Заказ №: '.$order['orderId'].'</h5>
                <div class="account-orders-item-detail">'.$productRender.'</div>
                <div class="account-orders-item-desc">
                Клиент: <b>'.$order['client']['name'].'</b><br>
                Телефон: <b>'.$order['client']['phone'].'</b><br>
                Email: <b>'.$order['client']['email'].'</b><br>
                Дата заказа: <b>'.$order['date'].'</b><br>
                Оплата: <b>'.$order['payment'].'</b><br>
                Доставка: <b>'.$order['delivery'].'</b><br>
                Сумма: <b>'.$order['total'].'</b><br>
                Адрес самовывоза: <b>'.$order['shipping_pickup'].'</b><br>
                Статус: <b>'.$order['status'].'</b>
                </div>';
				if ($order['status'] != 'Отменено' && $order['status'] != 'Выполнен') {
					$orderRender .= '<div class="account-orders-item-btn"><button class="order-change-status order-complete-btn" type="button">Исполнен</button> <button class="order-change-status order-close-btn" type="button">Отменён</button></div>';
				}
				$orderRender .= '</div>';
			}

			$message['status'] = 'success';
			$message['orders'] = $orderRender;
		} else {
			$message['text'] = 'За указанный период заказов не найдено';
		}
	} else {
		$message['text'] = 'Не указана дата';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_get_customer_orders_by_date', 'get_customer_orders_by_date');
add_action('wp_ajax_nopriv_get_customer_orders_by_date', 'get_customer_orders_by_date');

//Измение статуса заказа клиета
function change_customer_order_status() {
	if ( ! empty( $_POST['status'] ) && ! empty( $_POST['order_id'] ) ) {

		$message = array(
		'status' => 'error'
	);

	$current_user = wp_get_current_user();
	$current_user_roles = array();
	foreach ($current_user->roles as $user_role) {
		$current_user_roles[] = $user_role;
	}

	//Точки самовывоза для Менеджера магазина
	if(in_array('shop_manager',$current_user_roles)) {
		$shopPickupPoints = get_shop_pickup(get_current_user_id());
		foreach ($shopPickupPoints as $shopPickupPoint) {
			$ordersPickupPoints[] = $shopPickupPoint->post_title;
		}
	}

	//Точки самовывоза для Юридического лица
	if(in_array('user_magazin',$current_user_roles)) {
		$legalPickupPoints = get_all_pickup_with_coord(get_current_user_id());
		foreach ($legalPickupPoints as $legalPickupPoint) {
			$legalPickupPoint->the_post();
			$ordersPickupPoints[] = get_the_title();
		}
	}

	if(!empty($ordersPickupPoints)) {
		$dateParam = explode( ' - ', $_POST['date'] );
		$params    = array(
			'numberposts'  => - 1,
			'meta_key'     => 'shipping_pickup',
			'meta_compare' => 'IN',
			'meta_value'   => $ordersPickupPoints,
		);
		$result = wc_get_orders($params);
		if(count($result) > 0) {
			$acceptOrderID = array();
			foreach ($result as $item) {
				$acceptOrderID[] = $item->id;
			}
		}
	}
	if(in_array($_POST['order_id'],$acceptOrderID)) {
	    $userRoleAccept = true;
	} else {
		$userRoleAccept = false;
    }

	if($userRoleAccept) {
			$order = wc_get_order( $_POST['order_id'] );
			if ( $_POST['status'] == 'complete' ) {
				$status = 'completed';
			} else {
                if ( $_POST['status'] == 'close' ) {
                    $status = 'cancelled';
                }
			}
			$currentUser = wp_get_current_user();
			$note = 'Статус заказа изменён пользователем ' . $currentUser->data->display_name . ' (id: ' . $currentUser->data->ID . ')';
			if ( ! empty( $status ) && $order->update_status( $status, $note ) ) {
				$message['status'] = 'success';
			} else {
				$message['text'] = 'Не удалось изменить статус заказа';
			}
		} else {
		$message['text'] = 'Нет доступа';
		}
	} else {
		$message['text'] = 'Ошибка в данных';
    }
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_change_customer_order_status', 'change_customer_order_status');
add_action('wp_ajax_nopriv_change_customer_order_status', 'change_customer_order_status');

//Измение статуса заказа клиета
function cancel_order() {
	if (! empty( $_POST['order_id'] ) ) {
		$message = array(
			'status' => 'error'
		);
		$params    = array(
			'ID' => $_POST['order_id'],
			'numberposts'  => - 1,
			'customer_id' => wp_get_current_user()->ID
		);
		$result = wc_get_orders($params);
		if(count($result) > 0) {
			$order = wc_get_order( $_POST['order_id'] );
			$currentUser = wp_get_current_user();
			$note = 'Статус заказа изменён пользователем ' . $currentUser->data->display_name . ' (id: ' . $currentUser->data->ID . ')';
			if ($order->update_status( "cancelled", $note ) ) {
				$message['status'] = 'success';
			} else {
				$message['text'] = 'Не удалось изменить статус заказа';
			}
		} else {
			$message['text'] = 'Нет доступа';
		}
	} else {
		$message['text'] = 'Ошибка в данных';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_cancel_order', 'cancel_order');
add_action('wp_ajax_nopriv_cancel_order', 'cancel_order');

//Подробная информация о заказе в личном аккаунте
function account_order_detail() {
	$message = array(
		'status'=>'error'
	);
	if(!empty($_POST['order_id'])) {
		$total = 0;
		$orderId = $_POST['order_id'];
		if($order = wc_get_order( $orderId )) {
			$userId = $order->get_user_id();
			if($userId == get_current_user_id()) {
				$data['products'] = array();
				$pageTitle = get_bloginfo('name') . " Данные Заказа" . $orderId;
				$products = $order->get_items();
				$orderData = $order->get_data();
				foreach($products as $key => $product) {
					$productData = $product->get_data();
					$productInfo = $product->get_product();
					array_push($data['products'],array(
						'name' => $productData['name'],
						'quantity' => $productData['quantity'],
						'cost' => ($productData['quantity'] * $productInfo->get_price())." руб.",
					));
					$total += ($productInfo->get_price() * $productData['quantity']);
				}
				$data['orderData'] = array(
					'name' => $orderData['billing']['first_name']." ".$orderData['billing']['last_name'],
					'address' => $orderData['billing']['address_1'],
					'phone' => $orderData['billing']['phone'],
					'email' => $orderData['billing']['email'],
					'delivery' => get_post_meta($orderId, 'shipping_delivery', true).", ".get_post_meta($orderId, 'shipping_pickup', true),
					'payment' => get_post_meta($orderId, 'billing_payment', true),
					'payment_data' => $orderData['date_modified']->date('d.m.Y'),
					'status' => translate_order_status($orderData['status']),
					'total' => $total." руб.",
					'order_id' => $orderId
				);
				$message = array(
					'status' => 'success',
					'data' => $data,
				);
			} else {
				$message['text'] = 'Нет доступа';
			}
		} else {
			$message['text'] = 'Нет данных по заказу';
		}
	} else {
		$message['text'] = 'Не указан id заказа';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_account_order_detail', 'account_order_detail');
add_action('wp_ajax_nopriv_account_order_detail', 'account_order_detail');
//Обновление поля Пункт самовывоза по умлочанию
function update_default_pickup () {
	$message['status'] = 'error';
	if(isset($_POST['pickup'])) {
		update_user_meta(get_current_user_id(), 'default_pickup', $_POST['pickup']);
		$message['status'] = 'success';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_update_default_pickup', 'update_default_pickup');
add_action('wp_ajax_nopriv_update_default_pickup', 'update_default_pickup');

//Нормализация адреса через DaData
function normalizeAddress ($address) {
	if ( ! empty( $address ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "https://cleaner.dadata.ru/api/v1/clean/address" );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Token 8871a61b6905f973fa280ee83f47cfec14182446',
			'X-Secret: f1d2a95e321c1e10082c61dd00be4e41cb9d56cf'
		) );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( array( $address ) ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$server_output = curl_exec( $ch );
		curl_close( $ch );

		return json_decode( $server_output );
	}
}

function account_change_profile() {
	$message = array(
		'status' => 'error'
	);
	if (! empty( $_POST ) ) {
		$user_id = wp_update_user( [
			'ID'       => get_current_user_id(),
			'first_name' => $_POST['firstName'],
            'last_name' => $_POST['lastName']
		] );
		if ( is_wp_error( $user_id ) ) {
			$message['text'] = 'Нет доступа';
		} else {
		    $message['status'] = 'success';
		}
	} else {
		$message['text'] = 'Ошибка в данных';
	}
	echo json_encode($message);
	wp_die();
}
add_action('wp_ajax_account_change_profile', 'account_change_profile');
add_action('wp_ajax_nopriv_account_change_profile', 'account_change_profile');

function sendMail($message, $email, $subject) {
	$from    = 'korkinea@8168.ru';

	$boundary = md5( date( 'r', time() ) );
	$headers  = "MIME-Version: 1.0\r\n";
	$headers  .= "From: " . $from . "\r\n";
	$headers  .= "Reply-To: " . $from . "\r\n";
	$headers  .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    $message="
Content-Type: multipart/mixed; boundary=\"$boundary\"

--$boundary
Content-Type: text/html; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

$message";
    $message.="
--$boundary--";
	return mail( $email, $subject, $message, $headers );
}

function get_select_pickup_shop() {
    $message = array(
        'status' => 'error'
    );
    $pickupByOrderPickup = array(
        'post_type' => 'pickup',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby'        => 'date',
        'order' => 'ASC',
    );
    $pickupByOrderPickupQuery = new WP_Query($pickupByOrderPickup);
    $shopManagerWithPickup = array();
    if($pickupByOrderPickupQuery->have_posts()) {
        $message['pickupShopSelector'] = '';
        while ($pickupByOrderPickupQuery->have_posts()) {
            $pickupByOrderPickupQuery->the_post();
            $shopManagerWithPickup[] = get_post_meta(get_the_ID(), 'magazin', true);
        }
    }
    wp_reset_query();
	$users = get_users(array(
			'meta_key'     => 'legal-user-id',
			'meta_value'   => get_current_user_id(),
			'role' => 'shop_manager')
	);
    foreach( $users as $user ) {
        if(!in_array($user->data->ID,$shopManagerWithPickup)) {
            $message['pickupShopSelector'] .=   '<option value="'.$user->data->ID.'">'.$user->data->display_name.' (id - '.$user->data->ID.')</option>';
        }
    }
    $message['status'] = 'success';
    echo json_encode($message);
    wp_die();
}
add_action('wp_ajax_get_select_pickup_shop', 'get_select_pickup_shop');
add_action('wp_ajax_nopriv_get_select_pickup_shop', 'get_select_pickup_shop');

function get_pickup_manager() {
    $message = array(
        'status' => 'error'
    );
    if($_POST['pickup']) {
        $pickupByOrderPickup = array(
            'post_type' => 'pickup',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'title' => $_POST['pickup'],
        );
        $pickupByOrderPickupQuery = new WP_Query($pickupByOrderPickup);
        if ($pickupByOrderPickupQuery->have_posts()) {
            $message['manager'] = array();
            while ($pickupByOrderPickupQuery->have_posts()) {
                $pickupByOrderPickupQuery->the_post();
                $shopManagerId = get_post_meta(get_the_ID(), 'magazin', true);
                $shopManager = get_user_by('ID', $shopManagerId);
                $message['manager']['email'] = $shopManager->user_email;
                $message['manager']['firstname'] = $shopManager->user_firstname;
                $message['manager']['lastname'] = $shopManager->user_lastname;
                $message['manager']['phone'] = get_user_meta($shopManager->ID, 'phone', true);
                $message['manager']['address'] = get_user_meta($shopManager->ID, 'address1', true);
                $message['status'] = 'success';
            }
        }
    }
    echo json_encode($message);
    wp_die();
}
add_action('wp_ajax_get_pickup_manager', 'get_pickup_manager');
add_action('wp_ajax_nopriv_get_pickup_manager', 'get_pickup_manager');


/**
 * Отдаём цену по группе пользователя
 * (c) icmark.ru 2022
 */

add_filter( 'woocommerce_get_price', 'w4dev_woocommerce_get_price', 10, 2);
function w4dev_woocommerce_get_price( $price, $product ) {
    $userGroupGuid = get_user_meta( get_current_user_id(), 'guid_group_1c' );
    if(!empty($userGroupGuid[0])) {
        $params = array(
            'meta_key' => 'client_group-guid',
            'meta_value' => $userGroupGuid[0],
            'post_type'   => 'icm_client_group',
        );
        $groups = get_posts($params);
        if(!empty($groups)) {
            global $wpdb;
            $groupId = $groups[0]->ID;
            $priceRes = $wpdb->get_results($wpdb->prepare("SELECT price FROM wp_icm_products_prices WHERE product_id = %d AND group_id = %d",$product->id,$groupId), ARRAY_A);
            if(!empty($priceRes)) {
                return $priceRes[0]['price'];
            }
        }
    }

    return $price;
}

add_action('wp_ajax_get_pickup', 'get_pickup_func');
add_action('wp_ajax_nopriv_get_pickup', 'get_pickup_func');
function get_pickup_func() {
	$response = "";
	$pickups = explode(";", $_POST["pickup"]);
	$queryArgs = [
		"post_type" => "pickup",
		"posts_per_page" => "-1",
		"meta_query" => [
			[
				"key" => "coords",
				"value" => $pickups
			]
		]
	];
	$query = new WP_Query($queryArgs);

	while ($query->have_posts()) {
		$query->the_post();
		$response .= "<option data-point-coords='" . get_field("coords") . "' label='" . get_the_title() . "' value='" . get_the_title() . "'>" . get_the_title() . "</option>";
	}
	wp_reset_postdata();

	echo json_encode($response);
	wp_die();
}
?>