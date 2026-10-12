<?php
/**
 * Property Status
 */

get_header();
?>

<?php $term_description = term_description(); ?>

<main class="main-content">
    <section class="property-status-top">
        <div class="container-new">
            <div class="property-status-top__grid">
	            <h1><?php _e('Find your luxurious real estate right now ', 'Luxury'); ?>
                    <span>
                     <?php
                     $archive_title = get_the_archive_title();

                     if (!is_tax('property_status', 'for-sale')) {
	                     $archive_title = 'For' . $archive_title;
                     }

                     echo $archive_title;
                     ?>
                    </span>
                    <?php _e('among exclusive proposals in', 'Luxury'); ?>
                </h1>
	            <?php
	            $args = array(
		            'taxonomy'   => 'property_area',
		            'hide_empty' => true,
		            'parent'     => 0,
		            'exclude'    => 1
	            );
	            $property_area_parent_cats = get_terms( $args );?>
                <ul>
		            <?php foreach ( $property_area_parent_cats as $property_area_parent_cat ) :?>
                        <li class="property-status-top__parent-cat">
                            <a class="property-status-top__parent-cat-link" title="<?php echo $property_area_parent_cat->name; ?>" target="_self" href="<?php echo  get_term_link($property_area_parent_cat); ?>">
					            <?php echo $property_area_parent_cat->name; ?>
                            </a>
				            <?php $property_area_cats = get_terms( array(
					            'taxonomy'   => 'property_area',
					            'parent'     => $property_area_parent_cat->term_id,
					            'hide_empty' => true,
				            ) ); ?>

				            <?php if( $property_area_cats ) : ?>
                                <ul>
						            <?php foreach ( $property_area_cats as $property_area_cat ) :?>
                                        <li>
                                            <a title="<?php echo $property_area_cat->name; ?>" target="_self" href="<?php echo  get_term_link($property_area_cat); ?>">
									            <?php echo $property_area_cat->name; ?>
                                            </a>
                                            <span>,</span>
                                        </li>
						            <?php endforeach; ?>
                                </ul>
				            <?php endif;?>
                        </li>
		            <?php endforeach; ?>
                </ul>
                <div class="property-status-top__text">
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();