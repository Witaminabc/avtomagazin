<?php

function custom_menu($name){
    wp_nav_menu( array(
        'theme_location'  => '',
        'menu'            => $name,
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
        'items_wrap'      => '<ul class="navbar-nav mr-auto">%3$s</ul>',
        'depth'           => 0,
						'walker' => new Custom_Walker_Nav_Menu()

    ) );
}
class Custom_Walker_Nav_Menu  extends Walker_Nav_Menu {
	
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
				$html = "";
		if($depth == 0) {
			if($item->url != '#'){
											$output .= '<li class="nav-item dropdown" onclick="document.location.href=';
											$output.="'";
											$output.= $item->url;
											$output.="'";
											$output.='">';
			}
			else{
						$output .= '<li class="nav-item dropdown">';
			}
$output.='<a class="nav-link dropdown-toggle" href="'.$item->url.'" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$item->title.'</a>';
//c			$output .= sprintf($html, $item->url, $item->title);
			
		} //end if depth 0
		elseif($depth == 1) {
$output.='<a class="dropdown-item" href="'.$item->url.'">'.$item->title.'</a>';
		} //endelseif depth 1
	} //endfunction
	public function end_el(&$output, $item, $depth = 0, $args = array()) {

		if($depth == 0) {
			$output .= "</li>";
		} //endif
		} //endmethod

	public function start_lvl(&$output, $depth = 0, $args = array()) {
		if($depth == 0) {
			$output.='<div class="dropdown-menu" aria-labelledby="navbarDropdown">';

		}	
	}
	
	public function end_lvl(&$output, $depth = 0, $args = array()) {
		if($depth == 0) {
			$output.='</div>';
		}
	}
	

} //endclass