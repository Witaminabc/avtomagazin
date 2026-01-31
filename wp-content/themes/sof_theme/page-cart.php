<?php
/**
 * Template name:  Cart
 * Template Post Type: page
 */
get_header();
$user_email = '';
$user_firstname = '';
$user_lastname = '';
$phone = '';
$address1 = '';
if(is_user_logged_in()){
    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;
    $user_firstname = $current_user->user_firstname;
    $user_lastname = $current_user->user_lastname;
    $phone = get_user_meta($current_user->ID, 'phone', true);
    $address1 = get_user_meta($current_user->ID, 'address1', true);
}
$current_user_roles = array();
foreach ($current_user->roles as $user_role) {
    $current_user_roles[] = $user_role;
}
?>
<main>
    <div class="container">
        <div class="basket">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_home_url(); ?>">Главная</a></li>
                    <li><a href="<?php wc_get_cart_url(); ?>"><?php the_title(); ?></a></li>
                </ul>
            </div>
            <h2 class="main-headline"><?php the_title(); ?></h2>
            <div class="all-basket">
                <div class="all-steps">
                    <div class="step active first">
                        <h4><span>Шаг 1.</span> Ваш заказ</h4>
                    </div>
                    <div class="step second">
                        <h4><span>Шаг 2.</span> Оформить заказ</h4>
                    </div>
                    <div class="step third">
                        <h4><span>Шаг 3.</span> Данные по заказу</h4>
                    </div>
                </div><!-- end all steps -->
                <?php
                $total = 0;
                if(count(WC()->cart->get_cart()) > 0){
				$currentUser = wp_get_current_user();
                ?>
                <div class="first-step <? if(in_array("corporate_body", $currentUser->roles)) echo "corporate"; ?>">
                    <h3>Ваши заказы</h3>
                    <div class="buy-products">
                        <?php
                        //	$pickups = array();
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                            $_product = $values['data'];
                            $total += ($_product->get_price() * $values['quantity']);
//$pickups[] = $_product->id;
							$pickupList = [];
							$availabelListArgs = [
								"post_type" => "icm_available",
								"posts_per_page" => "-1",
								"meta_query" => [
									"relation" => "AND",
									[
										"key" => "available_product",
										"value" => $_product->get_ID()
									],
								]
							];
							$availabelList = new WP_Query($availabelListArgs);
							while ($availabelList->have_posts()) {
								$availabelList->the_post();
								if(strtotime(get_field("available_data")) >= strtotime("-14 days")) {
									$pickupList[] = get_field("available_geo");
								}
							}
							wp_reset_postdata();
                            ?>
                            <div class="one-product-basket" data-pickup="<?= implode(";", $pickupList) ?>">
                                <div class="image">
                                    <img src="<?php
                                    $img = get_option( 'woocommerce_placeholder_image', 0 );
                                    if(get_the_post_thumbnail_url( $_product->id )){
                                        echo get_the_post_thumbnail_url( $_product->id, 'full' );
                                    } else{
                                        
                                        echo $img;
                                    }
                                    ?>" alt="<?php echo strip_tags($_product->name); ?>">
                                </div><!-- end image -->
                                <div class="title">

                                    <h5><a href="<?php echo get_permalink($_product->id); ?>"><?php echo $_product->name; ?></a></h5>
                                </div>
                                <p><?php echo $_product->get_price(); ?> р.</p>
                                <div class="product-error">
                                    <p>Невозможные к заказу из данной точки</p>
                                </div>
								<style>
									.hide {
										display: none;
									}
								</style>
                                <div class="quantity-block">
                                    <input class="quantity-num" type="number" id="input_quantity_product<?php echo $cart_item_key; ?>" data-input_quantity_product="<?php echo $cart_item_key; ?>" data-current_price="<?php echo $_product->get_price(); ?>" data-regular_price="<?php echo $_product->get_regular_price(); ?>" data-low_stock="<?php echo $_product->get_stock_quantity(); ?>" value="<?php echo $values['quantity']; ?>" />
                                    <p>шт</p>
                                    <div class="symb">
                                        <button class="quantity-arrow-plus" data-cart_plus="<?php echo $cart_item_key; ?>"> + </button>
                                        <button class="quantity-arrow-minus" data-cart_minus="<?php echo $cart_item_key; ?>"> - </button>
                                    </div>
                                </div>
                                <div class="price">
                                    <p id="current_price<?php echo $cart_item_key; ?>"><?php echo ($values['quantity'] * $_product->get_price()); ?> р.</p>
                                    <p id="regular_price<?php echo $cart_item_key; ?>"><?php echo ($_product->get_regular_price() * $values['quantity']); ?> р.</p>
                                </div>
                                <div class="delete">
                                    <img src="<?= get_template_directory_uri().'/img/delete.png'; ?>" data-delete_product="<?php echo $cart_item_key; ?>" alt="удалить">
                                </div>
                            </div><!-- end product -->
                            <?php
                        } //endforeach
                        ?>
                    </div><!-- end buy product -->
                    <div class="bot">
                        <div class="all-sum">
                            <h3 id="cart_sum"><span>К оплате:</span> <?php echo $total; ?> р.</h3>
                        </div>
                    </div><!-- end bod -->
                    <div class="delivery">
                        <div class="delivery-pay">
                            <div class="method">
                                <h4>Способ доставки </h4>
                                <div class="change">
                                    <?php
                                    $QueryArgs = array(
                                        'post_type' => 'delivery',
                                        'posts_per_page' => -1,
                                        'post_status' => 'publish',
                                        'orderby'        => 'date',
                                        'order' => 'ASC',
                                    );
                                    $pc = new WP_Query($QueryArgs);
                                    $qty = 0;
                                    if($pc->have_posts()){
                                    while ($pc->have_posts()) {
                                    $pc->the_post();
                                    $qty++;
                                    ?>
                                    <?php
                                    if($qty == 1){
                                        echo'<div class="open-maps">';
                                    } //endif
                                    else{
                                        echo'<div class="close-maps">';
                                    } //endelse
                                    ?>
                                    <input type="radio" id="<?php the_field('delivery_id'); ?>" name="change-delivery" value="<?php echo get_the_title(); ?>"<?php if($qty == 1) echo' checked'; ?>>
                                    <label for="<?php the_field('delivery_id'); ?>">
                                        <?php
                                        $url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                        if(!empty($url)){
                                            echo'<img src="'.$url.'" alt="'.get_the_title().'">';
                                        } //endif
                                        else{
                                            echo get_the_title();
                                        } //endelse
                                        ?>
                                    </label>
                                </div>
                                <?php
                                } //endwhile
                                $pc->reset_postdata();
                                } //endif
                                ?>
                            </div><!-- end change -->
                        </div><!-- end method -->
                        <div class="method">
                            <h4>Способ оплаты</h4>
                            <div class="change">
                                <?php
                                $QueryArgs = array(
                                    'post_type' => 'payment',
                                    'posts_per_page' => -1,
                                    'post_status' => 'publish',
                                    'orderby'        => 'date',
                                    'order' => 'ASC',
                                );
                                $pc = new WP_Query($QueryArgs);
                                $qty = 0;
                                if($pc->have_posts()){
                                    while ($pc->have_posts()) {
                                        $pc->the_post();
                                        $qty++;
                                        ?>
                                        <div>
                                            <input type="radio" id="<?php the_field('payment_id'); ?>" name="change-pay" value="<?php echo get_the_title(); ?>"<?php if($qty == 1) echo' checked'; ?>>
                                            <label for="<?php the_field('payment_id'); ?>"><?php echo get_the_title(); ?><?php the_content(); ?></label>
                                        </div>
                                        <?php
                                    } //endwhile
                                    $pc->reset_postdata();
                                } //endif
                                ?>
                            </div><!-- end change -->
                        </div><!-- end method -->
                    </div><!-- end delivery pay -->
                    <?php
                    //получить точки самовывоза товаров и оставить только уникальные
                    if (in_array('user_magazin', $current_user_roles)) {
                        $pickupPosts = get_all_pickup_with_coord($current_user->ID);
                    } else {
                        $pickupPosts = get_all_pickup_with_coord();
                    }
                    $defaultPoint = get_user_meta($current_user->ID, 'default_pickup', true);
                    if(count($pickupPosts) > 0) :?>
                        <?php
                        $pickupPoints = array();
                            foreach ($pickupPosts as $pickupPost) {
                                $pickupPost->the_post();
                                $pointMeta = get_post_meta(get_the_ID());
                                array_push($pickupPoints, array(
                                        'active_point' => (isset($pointMeta["active_point"]) && $pointMeta["active_point"][0] == '1') ? true : false,
                                        'title' => get_the_title(),
                                        'coords' => $pointMeta["coords"][0]
                                ));
                            }
                        ?>
                        <div class="pickup">
                            <div class="adress">
                                <h3>
                                <?php
                                if (in_array('user_magazin', $current_user_roles)) {
                                    echo 'Ваши точки самовывоза';
                                } else {
                                    echo 'Выберите точку самовывоза';
                                }
                                ?>
                                </h3>
								<? if (!in_array('user_magazin', $current_user_roles)): ?>
									<a href="#" id="filter-pickup" class="pickup-link">Показать доступные точки самовывоза</a>
									<a href="<?= $_SERVER["REQUEST_URI"] ?>" class="pickup-link">Показать все точки самовывоза</a>
								<? endif ?>
								<select id="pickup" name="pickup">
									<option disabled value="" data-point-coords="">Выбрать точку самовывоза</option>
									<?php foreach($pickupPoints as $pickupPoint) :?>
										<?php if($pickupPoint["active_point"]):?>
											<?php $selected = ($defaultPoint == $pickupPoint['title']) ? 'selected' : '';?>
											<option data-point-coords="<?=$pickupPoint['coords']?>" label="<?=$pickupPoint['title']?>" value="<?=$pickupPoint['title']?>" <?=$selected?>><?= $pickupPoint["title"] ?></option>
										<?php endif;?>
									<?php endforeach;?>
								</select>
                            </div><!-- end adres -->
                            <?php // <div class="map-basket" id="map-basket"></div>?>
                            <div id="basket-map"></div>
                        </div><!-- end pickup -->
                        <?php endif; ?>
                </div><!-- end delivery -->
                <div class="buttons">
                    <div class="button">
                        <a href="#">Назад</a>
                    </div>
                    <div class="button-red">
                        <?php
                        $userMagazin = (in_array('user_magazin', $current_user_roles)) ? 'user-magazin-cart' : '';
                        ?>
                        <a href="#" class="not_available_button <?=$userMagazin?>" id="next-first-step">Перейти к заполнению контактных данных</a>
                    </div>
                </div><!-- end buttons -->
            </div><!-- end first step -->
            <div class="second-step courier-step">
                <h3>Ваши данные</h3>
                <div class="all-form">
                    <form>
                        <div>
                            <label for="surname">Фамилия <span>*</span></label>
                            <input type="text" name="surname" id="surname" value="<?php echo $user_lastname; ?>" required>
                        </div>
                        <div>
                            <label for="name">Имя <span>*</span></label>
                            <input type="text" name="name" id="name" value="<?php echo $user_firstname; ?>" required>
                        </div>
                        <div>
                            <label for="phone">Телефон <span>*</span></label>
                            <input type="text" name="phone" value="<?php echo $phone; ?>" id="phone" class="form-control" required>
                        </div>
                        <div>
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" value="<?php echo $user_email; ?>" required>
                        </div>
                        <div>
                            <label for="adress">Адрес</label>
                            <input type="text" name="adress" id="adress" value="<?php echo $address1; ?>" required>
                        </div>
                        <div class="politic">
                            <input type="radio" id="politic" checked>
                            <label for="politic">Я согласен (-на) с <a class="open-pop-Up" href='#' >Политикой конфиденциальности</a></label>
                        </div>
                        <div class="buttons">
                            <div class="button back-to-first">
                                <a href="javascript:void(0);">Назад</a>
                            </div>
                            <div class="button-red next-second-step1">
                                <a href="javascript:void(0);" id="checkout_courier">Оформить заказ</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="second-step pickup-step">
                <h3>Ваши данные</h3>
                <div class="all-form">
                    <form>
                        <div>
                            <label for="surname1">Фамилия <span>*</span></label>
                            <input type="text" class="inputCheck" name="surname" value="<?php echo $user_lastname; ?>" id="surname1">
                        </div>
                        <div>
                            <label for="name1">Имя <span>*</span></label>
                            <input type="text" class="inputCheck" name="name" value="<?php echo $user_firstname; ?>" id="name1">
                        </div>
                        <div>
                            <label for="phone1">Телефон <span>*</span></label>
                            <input type="text" class="inputCheck form-control" name="phone" value="<?php echo $phone; ?>" id="phone1">
                        </div>
                        <div>
                            <label for="email1">E-mail</label>
                            <input type="email" class="inputCheck" name="email" value="<?php echo $user_email; ?>" id="email1">
                        </div>
                        <div class="politic">
                            <input type="radio" id="politic" checked>
                            <label for="politic">Я согласен (-на) с <a class="open-pop-Up" href='#'>Политикой конфиденциальности</a></label>
                        </div>
                        <div class="buttons">
                            <div class="button back-to-first">
                                <a href="javascript:void(0);">Назад</a>
                            </div>
                            <div class="button-red next-second-step">
                                <a href="javascript:void(0);" id="checkout_pickup">Оформить заказ</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="third-step" id="result_checkout" data-linkqr="<?php echo get_permalink(274); ?>">

                <h3>Спасибо за заказ!</h3>
                <h4>Наш менеджер свяжется с вами в ближайшее время</h4>
            </div><!-- end thirt step -->

            <?php
            } //endif count
            else{
                echo'<p>В корзине 0 товаров</p>';
            } //endelse
            ?>

        </div><!-- end all basket -->
    </div><!-- end basket -->
    </div><!-- end container -->
</main>
<?php
get_footer();
?>
