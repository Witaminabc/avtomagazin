<?php
get_header(); ?>
<?php
	    if( is_home() or is_front_page() ) : ?>
			<?php confirm_email(); ?>
    <main>
        <div class="stamps">
            <div class="container">
                <h2 class="main-headline">Подбор по авто</h2>
				<section id="all_cars">
					<div class="all-stamps">
						<?php
							$c = get_posts(
								array(
									'numberposts' => -1,
									'post_type'   => 'cars',
									'orderby'     => 'date',
									'order'       => 'ASC',
								)
                            );
                            

                            $count = count($c);

                            $new_title = [];
                            $new_title_id = [];

                            $i = 0;
                            foreach($c as $k_c => $v_c){
                                if (in_array($v_c->post_title, $new_title)) {
                                    
                                } else {

                                    $new_title_id[$v_c->ID] = $v_c->ID;
                                    $new_title[$i] = $v_c->post_title;
                                    $i++;
                                }
                            }

							$QueryArgs = array(
								'post_type' => 'cars',
                                'posts_per_page' => -1,
                                'post__in'  => $new_title_id,
                                'include' => 'LADA, TOYOTA, HYUNDAI, NISSAN, KIA, RENAULT, VOLKSWAGEN, CHEVROLET, FORD, MITSUBISHI',
								'post_status' => 'publish',
								'orderby' => 'post__in',
								'order' => 'ASC',
							);
							$pc = new WP_Query($QueryArgs);
							if($pc->have_posts()){
								while ($pc->have_posts()) {
                                    $pc->the_post();
				
						?>
										<a href="/product-category/фильтры-3/?car=<?php //the_title(); ?>" class="stamp">
											<img src="<?php //$image = get_field('photo'); echo $image['url']; ?>" alt="<?php //echo $image['alt']; ?>">
											<p><?php //the_title(); ?></p>
										</a>
						<?php
								} //endwhile
								$pc->reset_postdata();
							} //endif
						?>
						<a href="/product-category/фильтры-3/?car=LADA" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/lada.jpg" alt="">
							<p>LADA</p>
						</a>
						<a href="/product-category/фильтры-3/?car=TOYOTA" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/toyota.jpg" alt="">
							<p>TOYOTA</p>
						</a>
						<a href="/product-category/фильтры-3/?car=HYUNDAI" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/hyundai.jpg" alt="">
							<p>HYUNDAI</p>
						</a>
						<a href="/product-category/фильтры-3/?car=NISSAN" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/nissan.jpg" alt="">
							<p>NISSAN</p>
						</a>
						<a href="/product-category/фильтры-3/?car=KIA" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/kia.jpg" alt="">
							<p>KIA</p>
						</a>
						<a href="/product-category/фильтры-3/?car=RENAULT" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/renault.jpg" alt="">
							<p>RENAULT</p>
						</a>
						<a href="/product-category/фильтры-3/?car=VOLKSWAGEN" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/volkswagen.jpg" alt="">
							<p>VOLKSWAGEN</p>
						</a>
						<a href="/product-category/фильтры-3/?car=CHEVROLET" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/chevrolet.jpg" alt="">
							<p>CHEVROLET</p>
						</a>
						<a href="/product-category/фильтры-3/?car=FORD" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/ford.jpg" alt="">
							<p>FORD</p>
						</a>
						<!-- <a href="/product-category/фильтры-3/?car=MITSUBISHI" class="stamp">
							<img src="https://xn-----8kcfbcq1fuaq.xn--p1ai/wp-content/uploads/2020/10/mitsubishi.jpg" alt="">
							<p>MITSUBISHI</p>
						</a> -->
					</div>
					<?php
						if($count > 10){
							echo'<a href="javascript:void(0)" data-count = "'.serialize($new_title_id).'" data-car="'.count($new_title_id).'" class="but-red">Больше авто</a>';
						} else {
							echo'<a href="javascript:void(0)" data-count = "'.serialize($new_title_id).'" data-car="'.count($new_title_id).'" class="but-red">Больше авто</a>';
						}
					?>
				</section>
            </div>
        </div>
		<div class="spore">
            <div class="container">
                <h2 class="main-headline">Подбор запчасти\масла</h2>
                <div class="row align-items-start">
					<?php
						$terms = get_terms(
							array(
								'taxonomy'     => 'product_cat',
								//'orderby'      => 'id',
								//'order'        => 'ASC',
								'hide_empty'   => 0,
								'exclude'      => '',
								'include'      => '385, 407, 1198, 415',
								'number'       => 4,
							)
						);
						if( $terms ){
							foreach( $terms as $cat ){
					?>
								<div class="col-md-6 col-sm-12 col-lg-3">
									<div class="some-spore">
										<div class="photo-oil">
											<?php 
												if(get_field('image_cat',  $cat->taxonomy.'_'.$cat->term_id)): 
											?>
													<img src="<?php the_field('image_cat',  $cat->taxonomy.'_'.$cat->term_id); ?>" alt="<?php echo $cat->name; ?>">
											<?php
												else:
											?>
													<img src="<?= get_template_directory_uri().'/img/spore_part_1.png'; ?>" alt="<?php echo $cat->name; ?>">
											<?php
												endif;
											?>
										</div>
										<h5 onclick="document.location.href='<?php echo esc_url( get_term_link( $cat ) ); ?>'"><?php echo $cat->name; ?></h5>
										<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">Подробнее</a>
									</div>
								</div>
					<?php
							}
						}
					?>
                </div>
                <div class="row align-items-end">
					<?php
						$terms = get_terms(
							array(
								'taxonomy'     => 'product_cat',
								//'orderby'      => 'id',
								//'order'        => 'ASC',
								'hide_empty'   => 0,
								'exclude'      => '',
								'include'      => '401, 411, 396, 371',
								'number'       => 4,
							)
						);
						if( $terms ){
							foreach( $terms as $cat ){
					?>
								<div class="col-md-6 col-sm-12 col-lg-3">
									<div class="some-spore">
										<div class="photo-oil">
											<?php 
												if(get_field('image_cat',  $cat->taxonomy.'_'.$cat->term_id)):
											?>
													<img src="<?php the_field('image_cat',  $cat->taxonomy.'_'.$cat->term_id); ?>" alt="<?php echo $cat->name; ?>">
											<?php
												else:
											?>
													<img src="<?= get_template_directory_uri().'/img/spore_part_1.png'; ?>" alt="<?php echo $cat->name; ?>">
											<?php
												endif;
											?>
										</div>
										<h5 onclick="document.location.href='<?php echo esc_url( get_term_link( $cat ) ); ?>'"><?php echo $cat->name; ?></h5>
										<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">Подробнее</a>
									</div>
								</div>
					<?php
							}
						}
					?>
                </div>
            </div>
        </div>
		<?php
			$QueryArgs = array(
				'post_type' => 'post',
				'cat'  => 41,
				'posts_per_page' => 4,
				'post_status' => 'publish',
				'orderby'   => 'date',
				'order' => 'DESC',
			);
			$pc = new WP_Query($QueryArgs);
			if($pc->have_posts()){
		?>
				<div class="all-sale">
					<div class="container">
						<div class="main-sale">
							<div class="all-some-sale">
								<?php
									while ($pc->have_posts()) {
										$pc->the_post();
								?>
										<div class="some-sale">
											<h2><?php echo get_the_title(); ?></h2>
											<!-- <p><?php echo get_the_excerpt(); ?></p> -->
											<a href="<?php echo get_the_permalink(); ?>" class="but-red">Подробнее</a>
										</div>
								<?php
									} //endwhile
									wp_reset_postdata();
								?>
							</div>
							<ul class="custom-arr">
								<li class="prev">&lsaquo;</li>
								<li class="next">&rsaquo;</li>
							</ul>
						</div>
					</div>
				</div>
		<?php
			} //endif
		?>
		<div class="about-us">
            <div class="container">
                <h2 class="main-headline">О нас</h2>
                <div class="all-about">
					<?php
						if( have_rows('about', 6) ): $qty = 0; reset_rows();
							while ( have_rows('about', 6) ) : the_row();
					?>
								<div class="num">
									<h5><?php the_sub_field('title_about'); ?></h5>
									<?php the_sub_field('text_about'); ?>
								</div>
					<?php
							endwhile;
						endif;
					?>
				</div>
				<h2 class="main-headline">Отзывы</h2>
			</div>
		</div>
		<div class="reviews">
            <div class="container-fluid">
                <div class="all-review">
					<?php
						if( have_rows('reviews', 6) ): $qty = 0; reset_rows();
							while ( have_rows('reviews', 6) ) : the_row();
					?>
								<div class="review">
									<img src="<?php the_sub_field('avatar_reviews'); ?>" alt="avatar">
									<h5><?php the_sub_field('name_reviews'); ?></h5>
									<p><?php the_sub_field('text_reviews'); ?></p>
								</div>
					<?php
							endwhile;
						endif;
					?>
				</div>
			</div>
		</div>
		<div class="news">
            <div class="container">
                <h2 class="main-headline">Новости</h2>
                <div class="all-news">
                    <div class="left-block">
						<?php
							$QueryArgs = array(
								'post_type' => 'post',
								'cat'  => 26,
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
									<div class="some-news">
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
							}
						?>
					</div>
					<div class="right-block">
						<?php
							$QueryArgs = array(
								'post_type' => 'post',
								'cat'  => 26,
								'posts_per_page' => 4,
								'post_status' => 'publish',
								'offset' => 4,
								'orderby'   => 'date',
								'order' => 'DESC',
							);
							$pc = new WP_Query($QueryArgs);
							if($pc->have_posts()){
								while ($pc->have_posts()) {
								$pc->the_post();
						?>
									<div class="some-news">
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
							}
						?>
					</div>
				</div>
			</div>
		</div>
	    <div class="main-map" id="main-map"></div>
	</main>
<?php

	elseif(is_product_category()):
		get_template_part( 'woocommerce-category' );
	elseif(is_product()):
		get_template_part( 'woocommerce-product' );
	endif;
	
	get_footer(); 

?>