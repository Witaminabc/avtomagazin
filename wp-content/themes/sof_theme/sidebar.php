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
	</div><!-- end down filter -->
						</div><!-- end all filter -->
				</aside>