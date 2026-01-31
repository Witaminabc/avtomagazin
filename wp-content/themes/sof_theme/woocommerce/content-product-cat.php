<?php 
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

get_header();
$cat_id = woocommerce_category_description();
		//получить общее кол-во товаров в категории
				$pcc = new WP_Query( array(
'post_type' => 'product',
'posts_per_page' => -1,
            'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $cat_id
                    )
                ),
'orderby' => 'date',
));
$count = $pcc->found_posts;

$limit = get_option('my_product_limit'); //кол-во товаров на странице
						//получить кол-во страниц
						$pages = ceil($count/$limit);
if(empty($_GET['page'])){
$cur_page = 1;
$offset = 0;
} //endif empty
else{
	$page = intval($_GET['page']);
if($page <= 1){ //если страница меньше равно 1
$cur_page = 1;
$offset = 0;
} //endif
elseif($page > $pages){ //если страница больше числа страниц
$cur_page = $pages;
$offset = (($pages * $limit) - $limit);
} //endelseif
else{
	$offset = (($page * $limit) - $limit);
	$cur_page = $page;
} //endelse
} //endelse not empty
?>
<main>
<p>work page catalog </p>
		       <div class="container">
            <div class="crumbs">
                <ul>
                    <li><a href="<?php echo get_permalink(6); ?>">Главная</a></li>
                    <li><a href="<?php echo get_term_link($cat_id, 'product_cat'); ?>">Каталог</a></li>
                </ul>
            </div>

            <h2 class="main-headline">Каталог</h2>
            <div class="table-list">
                <div class="list-view">
                    <img src="<?= get_template_directory_uri().'/img/black-list.png'; ?>" alt=" black List" class="black black-list">
                    <img src="<?= get_template_directory_uri().'/img/red-list.png'; ?>" alt="red List" class="red red-list">
                </div>
                <div class="table-view">
                    <img src="<?= get_template_directory_uri().'/img/black-table.png'; ?>" alt=" black table" class="black black-table">
                    <img src="<?= get_template_directory_uri().'/img/red-table.png'; ?>" alt="red table" class="red red-table">
                </div>
				</div><!-- end table list -->
				            <div class="all-catalog">
                <aside>
				                    <div class="all-filter">
                        <div class="top-filter">
                            <div class="open-select">
							<?php
							//моторные масла
							$category = get_term(30, 'product_cat');
							?>
                                <h5 class="headline-category"><?php echo $category->name; ?> <img src="<?= get_template_directory_uri().'/img/open.png'; ?>" alt="open"><img src="<?= get_template_directory_uri().'/img/close.png'; ?>" alt="close"></h5>
				<?php
								$terms = get_terms( array(
	'taxonomy'     => 'product_cat',
	'parent' => $category->term_id,
		'orderby'      => 'id',
	'order'        => 'ASC',

	'hide_empty'   => 0,
	));
	$labels = array('first', 'second', 'third', 'fourth');
	?>
	<?php if( $terms ): ?>
	                                <div class="category">
			<?php foreach( $terms as $key => $cat ): ?>
                                    <div>
                                        <input type="checkbox" name="motor-oil" id="motor-oil<?php echo $key; ?>" data-subcategory="<?php echo $cat->term_id; ?>" style="opacity:0">
                                        <label for="motor-oil<?php echo $key; ?>"><?php echo $cat->name; ?></label>
</div>
<?php
endforeach;
?>
</div><!-- end category -->
<?php
	endif;
	?>
	</div><!-- end open select -->
	                            <div class="open-select">
							<?php
							//тормозные жидкости
							$category = get_term(33, 'product_cat');
							?>
                                <h5 class="headline-category"><?php echo $category->name; ?> <img src="<?= get_template_directory_uri().'/img/open.png'; ?>" alt="open"><img src="<?= get_template_directory_uri().'/img/close.png'; ?>" alt="close"></h5>
				<?php
								$terms = get_terms( array(
	'taxonomy'     => 'product_cat',
	'parent' => $category->term_id,
		'orderby'      => 'id',
	'order'        => 'ASC',

	'hide_empty'   => 0,
	));
	$labels = array('first', 'second', 'third', 'fourth');
	?>
	<?php if( $terms ): ?>
	                                <div class="category">
			<?php foreach( $terms as $key => $cat ): ?>
                                    <div>
                                        <input type="checkbox" name="liquid" id="liquid<?php echo $key; ?>" data-subcategory="<?php echo $cat->term_id; ?>" style="opacity:0">
                                        <label for="liquid<?php echo $key; ?>"><?php echo $cat->name; ?></label>
</div>
<?php
endforeach;
?>
</div><!-- end category -->
<?php
	endif;
	?>
								</div><!-- end open select -->
								                            <div class="open-select">
							<?php
							//трансмиссионные масла
							$category = get_term(31, 'product_cat');
							?>
                                <h5 class="headline-category"><?php echo $category->name; ?> <img src="<?= get_template_directory_uri().'/img/open.png'; ?>" alt="open"><img src="<?= get_template_directory_uri().'/img/close.png'; ?>" alt="close"></h5>
				<?php
								$terms = get_terms( array(
	'taxonomy'     => 'product_cat',
	'parent' => $category->term_id,
		'orderby'      => 'id',
	'order'        => 'ASC',

	'hide_empty'   => 0,
	));
	?>
	<?php if( $terms ): ?>
	                                <div class="category">
			<?php foreach( $terms as $key => $cat ): ?>
                                    <div>
                                        <input type="checkbox" name="trans-oil" id="oil<?php echo $key; ?>" data-subcategory="<?php echo $cat->term_id; ?>" style="opacity:0">
                                        <label for="oil<?php echo $key; ?>"><?php echo $cat->name; ?></label>
</div>
<?php
endforeach;
?>
</div><!-- end category -->
<?php
	endif;
	?>
	</div><!-- end open select -->
							<?php
							//колодки
							$category = get_term(32, 'product_cat');
							?>
				<?php
								$terms = get_terms( array(
	'taxonomy'     => 'product_cat',
	'parent' => $category->term_id,
		'orderby'      => 'id',
	'order'        => 'ASC',

	'hide_empty'   => 0,
	));
	?>
	<?php if( $terms ): ?>
			<?php foreach( $terms as $key => $cat ): ?>
			                            <div class="one-select">
                                <div class="one-sel">
                                        <input type="checkbox" name="stop" id="stop<?php echo $key; ?>" data-subcategory="<?php echo $cat->term_id; ?>" style="opacity:0">
                                        <label for="stop<?php echo $key; ?>"><?php echo $cat->name; ?></label>
</div>
</div>
<?php
endforeach;
?>
<?php
	endif;
	?>
							<?php
							//фильтры
							$category = get_term(35, 'product_cat');
							?>
				<?php
								$terms = get_terms( array(
	'taxonomy'     => 'product_cat',
	'parent' => $category->term_id,
		'orderby'      => 'id',
	'order'        => 'ASC',

	'hide_empty'   => 0,
	));
	?>
	<?php if( $terms ): ?>
			<?php foreach( $terms as $key => $cat ): ?>
			                            <div class="one-select">
                                <div class="one-sel">
                                        <input type="checkbox" name="filter" id="filter<?php echo $key; ?>" data-subcategory="<?php echo $cat->term_id; ?>" style="opacity:0">
                                        <label for="filter<?php echo $key; ?>"><?php echo $cat->name; ?></label>
</div>
</div>
<?php
endforeach;
?>
<?php
	endif;
	?>
	</div><!-- end top filter -->
                        <div class="down-filter">
																<?php
																	    $QueryArgs = array(
        'post_type' => 'cars',
        'posts_per_page' => -1,
        'post_status' => 'publish',
'orderby'        => 'date',
        'order' => 'ASC',
    );
    $pc = new WP_Query($QueryArgs);
	if($pc->have_posts()){
		$cars = array();
		$models = array();
		$years = array();
		$engines = array();
    while ($pc->have_posts()) {
        $pc->the_post();
	
	$image = get_field('photo');
		$cars[] = array('image' => $image['url'], 'title' => get_the_title());
		$models[] = get_field('model');
		$years[] = get_field('year');
		$engines[] = get_field('engine');
	} //endwhile
		                   $pc->reset_postdata();
						   ?>
                            <h4>Подбор по машине</h4>
                            <div>
                                <h5>Марка автомобиля</h5>
                                <select id="mark-of-car">
								<option data-imagesrc="" value=""></option>
								<?php
								foreach($cars as $key => $value): ?>
								<option data-imagesrc="<?php echo $value['image']; ?>" value="<?php echo $value['title']; ?>"><?php echo $value['title']; ?></option>
								<?php endforeach; ?>
</select>
</div>
                            <div>
                                <h5>Модель</h5>
                                <select id="model">
								<option value=""></option>
								<?php foreach($models as $value): ?>
								<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
								<?php endforeach; ?>
                </select>
                            </div>
                            <div>
                                <h5>Год выпуска</h5>
                                <select id="year">
								<option value=""></option>
								<?php foreach($years as $value): ?>
                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
					<?php endforeach; ?>
                </select>
                            </div>
                            <div>
                                <h5>Двигатель</h5>
                                <select id="litr">
								<option value=""></option>
								<?php foreach($engines as $value): ?>
                    <option value="<?php echo $value; ?>"><?php echo $value; ?> л</option>
					<?php endforeach; ?>
                </select>
                            </div>
<?php
	} //endif
?>
</div><!-- end down filter -->
                        <div class="ready">
                            <div class="button-red close-filter">
							<a href="javascript:void(0);" id="get_filter" data-url="<?php echo get_permalink(244); ?>">Применить</a>

                            </div>
                        </div>

	</div><!-- end all filter -->
				</aside>
				                <div class="catalog-with-location">
                    <div class="sorting">
                        <div class="sel">
                            <h3>Доступно в</h3>
                            <select class="sort" id="location">
														<option value=""></option>
																<?php
																	    $QueryArgs = array(
        'post_type' => 'pickup',
        'posts_per_page' => -1,
        'post_status' => 'publish',
'orderby'        => 'date',
        'order' => 'ASC',
    );
    $pc = new WP_Query($QueryArgs);
	if($pc->have_posts()){
    while ($pc->have_posts()) {
        $pc->the_post();
		?>
<option value="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></option>
					<?php
	} //endwhile
		                   $pc->reset_postdata();
	} //endif
	?>
                            </select>
                        </div>
                        <div class="sel">
                            <h3>Упорядочить по</h3>
                            <select class="sort" id="price">
							<option value=""></option>
                                <option value="1">Цене, по убыванию</option> 
                                <option value="2">Цене, по возрастанию</option> 
                            </select>
                        </div>
					</div><!-- end sorting -->
					                    <h3 class="mobile-filter">Фильтры</h3>
										<?php
//получить товары
		$pc = new WP_Query( array(
'post_type' => 'product',
'posts_per_page' => $limit,
'offset' => $offset,
            'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $cat_id,
                    )
                ),
'orderby' => 'date',
));
	if($pc->have_posts()){
		?>
		                    <div class="main-catalog-table">
<?php
    while ($pc->have_posts()) {
        $pc->the_post();
		?>
		                        <div class="one-product-catalog">
		<?php
		$terms = wp_get_post_terms( get_the_ID(), 'product_tag' );
if( count($terms) > 0 ){
    foreach($terms as $term){
		echo'<div class="';
		if($term->name == 'Новый'){
echo'new popularity';
		}
		elseif($term->name == 'Акция'){
			echo'action popularity';
		}
				elseif($term->name == 'Популярный'){
			echo'popular popularity';
		}

		echo'">';
		echo'<p>'.$term->name.'</p>';
		echo'</div>';
	} //endforeach
} //endif tag
?>
                            <div class="image" onclick="document.location.href='<?php echo get_the_permalink(); ?>'">
                                <img src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>" alt="<?php echo strip_tags(get_the_title()); ?>">
                            </div>
                            <h3 onclick="document.location.href='<?php echo get_the_permalink(); ?>'"><?php echo get_the_title(); ?></h3>
                            <div class="more-info">
                                <p>подробнее</p>
								<?php
$_product = wc_get_product( get_the_ID() );
echo'<h3 id="price'.get_the_ID().'" data-price="'.$_product->get_price().'">'.$_product->get_price().' р.</h3>';
?>
</div><!-- end more info -->
                            <div class="buy-bask">
                                <input type="number" class="pp-number" id="list_input_product<?php echo get_the_ID(); ?>" data-product="<?php echo get_the_ID(); ?>" data-quantity="<?php echo $_product->get_stock_quantity(); ?>" value="1">
                                <div class="button">
                                    <a href="javascript:void(0)" data-add="<?php echo get_the_ID(); ?>"><img src="<?= get_template_directory_uri().'/img/red-basket.png'; ?>" alt="В корзину">В корзину</a>
                                </div>
								</div><!-- end buy bask -->
								</div><!-- end one product catalog -->
		<?php
	}
	wp_reset_postdata();
	?>
						</div><!-- end main catalog table -->
						                    <div class="main-catalog-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Цена</th>
                                    <th>Количество</th>
                                    <th>Стоимость</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
<?php
    while ($pc->have_posts()) {
        $pc->the_post();
		?>
                                <tr>
                                    <td onclick="document.location.href='<?php echo get_the_permalink(); ?>'"><?php echo get_the_title(); ?></td>
								<?php
$_product = wc_get_product( get_the_ID() );
echo'<td id="table_price'.get_the_ID().'" data-table_price="'.$_product->get_price().'">'.$_product->get_price().' р.</td>';
?>
                                    <td>
                                        <div class="quantity-block">
                                            <input class="quantity-num pp-number-table" id="input_product<?php echo get_the_ID(); ?>" data-table_product="<?php echo get_the_ID(); ?>" type="number" data-quantity="<?php echo $_product->get_stock_quantity(); ?>" value="1" />
                                            <p>шт</p>
                                            <div class="symb">
                                                <button class="quantity-arrow-plus pp-plus-btn" data-plus_btn="<?php echo get_the_ID(); ?>">+</button>
                                                <button class="quantity-arrow-minus pp-minus-btn" data-minus_btn="<?php echo get_the_ID(); ?>">-</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td id="result_price<?php echo get_the_ID(); ?>">200 р.</td>
                                    <td data-table_add="<?php echo get_the_ID(); ?>">В корзину</td>
                                </tr>
				<?php
	}
	wp_reset_postdata();
	?>
								</tbody>
                        </table>
											</div><!-- end main catalog list -->
											                    <div class="see-more">
                        <div class="button">
						<?php
						//получить текущую страницу
						//показать текущую, предыдущую и следующую страницы
													$more = $cur_page + 1;
						if($more > $pages ) echo'<a href="javascript:void(0)">Показать ещё</a>';
						else{
								echo'<a href="javascript:void(0);" data-more="'.get_term_link($cat_id, 'product_cat').'?page='.$more.'">Показать ещё</a>';
						}
	?>
                        </div>
                    </div><!-- end see more -->
                    <div class="pagination">
                        <ul>
<?php
						if($cur_page >= 3){
							if($cur_page == 3){
								echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page=1">1</a></li>';
							} //endif 3
							elseif($cur_page == 4){
																echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page=1">1</a></li>';
								echo'<li>...</li>';
							} //endif 4
						elseif($cur_page > 4){
																							echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page=1">1</a></li>';
																																														echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page=2">2</a></li>';
								echo'<li>...</li>';
						} //endif больше 4
						} //endif
						if($cur_page > $pages) $cur_page = $pages;
						for($x = ($cur_page - 1); $x <= ($cur_page + 1); $x++){
						//если предыдущая страница меньше 1 то не показывать
						if($x < 1) continue;
						//если последняя страница больше общего количества страниц, то прервать цикл
						if($x > $pages) break;
						//подсветить текущую страницу
												if($x == $cur_page) echo'<li class="active"><a href="javascript:void(0);">'.$x.'</a></li>';
else echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page='.$x.'">'.$x.'</a></li>';
						} //endfor
						if($cur_page <= ($pages - 2)){
														if($cur_page <= ($pages - 4)){
																							echo'<li>...</li>';
								echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page='.($pages - 1).'">'.($pages - 1).'</a></li>';
							echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page='.$pages.'">'.$pages.'</a></li>';
							} //end меншьше равно 4

							elseif($cur_page == ($pages - 2)){
															echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page='.$pages.'">'.$pages.'</a></li>';
							} //end 2
							elseif($cur_page == ($pages - 3)){
																															echo'<li>...</li>';
															echo'<li><a href="javascript:void(0);" data-category_page="'.get_term_link($cat_id, 'product_cat').'?page='.$pages.'">'.$pages.'</a></li>';
							} //end 3

						} //end -2
?>
						</ul>
					</div><!-- end pagenation -->
	<?php
	} //endif
	else{
		?>
				                    <div class="main-catalog-table">
<p>Товаров не найдено</p>
									</div>
													                    <div class="main-catalog-list">
<p>Товаров не найдено</p>
									</div>
<?php
	} //endelse
	?>
								</div><!-- end catalog with location -->
							</div><!-- end all catalog -->
			</div><!-- end container -->
			</main>

<?php get_footer(); ?>