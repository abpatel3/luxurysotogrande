<?php
/**
 * Template Name: Search Properties
 * Template Post Type: page
 */

// Get the search query
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Get other search parameters
$property_status = isset($_GET['property_status']) ? sanitize_text_field($_GET['property_status']) : '';
$property_type = isset($_GET['property_type']) ? sanitize_text_field($_GET['property_type']) : '';
$property_price = isset($_GET['property_price']) ? intval($_GET['property_price']) : 1000000; // Default to 1000000 if not provided

// Modify the WordPress query based on search parameters
$args = array(
	'post_type' => 'property',
	'posts_per_page' => -1, // Retrieve all posts matching the query
	's' => $search_query, // Search query
	'meta_query' => array(
		'relation' => 'AND', // Match all conditions
		array(
			'key' => 'property_status',
			'value' => $property_status,
			'compare' => '=',
		),
		array(
			'key' => 'property_type',
			'value' => $property_type,
			'compare' => '=',
		),
		array(
			'key' => 'property_price',
			'value' => $property_price,
			'compare' => '<=', // Match properties with price less than or equal to the provided price
			'type' => 'numeric',
		),
	),
);

// Perform the query
$search_results = new WP_Query($args);

// Include the header template
get_header();
?>

<!-- Display the search results -->
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <h2>Search Results for: <?php echo esc_html($search_query); ?></h2>
		<?php if ($search_results->have_posts()) : ?>
            <ul>
				<?php while ($search_results->have_posts()) : $search_results->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
            </ul>
		<?php else : ?>
            <p>No search results found.</p>
		<?php endif; ?>
    </main>
</div>

<?php
// Include the footer template
get_footer();
?>
