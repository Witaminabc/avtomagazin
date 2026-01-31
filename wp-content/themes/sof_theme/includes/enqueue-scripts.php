<?php
/**
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: Fakeyev Stanislav
 * Author e-mail: stas-fakeyev@ya.ru
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */
//подключение скриптов
function load_style_script(){
			wp_deregister_script('jquery-core');
		wp_deregister_script('jquery');
		// регистрируем

		    if( is_home() or is_front_page() ) {
	wp_enqueue_script('map-js', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full', array(), '1.1.0', false);
	wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
		wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);

		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);
		
			} //endif

	  
				wp_enqueue_style('index-css', get_template_directory_uri() . '/css/index.css', '', '5.2.2', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
		
				if(is_page_template('tpl-news.php')){
				wp_enqueue_style('news-css', get_template_directory_uri() . '/css/news.css', '', '5.2.2', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);

		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

				} //endif
								if(is_page_template('page-contacts.php')){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('contacts-css', get_template_directory_uri() . '/css/contact.css', '', '5.2.2', 'screen');
					wp_enqueue_script('map-js', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full', array(), true);
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
					wp_enqueue_script('code-jquery-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

								} //endif
																if(is_page_template('page-about.php')){
				wp_enqueue_style('about-us-css', get_template_directory_uri() . '/css/about-us.css', '', '5.2.2', 'screen');
					wp_enqueue_script('map-js', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full', array(), '1.1.0', false);
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
			wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

								} //endif
if(is_product_category()){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('catalog-css', get_template_directory_uri() . '/css/catalog.css', '', '5.2.7', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
																								wp_enqueue_style('atention-css', get_template_directory_uri() . '/css/popUp-attention.css', '', '5.2.2', 'all');
																								wp_enqueue_style('style-css', get_template_directory_uri() . '/css/style.css', '', '5.2.2', 'all');

									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
		wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('rawgit-js', 'https://cdn.rawgit.com/prashantchaudhary/ddslick/master/jquery.ddslick.min.js', array(), '1.1.0', true);
	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
			wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
    wp_enqueue_script('dd_slick-js', get_template_directory_uri() . '/js/resolve_problem/dd_slick.js', array(), '1.1.0', true);
													wp_enqueue_script('counter-js', get_template_directory_uri() . '/js/counter.js', array(), '1.1.0', true);

//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

} //endif
if(is_product()){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('card-of-product-css', get_template_directory_uri() . '/css/card-of-product.css', '', '5.2.4', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
																wp_enqueue_style('atention-css', get_template_directory_uri() . '/css/popUp-attention.css', '', '5.2.2', 'all');

					wp_enqueue_script('code-jquery-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

											wp_enqueue_script('counter-card-js', get_template_directory_uri() . '/js/counter-card.js', array(), '1.1.0', true);

										wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);
} //endif
																if(is_page_template('tpl-sale.php')){
				wp_enqueue_style('sale-css', get_template_directory_uri() . '/css/sale.css', '', '5.2.2', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('call-js', get_template_directory_uri() . '/js/call.js', array(), '1.1.0', true);

	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
			wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

																} //endif
																if(is_search()){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('result-of-search-css', get_template_directory_uri() . '/css/result-of-search.css', '', '5.2.2', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
																								wp_enqueue_style('atention-css', get_template_directory_uri() . '/css/popUp-attention.css', '', '5.2.2', 'all');

					wp_enqueue_script('code-jquery-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

											wp_enqueue_script('handler-search-js', get_template_directory_uri() . '/js/handler_search.js', array(), '1.1.0', true);

										wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);
																} //endif
																if(is_cart()){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('basket-css', get_template_directory_uri() . '/css/basket.css', '', '5.2.2', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
																wp_enqueue_style('popup-atention-css', get_template_directory_uri() . '/css/popUp-attention.css', '', '5.2.2', 'all');

					wp_enqueue_script('map-js', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full', array(), '1.1.0', false);
					wp_enqueue_script('code-jquery-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('rawgit-js', 'https://cdn.rawgit.com/prashantchaudhary/ddslick/master/jquery.ddslick.min.js', array(), '1.1.0', true);
										wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
                                                                    wp_enqueue_script('dd_slick-js', get_template_directory_uri() . '/js/resolve_problem/dd_slick.js', array(), '1.1.0', true);
												wp_enqueue_script('handler-basket-js', get_template_directory_uri() . '/js/handler_basket.js', array(), '1.1.0', true);
	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

																} //endif
																																if(is_page_template('page-account.php')){
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
								wp_enqueue_style('mCustomScrollbar-css', get_template_directory_uri() . '/css/jquery.mCustomScrollbar.css', '', '5.2.2', 'all');
								wp_enqueue_style('personal-area-css', get_template_directory_uri() . '/css/personal-area.css', '', '5.2.2', 'all');

									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
											wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

																					wp_enqueue_script('handler-account-js', get_template_directory_uri() . '/js/handler_account.js', array(), '1.1.0', true);
													wp_enqueue_script('code-jquery-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);

										wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
		wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
										wp_enqueue_script('mCustomScrollbar-js', get_template_directory_uri() . '/js/jquery.mCustomScrollbar.concat.min.js', array(), '1.1.0', true);
wp_enqueue_script('mCustomScrollbar-account-js', get_template_directory_uri() . '/js/resolve_problem/mCustomScrollbar.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

																																} //endif
																																if(is_page_template('page-catalog.php')){
				wp_enqueue_style('bootstrap-lib-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', '', '5.2.2', 'screen');
				wp_enqueue_style('catalog-css', get_template_directory_uri() . '/css/catalog.css', '', '5.2.7', 'screen');
								wp_enqueue_style('slick-css', get_template_directory_uri() . '/css/slick.css', '', '5.2.2', 'all');
																								wp_enqueue_style('atention-css', get_template_directory_uri() . '/css/popUp-attention.css', '', '5.2.2', 'all');
																								wp_enqueue_style('style-css', get_template_directory_uri() . '/css/style.css', '', '5.2.2', 'all');

									wp_enqueue_script('jquery-lib', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.1.0', true);
		wp_enqueue_script('jquery-mask', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array(), '1.1.0', true);
				wp_enqueue_script('theme-js', get_template_directory_uri() . '/js/theme.js', array(), '1.1.0', true);

	wp_enqueue_script('rawgit-js', 'https://cdn.rawgit.com/prashantchaudhary/ddslick/master/jquery.ddslick.min.js', array(), '1.1.0', true);
	wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array(), '1.1.0', true);
			wp_enqueue_script('cloudflare-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.1.0', true);
	wp_enqueue_script('bootstraplib-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '1.1.0', true);
		wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.min.js', array(), '1.1.0', true);
       wp_enqueue_script('dd_slick-js', get_template_directory_uri() . '/js/resolve_problem/dd_slick.js', array(), '1.1.0', true);                                                                                                                            
													wp_enqueue_script('counter-js', get_template_directory_uri() . '/js/counter.js', array(), '1.1.0', true);

//	wp_enqueue_script('script-js', get_template_directory_uri() . '/js/script.min.js', array(), '1.1.0', true);

} //endif

} //endfunction
/**
* загружаем скрипты и стили
*/
add_action('wp_enqueue_scripts', 'load_style_script');
//отключить стили woocommerce
add_filter('woocommerce_enqueue_styles', '__return_false');

?>