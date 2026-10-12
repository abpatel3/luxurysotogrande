<?php

/**
 * Template Name: Long Rentals Page
 */

$property_id = '';
$property_status = '';
$property_type = '';
$property_area = '';
$price_range = [];

if (is_page(224)) {
	$property_status = 'long-term-rental';
} elseif (is_page(224)) {
	$property_status = 'long-term-rental-es';
} elseif (is_page(219)) {
	$property_status = 'for-sale';
} elseif (is_page(1068)) {
	$property_status = 'for-sale-es';
} elseif (is_page(221)) {
	$property_status = 'holiday-rental';
} elseif (is_page(1073)) {
	$property_status = 'holiday-rental-es';
}

if (!empty($_GET['pid'])) {
	$property_id = $_GET['pid'];
}

if (!empty($_GET['min_price'])) {
	$price_range[] = $_GET['min_price'];
} else {
	$price_range[] = 1000;
}

if (!empty($_GET['max_price'])) {
	$price_range[] = $_GET['max_price'];
} else {
	$price_range[] = 10000000;
}

if (!empty($_GET['type'])) {
	$property_type = get_term($_GET['type'], 'property_type')->slug;
}

if (!empty($_GET['area'])) {
	$property_area = get_term($_GET['area'], 'property_area')->slug;
}

if (!empty($_GET['subarea'])) {
	$property_area = get_term($_GET['subarea'], 'property_area')->slug;
}



get_header();
get_section_templates();
?>
    <main class="rental-properties-main max-w-6xl px-5 py-20 mx-auto lg:px-0">
        <div class="rental-properties-main__grid filtered-properties">
			<?php
			$args = array(
				'post_type'      => 'property',
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'property_status',
						'field'    => 'slug', // You can change 'slug' to 'name' or 'term_id' depending on how you're identifying terms
						'terms'    => 'long-term-rental',
					),
				),
			);

			$query = new WP_Query($args);

			if ($query->have_posts()) :
				while ($query->have_posts()) :
					$query->the_post();

					// Retrieve necessary data
					$permalink = get_permalink();
					$title = get_the_title();
					$subtitle = get_field('subtitle');
					$property_id = get_field('property_id');
					$bedrooms = get_field('bedrooms');
					$bathrooms = get_field('bathrooms');
					$featured_img = get_post_thumbnail_id();
					$gallery = get_field('gallery');
					$reduced_price = get_field('property_reduced_price');
					// Check if property is sold
					$is_sold = get_field('sold');
					// Check if property is rented
					$is_rented = get_field('rented');

					?>
                    <div class="properties-item" data-aos="fade-up">
                        <div class="properties-item__gallery">
	                        <?php if ($is_sold) : ?>
                                <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold">
                                    <p class="text-center"><?= label('sold_label'); ?></p>
                                </div>
	                        <?php elseif ($is_rented) : ?>
                                <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold">
                                    <p class="text-center"><?php _e('Rented', 'purple'); ?></p>
                                </div>
	                        <?php elseif ($reduced_price) : ?>
                                <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold">
                                    <p class="text-center"><?= label('reduced_price_label'); ?></p>
                                </div>
	                        <?php endif; ?>
                            <!-- /banner -->
                            <div class="properties-item__gallery-wrapper">

								<?php
								// Display the featured image
								if ($featured_img) {
									echo '<div class="gallery-image">';
									echo '<a href="' . $permalink . '">';
									echo wp_get_attachment_image($featured_img, 'large', false, array('class' => 'object-cover w-full h-72 lg:h-128'));
									echo '</a>';
									echo '<span>';
									echo '<img width="298" height="80" src="' . get_stylesheet_directory_uri() . '/assets/images/lsg-logo-1.png" alt="lsg-logo-1">';
									echo '</span>';
									echo '</div>';
								}

								// Display next 5 images from the gallery
								$count = 0;
								if (!empty($gallery) && is_array($gallery)) {
									foreach ($gallery as $image) {
										// Skip if the image is the featured image
										if ($count >= 3) break;
										if ($image['ID'] != $featured_img) {
											echo '<div class="gallery-image">';

											echo '<a href="' . $permalink . '">';
											echo wp_get_attachment_image($image['ID'], 'large', false, array('class' => 'object-cover w-full h-72 lg:h-128'));
											echo '</a>';
											echo '<span>';
											echo '<img width="298" height="80" src="' . get_stylesheet_directory_uri() . '/assets/images/lsg-logo-1.png" alt="lsg-logo-1">';
											echo '</span>';
											echo '</div>';

											$count++;
										}
									}
								}
								?>
                            </div>
                        </div>
                        <a target="_self" href="<?php echo $permalink; ?>" title="<?php echo $title; ?>">

                            <div class="properties-item__content flex flex-col justify-between flex-1 p-4 space-y-2 bg-lsg-gray">
                                <div>
                                    <h2 class="text-2xl font-medium uppercase"><?php echo $title; ?></h2>
                                    <h3 class="text-xl font-light uppercase"><?php echo $subtitle; ?></h3>
                                </div>
                                <div class="flex items-center justify-between font-light">
                                    <div class="text-xl text-center uppercase">
                                        <p>Ref: <?php echo $property_id; ?></p>
                                    </div>
                                    <?php $poa = get_field('poa');
                                    $price_postfix = get_field('price_postfix_text');
                                    $price = get_field('property_price');
                                    ?>
                                    <div class="flex items-center space-x-2">
	                                    <?php if ($poa) : ?>
                                            <h3 class="">POA</h3>
	                                    <?php else : ?>
		                                    <?php if ($price && is_numeric($price)) : ?>
                                                <h3 class="<?= $reduced_price ? 'line-through text-gray-400' : ''; ?> text-xl text-center uppercase">
				                                    <?= number_format($price) . $price_postfix; ?>
                                                </h3>
		                                    <?php endif; ?>

		                                    <?php if ($reduced_price && is_numeric($reduced_price)) : ?>
                                                <h3 class="text-xl text-center uppercase">
				                                    <?= number_format($reduced_price) . $price_postfix; ?>
                                                </h3>
		                                    <?php endif; ?>
	                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 space-y-2 text-white bg-lsg-gold">
                                <div class="grid justify-between  grid-cols-2 gap-5 text-sm font-medium uppercase">
                                    <div class="text-center">
                                    <span class="block h-6 text-white">
                                        <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                    </span>
                                        <p class="text-center"><?php echo $bedrooms . ' ' . label('bedrooms_label'); ?></p>
                                    </div>
                                    <div class="text-center">
                                    <span class="block h-6 text-white">
                                        <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                    </span>
                                        <p class="text-center"><?php echo $bathrooms . ' ' . label('bathrooms_label'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
				<?php endwhile;
				wp_reset_postdata();
			else :
				// No posts found
			endif;
			?>
        </div>
    </main>


<?php //if (!is_page(22842)) : ?>
<!--    <section class="max-w-6xl px-5 py-20 mx-auto lg:px-0">-->
<!--		--><?php //get_template_part('template-parts/filtered-properties'); ?>
<!--    </section>-->
<?php //endif; ?>







<?php if($categories_content = get_field('categories_content')) : ?>
    <section class="property-page-content">
        <div class="container-new-800">
            <div class="property-page-content__block">
				<?php echo $categories_content; ?>
            </div>
        </div>
    </section>
<?php endif;?>

<?php $faq_global = get_field('faq', 'options');?>
<?php $faq = get_field('faq');?>

<?php if($faq_global || $faq ) : ?>
    <section class="faq bg-lsg-gray">
        <div class="container-new">
            <h2 class="text-3xl font-normal lg:text-4xl"><?php _e('FAQ', 'purple');?></h2>
			<?php $index = 1;?>
            <div class="faq__grid">
				<?php if ( have_rows( 'faq', 'options' ) ): ?>
					<?php while ( have_rows( 'faq', 'options' ) ): the_row();
						$title = get_sub_field( 'title' );
						$text = get_sub_field( 'text' ); ?>
                        <div class="faq__item bg-white shadow-xl">
                            <button class="faq__item-btn ">
								<?php echo $title; ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <p class="faq__item-text">
								<?php echo $text; ?>
                            </p>
                        </div>
						<?php $index++; ?>
					<?php endwhile; ?>
				<?php endif; ?>
				<?php $index_second = 150;?>
				<?php if ( have_rows( 'faq') ): ?>
					<?php while ( have_rows( 'faq') ): the_row();
						$title = get_sub_field( 'title' );
						$text = get_sub_field( 'text' ); ?>
                        <div class="faq__item bg-white shadow-xl">
                            <button class="faq__item-btn ">
								<?php echo $title; ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <p class="faq__item-text">
								<?php echo $text; ?>
                            </p>
                        </div>
						<?php $index_second++; ?>
					<?php endwhile; ?>
				<?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
get_footer();
