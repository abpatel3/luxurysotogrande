<?php
global $post; // Get the current post/page object
$slug = $post->post_name; // Get the slug of the current page
?>
<?php if ($slug === 'holiday-rental') : ?>

	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-costa') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-22" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->
	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-playa') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-2" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->
	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-alto') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-2" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->

    <!-- Section -->
	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-puerto') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-2" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->
	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-la-reserva') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-2" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->
	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'sotogrande-marina') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl order-3 123">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-222" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					$property_area_posts_page = get_field('property_area', $child->ID);

					echo '<li class="' . basename(get_the_permalink($child->ID)) . '">';
					echo '<a href="' . get_permalink($child->ID) . '">' . get_the_title($child);
					foreach ($property_area_posts_page as $property_area_post_page) {

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
						$the_property_query = new WP_Query($args);
						echo '<span class="sub-sales-span"> (';
						echo $the_property_query->found_posts;
						echo ')</span>';
					}
					echo '</a>';
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->

	<?php
	// Get the current page
	$current_page = get_post();

	// Check if the current page has a parent
	if ($current_page->post_parent) {
		// Get the parent of the current page
		$parent_page = get_post($current_page->post_parent);

		// Check if the parent page is "sotogrande-costa"
		if ($parent_page->post_name === 'all-sotogrande') {
			// Get all children of the "sotogrande-costa" page
			$sales_children = get_pages(array(
				'child_of' => $parent_page->ID,
			));

			if (!empty($sales_children)) {
				echo '<div class="property-page-items-blocks__item bg-white shadow-xl">';
				echo '<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">';
				echo '<span class="property-page-items-blocks-title">' . get_the_title($parent_page) . '</span>';
				echo '<img width="16" height="16" alt="' . __('Arrow', 'purple') . '" src="' . get_stylesheet_directory_uri() . '/assets/images/arrow.svg">';
				echo '</button>';
				echo '<ul id="list-sale-sub-2" class="'. basename(get_the_permalink()) .'">';

				foreach ($sales_children as $child) {
					echo '<li class="'. basename(get_the_permalink($child->ID)) . '"><a href="' . get_permalink($child->ID) . '">' . get_the_title($child) . '</a></li>';
				}

				echo '</ul>';
				echo '</div>';
			}
		}
	}
	?>
    <!-- Section -->
<?php endif; ?>

<?php
// Get the current page
$current_page = get_post();
$sales_parent_page = get_page_by_path('holiday-rental');
// Check if the current page is a child of a "sales" child page
$sales_children = get_pages(array(
	'child_of' => $sales_parent_page->ID,
));

if (in_array($current_page->ID, array_map(function ($child) {
	return $child->ID;
}, $sales_children))) {
	// If the current page is a child of a "sales" child page, display its children
	$args = array(
		'post_type'      => 'page',
		'order'          => 'ASC',
		'orderby'        => 'menu_order',
		'posts_per_page' => -1,
		'post_parent'    => $current_page->ID, // Display children of the current child page
	);
	$the_query = new WP_Query($args);
	$i = 2;
	if ($the_query->have_posts()) : ?>
		<div class="property-page-items-blocks__item bg-white shadow-xl 33">
			<button id="btn-open-closed-sale-sub-2" class="btn-open-closed btn-open-closed-active">
				<span class="property-page-items-blocks-title"><?php the_title(); ?></span>
				<img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>"
				     src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
			</button>
			<ul id="list-sale-sub-2" class="">
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
                                    <span class="sub-sales-span">(<?php echo $the_property_query->found_posts; ?>)</span>
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

}
?>
