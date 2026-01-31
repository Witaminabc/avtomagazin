<?php
/**
 * Template name:  about
 * Template Post Type: page
 */
 get_header();
?>
<main>
        <div class="all-about">
        <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                    <li><a href="<?php echo get_permalink(get_the_ID()); ?>">О нас</a></li>
                </ul>
            </div>
			            <h2 class="main-headline"><?php the_title(); ?></h2>
						                <div class="about-us">
                    <div class="left-about">
<?php the_post(); the_content(); ?>
                    </div>
                    <div class="right-about">
                        <img src="<?php $image = get_field('right_image_about'); echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
<?php the_field('right_text_about'); ?>
                    </div>
                </div>
                <div class="pickup-points">
                    <h3><?php the_field('right_title_about'); ?></h3>
                    <div id="map-pickup">
                    </div>
                </div>
            </div>
        </div>
	</main>
						<?php
						get_footer();
?>
