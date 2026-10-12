<?php
/**
 * Template Name: Long Term Rental Properties Page
 */
get_header(); ?>


<!-- Add the "noindex" meta tag -->

<?php $bg = get_field('background_image_property', 'options'); ?>
<?php $image_default = get_the_post_thumbnail_url(); ?>
<?php $property_status = get_field('property_status'); ?>
<?php $property_area = get_field('property_area'); ?>
<?php $property_types = get_field('property_types'); ?>
<?php $properties_order = get_field('properties_order'); ?>

<main class="main-content-property posts-list">
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
					<?php _e(' Properties', 'purple'); ?>
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

	<?php get_template_part('parts/search-bar'); ?>

	<div class="posts-container mx-auto web-section">
		<?php if($properties_order) : ?>
			<?php
			// Query args
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;

			$args = array(
				'post_type'      => 'property',
				'orderby'        => 'post__in',         // Order the posts by the order in the array
				'order'          => 'ASC',
				'posts_per_page' => get_option('posts_per_page'),
				'paged'          => $paged,
				'post__in'       => $properties_order, // Filter by the IDs from the ACF relationship
			);

			$the_query = new WP_Query($args);

			if ( $the_query->have_posts() ) : ?>
				<div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
					<?php while ( $the_query->have_posts() ) :
						$the_query->the_post();
						get_template_part( 'parts/loop', 'teseo' ); // Post item
					endwhile; ?>
				</div>
				<?php bootstrap_pagination($the_query); ?>
			<?php else: ?>
				<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'default' ); ?></p>
				<section class="contacts">
					<div class="contacts__container">
						<?php if ( $title = get_field('title_search', 'options') ): ?>
							<h1 class="contacts__title"><?php echo esc_html($title); ?></h1>
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
												<div class="contacts-column-text"><?php echo esc_html($text); ?></div>
											<?php endif; ?>
										<?php endwhile; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</section>
			<?php endif;
			wp_reset_postdata(); ?>
		<?php else:?>
			<?php
			$property_status = get_field('property_status');
			$property_area = get_field('property_area');
			$property_types = get_field('property_types');

			// Get the term slug for property_status (single term object)
			$property_status_slug = is_object($property_status) ? $property_status->slug : '';

			// Get the term slug for property_area (array of term objects)
			$property_area_slugs = [];
			if (is_array($property_area)) {
				foreach ($property_area as $area) {
					if (is_object($area)) {
						$property_area_slugs[] = $area->slug;
					}
				}
			}

			// If property_types exists and it's a valid value, you can get its term slug similarly
			$property_types_slugs = [];
			if (is_array($property_types)) {
				foreach ($property_types as $type) {
					if (is_object($type)) {
						$property_types_slugs[] = $type->slug;
					}
				}
			}

			// Query args
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
			$args = array(
				'post_type'      => 'property',
				'meta_key'       => 'property_price',  // The ACF field key
				'orderby'        => 'meta_value_num',  // Order by numerical value
				'order'          => 'DESC',            // Highest to lowest
				'posts_per_page' => get_option('posts_per_page'),
				'paged'          => $paged,
				'tax_query'      => array('relation' => 'AND'),
				//   'post_type'      => 'property',
				//     'meta_key'       => 'property_price',  // Sorting by price
				//     'orderby'        => 'meta_value_num',  // Ensures numeric sorting
				//     'order'          => $order,            // Dynamic order (ASC or DESC)
				//     'posts_per_page' => get_option('posts_per_page'),
				//     'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
				//     'tax_query'      => array('relation' => 'AND'),
			);

			// Add 'property_status' filter if it's set
			if (!empty($property_status_slug)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'property_status',
					'field'    => 'slug',
					'terms'    => $property_status_slug,
				);
			}

			// Add 'property_area' filter if it's set
			if (!empty($property_area_slugs)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'property_area',
					'field'    => 'slug',
					'terms'    => $property_area_slugs,
				);
			}

			// Add 'property_types' filter if it's set
			if (!empty($property_types_slugs)) {
				$args['tax_query'][] = array(
					'taxonomy' => 'property_types',
					'field'    => 'slug',
					'terms'    => $property_types_slugs,
				);
			}

			$the_query = new WP_Query($args);


			if ( $the_query->have_posts() ) : ?>
				<div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
					<?php while ( $the_query->have_posts() ) :
						$the_query->the_post();
						get_template_part( 'parts/loop', 'teseo' ); // Post item
					endwhile; ?>
				</div>
				<?php bootstrap_pagination($the_query); ?>
			<?php else: ?>
				<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'default' ); ?></p>
				<section class="contacts">
					<div class="contacts__container">
						<?php if ( $title = get_field('title_search', 'options') ): ?>
							<h1 class="contacts__title"><?php echo esc_html($title); ?></h1>
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
												<div class="contacts-column-text"><?php echo $text; ?></div>
											<?php endif; ?>
										<?php endwhile; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</section>
			<?php endif;
			wp_reset_postdata(); ?>
		<?php endif;?>
	</div>


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
