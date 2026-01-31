<?php
/**
 * Template Name: Sale
 * Template Post Type: post
 * Сompany: Plemya Studio
 * Сompany URI: http://plemyastudio.ru
 * Author: Stanislav Fakeyev
 * Author e-mail: stas-fakeyev@ya.ru
 * License: Copyrighted Commercial Software
 * Version: 3.0.0
 */
get_header(); ?>
<main>
        <div class="top-sale">
            <div class="container">
                <div class="crumbs">
                    <ul>
                        <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                        <li><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo get_the_title(); ?></a></li>
                    </ul>
                </div>
            </div><!-- end container -->
		</div><!-- end top sale -->
		        <div class="headline-sale">
            <div class="container">
                <h1><?php echo get_the_title(); ?></h1>
            </div>
				</div><!-- end headline sale -->
				        <div class="container">
            <!-- <h4 id="sale_title" data-sale="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></h4> -->
            <div class="info-sale">
                <div class="left-block">
<?php the_post(); the_content(); ?>
</div><!-- end left block -->
                <div class="right-block">
<?php the_field('right_text_sale'); ?>
				</div><!-- end right block -->
</div><!-- end info sale -->
            <div class="two-but">
                <a class="but-white open-sale-popUp">Подробнее</a>
                <a href="<?php echo get_permalink(get_field('product_sale')); ?>" class="but-red">Заказать</a>
			</div><!-- end two but -->
						</div><!-- end container -->
</main>
<?php get_footer(); ?>