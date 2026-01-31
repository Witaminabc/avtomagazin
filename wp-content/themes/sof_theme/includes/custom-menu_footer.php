<?php

function custom_menu_catalog($name){
    wp_nav_menu( array(
        'theme_location'  => '',
        'menu'            => 'Меню Каталог в подвале',
        'container'       => '',
        'container_class' => '',
        'container_id'    => '',
        'menu_class'      => '',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => '',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<div class="catalog">%3$s</div>',
        'depth'           => 0,
						'walker' => new Custom_Walker_Nav_Menu_Footer()

    ) );
}
function custom_menu_pages($name){
    wp_nav_menu( array(
        'theme_location'  => '',
        'menu'            => 'Меню Страницы в подвале',
        'container'       => '',
        'container_class' => '',
        'container_id'    => '',
        'menu_class'      => '',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => '',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<div class="navigation">%3$s</div>',
        'depth'           => 0,
						'walker' => new Custom_Walker_Nav_Menu_Footer()

    ) );
}

class Custom_Walker_Nav_Menu_Footer  extends Walker_Nav_Menu {
	
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
	$output.='<a href="'.$item->url.'">'.$item->title.'</a>';
	} //endfunction
	public function end_el(&$output, $item, $depth = 0, $args = array()) {

		} //endmethod

	public function start_lvl(&$output, $depth = 0, $args = array()) {

	}
	
	public function end_lvl(&$output, $depth = 0, $args = array()) {
	}
	

} //endclass