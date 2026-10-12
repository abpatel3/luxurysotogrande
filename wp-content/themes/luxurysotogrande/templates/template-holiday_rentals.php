<?php
/**
 * Template Name: Holiday Rentals Page
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

<section class="new-search-widget">
    <div class="max-w-6xl px-5 py-5 mx-auto lg:px-0">
	    <?php echo do_shortcode('[wpbs-search calendars="all" language="auto" start_day="1" title="yes" mark_selection="yes" selection_type="multiple" minimum_stay="0" featured_image="yes" starting_price="yes" results_layout="list" results_per_page="10" redirect=""]'); ?>
    </div>
</section>


<main class="rental-properties-main max-w-6xl px-5 py-20 mx-auto lg:px-0">
    <div class="rental-properties-main__grid">
	    <?php
	    $properties = get_field('properties');
	    if( $properties ): ?>
            <div class="properties-items filtered-properties">
			    <?php foreach( $properties as $properties_item ):
				    $permalink = get_permalink( $properties_item );
				    $title = get_the_title( $properties_item );
				    $gallery = get_field('gallery', $properties_item );
				    $featured_img = get_post_thumbnail_id($properties_item,'full');
				    $subtitle = get_field('subtitle', $properties_item);
				    $property_id = get_field('property_id', $properties_item);
				    $bedrooms = get_field('bedrooms', $properties_item);
			        $poa = get_field('poa', $properties_item);
				    $bathrooms = get_field('bathrooms', $properties_item); ?>
                    <div class="properties-item properties-item-column" data-aos="fade-up">
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
	                                    <?php if ($poa) : ?>
                                            <h3 class="text-xl text-right uppercase">POA</h3>
	                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 space-y-2 text-white bg-lsg-gold">
                                <div class="grid justify-between  grid-cols-2 gap-5 text-sm font-medium uppercase">
                                    <div class="text-center">
                                    <span class="block text-white">
                                        <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                    </span>
                                        <p class="text-center"><?php echo $bedrooms . ' ' . label('bedrooms_label'); ?></p>
                                    </div>
                                    <div class="text-center">
                                    <span class="block text-white">
                                        <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                                    </span>
                                        <p class="text-center"><?php echo $bathrooms . ' ' . label('bathrooms_label'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
			    <?php endforeach; ?>
            </div>
            <div class="container text-center">
                <a href="" class="load-more load-more-my inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold">
		            <?php _e('Load More', 'purple')?>
                </a>
            </div>

	    <?php endif; ?>

    </div>
</main>


<?php get_footer(); ?>

