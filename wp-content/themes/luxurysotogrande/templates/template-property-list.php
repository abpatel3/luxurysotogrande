<?php
/**
 * Template Name: Property List Page
 */
get_header(); ?>


<!-- Add the "noindex" meta tag -->
<meta name="robots" content="noindex">



<main class="main-content-list-property">
	<div class="container">
		<div class="sales-page">
            <h2 class="mb-4 text-xl uppercase text-lsg-gold"><?php _e("Sales Pages", 'purple');?></h2>
			<?php
			// Create a WP Query to get the Sales page and its children
			$args = array(
				'name' => 'sales', // Replace with the actual slug of your Sales page
				'post_type' => 'page',
				'posts_per_page' => 1,
			);
			$query = new WP_Query($args);

			if ($query->have_posts()) {
				$parentNumber = 1;
				while ($query->have_posts()) {
					$query->the_post();
					// Display the Sales Page with a link and number
					echo '<p>' . $parentNumber . '. <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
					$parentNumber++;

					// Get the children of the Sales Page
					$children_args = array(
						'post_parent' => get_the_ID(),
						'post_type' => 'page',
						'posts_per_page' => -1,
						'order' => 'ASC', // Order the children by title
						'orderby' => 'title',
					);
					$children_query = new WP_Query($children_args);

					if ($children_query->have_posts()) {
						$childNumber = 1;
						echo '<ul class="first-level-ul">'; // Start an unordered list for children
						while ($children_query->have_posts()) {
							$children_query->the_post();
							// Display the children of the Sales Page with a link and number
							echo '<li>' . $parentNumber . '.' . $childNumber . ' <a href="' . get_permalink() . '">' . get_the_title() . '</a>';

							// Get the grandchildren of the current child
							$grandchildren_args = array(
								'post_parent' => get_the_ID(),
								'post_type' => 'page',
								'posts_per_page' => -1,
								'order' => 'ASC', // Order the grandchildren by title
								'orderby' => 'title',
							);
							$grandchildren_query = new WP_Query($grandchildren_args);

							if ($grandchildren_query->have_posts()) {
								$grandchildNumber = 1;
								echo '<ul class="second-level-ul">'; // Start an unordered list for grandchildren
								while ($grandchildren_query->have_posts()) {
									$grandchildren_query->the_post();
									// Display the grandchildren of the current child with a link and number
									echo '<li>' . $parentNumber . '.' . $childNumber . '.' . $grandchildNumber . ' <a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
									$grandchildNumber++;
								}
								echo '</ul>'; // End the unordered list for grandchildren
							}

							echo '</li>';
							$childNumber++;
						}
						echo '</ul>'; // End the unordered list for children
					}
				}
				wp_reset_postdata(); // Restore the original query
			}
			?>

        </div>


        <div class="holiday-rental-page">
            <h2 class="mb-4 text-xl uppercase text-lsg-gold"><?php _e("Rental Pages", 'purple');?></h2>
			<?php
			// Create a WP Query to get the Sales page and its children
			$args = array(
				'name' => 'holiday-rental', // Replace with the actual slug of your Sales page
				'post_type' => 'page',
				'posts_per_page' => 1,
			);
			$query = new WP_Query($args);

			if ($query->have_posts()) {
				$parentNumber = 2;
				while ($query->have_posts()) {
					$query->the_post();
					// Display the Sales Page with a link and number
					echo '<p>' . $parentNumber . '. <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
					$parentNumber++;

					// Get the children of the Sales Page
					$children_args = array(
						'post_parent' => get_the_ID(),
						'post_type' => 'page',
						'posts_per_page' => -1,
						'order' => 'ASC', // Order the children by title
						'orderby' => 'title',
					);
					$children_query = new WP_Query($children_args);

					if ($children_query->have_posts()) {
						$childNumber = 1;
						echo '<ul class="first-level-ul">'; // Start an unordered list for children
						while ($children_query->have_posts()) {
							$children_query->the_post();
							// Display the children of the Sales Page with a link and number
							echo '<li>' . $parentNumber . '.' . $childNumber . ' <a href="' . get_permalink() . '">' . get_the_title() . '</a>';

							// Get the grandchildren of the current child
							$grandchildren_args = array(
								'post_parent' => get_the_ID(),
								'post_type' => 'page',
								'posts_per_page' => -1,
								'order' => 'ASC', // Order the grandchildren by title
								'orderby' => 'title',
							);
							$grandchildren_query = new WP_Query($grandchildren_args);

							if ($grandchildren_query->have_posts()) {
								$grandchildNumber = 1;
								echo '<ul class="second-level-ul">'; // Start an unordered list for grandchildren
								while ($grandchildren_query->have_posts()) {
									$grandchildren_query->the_post();
									// Display the grandchildren of the current child with a link and number
									echo '<li>' . $parentNumber . '.' . $childNumber . '.' . $grandchildNumber . ' <a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
									$grandchildNumber++;
								}
								echo '</ul>'; // End the unordered list for grandchildren
							}

							echo '</li>';
							$childNumber++;
						}
						echo '</ul>'; // End the unordered list for children
					}
				}
				wp_reset_postdata(); // Restore the original query
			}
			?>

        </div>
	</div>



</main>


<?php get_footer(); ?>

