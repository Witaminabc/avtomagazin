<?php
// Подключение файла с необходимыми стилями и скриптами для темы
get_template_part( 'includes/enqueue', 'scripts' );
// Подключение файла с перечнем того что поддерживает тема
get_template_part( 'includes/theme', 'support' );
//подключение кастомных функций
get_template_part( 'includes/custom', 'functions' );

//подключение кастомного меню
get_template_part( 'includes/custom', 'menu' );
get_template_part( 'includes/custom', 'menu_footer' );
//подключение виджетов
get_template_part( 'includes/widgets', 'init' );

//Доп. функции в личном кабинете
get_template_part( 'includes/custom', 'account_page' );
//add_filter('wp_image_editors', 'change_graphic_lib');
//function change_graphic_lib($array){
//	
//return array('WP_Image_Editor_GD', 'WP_Image_Editor_Imagick');
//}
?>
