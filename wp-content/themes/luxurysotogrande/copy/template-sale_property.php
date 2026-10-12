<?php
/**
 * Template Name: Sale(COPY) Properties Page
 */
get_header(); ?>


<!-- Add the "noindex" meta tag -->

<?php $bg = get_field('background_image_property', 'options'); ?>
<?php $image_default = get_the_post_thumbnail_url(); ?>
<?php $property_status = get_field('property_status'); ?>
<?php $property_area = get_field('property_area'); ?>
<?php $property_types = get_field('property_types'); ?>


<main class="main-content-property">
	<!-- Section -->
	<section class="property-page-top">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'full', array( 'class' => 'property-page-top-img skip-lazy' ) ); ?>
		<?php else: ?>
			<?php echo wp_get_attachment_image($bg, 'full', false, array( 'class' => 'property-page-top-img skip-lazy' ) ); ?>
		<?php endif; ?>
		<div class="property-page-top-overlay"></div>
		<div class="container-new-full">
			<div class="property-page-top__block">
				<h1 class="property-page-top__block-title">
					<?php _e(' properties', 'purple'); ?>
					<?php if($property_status) : ?>
						<?php if($property_status->slug !== 'for-sale') : ?>
							<span class="property-page-top__block-title-type"><?php _e('for ', 'purple'); ?>
								<?php if($property_status) : ?>
									<?php echo $property_status->name; ?>
								<?php endif;?>
                        </span>
						<?php else:?>
							<span class="property-page-top__block-title-type">
                            <?php if($property_status) : ?>
	                            <?php echo $property_status->name; ?>
                            <?php endif;?>
                        </span>
						<?php endif; ?>
					<?php else: ?>
						<?php _e('For Sale or Rental', 'purple'); ?>
					<?php endif;?>

					<?php if($property_area) : ?>
						<p class="property-page-top__block-title-area">
							<?php foreach ($property_area as $property_area_item ): ?>
								<span class="property-page-top__block-title-area-parent">
                                    <?php _e('in <span> </span>', 'Luxury'); ?>
									<?php echo $property_area_item->name; ?>
                                    <span class="coma">,</span>
                                 </span>
							<?php endforeach;?>
						</p>
					<?php else: ?>
						<p class="property-page-top__block-title-area"><?php _e('Sotogrande','purple'); ?></p>
					<?php endif; ?>
				</h1>
				<div class="property-page-top__text">
					<h2>
						<?php _e('Welcome to the luxurious world of real estate ', 'purple'); ?>
						<?php if($property_status) : ?>
							<?php if($property_status->slug !== 'for-sale') : ?>
								<span class="property-page-top__text-status"><?php _e('for ', 'purple'); ?>
									<?php if($property_status) : ?>
										<?php echo $property_status->name; ?>
									<?php endif;?>
                        </span>
							<?php else:?>
								<span class="property-page-top__text-status">
                            <?php if($property_status) : ?>
	                            <?php echo $property_status->name; ?>
                            <?php endif;?>
                        </span>
							<?php endif; ?>
						<?php else: ?>
							<?php _e('For Sale or Rental', 'purple'); ?>
						<?php endif;?>
						<?php _e('in <strong>Sotogrande</strong>, with its tranquil coastline and untouched beauty in the <strong>heart of Costa del Sol</strong>. We offer you exclusive opportunities in<span> </span>', 'purple'); ?>

						<?php if($property_area) : ?>
							<p class="property-page-top__text-area">
								<?php foreach ($property_area as $property_area_item ): ?>
									<span class="property-page-top__text-area-parent">
                                     <?php echo $property_area_item->name; ?>
                                    <span class="coma">,</span>
                                 </span>
								<?php endforeach;?>
							</p>
						<?php else: ?>
							<p class="property-page-top__text-area"><?php _e('Sotogrande ,','purple'); ?></p>
						<?php endif; ?>
						<?php _e('including villas, apartments, and plots where dreams become reality. Start your journey to luxury and comfort right now – contact us.', 'purple'); ?>
					</h2>
				</div>
			</div>
		</div>
	</section>


	<section class="filters-sort">
		<button class="openFilter">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/filter-solid.svg" width="16" height="16" alt="filter">
		</button>
		<div class="filters-sort__container">
			<div class="filters-sort__row">
				<div class="filters-sort__links">
					<div class="filters-sort__links-block">
						<!-- Filters -->
						<?php
						$property_status_cats = get_field('property_status_cats', 'options');
						if( $property_status_cats ): ?>
							<div class="filters-sort__status filters-sort-filters">
								<label for="property-status-terms"><?php _e('Property Status', 'purple'); ?></label>
								<select class="select-2" name="property-status-terms" id="property-status-terms" onchange="document.location.href=this.value">
									<?php foreach( $property_status_cats as $property_status_cats_page ):
										$permalink = get_permalink( $property_status_cats_page->ID );
										$title = get_the_title( $property_status_cats_page->ID );
										$slug = basename($permalink);
										?>
										<option value="<?php echo $permalink; ?>">
											<?php echo esc_html($title); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
						<!-- Filters -->

						<?php
						$sale_page_options = get_field('sale_page', 'options');
						$sales_pages_id = $sale_page_options;
						$args = array(
							'post_type'      => 'page',  // Only retrieve pages
							'post_parent'    => $sales_pages_id,  // Get child pages of the current page
							'posts_per_page' => -1,  // No limit to the number of pages
							'post_status'    => 'publish',  // Only published pages
							'orderby'        => 'title',       // Order by title (alphabetical)
							'order'          => 'ASC',         // Ascending order
						);
						$sale_child_pages = new WP_Query($args);
						?>

						<?php if($sale_child_pages->have_posts()) : ?>
							<div class="filters-sort__area filters-sort-filters">
								<label for="property-filter-area-terms"><?php _e('Property Area', 'purple'); ?></label>
								<select class="select-2" name="property-filter-area-terms" id="property-filter-area-terms" onchange="document.location.href=this.value">
									<option>
										<?php
										global $post;

										// Get the current page's parent ID
										$parent_id = wp_get_post_parent_id($post->ID);
										$level = 0;

										// Loop to calculate the hierarchy level
										while ($parent_id) {
											$level++;
											$parent_id = wp_get_post_parent_id($parent_id);
										}

										// Conditional checks based on the hierarchy level
										if ($level == 1) :
											// Output the current page's title if it's at level 1
											echo esc_html(get_the_title($post->ID));
										elseif ($level == 2) :
											// Output the parent page's title if it's at level 2
											$parent_id = wp_get_post_parent_id($post->ID);
											echo esc_html(get_the_title($parent_id));
										else :


										endif;
										?>
									</option>

									<?php while ($sale_child_pages->have_posts()) : $sale_child_pages->the_post(); ?>
										<option title="" value="<?php echo esc_attr(get_permalink()); ?>">
											<?php echo esc_html(get_the_title()); ?>
											<?php $property_area_posts_page = get_field('property_area', get_the_ID()); ?>
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
															'terms' => 'for-sale'
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
										</option>
									<?php endwhile; ?>
								</select>
							</div>
							<?php wp_reset_postdata(); ?>
						<?php endif; ?>
						<!-- Filters -->
						<?php
						$parent_id = wp_get_post_parent_id($post->ID);
						$level = 0;

						while ($parent_id) {
							$level++;
							$parent_id = wp_get_post_parent_id($parent_id);
						}

						if ($level == 1) : ?>
							<?php
							// Get child pages of the current page
							$parent_page_id = get_the_ID(); // Get the ID of the current page
							$args = array(
								'post_type'      => 'page',  // Only retrieve pages
								'post_parent'    => $parent_page_id,  // Get child pages of the current page
								'posts_per_page' => -1,  // No limit to the number of pages
								'post_status'    => 'publish',  // Only published pages
								'orderby'        => 'title',       // Order by title (alphabetical)
								'order'          => 'ASC',         // Ascending order
							);
							$child_pages = new WP_Query($args);
							?>

							<?php if($child_pages->have_posts()) : ?>
								<div class="filters-sort__area filters-sort-filters">
									<label for="child-property-filter-area-terms"><?php _e('Sub-Area', 'purple'); ?></label>
									<select class="select-2" name="child-property-filter-area-terms" id="child-property-filter-area-terms" onchange="document.location.href=this.value">

										<?php while ($child_pages->have_posts()) : $child_pages->the_post(); ?>
											<option title="" value="<?php echo esc_attr(get_permalink()); ?>">

												<?php
												$property_area_posts_page_child = get_field('property_area', get_the_ID());

												if (!empty($property_area_posts_page_child)) :
													foreach ($property_area_posts_page_child as $property_area_post_page):

														// Check if the term is a level 2 child
														$term = get_term($property_area_post_page->term_id);
														if ($term && $term->parent !== 0): // Only process terms that have a parent (level 2+)

															$parent_term = get_term($term->parent);
															if ($parent_term && $parent_term->parent === 0): // Ensure its parent is a level 1 term (making this term level 2)

																// WP_Query arguments
																$args = array(
																	'post_type' => 'property', // Your custom post type
																	'posts_per_page' => -1, // Display all posts
																	'tax_query' => array(
																		'relation' => 'AND',
																		array(
																			'taxonomy' => 'property_status',
																			'field'    => 'slug',
																			'terms'    => 'for-sale', // Filter by 'for-sale' term in 'property_status'
																		),
																		array(
																			'taxonomy' => 'property_area',
																			'field'    => 'term_id',
																			'terms'    => $property_area_post_page->term_id, // Use the term ID
																		)
																	),
																);

																// Query posts
																$the_property_query_child = new WP_Query($args); ?>

																<!-- Output the term name and post count -->
																<?php echo esc_html($term->name); ?> (<?php echo esc_html($the_property_query_child->found_posts); ?>)
																<?php wp_reset_postdata(); // Always reset post data ?>
															<?php endif;
														endif;
													endforeach;
												endif;
												?>
											</option>
										<?php endwhile; ?>
									</select>
								</div>
								<?php wp_reset_postdata(); ?>
							<?php endif; ?>
						<?php elseif ($level == 2) :?>
							<?php
							// Get the current page ID
							$current_page_id = get_the_ID();

							// Get the parent page ID
							$parent_page_id = wp_get_post_parent_id($current_page_id);

							// If the current page has a parent, use that as the parent ID
							if (!$parent_page_id) {
								// If the page has no parent (it's a top-level page), use the current page's ID
								$parent_page_id = $current_page_id;
							}

							// Set up the query to get child pages
							$args = array(
								'post_type'      => 'page',   // Only retrieve pages
								'post_parent'    => $parent_page_id,  // Get child pages of the parent page
								'posts_per_page' => -1,       // No limit to the number of pages
								'post_status'    => 'publish',  // Only published pages
								'orderby'        => 'title',   // Order by title (alphabetical)
								'order'          => 'ASC',     // Ascending order
							);

							// Execute the query to get child pages
							$child_pages = new WP_Query($args);

							if ($child_pages->have_posts()) : ?>
								<div class="filters-sort__area filters-sort-filters">
									<label for="child-property-filter-area-terms"><?php _e('Sub-Area', 'purple'); ?></label>
									<select class="select-2" name="child-property-filter-area-terms" id="child-property-filter-area-terms" onchange="document.location.href=this.value">
										<option>
											<?php the_title(); ?>
										</option>
										<?php while ($child_pages->have_posts()) : $child_pages->the_post(); ?>
											<option value="<?php echo esc_attr(get_permalink()); ?>">

												<?php
												$property_area_posts_page_child = get_field('property_area', get_the_ID());

												if (!empty($property_area_posts_page_child)) :
													foreach ($property_area_posts_page_child as $property_area_post_page):

														// Check if the term is a level 2 child
														$term = get_term($property_area_post_page->term_id);
														if ($term && $term->parent !== 0): // Only process terms that have a parent (level 2+)

															$parent_term = get_term($term->parent);
															if ($parent_term && $parent_term->parent === 0): // Ensure its parent is a level 1 term (making this term level 2)

																// WP_Query arguments
																$args = array(
																	'post_type' => 'property', // Your custom post type
																	'posts_per_page' => -1, // Display all posts
																	'tax_query' => array(
																		'relation' => 'AND',
																		array(
																			'taxonomy' => 'property_status',
																			'field'    => 'slug',
																			'terms'    => 'for-sale', // Filter by 'for-sale' term in 'property_status'
																		),
																		array(
																			'taxonomy' => 'property_area',
																			'field'    => 'term_id',
																			'terms'    => $property_area_post_page->term_id, // Use the term ID
																		)
																	),
																);

																// Query posts
																$the_property_query_child = new WP_Query($args); ?>

																<!-- Output the term name and post count -->
																<?php echo esc_html($term->name); ?> (<?php echo esc_html($the_property_query_child->found_posts); ?>)
																<?php wp_reset_postdata(); // Always reset post data ?>
															<?php endif;
														endif;
													endforeach;
												endif;
												?>
											</option>
										<?php endwhile; ?>
									</select>
								</div>
								<?php wp_reset_postdata(); ?>
							<?php endif; ?>

						<?php else : ?>

						<?php endif; ?>
					</div>
				</div>
				<div class="filter-sort__sort">
					<!-- Filters -->
					<?php if (!empty($property_features = get_terms('property_feature'))) : ?>
						<div class="filters-sort__feature filters-sort-filters filter-sort__sort-item">
							<label for="property_feature-terms"><?php _e('Property Features', 'purple'); ?></label>
							<select class="select-2 property_feature-terms" name="property_feature-terms[]" id="property_feature-terms" multiple>
								<?php foreach ($property_features as $property_feature) : ?>
									<option value=".<?php echo esc_attr($property_feature->slug); ?>">
										<?php echo esc_html($property_feature->name); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<!-- Filters -->
					<!-- Filters -->
					<!--                    <div class="filter-price filter-sort__sort-item">-->
					<!--                        <h4>--><?php //_e('PRICE', 'purple'); ?><!--</h4>-->
					<!--                        <div class="filter-price__input">-->
					<!--                            <label for="pfrom">--><?php //_e('From', 'purple'); ?><!--</label>-->
					<!---->
					<!--                            <input type="range" id="pfrom" name="pfrom" min="0" max="2000000" value="0" step="100000" oninput="this.nextElementSibling.value = this.value">-->
					<!--                            <output>0</output>-->
					<!--                        </div>-->
					<!--                        <div class="filter-price__input filter-price__input-to">-->
					<!--                            <label for="pto">--><?php //_e('To', 'purple'); ?><!--</label>-->
					<!---->
					<!--                            <input type="range" id="pto" name="pto" min="0" max="2000000" value="2000000" step="100000" oninput="this.nextElementSibling.value = this.value">-->
					<!--                            <output>2000000</output>-->
					<!--                        </div>-->
					<!---->
					<!--                    </div>-->
					<div class="filter-price filter-sort__sort-item filter-sort__sort-item-range">
						<h4 style="display: none"><?php _e('PRICE', 'purple'); ?></h4>
						<div class="filter-sort__sort-item__range">
                            <span class="rangeValues">
                                <div class="price-from">
                                     <span id="price-from">10000</span>
                                    <p>
                                      €
                                    </p>
                                </div>
                                <div class="price-to">
                                       <span id="price-to">20000000</span></span>
							<p>
								€
							</p>
						</div>

						<input value="10000" min="10000" max="20000000" step="10000" type="range" id="price-from-range">
						<input value="20000000" min="10000" max="20000000" step="10000" type="range" id="price-to-range">
					</div>
				</div>





				<!-- Filters -->
				<div class="search-input filter-sort__sort-item">
					<label for="quicksearch"><?php _e('Keyword', 'purple'); ?></label>
					<input type="text" id="quicksearch" name="quicksearch" class="quicksearch" placeholder="<?php _e('Enter the keyword...', 'purple'); ?>" />
				</div
			</div>
		</div>
		</div>
	</section>

	<section class="property-list-items">
		<div class="property-list-items__container">
			<div class="property-list-items__row">
				<?php get_template_part('template-parts/properties-list'); ?>
			</div>
		</div>
	</section>








	<?php if($categories_content = get_field('categories_content')) : ?>
		<section class="property-page-content">
			<div class="container-new-800">
				<div class="property-page-content__block">
					<?php echo $categories_content; ?>
				</div>
			</div>
		</section>
	<?php endif;?>

	<?php $faq_global = get_field('faq', 'options');?>
	<?php $faq = get_field('faq');?>

	<?php if($faq_global || $faq ) : ?>
		<section class="faq">
			<div class="container-new">
				<h2 class="text-3xl font-normal lg:text-4xl"><?php _e('Frequently Asked Questions', 'purple');?></h2>
				<?php $index = 1;?>
				<?php if ( have_rows( 'faq' ) ): ?>
					<div class="faq-section__items">
						<?php while ( have_rows( 'faq' ) ): the_row();
							$title     = get_sub_field( 'title' );
							$text      = get_sub_field( 'text' );  ?>
							<div class="faq-item">
								<div class="faq-item__title">
									<h5><?php echo $title; ?></h5>
									<span class="plus-minus"></span>
								</div>
								<div class="faq-item__content">
									<p><?php echo $text; ?></p>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php get_template_part('parts/schema'); ?>


<?php get_footer(); ?>
