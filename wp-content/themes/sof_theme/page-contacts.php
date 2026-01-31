<?php
/**
 * Template name:  Contacts
 * Template Post Type: page
 */
 get_header();
?>
<main>
        <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                    <li><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo get_the_title(get_the_ID()); ?></a></li>
                </ul>
            </div>
			            <h2 class="main-headline"><?php the_title(); ?></h2>
            <h3><?php the_field('title_ofice1'); ?></h3>
            <div class="office">
                <div class="left-info">
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/geo.png'; ?>" alt="адрес">Адрес:</h4>
                        <p><?php the_field('location_ofice1'); ?></p>
                    </div>
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/phone.png'; ?>" alt="телефон">Телефон:</h4>
                        <a href="tel:<?php the_field('phone_ofice1'); ?>"><?php the_field('phone_ofice1'); ?></a>
                    </div>
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/002-clock.png'; ?>">График работы:</h4>
                        <div class="schedule">
						<?php the_field('shedule_ofice1'); ?>
                        </div>
                    </div>
                    <div>
                        <h4>Соц. сети:</h4>
                        <div class="social">
                            <a href="<?php the_field('vk', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/vk.png'; ?>" alt="VK"></a>
                            <a href="<?php the_field('instagram', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/instagram.png'; ?>" alt="Instagram"></a>
                        </div>
                    </div>
                </div>
                <div id="map-office">
                </div>
            </div>
            <h3><?php the_field('title_ofice2'); ?></h3>
            <div class="office">
                <div class="left-info">
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/geo.png'; ?>" alt="адрес">Адрес:</h4>
                        <p><?php the_field('location_ofice2'); ?></p>
                    </div>
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/phone.png'; ?>" alt="телефон">Телефон:</h4>
                        <a href="tel:<?php the_field('phone_ofice2'); ?>"><?php the_field('phone_ofice1'); ?></a>
                    </div>
                    <div>
                        <h4> <img src="<?= get_template_directory_uri().'/img/002-clock.png'; ?>">График работы:</h4>
                        <div class="schedule">
						<?php the_field('shedule_ofice2'); ?>
                        </div>
                    </div>
                    <div>
                        <h4>Соц. сети:</h4>
                        <div class="social">
                            <a href="<?php the_field('vk', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/vk.png'; ?>" alt="VK"></a>
                            <a href="<?php the_field('instagram', 'options'); ?>"><img src="<?= get_template_directory_uri().'/img/instagram.png'; ?>" alt="Instagram"></a>
                        </div>
                    </div>
                </div>
                <div id="map-office2">
                </div>
            </div>

        </div><!-- end container -->
	</main>
                    <script>
                        var office1 = {
              title : '<?php echo get_field("title_ofice1");?>',
              coords: JSON.parse('<?php echo json_encode(explode(",",get_field("coords_1")));?>')
            };
                        var office2 = {
              title : '<?php echo get_field("title_ofice2");?>',
              coords: JSON.parse('<?php echo json_encode(explode(",",get_field("coords_2")));?>')
            };
                    </script>
						<?php
						get_footer();
?>
