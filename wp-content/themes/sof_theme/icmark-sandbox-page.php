<?php
/**
 * Template name:  icmark sandbox
 * Template Post Type: page
 */
get_header();
?>
<?php
//$pickup = 'Россия, Свердловская область, Екатеринбург, микрорайон Юго-Западный, улица Академика Бардина, 4';
//$pickupByOrderPickup = array(
//    'post_type' => 'pickup',
//    'posts_per_page' => -1,
//    'post_status' => 'publish',
//    'orderby'        => 'date',
//    'order' => 'ASC',
//    'title' => $pickup,
//);
//$str = 'test';
//$pickupByOrderPickupQuery = new WP_Query($pickupByOrderPickup);
//if($pickupByOrderPickupQuery->have_posts()) {
//    while ($pickupByOrderPickupQuery->have_posts()) {
//        $pickupByOrderPickupQuery->the_post();
//        $shopManagerId = get_post_meta(get_the_ID(), 'magazin', true);
//        $shopManager = get_user_by( 'ID', $shopManagerId );
//        if(!empty($shopManager->user_email)) {
//            sendMail($str,$shopManager->user_email,'Новый заказ');
//        }
//    }
//}
?>

<?php
get_footer();

?>
