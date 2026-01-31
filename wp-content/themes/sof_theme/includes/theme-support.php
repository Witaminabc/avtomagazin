<?php
/**
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: Факеев Станислав
 * Author e-mail: stas-fakeyev@ya.ru
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */

// Добавлена поддержка изображений в записях и страницах
add_theme_support( 'post-thumbnails', array( 'post', 'page' ,'product', 'delivery') );
add_image_size( 'news-thumb', 300, 140, true );

// Добавлена поддержка меню
add_theme_support('menus');

// Добавляем поддержку разметки HTML5
add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

// Добавляем поддержу логотипа
add_theme_support( 'custom-logo' );

// Добавляем поддержу управления заголовком сайта
add_theme_support( 'title-tag' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'do_shortcode' );
//c add_filter( 'send_email_change_email', '__return_false' );
/**
 * Добавляем кастомные поля в настройки темы
 */
function custom_customize_register( $wp_customize ){
	//email
	    $wp_customize->add_setting('email', array(
        'default' => ''
    ));

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'email', array(
        'label' => 'email менеджера:',
        'section' => 'title_tagline',
        'settings' => 'email',
        'type' => 'text',
    )));

//телефон
	    $wp_customize->add_setting('phone_manager', array(
        'default' => ''
    ));

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'phone_manager', array(
        'label' => 'Телефон менеджера:',
        'section' => 'title_tagline',
        'settings' => 'phone_manager',
        'type' => 'text',
    )));
//адрес
	    $wp_customize->add_setting('location', array(
        'default' => ''
    ));

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'location', array(
        'label' => 'Адрес:',
        'section' => 'title_tagline',
        'settings' => 'location',
        'type' => 'text',
    )));
//пн-пт
	    $wp_customize->add_setting('pn-pt', array(
        'default' => ''
    ));

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'pn-pt', array(
        'label' => 'График работы пн-пт:',
        'section' => 'title_tagline',
        'settings' => 'pn-pt',
        'type' => 'text',
    )));
//сб вс
	    $wp_customize->add_setting('sb-vs', array(
        'default' => ''
    ));

    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sb-vs', array(
        'label' => 'График работы сб-вс:',
        'section' => 'title_tagline',
        'settings' => 'sb-vs',
        'type' => 'text',
    )));

} //end
add_action( 'customize_register', 'custom_customize_register' );
//страници опций
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
    'page_title'  => 'Соц-сети',
    'menu_title'  => 'Соц-сети',
    'menu_slug'   => 'socialnetworks-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));

} //end
/**
 * Добавляем поддержку WooCommerce
 */
function customtheme_add_woocommerce_support()
{
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'customtheme_add_woocommerce_support' );
