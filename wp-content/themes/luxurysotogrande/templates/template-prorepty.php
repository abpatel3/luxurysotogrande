<?php
/**
 * Template Name: Property Page
 */
get_header(); ?>


<!-- Add the "noindex" meta tag -->

<?php $bg = get_field('background_image_property', 'options'); ?>
<?php $image_default = get_the_post_thumbnail_url(); ?>
<?php $property_status = get_field('property_status'); ?>
<?php $property_area = get_field('property_area'); ?>
<?php $property_types = get_field('property_types'); ?>


<main class="main-content-property">
    <!-- Section -->
	<section class="property-page-top">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'full', array( 'class' => 'property-page-top-img skip-lazy' ) ); ?>
        <?php else: ?>
            <?php echo wp_get_attachment_image($bg, 'full', false, array( 'class' => 'property-page-top-img skip-lazy' ) ); ?>
		<?php endif; ?>
        <div class="property-page-top-overlay"></div>
		<div class="container-new-full">
			<div class="property-page-top__block">
				<h1 class="property-page-top__block-title">
					<?php _e(' Properties', 'purple'); ?>
                    <?php if($property_status) : ?>
	                    <?php if($property_status->slug !== 'for-sale') : ?>
                            <span class="property-page-top__block-title-type"><?php _e('for ', 'purple'); ?>
			                    <?php if($property_status) : ?>
				                    <?php echo $property_status->name; ?>
			                    <?php endif;?>
                        </span>
	                    <?php else:?>
                            <span class="property-page-top__block-title-type">
                            <?php if($property_status) : ?>
	                            <?php echo $property_status->name; ?>
                            <?php endif;?>
                        </span>
	                    <?php endif; ?>
                    <?php else: ?>
                    <?php _e('For Sale or Rental', 'purple'); ?>
                    <?php endif;?>

					<?php if($property_area) : ?>
                        <p class="property-page-top__block-title-area">
	                        <?php foreach ($property_area as $property_area_item ): ?>
                                <span class="property-page-top__block-title-area-parent">
                                    <?php _e('in <span> </span>', 'Luxury'); ?>
                                     <?php echo $property_area_item->name; ?>
                                    <span class="coma">,</span>
                                 </span>
	                        <?php endforeach;?>
                        </p>
                    <?php else: ?>
                        <p class="property-page-top__block-title-area"><?php _e('Sotogrande','purple'); ?></p>
					<?php endif; ?>
				</h1>
                <div class="property-page-top__text">
                    <h2>
                        <?php _e('Welcome to the luxurious world of real estate ', 'purple'); ?>
	                    <?php if($property_status) : ?>
		                    <?php if($property_status->slug !== 'for-sale') : ?>
                                <span class="property-page-top__text-status"><?php _e('for ', 'purple'); ?>
				                    <?php if($property_status) : ?>
					                    <?php echo $property_status->name; ?>
				                    <?php endif;?>
                        </span>
		                    <?php else:?>
                                <span class="property-page-top__text-status">
                            <?php if($property_status) : ?>
	                            <?php echo $property_status->name; ?>
                            <?php endif;?>
                        </span>
		                    <?php endif; ?>
	                    <?php else: ?>
		                    <?php _e('For Sale or Rental', 'purple'); ?>
	                    <?php endif;?>
                        <?php _e('in <strong>Sotogrande</strong>, with its tranquil coastline and untouched beauty in the <strong>heart of Costa del Sol</strong>. We offer you exclusive opportunities in<span> </span>', 'purple'); ?>

	                    <?php if($property_area) : ?>
                            <p class="property-page-top__text-area">
			                    <?php foreach ($property_area as $property_area_item ): ?>
                                    <span class="property-page-top__text-area-parent">
                                     <?php echo $property_area_item->name; ?>
                                    <span class="coma">,</span>
                                 </span>
			                    <?php endforeach;?>
                            </p>
	                    <?php else: ?>
                            <p class="property-page-top__text-area"><?php _e('Sotogrande ,','purple'); ?></p>
	                    <?php endif; ?>
                        <?php _e('including villas, apartments, and plots where dreams become reality. Start your journey to luxury and comfort right now – contact us.', 'purple'); ?>
                    </h2>
                </div>
			</div>
		</div>
	</section>
    <!-- Section -->
    <section class="property-page-items bg-lsg-gray" id="property-page-items-section">
        <div class="container-new">
            <div class="property-page-items__grid">
                <div class="property-page-items__links">
	                <?php
	                $property_status_cats = get_field('property_status_cats', 'options');
	                if( $property_status_cats ): ?>
                        <div class="property-page-items__links-property-status bg-white shadow-xl">
                            <button id="btn-open-closed-status" class="btn-open-closed btn-open-closed-active">
				                <?php _e('Property Status')?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <ul id="list-status" class="<?php echo $property_status->slug == true ? $property_status->slug : ''; ?>">
				                <?php foreach( $property_status_cats as $property_status_cats_page ):
					                $permalink = get_permalink( $property_status_cats_page->ID );
					                $title = get_the_title( $property_status_cats_page->ID );
                                    $slug = basename($permalink);

                                    ?>
                                    <li class="<?php echo $property_status->slug  ? $slug : ''; ?>">
                                        <a href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
                                    </li>
				                <?php endforeach; ?>
                            </ul>
                        </div>
	                <?php endif; ?>

	                <?php get_template_part('template-parts/sales-property-area'); ?>

	                <?php get_template_part('template-parts/sales-property-area-esp'); ?>

	                <?php get_template_part('template-parts/sales-sub-property-area'); ?>

	                <?php get_template_part('template-parts/holiday-rental-property-area'); ?>
	                <?php get_template_part('template-parts/holiday-rental-property-area-esp'); ?>
	                <?php get_template_part('template-parts/holiday-rental-sub-property-area'); ?>


                </div>
                <div class="property-page-items__properties">
                    <h3 class="text-3xl font-normal lg:text-4xl"><?php _e('Property Sort', 'purple'); ?></h3>
                    <div id="sorts" class="btn-group sorter">
                        <div>
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-bottom" data-sort-value="property-size" data-sort-direction="asc">
		                        <?php _e('Property Size', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-top" data-sort-value="property-size" data-sort-direction="desc">
		                        <?php _e('Property Size', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                        </div>

                        <div>
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-bottom" data-sort-value="plot-size" data-sort-direction="asc">
		                        <?php _e('Plot Size', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-top" data-sort-value="plot-size" data-sort-direction="desc">
		                        <?php _e('Plot Size', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                        </div>

                        <div class="price-size-wrapper">
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-bottom" data-sort-value="number" data-sort-direction="asc">
		                        <?php _e('Price', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <button class="btn btn-primary w-full p-2 text-lg text-white uppercase bg-lsg-gold price-top" data-sort-value="number" data-sort-direction="desc">
		                        <?php _e('Price', 'purple'); ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                        </div>
                    </div>


                    <?php $properties_order = get_field('properties_order'); ?>


	                <?php if( $properties_order ): ?>
                        <div class="isotope 123">
			                <?php foreach( $properties_order as $properties_post ): ?>
				                <?php

				                $property_features = get_the_terms($properties_post, 'property_feature'); // Get the terms for the current post

				                $property_feature_classes = array(); // Create an array to store term slugs as classes

				                if ($property_features) {
					                foreach ($property_features as $property_feature) {
						                $property_feature_classes[] = $property_feature->slug; // Add term slugs with a prefix
					                }
				                }

				                $permalink = get_permalink( $properties_post );
				                $title = get_the_title( $properties_post );
				                $featured_img_url = get_post_thumbnail_id($properties_post,'medium_large');


				                ?>

                                <div class="element-item <?php echo implode(' ', $property_feature_classes); ?>">

                                    <a target="_self" title="<?php echo $title; ?>" href="<?php echo $permalink; ?>">
                                        <div class="property-page-image">
							                <?php if ( $featured_img_url ) : ?>
								                <?php echo wp_get_attachment_image($featured_img_url, 'large'); ?>
							                <?php else: ?>
                                                <img width="1280" height="800" alt="<?php _e('no-photo', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/placeholder.jpg">
							                <?php endif;?>
                                        </div>
                                        <div class="property-page-titles">
                                            <h4 class="name uppercase"><?php echo $title; ?></h4>
							                <?php if($subtitle = get_field('subtitle', $properties_post)) : ?>
                                                <h5><?php echo $subtitle; ?></h5>
							                <?php endif; ?>
                                        </div>
                                        <div class="property-page-info">
							                <?php if($property_id = get_field('property_id', $properties_post)) : ?>
                                                <p class="pproperty-page-info-id">
									                <?php _e('REF:', 'purple'); ?><?php echo $property_id; ?>
                                                </p>
							                <?php endif; ?>
							                <?php if($poa = get_field('poa', $properties_post)) : ?>
                                                <p class="property-page-info-poa">
									                <?php _e('POA', 'purple'); ?>
                                                </p>
							                <?php else:?>
								                <?php if($property_price = get_field('property_price', $properties_post)) : ?>
                                                    <p class="property-page-info-price">
										                <?php echo number_format($property_price); ?> €
                                                    </p>
								                <?php endif; ?>
							                <?php endif; ?>
                                        </div>
                                        <div class="number">
							                <?php if($poa) : ?>
                                                1
							                <?php elseif($property_price): ?>
								                <?php echo $property_price; ?>
							                <?php else: ?>
                                                1
							                <?php endif;?>
                                        </div>
                                        <div class="bedrooms">
							                <?php if($bedrooms = get_field('bedrooms', $properties_post)) : ?>
								                <?php echo $bedrooms;?>
							                <?php else: ?>
                                                1
							                <?php endif; ?>
                                        </div>
                                        <div class="bathrooms">
							                <?php if($bathrooms = get_field('bathrooms', $properties_post)) : ?>
								                <?php echo $bathrooms;?>
							                <?php else: ?>
                                                1
							                <?php endif; ?>
                                        </div>
                                        <div class="property-size">
							                <?php if($internal_size = get_field('internal_size', $properties_post)) : ?>
								                <?php echo $internal_size['size'];?>
							                <?php else: ?>
                                                1
							                <?php endif; ?>
                                        </div>
                                        <div class="plot-size">
							                <?php if($plot_size = get_field('plot_size', $properties_post)) : ?>
								                <?php echo $plot_size['size'];?>
							                <?php else: ?>
                                                1
							                <?php endif; ?>
                                        </div>
                                        <div class="bg-lsg-gold property-page-item-info">
							                <?php if($internal_size = get_field('internal_size', $properties_post)) : ?>
                                                <span class="block h-6 text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $internal_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
							                <?php endif;?>

							                <?php if($plot_size = get_field('plot_size', $properties_post)) : ?>
                                                <span class="block h-6 text-white">
                                                     <?php get_template_part('template-parts/icons/tree', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $plot_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
							                <?php endif;?>

							                <?php if($bedrooms = get_field('bedrooms', $properties_post)) : ?>
                                                <span class="block h-6 text-white">
                                                     <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                      <p class="text-center text-white"><?php echo $bedrooms; ?><?php _e(' Bedrooms', 'purple');?></p>
                                                </span>
							                <?php endif;?>

							                <?php if($bathrooms = get_field('bathrooms', $properties_post)) : ?>
                                                <span class="block h-6 text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                     <p class="text-center text-white"><?php echo $bathrooms; ?><?php _e(' Bathrooms', 'purple');?></p>
                                                </span>

							                <?php endif;?>
                                        </div>
                                    </a>
                                </div>

			                <?php endforeach; ?>
                        </div>
                    <?php else: ?>
		                <?php
		                $arg = array(
			                'post_type'      => 'property',
			                'order'          => 'DESC',
			                'orderby'        => 'date',
			                'post_status'    => 'publish',
			                'posts_per_page' => -1,
			                'tax_query'      => array(
				                'relation' => 'AND',
			                ),
		                );

		                if ($property_status) {
			                $arg['tax_query'][] = array(
				                'taxonomy' => 'property_status',
				                'field'    => 'slug',
				                'terms'    => $property_status,
			                );
		                }

		                if ($property_types) {
			                $arg['tax_query'][] = array(
				                'taxonomy' => 'property_type',
				                'field'    => 'slug',
				                'terms'    => $property_types,
			                );
		                }

		                if ($property_area && is_array($property_area)) {
			                $tax_queries = array();

			                foreach ($property_area as $area) {
				                $tax_queries[] = array(
					                'taxonomy' => 'property_area',
					                'field' => 'slug',
					                'terms' => $area,
					                'include_children' => true,
				                );
			                }

			                // Add all tax queries to the main $arg['tax_query']
			                $arg['tax_query'][] = $tax_queries;
		                }


		                $the_query = new WP_Query($arg);

		                if ( $the_query->have_posts() ) : ?>
                            <div class="isotope 345">
				                <?php while ( $the_query->have_posts() ) :
					                $the_query->the_post(); ?>
					                <?php $bedrooms = get_field('bedrooms'); ?>

					                <?php $bathrooms = get_field('bathrooms'); ?>

					                <?php

					                $property_features = get_the_terms(get_the_ID(), 'property_feature'); // Get the terms for the current post

					                $property_feature_classes = array(); // Create an array to store term slugs as classes

					                if ($property_features) {
						                foreach ($property_features as $property_feature) {
							                $property_feature_classes[] = $property_feature->slug; // Add term slugs with a prefix
						                }
					                }
					                ?>
                                    <div class="element-item <?php echo implode(' ', $property_feature_classes); ?>">

                                        <a target="_self" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                            <div class="property-page-image">
								                <?php if ( has_post_thumbnail() ) : ?>
									                <?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'large'); ?>
								                <?php else: ?>
                                                    <img width="1280" height="800" alt="<?php _e('no-photo', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/placeholder.jpg">
								                <?php endif;?>
                                            </div>
                                            <div class="property-page-titles">
                                                <h4 class="name uppercase"><?php the_title(); ?></h4>
								                <?php if($subtitle = get_field('subtitle')) : ?>
                                                    <h5><?php echo $subtitle; ?></h5>
								                <?php endif; ?>
                                            </div>
                                            <div class="property-page-info">
								                <?php if($property_id = get_field('property_id')) : ?>
                                                    <p class="pproperty-page-info-id">
										                <?php _e('REF:', 'purple'); ?><?php echo $property_id; ?>
                                                    </p>
								                <?php endif; ?>
								                <?php if($poa = get_field('poa')) : ?>
                                                    <p class="property-page-info-poa">
										                <?php _e('POA', 'purple'); ?>
                                                    </p>
								                <?php else:?>
									                <?php if($property_price = get_field('property_price')) : ?>
                                                        <p class="property-page-info-price">
											                <?php echo number_format($property_price); ?> €
                                                        </p>
									                <?php endif; ?>
								                <?php endif; ?>
                                            </div>
                                            <div class="number">
								                <?php if($poa) : ?>
                                                    1
								                <?php elseif($property_price): ?>
									                <?php echo $property_price; ?>
								                <?php else: ?>
                                                    1
								                <?php endif;?>
                                            </div>
                                            <div class="bedrooms">
								                <?php if($bedrooms = get_field('bedrooms')) : ?>
									                <?php echo $bedrooms;?>
								                <?php else: ?>
                                                    1
								                <?php endif; ?>
                                            </div>
                                            <div class="bathrooms">
								                <?php if($bathrooms = get_field('bathrooms')) : ?>
									                <?php echo $bathrooms;?>
								                <?php else: ?>
                                                    1
								                <?php endif; ?>
                                            </div>
                                            <div class="property-size">
								                <?php if($internal_size = get_field('internal_size')) : ?>
									                <?php echo $internal_size['size'];?>
								                <?php else: ?>
                                                    1
								                <?php endif; ?>
                                            </div>
                                            <div class="plot-size">
								                <?php if($plot_size = get_field('plot_size')) : ?>
									                <?php echo $plot_size['size'];?>
								                <?php else: ?>
                                                    1
								                <?php endif; ?>
                                            </div>
                                            <div class="bg-lsg-gold property-page-item-info">
								                <?php if($internal_size = get_field('internal_size')) : ?>
                                                    <span class="block h-6 text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $internal_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
								                <?php endif;?>

								                <?php if($plot_size = get_field('plot_size')) : ?>
                                                    <span class="block h-6 text-white">
                                                     <?php get_template_part('template-parts/icons/tree', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $plot_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
								                <?php endif;?>

								                <?php if($bedrooms = get_field('bedrooms')) : ?>
                                                    <span class="block h-6 text-white">
                                                     <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                      <p class="text-center text-white"><?php echo $bedrooms; ?><?php _e(' Bedrooms', 'purple');?></p>
                                                </span>
								                <?php endif;?>

								                <?php if($bathrooms = get_field('bathrooms')) : ?>
                                                    <span class="block h-6 text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                     <p class="text-center text-white"><?php echo $bathrooms; ?><?php _e(' Bathrooms', 'purple');?></p>
                                                </span>

								                <?php endif;?>
                                            </div>
                                        </a>
                                    </div>
				                <?php endwhile; ?>
                            </div>

		                <?php
		                else:

		                endif;
		                wp_reset_query(); ?>

	                <?php endif; ?>
                </div>
                <div class="filter-property-features" id="form-ui">
                    <h3 class="text-3xl font-normal lg:text-4xl"><?php _e('Property Features', 'purple'); ?></h3>
		            <?php if (!empty($property_features = get_terms('property_feature' ))) :?>
                        <p>
				            <?php foreach ( $property_features as $property_feature ) :?>
                                <label class="label-container">
                                    <input type="checkbox" value=".<?php echo $property_feature->slug; ?>" />
                                    <span class="checkmark"></span>
                                    <span>
                                        <?php echo $property_feature->name; ?>
                                    </span>
                                </label>
				            <?php endforeach; ?>
                        </p>
		            <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

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
</main>


<?php get_template_part('parts/schema'); ?>

