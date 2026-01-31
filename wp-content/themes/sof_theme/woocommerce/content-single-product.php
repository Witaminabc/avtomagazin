<?php get_header();
$cat_id = woocommerce_category_data();
?>
    <main>
        <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                    <li><a href="<?php echo get_term_link($cat_id, 'product_cat'); ?>">Каталог</a></li>
                    <li><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo get_the_title(); ?></a></li>
                </ul>
            </div><!-- end crumbs -->
            <div class="main-info">
                <h2 class="main-headline"><?php echo get_the_title(); ?></h2>
                <div class="about-product">
                    <div class="gallery">
                        <?php
                        $_product = wc_get_product( get_the_ID() );
                        $attachment_ids = $_product->get_gallery_image_ids();
                        if(count($attachment_ids) > 0){
                            foreach( array_slice( $attachment_ids, 0,3 ) as $attachment_id ) {
                                $thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
                                echo'<img src="'.$thumbnail_url.'" alt="'.strip_tags(get_the_title()).'">';
                            } //endforeach
                        } //endif count
                        ?>
                    </div><!-- end gallery -->
                    <div class="description">
                        <?php the_post(); the_content(); ?>
                        <div class="sum">
                            <h2 id="price" data-price="<?php echo $_product->get_price(); ?>" data-quantidi="<?php echo $_product->get_stock_quantity(); ?>" data-lowstock="<?php echo $_product->get_low_stock_amount(); ?>"><?php echo $_product->get_price(); ?> р.</h2>
                            <div class="count-product">
                                <label for="count">Количество :</label>
                                <input type="number" class="pp-number" id="count" value="1">
                            </div>
                        </div><!-- end sum -->
                        <div class="buy-product">
                            <div class="button to-basket">
                                <a href="javascript:void(0);" data-product_add="<?php echo get_the_ID(); ?>"><img src="<?= get_template_directory_uri().'/img/red-basket.png'; ?>" alt="red" class="img-basc"><img src="<?= get_template_directory_uri().'/img/white-basket.png'; ?>" alt="white" class="img-basc">В корзину</a>
                            </div>
                            <div class="button-red buy-now">
                                <a href="javascript:void(0);" data-oneclick="<?php echo get_the_ID(); ?>" class="buy-one-click">Купить в 1 клик</a>
                            </div>
                        </div><!-- end buy product -->
                    </div><!-- end description -->
                </div><!-- end about product -->
            </div><!-- end main info -->
            <?php
            $crosssell_ids = get_post_meta( get_the_ID(), '_crosssell_ids' );
            $crosssell_ids=$crosssell_ids[0];
            if(count($crosssell_ids)>0){
                ?>
                <div class="similar-products">
                    <h2 class="main-headline">С этим товаром чаще всего выбирают</h2>
                    <div class="all-similar">
                        <?php
                        $pc = new WP_Query( array(
                            'post_type' => 'product',
                            'posts_per_page' => 4,
                            'post__in' => $crosssell_ids,
                            'orderby' => 'rand',
                        ));
                        if($pc->have_posts()){
                            while ($pc->have_posts()) {
                                $pc->the_post();
                                ?>
                                <div class="one-product-catalog">
                                    <div class="image" onclick="document.location.href='<?php echo get_the_permalink(); ?>'">
                                        <img src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>" alt="<?php echo strip_tags(get_the_title()); ?>">
                                    </div>
                                    <h3 onclick="document.location.href='<?php echo get_the_permalink(); ?>'"><?php echo get_the_title(); ?></h3>
                                    <div class="more-info">
                                        <p>подробнее</p>
                                        <?php
                                        $_product2 = wc_get_product( get_the_ID() );
                                        echo'<h3 id="cross_price'.get_the_ID().'" data-cross_price="'.$_product2->get_price().'">'.$_product2->get_price().' р.</h3>';
                                        ?>
                                    </div><!-- end more info -->
                                    <div class="buy-bask">
                                        <input type="number" class="pp-cross-number" id="cross_product<?php echo get_the_ID(); ?>" data-cross_product="<?php echo get_the_ID(); ?>" data-quantity="<?php echo $_product2->get_stock_quantity(); ?>" value="1">
                                        <div class="button">
                                            <a href="javascript:void(0);" data-cross_add="<?php echo get_the_ID(); ?>">																<img src="<?= get_template_directory_uri().'/img/red-basket.png'; ?>" alt="red" class="img-basc"><img src="<?= get_template_directory_uri().'/img/white-basket.png'; ?>" alt="white" class="img-basc">
                                                В корзину</a>
                                        </div>
                                    </div><!-- end buy bask -->
                                </div><!-- end one product -->

                                <?php
                            }
                            wp_reset_postdata();
                        } //endif
                        ?>
                    </div><!-- end all similar -->
                </div><!-- end similar -->
                <?php
            } //endif count crossseills
            ?>
        </div><!-- end container -->
    </main>
<?php get_footer(); ?>