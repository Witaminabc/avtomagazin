<!DOCTYPE html>

<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?php bloginfo( 'name' ).wp_title( '|', true, 'left' ); ?></title>
    <meta name="cmsmagazine" content="5a7189a28fb3dc68238dbdb44c9ea99c" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" type="image/png" sizes="16x16" href="">
    <?php wp_head(); ?>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=a0f6e80e-4723-425f-9028-e25ed38db01e&lang=ru_RU" type="text/javascript">
    </script>
</head>
<body>
<header>
    <div class="container">
        <div class="under-menu">
            <div class="logo">
                <a href="<?php echo home_url('/'); ?>">
                    <h2>СПД-Групп</h2>
                </a>
            </div>
            <form class="search" method="GET" action="<?php echo get_home_url(); ?>">
                <a href="javascript:void(0)"><img src="<?= get_template_directory_uri().'/img/search-img.png'; ?>" alt="Поиск"></a>
                <input type="text" name="s" placeholder="Поиск по сайту">
            </form>
            <div class="select-of-car">
                <a href="<?php echo get_permalink(244); ?>#model" class="but-white">Подбор по машине</a>
                <div class="basket-link">
                    <?php
                    if(WC()->cart->get_cart_contents_count() == 0){
                        ?>
                        <a id="basket_url" href="javascript:void(0)"><img src="<?= get_template_directory_uri().'/img/basket-img.png'; ?>" alt="Корзина"></a>
                        <?php
                    } //endif
                    else{
                        ?>
                        <a id="basket_url" href="<?php echo wc_get_cart_url(); ?>"><img src="<?= get_template_directory_uri().'/img/basket-img.png'; ?>" alt="Корзина"></a>
                        <?php
                    }
                    ?>
                    <p class="open-productsBasket" id="open_product_basket"><?php echo WC()->cart->get_cart_contents_count(); ?></p>
                </div><!-- end basket link -->
            </div><!-- end select of car -->
            <div class="productsBasket oneOfPopUp" style="display: none;">
                <h4>Корзина</h4>
                <?php
                $total = 0;
                $qt = 0;
                if(count(WC()->cart->get_cart()) > 0){
//подсчитать кол-во уникальных товаров
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                        $qt++;
                    } //endforeach
                } //endif count
                echo'<p class="col-product" id="col_product">В корзине '.$qt.' товара.</p>';
                echo'<div class="all-products" id="all_product">';

                if(count(WC()->cart->get_cart()) > 0){
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                        $_product = $values['data'];
                        $total += ($_product->get_price() * $values['quantity']);
                        if(get_the_post_thumbnail_url( $_product->id)){
                            $img = get_the_post_thumbnail_url( $_product->id, 'full' );
                        } else{
                            $imgall = get_option( 'woocommerce_placeholder_image', 0 ); 
                            $img = $imgall;
                        }
                        echo'<div class="products">
                        <div class="image">
                        <img src="'.$img.'" alt="">
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
                } //endif count
                echo'</div><!-- end all products -->
                        <div class="all-price">
                        <p>Сумма заказа:</p>
                        <p id="all_price">'.$total.' р.</p>
                        </div><!-- all price -->';
                ?>
                <?php
                if(count(WC()->cart->get_cart()) == 0){
                    ?>
                    <button class="but-white" onclick="document.location.href='<?php echo wc_get_cart_url(); ?>'" id="my_checkout_btn" style="display: none;">Оформить заказ</button>
                    <?php
                } //endifcount
                else{
                    ?>
                    <button class="but-white" onclick="document.location.href='<?php echo wc_get_cart_url(); ?>'">Оформить заказ</button>
                    <?php
                }
                ?>
                <h6 class="close-oneOfPopUp">&#10006</h6>
            </div><!-- end popup basket -->
        </div><!-- end under menu -->
    </div><!-- end container -->
    <div class="header-menu">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <?php custom_menu('Главное меню'); ?>
                    <div class="form-inline my-2 my-lg-0">
                        <img src="<?= get_template_directory_uri().'/img/log-in-img.png'; ?>" alt="log">
                        <?php
                        if(is_user_logged_in()){
                            $current_user = wp_get_current_user();
                            echo '<a href="'.get_permalink(9).'">Здравствуйте,<span> '.$current_user->user_firstname.'</span></a>';
                        }
                        else{
                            echo'<a href="javascript:void(0);" class="open-popUpLogin">Войти</a>';
                        }
                        ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
	<?php
	if(is_user_logged_in()){
		?>
        <div class="post-header">
            <div class="container">
                                <?php
				$metaPM = array( 'phone_manager' => '', 'email_manager' => '');
				$current_user = wp_get_current_user();
				$current_user_roles = array();
				foreach ( $current_user->roles as $user_role ) {
					$current_user_roles[] = $user_role;
				}
				$notNaturalUser = false;
				if ( in_array( 'shop_manager', $current_user_roles ) || in_array( 'user_magazin', $current_user_roles ) ) {
					$notNaturalUser = true;
					$metaPM = get_metadata( 'user', $current_user->ID );
				} else {
					$naturalPM = $users = get_users( array(
						'role' => 'natural_person_manager'
					) );
					if ( count( $naturalPM ) > 0 ) {
						$metaPM = get_metadata( 'user', $naturalPM[0]->data->ID );
					}
				}?>
                <div class="<?=($notNaturalUser) ? 'head' : "head-notLogin"?>">
				<?php if($notNaturalUser):?>
                    <div class="block-head-1">
                        <p>Текущая задолженность: <span>999 999 р.</span></p>
                    </div>
                    <div class="block-head" onclick="document.location.href='<?php echo get_permalink(9); ?>?tab=settlements'">
                        <img src="<?= get_template_directory_uri().'/img/img-calc.png'; ?>" alt="calculator">
                        <p>Взаиморасчеты </p>
                    </div>
                    <div class="block-head" onclick="document.location.href='<?php echo get_permalink(9); ?>?tab=shipments'">
                        <img src="<?= get_template_directory_uri().'/img/box-img.png'; ?>" alt="calculator">
                        <p>Отгрузки</p>
                    </div>
					<?php endif;?>
                    <div class="block-head" onclick="document.location.href='<?php echo get_permalink(9); ?>?tab=orders'">
                        <img src="<?= get_template_directory_uri().'/img/contract-img.png'; ?>" alt="calculator">
                        <p>Заказы</p>
                    </div>
                    <div class="block-head">
                        <p>Менеджер: <a href="tel:<?=$metaPM['phone_manager'][0]?>"><?=$metaPM['phone_manager'][0]?></a></p>
                        <a href="mailto:<?=$metaPM['email_manager'][0]?>"><?=$metaPM['email_manager'][0]?></a>
                    </div><!-- end block head -->
                </div><!-- end head -->
            </div><!-- end container -->
        </div><!-- end post header -->
		<?php
	} //endif loged
	?>
</header>
<div class="bg-black"></div>
<div class="errorLogPas mainPop-up oneOfPopUp" style="display: none;">
    <h4 class="headline-popUp">Вход</h4>
    <p id="handler_error">Логин и пароль не найдены</p>
    <button class="but-red open-popUpLogin">ОК</button>
    <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
</div>
<div class="bg-black"></div>
<div class="pop-up oneOfPopUp">
    <div class="main-info">
        <h4>Политика конфиденциальности</h4>
        <div>
            <?php dynamic_sidebar('politic'); ?>
        </div>
        <h5>Используя этот ресурс Вы подтверждаете свое согласие с данной политикой
        </h5>
        <div class="button">
            <a class="close-pop-up" href="#">Продолжить</a>
        </div>
        <h6 class="close-oneOfPopUp">&#10006</h6>
    </div>
</div><!-- end one of popup -->
<div class="popUpLogin oneOfPopUp" style="display: none;">
    <h2>Вход</h2>
    <form id="log_form" method="POST" action="javascript:void(0)" class="form">
        <div>
            <p>Логин</p>
            <input type="login" name="log" id="checkLogin" placeholder="Введите Ваш логин">
        </div>
        <div>
            <p>Пароль</p>
            <input type="login" name="pwd" id="checkPas" placeholder="Введите Ваш пароль">
            <input type="hidden" name="action" value="logi">
            <input type="hidden" name="rememberme" value="1">
        </div>
        <div class="but-bot">
            <button type="button" id="log_btn" class="but-red checkLogPas">Вход</button>
            <button class="but-white open-register">Регистрация</button>
        </div>
    </form>
    <h6 class="close-oneOfPopUp">&#10006</h6>
</div><!-- end login popup -->
<div class="popUp-registration oneOfPopUp" style="display: none;">
    <div class="top-div">
        <div class="phis">
            <div class="phis-img">
                <?= get_template_part('svg.php'); ?>
            </div>
            <p>Физическое лицо</p>
        </div><!-- end phiz -->
        <div class="entity active-reg">
            <div class="entity-img">
                <?= get_template_part('entity_svg.php'); ?>
            </div>
            <p>Юридическое лицо</p>
        </div><!-- end entity active reg -->
    </div><!-- end top -->
    <form class="phis-form some-form" style="display: block;" id="phiz_reg_form" action="javascript:void(0)" method="POST">
        <h2>Регистрация</h2>
        <h3>Отправьте менеджеру заявку на регистрацию</h3>
        <div>
            <p class="p-text">Имя</p>
            <input type="login" name="name" placeholder="Введите Ваше имя">
        </div>
        <div>
            <p class="p-text">E-mail</p>
            <input type="email" name="email" placeholder="Введите Вашу почту">
        </div>
        <div>
            <p class="p-text">Телефон</p>
            <input type="text" name="phone" id="phone_phiz" class="form-control">
        </div>
        <div>
            <p class="p-text">Комментарий</p>
            <textarea name="message" placeholder="Введите Ваш коментарий"></textarea>
            <input type="hidden" name="action" value="regentity">
        </div>
        <div class="politic">
            <p>Отправляя свои данные, вы соглашаетесь с </br>
                <span class="open-pop-Up">Политикой конфиденциальности</span>
            </p>
        </div>
        <div class="but-bot">
            <button class="but-white open-login">Вход</button>
            <button type="button" class="but-red" id="phiz_reg_btn">Регистрация</button>
        </div>
    </form>
    <form class="entity-form some-form" style="display: none;" id="entity_reg_form" action="javascript:void(0)" method="POST">
        <h2>Регистрация</h2>
        <h3>Отправьте менеджеру заявку на регистрацию</h3>
        <div>
            <p class="p-text">E-mail</p>
            <input type="email" name="email" placeholder="Введите Вашу почту">
        </div>
        <div>
            <p class="p-text">Пароль</p>
            <input type="password" name="password" placeholder="Введите пароль">
        </div>
        <div>
            <p class="p-text">Подтвердите Пароль</p>
            <input type="password" name="password2" placeholder="Подтвердите пароль">
            <input type="hidden" name="action" value="regphiz">
        </div>
        <div class="politic">
            <p>Отправляя свои данные, вы соглашаетесь с </br>
                <span class="open-pop-Up">Политикой конфиденциальности</span>
            </p>
        </div>
        <div class="but-bot">
            <button class="but-white open-login">Вход</button>
            <button type="button" id="entity_reg_btn" class="but-red">Регистрация</button>
        </div>
    </form>
    <h6 class="close-oneOfPopUp">&#10006</h6>
</div><!-- end registration popup -->
<div class="popUpThank oneOfPopUp" style="display: none;">
    <h4 class="headline-popUp">Регистрация</h4>
    <p>Спасибо за заявку наш менеджер с вами свяжется</p>
    <button class="but-red close-oneOfPopUp">ОК</button>
    <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
</div>
