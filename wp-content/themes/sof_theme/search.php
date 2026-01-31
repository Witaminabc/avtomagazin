<?php
get_header();
?>
<main>
        <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_home_url(); ?>">Главная</a></li>
                    <li><a href="<?php echo get_home_url(); ?>?s=<?php echo $s; ?>">Результаты поиска</a></li>
                </ul>
            </div>
            <form class="search-string" method="GET" action="<?php echo get_home_url(); ?>">
                <input type="search" name="s" value="<?php echo $s; ?>">
                <span class="close" onclick="this.previousElementSibling.value = ''">
                    X</span>
                <img src="<?= get_template_directory_uri().'/img/search-img.png'; ?>" alt="Search">
            </form>
<?php
		$pc = new WP_Query( array(
'post_type' => 'product',
'posts_per_page' => -1,
		's' => $s,
		'sentence' => true,
            'meta_query' => array(
                        array(
                            'key' => '_stock_status',
                            'value' => 'instock'
                        )
                    ),
'orderby' => 'date',
'order' => 'DESC',
));
	if($pc->have_posts()){
		?>
		            <div class="all-results">
<?php
    while ($pc->have_posts()) {
        $pc->the_post();
		?>
                    <div class="one-product-catalog">
                        <div class="image" onclick="document.location.href='<?php echo get_the_permalink(); ?>'">
						                                <img src="<?php
                                            if(get_the_post_thumbnail_url( get_the_ID() )){
                                   echo get_the_post_thumbnail_url( get_the_ID(), 'full' );
                                            } else{
                                        $img = get_field( "img_default", 6 );
                                    echo $img['url'];
                                            } ?>" alt="<?php echo strip_tags(get_the_title()); ?>" alt="<?php echo strip_tags(get_the_title()); ?>">
                        </div>
                            <h3 onclick="document.location.href='<?php echo get_the_permalink(); ?>'"><?php echo get_the_title(); ?></h3>
                            <div class="more-info">
                                <p>подробнее</p>
								<?php
$_product = wc_get_product( get_the_ID() );
echo'<h3 id="cross_price'.get_the_ID().'" data-cross_price="'.$_product->get_price().'">'.$_product->get_price().' р.</h3>';
?>
</div><!-- end more info -->
                            <div class="buy-bask">
                                <input type="number" class="pp-cross-number" id="cross_product<?php echo get_the_ID(); ?>" data-cross_product="<?php echo get_the_ID(); ?>" data-quantity="<?php echo $_product->get_stock_quantity(); ?>" value="1">
                                <div class="button">
								                                <a href="javascript:void(0)" data-cross_add="<?php echo get_the_ID(); ?>">																<img src="<?= get_template_directory_uri().'/img/red-basket.png'; ?>" alt="в корзину">В корзину</a>
                                </div>
								</div><!-- end buy bask -->
														</div><!-- end one product -->
						
												<?php
	}
	wp_reset_postdata();
	?>
	</div><!-- end all results -->
	<?php
	} //endif
	?>

		</div><!-- end container -->
</main>
<?php
get_footer();
?>


