<?php
/**
 * Template Name: News
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
	        <div class="news">
            <div class="container">
                <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                    <li><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo get_the_title(get_the_ID()); ?></a></li>
                </ul>
            </div>
			                <h2 class="main-headline">Новости</h2>
                <div class="some-news">
                    <div class="left-news">
                        <h3><?php echo get_the_title(); ?></h3>
                        <span><?php echo get_the_date('d.m.Y'); ?></span>
<?php the_post(); the_content(); ?>
</div><!-- end left block -->
                    <div class="right-news">
					<img src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'news-thumb' ); ?>" alt="<?php echo get_the_title(); ?>" />
					<?php the_field('text_news'); ?>
					                        <a href="<?php echo home_url('/'); ?>">Назад</a>
					</div><!-- end right block -->
</div><!-- end some news -->
                <div class="any-news">
                    <h2 class="main-headline">Другие новости</h2>
                    <div class="some-any-news">
						 				<?php
				    $QueryArgs = array(
        'post_type' => 'post',
        'cat'  => 26,
		'post__not_in' => array(get_the_ID()),
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'orderby'   => 'date',
        'order' => 'DESC',
    );
    $pc = new WP_Query($QueryArgs);
	if($pc->have_posts()){
    while ($pc->have_posts()) {
        $pc->the_post();
		?>
		                        <div class="news">
								                            <div class="someImg">
                            <img src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'news-thumb' ); ?>" alt="<?php echo get_the_title(); ?>">
							</div>
                            <div>
                                <span><?php echo get_the_date("d.m.Y"); ?></span>
                                <h5><?php echo get_the_title(); ?></h5>
                                <a href="<?php echo get_the_permalink(); ?>">Подробнее</a>
                            </div>
                        </div>
		<?php
	}
	wp_reset_postdata();
	} //endif
	?>
					</div><!-- end some any news -->
					</div><!-- end any news -->
        </div><!-- end container -->
		</div><!-- end news -->
</main>
<?php get_footer(); ?>
