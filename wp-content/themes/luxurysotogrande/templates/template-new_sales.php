<?php
/**
 * Template Name: New Sales Page
 */
get_header(); ?>




<main class="main-content-properties">
	<?php $arg = array(
		'post_type'      => 'property',
		'order'          => 'DESC',
		'orderby'        => 'date',
		'posts_per_page' => 3,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'property_status',
				'field' => 'slug',
				'terms' => 'for-sale'
			)
		)
	);
	$the_query = new WP_Query( $arg );
	if ( $the_query->have_posts() ) : ?>
        <section class="slider glide">
            <div class="glide__track" data-glide-el="track">
                <ul class="h-96 xl:h-192 glide__slides">
	                <?php while ( $the_query->have_posts() ) :
		                $the_query->the_post(); ?>
                        <li class="relative flex items-center justify-center glide__slide">
	                        <?php the_post_thumbnail( 'full', array( 'class' => 'h-full w-full object-cover' ) ); ?>

                            <div class="absolute px-4 space-y-4 text-center text-white md:space-y-8">
                                <div class="space-y-4">
                                    <div>
                                        <h2 class="inline-block p-3 text-xl font-bold uppercase md:p-5 md:text-4xl bg-black/25">
							                <?php the_title(); ?>
                                        </h2>
                                    </div>
	                                <?php if($subtitle = get_field('subtitle')) : ?>
                                        <h3 class="inline-block p-3 text-2xl uppercase md:p-5 md:text-3xl bg-black/25">
                                            <?php echo $subtitle; ?>
                                        </h3>
	                                <?php endif;?>
                                </div>
                                <a href="<?php the_permalink();?>"
                                    class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold">
                                    <?php _e('TAKE A LOOK INSIDE', 'luxury'); ?>
                                </a>
                            </div>
                        </li>
	                <?php endwhile; ?>
                </ul>
            </div>
        </section>
	<?php endif;
	wp_reset_query(); ?>
	<?php get_template_part('template-parts/search-bar'); ?>
    <section class="new-sale-properties-main max-w-6xl px-5 py-20 mx-auto lg:px-0">
        <div class="new-sale-properties-main__grid filtered-properties" id="main-posts-property">
			<?php
			$paged = get_query_var( 'paged' )  ? get_query_var( 'paged' ) : 1;
			$args = array(
				'post_type'      => 'property',
				'order'          => 'DESC',
				'orderby'        => 'date',
				'posts_per_page' => 8,
				'post_status' => 'publish',
				'paged' => $paged,
				'tax_query' => array(
					array(
						'taxonomy' => 'property_status',
						'field' => 'slug',
						'terms' => 'for-sale'
					)
				)
			);

			$properties_query = new WP_Query( $args );

			if ( $properties_query->have_posts() ) :
				while ( $properties_query->have_posts() ) : $properties_query->the_post();

					$permalink = get_permalink();
					$title = get_the_title();
					$gallery = get_field('gallery');
					$featured_img = get_post_thumbnail_id();
					$subtitle = get_field('subtitle');
					$property_id = get_field('property_id');
					$bedrooms = get_field('bedrooms');
					$bathrooms = get_field('bathrooms');
					?>
                    <div class="properties-item" data-aos="fade-up">
                        <div class="properties-item__gallery">
                            <div class="properties-item__gallery-wrapper">
								<?php
								// Display the featured image
								if ($featured_img) {
									echo '<div class="gallery-image">';
									echo '<a href="' . $permalink .'">';
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
											echo '<a href="' . $permalink .'">';
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
                        <a target="_self" href="<?php echo $permalink;?>" title="<?php echo $title; ?>">
                            <div class="properties-item__content flex flex-col justify-between flex-1 p-4 space-y-2 bg-lsg-gray">
                                <div>
                                    <h2 class="text-2xl font-medium uppercase"><?php echo $title; ?></h2>
                                    <h3 class="text-xl font-light uppercase"><?php echo $subtitle; ?></h3>
                                </div>
                                <div class="flex items-center justify-between font-light">
                                    <div class="text-xl text-center uppercase">
                                        <p>Ref: <?php echo $property_id; ?></p>
                                    </div>
                                    <div class="flex items-center space-x-2">

                                        <?php $is_holiday_rental = isset($terms[0]) && in_array($terms[0]->slug, ['holiday-rental', 'holiday-rental-es']);
                                        $reduced_price = get_field('property_reduced_price'); ?>
		                                <?php if (!$is_holiday_rental) : ?>
                                            <?php $poa = get_field('poa'); ?>
			                                <?php $property_price = get_field('property_price'); ?>
			                                <?php $property_reduced_price = get_field('property_reduced_price'); ?>
			                                <?php $price_postfix_text = get_field('price_postfix_text'); ?>
			                                <?php if ($poa) : ?>
                                                <h3 class="text-xl text-right uppercase">POA</h3>
			                                <?php else : ?>
				                                <?php if ($property_price && is_numeric($property_price)) : ?>
                                                    <h3 class="text-xl text-right uppercase <?= $reduced_price ? 'line-through text-gray-400' : ''; ?>">
						                                <?= number_format($property_price) . $price_postfix_text; ?>
                                                    </h3>
				                                <?php endif; ?>

				                                <?php if ($reduced_price && is_numeric($reduced_price)) : ?>
                                                    <h3 class="text-xl text-right uppercase ">
						                                <?= number_format($property_reduced_price) . $price_postfix_text; ?>
                                                    </h3>
				                                <?php endif; ?>
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
				<?php
				endwhile;
				wp_reset_postdata();
			endif;
			?>
        </div>
        <!-- BEGIN of pagination -->
	    <?php bootstrap_paginationProperty($properties_query); ?>
        <!-- END of pagination -->
    </section>
</main>

<?php get_footer(); ?>


