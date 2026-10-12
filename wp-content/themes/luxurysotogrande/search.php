<?php
/**
 * Index
 *
 * Standard loop for the search result page
 */
get_header(); ?>

<?php get_template_part('parts/search-bar'); ?>

<div class="px-5 mx-auto my-10 max-w-screen-2xl lg:px-0 posts-container">
    <div class="row posts-list">
        <div class="col-12 col-sm-12">
            <!-- BEGIN of search results -->
            <h1 class="text-3xl font-normal lg:text-4xl search-bar__title">
		        <?php
		        global $wp_query;
		        $total_properties = $wp_query->found_posts; // Get total post count
		        $query_parts = [];

		        if (!empty($_GET['property_status'])) {
			        $query_parts[] = '<span>' . ucfirst(str_replace('-', ' ', $_GET['property_status'])) . '</span>';
		        }

		        if (!empty($_GET['property_area'])) {
			        $property_areas = explode(',', $_GET['property_area']);
			        $formatted_areas = array_map(function($area) {
				        return '<span>' . ucfirst(str_replace('-', ' ', $area)) . '</span>';
			        }, $property_areas);
			        $query_parts[] = implode(', ', $formatted_areas);
		        }

		        // Check if sub-property_area exists (corrected parameter name)
		        if (!empty($_GET['sub-property_area'])) {
			        $sub_property_areas = explode(',', $_GET['sub-property_area']);
			        $formatted_sub_areas = array_map(function($area) {
				        return '<span>' . ucfirst(str_replace('-', ' ', $area)) . '</span>';
			        }, $sub_property_areas);
			        $query_parts[] = 'Sub Area: ' . implode(', ', $formatted_sub_areas);
		        }

		        if (!empty($_GET['price_min']) || !empty($_GET['price_max'])) {
			        $price_min = !empty($_GET['price_min']) ? number_format($_GET['price_min']) : '0';
			        $price_max = !empty($_GET['price_max']) ? number_format((float) str_replace(['.', ','], '', $_GET['price_max'])) : 'No limit';

			        $query_parts[] = '<span>Price: ' . $price_min . ' - ' . $price_max . ' €</span>';
		        }

		        if (!empty($_GET['bedrooms'])) {
			        $query_parts[] = '<span>' . $_GET['bedrooms'] . ' Bedrooms</span>';
		        }

		        if (!empty($_GET['property_type'])) {
			        $query_parts[] = '<span>' . ucfirst(str_replace('-', ' ', $_GET['property_type'])) . '</span>';
		        }

		        if (!empty($_GET['s'])) {
			        $query_parts[] = '<span>Reference: ' . esc_html($_GET['s']) . '</span>';
		        }
				if (!empty($_GET['wpbs-search-start-date']) && !empty($_GET['wpbs-search-end-date'])) {
					$check_in = date_i18n(get_option('date_format'), strtotime($_GET['wpbs-search-start-date']));
					$check_out = date_i18n(get_option('date_format'), strtotime($_GET['wpbs-search-end-date']));
					$query_parts[] = '<span>From ' . $check_in . ' to ' . $check_out . '</span>';
				}
				
                ?>
		        <?php
		        echo sprintf(
			        __('Showing <span>%d</span> properties for: %s', 'purple'),
			        $total_properties,
			        !empty($query_parts) ? implode(', ', $query_parts) : '<span>' . __('All Properties', 'purple') . '</span>'
		        );
		        ?>

            </h1>


            <main class="main-content">
				<?php if ( have_posts() ) : ?>
                    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'parts/loop', 'teseo' ); // Post item ?>
						<?php endwhile; ?>
                    </div>
				<?php else: ?>
                    <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'default' ); ?></p>
                    <section class="contacts">
                        <div class="contacts__container">
							<?php if ( $title = get_field('title_search', 'options') ): ?>
                                <h1 class="contacts__title">
									<?php echo $title; ?>
                                </h1>
							<?php endif; ?>
                            <div class="contacts__block">
                                <div class="contacts__row">
                                    <div class="contacts__image" data-aos="zoom-in">
										<?php if($image = get_field('image_search', 'options')) : ?>
											<?php echo wp_get_attachment_image($image, 'full'); ?>
										<?php endif;?>
                                    </div>
                                    <div class="contacts__form">
                                        <div class="contacts__form-space"></div>
										<?php if ( $contact_form = get_field('contact_form_search', 'options') ): ?>
                                            <div data-aos="fade-up" data-aos-easing="ease" data-aos-delay="400">
												<?php echo do_shortcode( $contact_form ); ?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                </div>
								<?php if ( have_rows( 'columns_search', 'options' ) ): ?>
                                    <div class="contacts__columns">
                                        <div class="contacts__columns-row">
											<?php while ( have_rows( 'columns_search', 'options' ) ): the_row(); ?>
												<?php if ( $text = get_sub_field('text') ): ?>
                                                    <div class="contacts-column-text">
														<?php echo $text; ?>
                                                    </div>
												<?php endif; ?>
											<?php endwhile; ?>
                                        </div>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </section>
				<?php endif; ?>
                <!-- BEGIN of pagination -->
				<?php bootstrap_pagination(); ?>
                <!-- END of pagination -->
            </main>
        </div>
        <!-- END of search results -->
    </div>
</div>

<?php get_footer(); ?>
