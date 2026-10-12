<?php
/**
 * Renders the content section template
 */


?>


<?php $section_id = get_sub_field('section_id'); ?>

<section class="featured-properties-section" id="<?php echo $section_id; ?>">
    <div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
	    <?php if ( $text = get_sub_field('title') ): ?>
            <h2 class="featured-properties-section__title">
			    <?php echo $text; ?>
            </h2>
	    <?php endif; ?>
	    <?php
	    $properties = get_sub_field('properties');
	    if( $properties ): ?>
            <div class="flex-3">
			    <?php foreach( $properties as $property ):
				    $custom_field = get_field( 'field_name', $property->ID ); ?>
				    <?php
                    // Get the reduced price if set
				    $reduced_price = get_field('property_reduced_price', $property->ID);
				    // Check if property is sold
				    $is_sold = get_field('sold', $property->ID);
				    // Check if property is rented
				    $is_rented = get_field('rented', $property->ID);
				    // Check to see if property is holiday rental
				    $is_holiday_rental = isset($terms[0]) && in_array($terms[0]->slug, ['holiday-rental', 'holiday-rental-es']);
				    // Check to see if property is for sale
				    $is_for_sale = isset($terms[0]) && in_array($terms[0]->slug, ['for-sale', 'for-sale-es']);
				    ?>
				    <?php $price = get_field('property_price', $property->ID); ?>
				    <?php $property_id = get_field('property_id', $property->ID); ?>
				    <?php $bedrooms = get_field('bedrooms', $property->ID); ?>
				    <?php $bathrooms = get_field('bathrooms', $property->ID); ?>
				    <?php $plot_size = get_field('plot_size', $property->ID);
				    $slug = get_post_field('post_name', $property->ID);
				    $poa = get_field('poa', $property->ID); ?>
                    <div class="property-item-new property-<?php echo esc_attr($slug); ?>">
                        <div class="card-top">
                            <div class="property-item-new-image">
							    <?php if ( has_post_thumbnail($property->ID) ) : ?>
                                    <a target="_self" href="<?php the_permalink($property->ID)?>">
									    <?php echo wp_get_attachment_image(get_post_thumbnail_id($property->ID), 'large', false, array( 'class' => 'featured-image-block' )); ?>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">

                                    </a>
							    <?php else: ?>
                                    <a target="_self" href="<?php the_permalink($property->ID)?>">
                                        <img class="featured-image-block" width="1280" height="800" alt="<?php _e('no-photo', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/placeholder.jpg">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">

                                    </a>
							    <?php endif;?>
                            </div>
						    <?php
						    $images = get_field('gallery',$property->ID);
						    $size = 'large'; // (thumbnail, medium, large, full or custom size)
						    if( $images ):
							    // Limit to first 5 images
							    $images = array_slice($images, 1, 5);
							    ?>
							    <?php foreach( $images as $image_id ): ?>
                                <div class="property-page-image">
                                    <a target="_self" href="<?php the_permalink($property->ID)?>">
									    <?php echo wp_get_attachment_image( $image_id['ID'], $size, false, array( 'class' => 'featured-image-block' ) ); ?>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">
                                    </a>
                                </div>
						    <?php endforeach; ?>
						    <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <a target="_self" href="<?php the_permalink($property->ID)?>">
                                <div class="card-body__top">
								    <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status', $property->ID ) ) : ?>
									    <?php $property_reduced_price = get_field('property_reduced_price', $property->ID); ?>
									    <?php if ($poa) : ?>
                                            <h4 class="card-body__top-price">
                                                <p class="normal-price">
												    <?php _e('POA', 'purple'); ?>
                                                </p>
                                            </h4>
									    <?php else: ?>
										    <?php if ($price) : ?>
                                                <h4 class="card-body__top-price <?php echo $property_reduced_price == true ? 'card-body__top-price-reduced' : ''; ?>">
                                                    <p class="normal-price"><?php echo number_format($price, 0, '.', '.'); ?> <span>€</span></p>
												    <?php if ($property_reduced_price) : ?>
                                                        <p>
														    <?php echo number_format($property_reduced_price, 0, '.', '.'); ?> <span>€</span>
                                                        </p>
												    <?php endif; ?>
                                                </h4>
										    <?php endif; ?>
									    <?php endif; ?>
								    <?php endif; ?>
								    <?php if ($property_id) : ?>
                                        <h5 class="card-body__top-id">
										    <?php echo $property_id; ?>
                                        </h5>
								    <?php endif; ?>
                                </div>
                                <h2 class="card-body__title"><?php echo get_the_title($property->ID); ?></h2>
                                <div class="card-body__excerpt">
								    <?php
								    $excerpt = get_the_excerpt($property->ID);
								    echo wp_trim_words($excerpt, 22, '...'); // The third argument is the custom ending (set to empty string to remove '...').
								    ?>
                                </div>
                            </a>
                        </div>
                        <div class="card-body__bottom">
						    <?php if ($bedrooms) : ?>
                                <div class="card-body__bottom-bed">
                                    <span><?php _e('Beds:', 'purple'); ?></span>
								    <?php echo $bedrooms; ?>
                                </div>
						    <?php endif; ?>
						    <?php if ($bathrooms) : ?>
                                <div class="card-body__bottom-bed">
                                    <span><?php _e('Baths:', 'purple'); ?></span>
								    <?php echo $bathrooms; ?>
                                </div>
						    <?php endif; ?>

						    <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status', $property->ID ) ) : ?>
							    <?php if( have_rows('internal_size', $property->ID) ): ?>
								    <?php while( have_rows('internal_size', $property->ID) ): the_row();
									    $size = get_sub_field('size');
									    $postfix_text = get_sub_field('postfix_text'); ?>
									    <?php if ($size) : ?>
                                            <div class="card-body__size">
                                                <span><?php _e('Internal:', 'purple'); ?></span>
											    <?php echo $size; ?>
											    <?php if ($postfix_text) : ?>
												    <?php echo $postfix_text; ?>
											    <?php endif; ?>
                                            </div>
									    <?php endif; ?>
								    <?php endwhile; ?>
							    <?php endif; ?>
						    <?php endif; ?>

						    <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status', $property->ID ) ) : ?>
							    <?php if( have_rows('plot_size', $property->ID) ): ?>
								    <?php while( have_rows('plot_size', $property->ID) ): the_row();
									    $size = get_sub_field('size');
									    $postfix_text = get_sub_field('postfix_text'); ?>
									    <?php if ($size) : ?>
                                            <div class="card-body__size">
                                                <span><?php _e('Plot:', 'purple'); ?></span>
											    <?php echo $size; ?>
											    <?php if ($postfix_text) : ?>
												    <?php echo $postfix_text; ?>
											    <?php endif; ?>
                                            </div>
									    <?php endif; ?>
								    <?php endwhile; ?>
							    <?php endif; ?>
						    <?php endif; ?>

                        </div>
                    </div>
			    <?php endforeach; ?>
            </div>
	    <?php endif; ?>
    </div>
</section>
