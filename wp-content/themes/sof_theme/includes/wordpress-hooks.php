<?php
/**
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: Nemirovskiy Vitaliy
 * Author e-mail: nemirovskiyvitaliy@gmail.com
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */

/**
 * Отключение автоматического обарачивание в тег абзаца для котента и сокращения статьи
 */
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');

/**
 * Подключаем шорткоды в текстовый виджет
 */
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'do_shortcode' );