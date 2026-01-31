    <footer>
        <div class="container">
            <div class="all-footer">
                <div class="logo">
                    <a href="<?php echo home_url('/'); ?>">
                        <h2>СПД-Групп</h2>
                    </a>
					                    <a href="javascript:void(0);" class="link open-pop-Up">
                        <p>Политика</p>
                        <p>конфиденциальности</p>
                    </a>
                </div>
				<?php custom_menu_catalog('Меню Каталог в подвале'); ?>
				<?php custom_menu_pages('Меню Страницы в подвале'); ?>
                <div class="contact">
                    <div class="social">
                        <a href="<?php the_field('vk', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/vk.png'; ?>" alt="VK"></a>
                        <a href="<?php the_field('instagram', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/instagram.png'; ?>" alt="Instagram"></a>
                    </div>
                    <div class="adress">
                        <h4><img src="<?= get_template_directory_uri().'/img/geo.png'; ?>" alt="Location">Адрес</h4>
                        <h5><?php echo get_theme_mod('location'); ?></h5>
                    </div>
                    <div class="phone">
                        <h4><img src="<?= get_template_directory_uri().'/img/phone.png'; ?>" alt="Phone">Телефон</h4>
                        <a href="tel:<?php echo get_theme_mod('phone_manager'); ?>"><?php echo get_theme_mod('phone_manager'); ?></a>
                    </div>
                    <div class="schedule">
                        <h4><img src="<?= get_template_directory_uri().'/img/clock.svg'; ?>" alt="График работы">График работы</h4>
                        <p>пн-пт <?php echo get_theme_mod('pn-pt'); ?></p>
                        <p>сб-вс <?php echo get_theme_mod('sb-vs'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
    if(document.getElementById("main-map")){
        var optinsMain = {
              address : '<?php echo get_field("addres_main",6);?>',
              phone : '<?php echo get_field("phone_main",6);?>',
              grafik: '<?php $str="";
            if( have_rows("grafik_main",6) ):
                        while( have_rows('grafik_main',6) ): the_row();
            $str.='<p>'.get_sub_field('days').'<span>'.get_sub_field('time').'</span></p>';
            endwhile;
            endif;
            echo $str;
            ?>',
              cords: JSON.parse('<?php echo json_encode(explode(",",get_field("cords",6)));?>')
            };
    }
</script>
   
                      <?php if(is_cart() || is_page_template('page-about.php') ){
                        $option_pickup = array();
                        $centr=explode(',',get_field('centr_coords',get_the_ID()));
                        
																	    $QueryPickup = array(
        'post_type' => 'pickup',
        'posts_per_page' => -1,
        'post_status' => 'publish',
'orderby'        => 'date',
        'order' => 'ASC',
    );
    $pickup = new WP_Query($QueryPickup);
	if($pickup->have_posts()){
    while ($pickup->have_posts()) {
        $pickup->the_post();
		$option_pickup[] = array('title' => get_the_title(),'coords'=>explode(',',get_field('coords')));
	} //endwhile
			    wp_reset_query();
	} //endif

                        ?>
                        <script>
var optionPickup=JSON.parse('<?php echo json_encode($option_pickup);?>');
var $centr=JSON.parse('<?php echo json_encode($centr);?>');
</script>
	<?php }
	if(is_product()): ?>
	    <div class="bg-black"></div>
    <div class="pop-up-attention oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Внимание</h4>
        <p>На данный момент этот товар можно заказать в количестве не более <b id="lowstock"></b> Для выяснения подробностей свяжитесь с Вашим менеджером.</p>
        <a href="javascript:void(0);" class="but-white close-oneOfPopUp">Продолжить</a>
    </div>
	<?php elseif(is_page_template('tpl-sale.php')): ?>
    <div class="bg-black"></div>
    <div class="pop-up-sale oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Узнать подробности</h4>
        <form id="call_form" class="some-form" method="POST" action="javascript:void(0)">
            <div>
                <p>Имя</p>
                <input type="text" name="name" placeholder="Введите Ваше имя" data-validate>
            </div>
            <div>
                <p>Телефон</p>
                <input type="number" name="tel" placeholder="+7 (____) ___-__-__" data-validate>
            </div>
            <div>
                <p>E-mail</p>
                <input type="email" name="email" placeholder="Введите Вашу почту" data-validate>
            </div>
            <div class="sale-but">
			<input type="hidden" name="action" value="call" />
			<input type="hidden" id="sale_name" name="sale" value="" />
                <input type="button" value="Отправить" id="call_btn" class="but-white">
            </div>
        </form>
		        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
	    <div class="pop-up-post-sale oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Благодарим Вас за заявку</h4>
        <p>В ближайшее время с Вами свяжется наш менеджер.</p>
        <button class="but-red close-thank-sale">ОК</button>
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
		<?php elseif(is_page_template('page-cart.php')): ?>
			    <div class="bg-black"></div>
    <div class="pop-up-attention oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Внимание</h4>
        <p>На данный момент этот товар можно заказать в количестве не более <b id="lowstock"></b> Для выяснения подробностей свяжитесь с Вашим менеджером.</p>
        <a href="javascript:void(0);" class="but-white close-oneOfPopUp">Продолжить</a>
    </div>
    <div class="secondQr popUpQr oneOfPopUp" style="display: none;">
<?php echo do_shortcode('[kaya_qrcode content="Заказы" ecclevel="M" align="alignnone" url="'.get_permalink(9).'"]'); ?>
<img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>

	<?php elseif(is_search()): ?>
		    <div class="bg-black"></div>
    <div class="pop-up-attention oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Внимание</h4>
        <p>На данный момент этот товар можно заказать в количестве не более <b id="lowstock"></b> Для выяснения подробностей свяжитесь с Вашим менеджером.</p>
        <a href="javascript:void(0);" class="but-white close-oneOfPopUp">Продолжить</a>
    </div>
	<?php elseif(is_product_category()): ?>
		    <div class="bg-black"></div>
    <div class="pop-up-attention oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Внимание</h4>
        <p>На данный момент этот товар можно заказать в количестве не более <b id="lowstock"></b> Для выяснения подробностей свяжитесь с Вашим менеджером.</p>
        <a href="javascript:void(0);" class="but-white close-oneOfPopUp">Продолжить</a>
    </div>
	<?php elseif(is_page_template('page-account.php')): ?>
    <div class="bg-black" style="display: none;"></div>
    <div class="successPas mainPop-up oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Обновление пароля</h4>
        <p>Ваш пароль успешно изменён.</p>
        <button class="but-red close-thank-sale">ОК</button>
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
    <div class="errorPas mainPop-up oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Обновление пароля</h4>
        <p>Ошибка данных!</p>
        <button class="but-red close-thank-sale">ОК</button>
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
        <div class="pickupSuccessRemove mainPop-up oneOfPopUp" style="display: none;">
            <h4 class="headline-popUp">Удаление адреса</h4>
            <p>Адрес успешно удалён.</p>
            <button class="but-red close-thank-sale">ОК</button>
            <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
        </div>
        <div class="pickupErrorRemove mainPop-up oneOfPopUp" style="display: none;">
            <h4 class="headline-popUp">Удаление адреса</h4>
            <p>Адрес не удалён. Ошибка данных!</p>
            <button class="but-red close-thank-sale">ОК</button>
            <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
        </div>
        <div class="pickupSuccessAdd mainPop-up oneOfPopUp" style="display: none;">
            <h4 class="headline-popUp">Добавление адреса</h4>
            <p>Адрес самовывоза успешно добавлен.</p>
            <button class="but-red close-thank-sale">ОК</button>
            <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
        </div>
        <div class="pickupErrorAdd mainPop-up oneOfPopUp" style="display: none;">
            <h4 class="headline-popUp">Добавление адреса</h4>
            <p>Адрес не добавлен. Проверьте выбранную точку.</p>
            <button class="but-red close-thank-sale">ОК</button>
            <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
        </div>

    <div class="errorEmail mainPop-up oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp">Обновление адреса электронной почты</h4>
        <p>На Ваш почтовый ящик отправлено сообщение, содержащее ссылку для подтверждения смены e-mail адреса. Пожалуйста, перейдите по ссылке для завершения.</p>
        <button class="but-red close-thank-sale">ОК</button>
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
    <div class="phisQr popUpQr oneOfPopUp" style="display: none;">
<?php echo do_shortcode('[kaya_qrcode content="Заказы" ecclevel="M" align="alignnone" url="'.get_permalink(9).'?stranica='.$_GET['stranica'].'"]'); ?>
<img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
    <div class="urQr popUpQr oneOfPopUp" style="display: none;">
<?php echo do_shortcode('[kaya_qrcode content="Заказы" ecclevel="M" align="alignnone" url="'.get_permalink(9).'?stranica='.$_GET['stranica'].'"]'); ?>
<img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>



<?php
endif;
	?>
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
                <a class="close-oneOfPopUp" href="javascript:void(0);">Продолжить</a>
            </div>
            <h6 class="close-oneOfPopUp">&#10006</h6>
        </div>
    </div>
    <div class="universal-popup mainPop-up oneOfPopUp" style="display: none;">
        <h4 class="headline-popUp"></h4>
        <p></p>
        <button class="but-red close-thank-sale">ОК</button>
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
    <div class="universalQR popUpQr oneOfPopUp" style="display: none;">
        <img src="" class="shortcode">
        <img src="<?= get_template_directory_uri().'/img/x.png'; ?>" alt="Close" class="close-oneOfPopUp">
    </div>
<?php wp_footer(); ?>
</body>
	<script src="<?= get_template_directory_uri().'/js/select2/select2.min.js'; ?>"></script>
	<link rel="stylesheet" href="<?= get_template_directory_uri().'/js/select2/select2.min.css'?>">
    <link rel="stylesheet" href="<?= get_template_directory_uri().'/css/icmark.css'; ?>">
    <script src="<?= get_template_directory_uri().'/js/icmark.js'; ?>"></script>
    <script src="<?= get_template_directory_uri().'/js/datepicker/datepicker.js'; ?>"></script>
    <link rel="stylesheet" href="<?= get_template_directory_uri().'/js/datepicker/datepicker.min.css'?>">
</html>
