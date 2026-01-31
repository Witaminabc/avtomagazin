<?php
/**
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: fakeyev stanislav
 * Author e-mail: stas-fakeyev@ya.ru
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */

// Добавление позиция для виджетов в админ панели
add_action( 'widgets_init', 'master_widgets_init' );

function master_widgets_init() {
    $positions = array(
        "filters"     => esc_html__("Умный фильтр", "master-theme-blog"),
        "politic"     => esc_html__("Политика конфиденциальности", "master-theme-blog"),

    );

    foreach ( $positions as $name => $desc ) {
        register_sidebar( array(
            'name'          => $name,
            'id'            => $name,
            'description'   => $desc,
            'before_widget' => '<!--widget-%1$s<%2$s>-->',
            'after_widget'  => '<!--widget-end-->',
            'before_title'  => '<!--title-start-->',
            'after_title'   => '<!--title-end-->',
        ));
    }
}