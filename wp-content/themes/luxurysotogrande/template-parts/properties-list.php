<?php $properties_order = get_field('properties_order'); ?>
<?php $property_status = get_field('property_status'); ?>
<?php $property_area = get_field('property_area'); ?>
<?php $property_types = get_field('property_types'); ?>

<?php if( $properties_order ): ?>

	<div class="new-isotope">
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

			$property_price = get_field('property_price', $properties_post)
			?>

			<div class="element-item <?php echo implode(' ', $property_feature_classes); ?>" data-price="<?php echo $property_price;?>">

                <div class="property-page-images">
                    <div class="property-page-image">
	                    <?php if ( $featured_img_url ) : ?>
		                    <?php echo wp_get_attachment_image($featured_img_url, 'large', false, array( 'class' => 'featured-image-block' )); ?>
	                    <?php else: ?>
                            <img class="featured-image-block" width="1280" height="800" alt="<?php _e('no-photo', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/placeholder.jpg">
	                    <?php endif;?>
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">
                    </div>
					<?php
					$images = get_field('gallery', $properties_post);
					$size = 'large'; // (thumbnail, medium, large, full or custom size)
					if( $images ):
						// Limit to first 5 images
						$images = array_slice($images, 0, 5);
						?>
						<?php foreach( $images as $image_id ): ?>
                        <div class="property-page-image">
	                        <?php echo wp_get_attachment_image( $image_id['ID'], $size, false, array( 'class' => 'featured-image-block' ) ); ?>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">
                        </div>
					<?php endforeach; ?>
					<?php endif; ?>
                </div>
                <a target="_self" title="<?php echo $title; ?>" href="<?php echo $permalink; ?>">
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
                            <span class="block text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $internal_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
		                <?php endif;?>

		                <?php if($plot_size = get_field('plot_size', $properties_post)) : ?>
                            <span class="block text-white plot-size-attr">
                                                     <?php get_template_part('template-parts/icons/tree', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $plot_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
		                <?php endif;?>

		                <?php if($bedrooms = get_field('bedrooms', $properties_post)) : ?>
                            <span class="block text-white bedrooms-size-attr">
                                                     <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                      <p class="text-center text-white"><?php echo $bedrooms; ?><?php _e(' Beds', 'purple');?></p>
                                                </span>
		                <?php endif;?>

		                <?php if($bathrooms = get_field('bathrooms', $properties_post)) : ?>
                            <span class="block text-white">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                     <p class="text-center text-white"><?php echo $bathrooms; ?><?php _e(' Baths', 'purple');?></p>
                                                </span>

		                <?php endif;?>
                    </div>
                </a>
			</div>
		<?php endforeach; ?>
	</div>
<?php else: ?>

	<?php
	$order = 'DESC';  // Default to descending
	$orderby = 'date';  // Default to sorting by date

	// Get the selected sorting option from the URL
	if (isset($_GET['order'])) {
		switch ($_GET['order']) {
			case 'newest':
				$order = 'DESC';
				$orderby = 'date';  // Sorting by date (Newest first)
				break;
			case 'latest':
				$order = 'ASC';
				$orderby = 'date';  // Sorting by date (Latest first)
				break;
			case 'title_asc':
				$order = 'ASC';
				$orderby = 'title';  // Sorting by title (A to Z)
				break;
			case 'title_desc':
				$order = 'DESC';
				$orderby = 'title';  // Sorting by title (Z to A)
				break;
			default:
				$order = 'DESC';
				$orderby = 'date';  // Default sorting by date (Newest first)
				break;
		}
	}


	$arg = array(
		'post_type'      => 'property',
		'order'          => $order,
		'orderby'        => $orderby,
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
        <div class="properties-info">
            <h2 class="properties-info__count">
                <?php echo $the_query->found_posts; ?>
                <span>
                    <?php _e('properties found', 'purple'); ?>
                </span>
            </h2>
            <div class="sort-first-latest">
                <label for="sorting-properties"><?php _e('Sorting', 'purple'); ?></label>
                <select class="select-2" name="sorting-properties" id="sorting-properties">
                    <option value="newest" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'newest'); ?>>
				        <?php _e('Newest first', 'purple'); ?>
                    </option>
                    <option value="latest" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'latest'); ?>>
				        <?php _e('Latest first', 'purple'); ?>
                    </option>
                    <option value="title_asc" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'title_asc'); ?>>
				        <?php _e('Title: A to Z', 'purple'); ?>
                    </option>
                    <option value="title_desc" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'title_desc'); ?>>
				        <?php _e('Title: Z to A', 'purple'); ?>
                    </option>
                </select>
            </div>
        </div>
		<div class="new-isotope">
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

				// Check if the post is a draft
				$post_status = get_post_status();
				if ($post_status === 'draft') {
					$property_feature_classes[] = 'draft'; // Add 'draft' class if the post is a draft
				}
				$property_price = get_field('property_price')
				?>
				<div class="element-item <?php echo implode(' ', $property_feature_classes); ?>" data-price="<?php echo $property_price;?>" data-display="<?php echo esc_attr(get_the_date('Y-m-d', get_the_ID())); ?>">
                    <div class="property-page-images">
                        <div class="property-page-image">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'large', false, array( 'class' => 'featured-image-block' )); ?>
							<?php else: ?>
                                <img class="featured-image-block" width="1280" height="800" alt="<?php _e('no-photo', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/placeholder.jpg">
							<?php endif;?>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">

                        </div>
						<?php
						$images = get_field('gallery');
						$size = 'large'; // (thumbnail, medium, large, full or custom size)
						if( $images ):
							// Limit to first 5 images
							$images = array_slice($images, 0, 5);
							?>
							<?php foreach( $images as $image_id ): ?>
                            <div class="property-page-image">
								<?php echo wp_get_attachment_image( $image_id['ID'], $size, false, array( 'class' => 'featured-image-block' ) ); ?>
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">

                            </div>
						<?php endforeach; ?>
						<?php endif; ?>
                    </div>
                    <a target="_self" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
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
                        <div class="property-page-info__excerpt">
		                    <?php
		                    $excerpt = get_the_excerpt();
		                    echo wp_trim_words($excerpt, 20, '...'); // The third argument is the custom ending (set to empty string to remove '...').
		                    ?>
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
                                <span class="block text-white icon-property-page-item-info">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $internal_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
		                    <?php endif;?>

		                    <?php if($plot_size = get_field('plot_size')) : ?>
                                <span class="block text-white icon-property-page-item-info">
                                                     <?php get_template_part('template-parts/icons/tree', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                    <p class="text-center text-white"><?php echo $plot_size['size']; ?><?php _e(' m2', 'purple');?></p>
                                                </span>
		                    <?php endif;?>

		                    <?php if($bedrooms = get_field('bedrooms')) : ?>
                                <span class="block text-white icon-property-page-item-info">
                                                     <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                      <p class="text-center text-white"><?php echo $bedrooms; ?><?php _e(' Beds', 'purple');?></p>
                                                </span>
		                    <?php endif;?>

		                    <?php if($bathrooms = get_field('bathrooms')) : ?>
                                <span class="block text-white icon-property-page-item-info">
                                                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                                     <p class="text-center text-white"><?php echo $bathrooms; ?><?php _e(' Baths', 'purple');?></p>
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