<?php
/**
 * Property Featured
 */

get_header();
?>

<?php $term_description = term_description(); ?>
<?php $bg = get_field('background_image_property', 'options'); ?>
<?php $image_default = get_the_post_thumbnail_url(); ?>
<?php $property_status = get_field('property_status'); ?>
<?php $property_area = get_field('property_area'); ?>
<?php $property_types = get_field('property_types'); ?>

	<main class="main-content-property property-feature-cat-main">
        <section class="property-page-top">
			<?php if ( $bg ) : ?>
				<?php echo wp_get_attachment_image($bg, 'full', false, array( 'class' => 'property-page-top-img skip-lazy' ) ); ?>
			<?php endif; ?>
            <div class="property-page-top-overlay"></div>
            <div class="container-new-full">
                <div class="property-page-top__block">
                    <h1 class="property-page-top__block-title">
	                    <?php echo get_the_archive_title(); ?>
                    </h1>
                    <div class="property-page-top__block-links">
                        <a class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold" href="/sales/">
	                        <?php _e('All Sales', 'purple'); ?>
                        </a>
                        <a class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold" href="/holiday-rentals/">
                            <?php _e('All Rentals', 'purple'); ?>
                        </a>
                    </div>
	                <?php if($term_description) : ?>
                        <div class="property-page-top__text">
                            <?php echo $term_description; ?>
                        </div>
	                <?php endif;?>
                </div>
            </div>
        </section>

        <section class="property-page-items bg-lsg-gray">
            <div class="container-new">
                <div class="property-page-items__grid">
                    <div class="property-page-items__properties">
                        <h3 class="text-3xl font-normal lg:text-4xl"><?php _e('Properties', 'purple'); ?></h3>
	                    <?php $current_category = get_queried_object(); // Get the current category ?>

	                    <?php $arg = array(
		                    'post_type'      => 'property',
		                    'order'          => 'DESC',
		                    'orderby'        => 'date',
		                    'posts_per_page' => - 1,
		                    'tax_query' => array(
			                    array (
				                    'taxonomy' => 'property_feature',
				                    'field' => 'term_id',
				                    'terms' => $current_category->term_id,
			                    )
		                    ),
	                    );
	                    $the_query = new WP_Query( $arg );
	                    if ( $the_query->have_posts() ) : ?>
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
                            <div class="property-feature-cat-grid">
			                    <?php while ( $the_query->have_posts() ) :
			                    $the_query->the_post(); ?>
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
                                                    <p class="property-page-info-id">
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
	                    <?php endif;
	                    wp_reset_query(); ?>

                    </div>
                </div>
            </div>
        </section>



    </main>

<?php
get_footer();