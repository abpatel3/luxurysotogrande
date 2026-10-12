<?php
// Get the current page
$current_page = get_post();

// Check if the current page is a child page of "sales"
$parent_pages = get_ancestors($current_page->ID, 'page');

if (is_array($parent_pages) && in_array('holiday-rental', array_map(function ($parent_id) {
		$parent_post = get_post($parent_id);
		return $parent_post->post_name;
	}, $parent_pages))) {
	// If the current page is a child of "sales," get the children of "sales"
	$sales_parent_page = get_page_by_path('holiday-rental');

	$args = array(
		'post_type'      => 'page',
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'posts_per_page' => -1,
		'post_parent'    => $sales_parent_page->ID, // Display child pages of the "sales" parent page
	);
	$the_query = new WP_Query($args);
	$i = 1;
	if ($the_query->have_posts()) : ?>
		<?php
		global $post;
		$current_page_slug = $post->post_name;
		// Get the current page's slug
		$current_page_slug = get_post_field('post_name');

		// Define a default class
		$default_class = 'default-class';

		// Check if the current page slug is not empty
		if (!empty($current_page_slug)) {
			// Use the current page slug as the class
			$ul_class = $current_page_slug;
		} else {
			// If the slug is empty, use the default class
			$ul_class = $default_class;
		}
		?>
		<?php
		// Get the current page's URL
		$current_url = get_permalink();

		// Parse the URL to extract the parts
		$url_parts = parse_url($current_url);

		// Get the host (domain)
		$domain = $url_parts['host'];

		// Get the path and remove the leading slash
		$path = ltrim($url_parts['path'], '/');

		// Split the path into segments
		$path_segments = explode('/', $path);

		// Extract and display the desired parts
		$domain_part = $domain;
		$sales_part = isset($path_segments[0]) ? $path_segments[0] : '';
		$slug_part = isset($path_segments[1]) ? $path_segments[1] : '';


		?>
		<div class="property-page-items-blocks__item bg-white shadow-xl 454">
			<button id="btn-open-closed-sale-sub-1" class="btn-open-closed btn-open-closed-active">
				<span class="property-page-items-blocks-title"><?php _e('Property Area', 'purple'); ?></span>
				<img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>"
				     src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
			</button>
			<ul id="list-sale-sub-1" class="<?php echo $current_page_slug; ?> <?php echo $slug_part; ?>">
				<?php while ($the_query->have_posts()) :
					$the_query->the_post(); ?>
					<?php $property_area_posts_page = get_field('property_area', get_the_ID());

					?>
					<li class="<?php echo basename(get_the_permalink()); ?>">
                        <a href="<?php the_permalink(); ?>">
							<?php the_title(); ?>
                            <span>
                                <?php foreach( $property_area_posts_page as $property_area_post_page ): ?>

	                                <?php
	                                // Modify the query to include tax_query for both 'property_status' and 'property_area'
	                                $args = array(
		                                'post_type' => 'property', // Change 'post' to your custom post type if needed
		                                'posts_per_page' => -1, // Display all posts
		                                'tax_query' => array(
			                                'relation' => 'AND', // Ensure both taxonomies are met
			                                array(
				                                'taxonomy' => 'property_status',
				                                'field' => 'slug',
				                                'terms' => 'holiday-rental'
			                                ),
			                                array(
				                                'taxonomy' => 'property_area',
				                                'field' => 'term_id',
				                                'terms' => $property_area_post_page->term_id // Use the term ID of 'property_area'
			                                )
		                                )
	                                );
	                                $the_property_query = new WP_Query($args); ?>
                                    (<?php echo $the_property_query->found_posts; ?>)
                                <?php endforeach; ?>
                            </span>
                        </a>
					</li>
					<?php $i++; ?>
				<?php endwhile; ?>
			</ul>
		</div>
	<?php endif;
	wp_reset_query();
} else {
	// Get the current page ID
	$current_page_id = get_the_ID();

	// Check if the current page is the "sales" parent page
	$sales_parent_page = get_page_by_path('holiday-rental');

	if ($sales_parent_page && $sales_parent_page->ID == $current_page_id) {
		// If the current page is the "sales" parent page, get its children
		$args      = array(
			'post_type'      => 'page',
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'posts_per_page' => - 1,
			'post_parent'    => $current_page_id, // Display child pages of the current page
		);
		$the_query = new WP_Query( $args );
		$i         = 1;
		if ( $the_query->have_posts() ) : ?>
			<?php
			global $post;
			$current_page_slug = $post->post_name;
			// Get the current page's slug
			$current_page_slug = get_post_field('post_name');

			// Define a default class
			$default_class = 'default-class';

			// Check if the current page slug is not empty
			if (!empty($current_page_slug)) {
				// Use the current page slug as the class
				$ul_class = $current_page_slug;
			} else {
				// If the slug is empty, use the default class
				$ul_class = $default_class;
			}
			?>
			<?php
			// Get the current page's URL
			$current_url = get_permalink();

			// Parse the URL to extract the parts
			$url_parts = parse_url($current_url);

			// Get the host (domain)
			$domain = $url_parts['host'];

			// Get the path and remove the leading slash
			$path = ltrim($url_parts['path'], '/');

			// Split the path into segments
			$path_segments = explode('/', $path);

			// Extract and display the desired parts
			$domain_part = $domain;
			$sales_part = isset($path_segments[0]) ? $path_segments[0] : '';
			$slug_part = isset($path_segments[1]) ? $path_segments[1] : '';


			?>
			<div class="property-page-items-blocks__item bg-white shadow-xl">
				<button id="btn-open-closed-sale-sub-1" class="btn-open-closed btn-open-closed-active">
					<span class="property-page-items-blocks-title"><?php _e( 'Property Area', 'purple' ); ?></span>
					<img width="16" height="16" alt="<?php _e( 'Arrow', 'purple' ); ?>"
					     src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
				</button>
				<ul id="list-sale-sub-1" class="<?php echo $current_page_slug; ?> <?php echo $slug_part; ?>">
					<?php while ( $the_query->have_posts() ) :
						$the_query->the_post(); ?>
						<?php $property_area_posts_page = get_field('property_area', get_the_ID());

						?>
						<li class="<?php echo basename(get_the_permalink()); ?>">
                            <a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
                                <span>
                                <?php foreach( $property_area_posts_page as $property_area_post_page ): ?>

	                                <?php
	                                // Modify the query to include tax_query for both 'property_status' and 'property_area'
	                                $args = array(
		                                'post_type' => 'property', // Change 'post' to your custom post type if needed
		                                'posts_per_page' => -1, // Display all posts
		                                'tax_query' => array(
			                                'relation' => 'AND', // Ensure both taxonomies are met
			                                array(
				                                'taxonomy' => 'property_status',
				                                'field' => 'slug',
				                                'terms' => 'holiday-rental'
			                                ),
			                                array(
				                                'taxonomy' => 'property_area',
				                                'field' => 'term_id',
				                                'terms' => $property_area_post_page->term_id // Use the term ID of 'property_area'
			                                )
		                                )
	                                );
	                                $the_property_query = new WP_Query($args); ?>
                                    (<?php echo $the_property_query->found_posts; ?>)
                                <?php endforeach; ?>
                            </span>
                            </a>
						</li>
						<?php $i ++; ?>
					<?php endwhile; ?>
				</ul>
			</div>
		<?php endif;
		wp_reset_query();
	}
}
?>


