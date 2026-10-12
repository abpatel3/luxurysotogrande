<?php

/**
 * Single property template file
 */



global $post;
$images = get_field('gallery');
$terms = get_the_terms($post, 'property_status');

$is_holiday_rental = isset($terms[0]) && in_array($terms[0]->slug, ['holiday-rental', 'holiday-rental-es']);
$price = get_field('property_price');
$reduced_price = get_field('property_reduced_price');
$poa = get_field('poa');
$price_postfix = get_field('price_postfix_text');

$similar_properties = [];
if (!get_field('related_properties')) {
    $property_area = get_the_terms($post, 'property_area') ?? [];
    $property_features = get_the_terms($post, 'property_feature') ?? [];
    $property_type = get_the_terms($post, 'property_type') ?? [];
    $property_city = get_the_terms($post, 'property_city') ?? [];
    $property_status = get_the_terms($post, 'property_status') ?? [];

    $tax_query = [
        'relation' => 'AND'
    ];

    if ($property_area) {
        $tax_query[] = [
            'taxonomy' => 'property_area',
            'field' => 'slug',
            'terms' => array_map(fn ($item) => $item->slug, $property_area)
        ];
    }

    if ($property_features) {
        $tax_query[] = [
            'taxonomy' => 'property_feature',
            'field' => 'slug',
            'terms' => array_map(fn ($item) => $item->slug, $property_features)
        ];
    }

    if ($property_type) {
        $tax_query[] = [
            'taxonomy' => 'property_type',
            'field' => 'slug',
            'terms' => array_map(fn ($item) => $item->slug, $property_type)
        ];
    }

    if ($property_city) {
        $tax_query[] = [
            'taxonomy' => 'property_city',
            'field' => 'slug',
            'terms' => array_map(fn ($item) => $item->slug, $property_city)
        ];
    }

    if ($property_status) {
        $tax_query[] = [
            'taxonomy' => 'property_status',
            'field' => 'slug',
            'terms' => array_map(fn ($item) => $item->slug, $property_status)
        ];
    }

    $similar_properties = get_posts([
        'post_type' => 'property',
        'posts_per_page' => 9,
        'tax_query' => $tax_query,
        'post__not_in' => [$post->ID]
    ]);
} else {
    $similar_properties = get_field('related_properties');
}
$sold = get_field('sold');
?>
<main class="main-content" id="capture-target">
<?php get_header();
?>
<?php if (!post_password_required()) : ?>
    <?php while (have_posts()) : the_post(); ?>
    <section class="hero-section-property">
        <div class="hero-section-property__container container-property">
            <div class="back-button">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow-back.png" width="16" height="12" alt="">
                <a href="javascript:void(0);" class="go-back">
			        <?php _e('Back', 'purple'); ?>
                </a>
            </div>
	        <?php
	        // Get the featured image ID and URL
	        $featured_image_id = get_post_thumbnail_id();
	        $images = get_field('gallery');
	        if ($images): ?>
		        <?php $i = 1; ?>
                <ul class="gallery__list">
			        <?php foreach ($images as $image): ?>
                        <li class="gallery-item gallery-item-<?php echo $i; ?> <?php echo $i > 3 ? 'hidden-item' : ''; ?>">
                            <a href="<?php echo esc_url(wp_get_attachment_url($image['ID'], 'full')); ?>" class="gallery-item" title="<?php the_title(); ?>">
						        <?php
						        echo wp_get_attachment_image($image['ID'], 'full', false, ['loading' => 'eager']);
						        ?>
						        <?php if ($i == 3) : ?>
                                    <div class="gallery-item__overlay"></div>
						        <?php endif; ?>
	                            <?php if ($sold) : ?>
                                    <p class="sold-banner bg-lsg-gold">
	                                    <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status' ) ) : ?>
		                                    <?php _e('Sold', 'luxury'); ?>
	                                    <?php else: ?>
		                                    <?php _e('Rented', 'luxury'); ?>
	                                    <?php endif;?>
                                    </p>
	                            <?php endif; ?>
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png" width="298" height="90" alt="lsg-logo-1" class="watermark">
                            </a>
					        <?php if ($i == 3) : ?>
                                <span class="gallery-item-item__count">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/camera_ico.png" width="18" height="18" alt="camera-icon">
                        <?php echo count($images); // Total count of images ?>
							        <?php _e('Photos', 'purple'); ?>
                    </span>
					        <?php endif; ?>
                        </li>
				        <?php $i++; ?>
			        <?php endforeach; ?>
                </ul>
	        <?php endif; ?>

        </div>
    </section>
    <section class="property-item-breadcrumbs">
        <div class="container-property">
            <div class="property-content__row">

            </div>
        </div>
    </section>
    <section class="property-content">
        <div class="container-property">
            <div class="property-content__row">
                <div class="property-content__info">
                    <div class="property-item-breadcrumbs__row">
                        <div class="property-item-breadcrumbs__items">
                            <p>
				                <?php _e('Real Estate', 'purple'); ?>
                            </p>
                            <span>
                        >
                    </span>
			                <?php
			                $property_status = get_the_terms(get_the_ID(), 'property_status');
			                ?>
			                <?php if ($property_status) : ?>
				                <?php foreach ($property_status as $status) : ?>
                                    <a href="<?php echo esc_url(get_term_link($status)); ?>">
						                <?php echo esc_html($status->name); ?>
                                    </a>
				                <?php endforeach; ?>
			                <?php endif; ?>
                            <span> > </span>
			                <?php
			                $property_area = get_the_terms(get_the_ID(), 'property_area');
			                ?>
			                <?php if ($property_area) : ?>
				                <?php foreach ($property_area as $area) : ?>
                                    <a class="property-item-breadcrumbs__property-area" href="<?php echo esc_url(get_term_link($area)); ?>">
						                <?php echo esc_html($area->name); ?>
                                        <span>
                                    ,
                                </span>
                                    </a>
				                <?php endforeach; ?>
			                <?php endif; ?>
                        </div>
                        <div class="property-item-breadcrumbs__id">
                            <h3>REF: <?php the_field('property_id'); ?></h3>
                        </div>
                    </div>
	                <?php
	                $property_status = get_the_terms(get_the_ID(), 'property_status');
	                if ($property_status && !is_wp_error($property_status)) {
		                $status_class = sanitize_html_class($property_status[0]->slug);
		                echo '<h1 class="property-content__title property-title property-content__title-' . $status_class . '">';
	                } else {
		                echo '<h1 class="property-content__title property-title">';
	                }
	                ?>
	                <?php the_title(); ?>
                    </h1>


                    <div class="property-content__price">
		                <?php if (!$is_holiday_rental) : ?>
			                <?php if ($poa) : ?>
                                <h3>POA</h3>
			                <?php else : ?>
				                <?php if ($price && is_numeric($price)) : ?>
                                    <h3 class="<?= $reduced_price ? 'line-through text-gray-400' : ''; ?>">
						                <?= number_format($price, 0, '', '.') . $price_postfix; ?>
                                    </h3>
				                <?php endif; ?>

				                <?php if ($reduced_price && is_numeric($reduced_price)) : ?>
                                    <h3>
	                                    <?= number_format($reduced_price, 0, '', '.') . $price_postfix; ?>
                                    </h3>
				                <?php endif; ?>
			                <?php endif; ?>
		                <?php endif; ?>
                    </div>
                    <ul class="property-content__important-info">
                        <li class="">
                            <span><?php the_field('bedrooms'); ?></span>
                                        <span class="font-medium ">
                                            <?php _e('Bedrooms', 'purple');?>
                                        </span>

                        </li>
                        <li class="">
                            <span><?php the_field('bathrooms'); ?></span>
                                        <span class="font-medium ">
                                              <?php _e('Bathrooms', 'purple');?>
                                        </span>

                        </li>
                        <?php if (!$is_holiday_rental) : ?>
                        <li class="single-plot-size">

                            <span class="font-medium ">
                                            <?php _e('Plot', 'purple');?>
                                        </span>

                            <span><?php echo get_field('plot_size')['size'] ?? ''; ?> <?php echo get_field('plot_size')['postfix_text'] ?? ''; ?></span>

                        </li>
                        <li class="single-internal-size">

                            <span class="font-medium ">
                                             <?php _e('Internal', 'purple');?>
                                        </span>

                            <span><?php echo get_field('internal_size')['size'] ?? ''; ?> <?php echo get_field('internal_size')['postfix_text'] ?? ''; ?></span>

                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="property-main-content">
                        <?php the_content(); ?>
                    </div>
                    <div class="features new-features">
		                <?php $features = get_the_terms(get_the_ID(), 'property_feature'); ?>
                        <h2 class="new-features__title"><?php the_field('property_features', 'options'); ?></h2>
                        <ul class="mb-3 columns-1 xl:columns-2">
			                <?php if ($features) : ?>
				                <?php foreach ($features as $feature) : ?>
					                <?php
					                // Get the ACF image field value for the feature term
					                $image_id = get_field('features_image', 'property_feature_' . $feature->term_id);
					                ?>
                                    <li class="flex items-center space-x-3 new-features-item">
	                                    <?php if ($image_id) : ?>
                                            <?php echo wp_get_attachment_image($image_id, 'full');?>
	                                    <?php else : ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
	                                    <?php endif; ?>
                                        <p><?php echo $feature->name; ?></p>
                                    </li>
				                <?php endforeach; ?>
			                <?php endif; ?>
                        </ul>
                    </div>
	                <?php

	                // Load value.
	                $iframe = get_field('youtube'); ?>
                    <?php if ($iframe) : ?>
                    <?php
	                // Use preg_match to find iframe src.
	                preg_match('/src="(.+?)"/', $iframe, $matches);
	                $src = $matches[1];

	                // Add extra parameters to src and replace HTML.
	                $params = array(
		                'controls'  => 0,
		                'hd'        => 1,
		                'autohide'  => 1
	                );
	                $new_src = add_query_arg($params, $src);
	                $iframe = str_replace($src, $new_src, $iframe);

	                // Add extra attributes to iframe HTML.
	                $attributes = 'frameborder="0"';
	                $iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);

	                // Display customized HTML.
	                ?>
                    <?php endif;?>
	                <?php if($iframe) : ?>
                        <div class="property-video-youtube">
                            <?php echo $iframe; ?>
                        </div>
	                <?php endif;?>
	                <?php $property_video_file = get_field('property_video_file'); ?>
	                <?php if($property_video_file) : ?>
		                <div class="property-video-file">
                            <div class="property-video-file__container">
                                <video class="hero-section__video" muted loop controls autoplay loop loading="lazy" preload="metadata">
                                    <source src="<?php echo esc_url($property_video_file); ?>" type="video/mp4">
                                </video>
                            </div>
                        </div>
	                <?php endif;?>
	                <?php if (have_rows('expenses')) : ?>
                        <div class="new-expenses">
                            <div class="new-expenses__title">
                                <h2 class=""><?php _e('Expenses', 'purple'); ?></h2>
                            </div>
                            <ul class="new-expenses__grid">
				                <?php while (have_rows('expenses')) : the_row(); ?>
                                    <li class="flex flex-col">
                                        <span class="new-expense__title"><?php the_sub_field('title'); ?></span> <span class="new-expense__subtitle"><?php the_sub_field('value'); ?> &euro;</span>
                                    </li>
				                <?php endwhile; ?>
                            </ul>
                        </div>
	                <?php endif; ?>
                    <div id="map" class="w-full h-96" data-lat="<?php echo get_field('property_location')['lat'];
	                ?>" data-lng="<?php  echo get_field('property_location')['lng'];
	                ?>"></div>
                </div>
                <div class="property-content__form">
                    <div class="property-content__form__block">
		                <?php if (isset($terms[0])) : ?>
			                <?php
			                if (
				                in_array($terms[0]->slug, ['for-sale', 'for-sale-es'])
				                ||  in_array($terms[0]->slug, ['long-term-rental', 'long-term-rental-es'])
			                ) :
				                ?>
                                <div class="property-content__form__title">
                                    <h2 class=""><?php _e('Enquire Now', 'purple'); ?></h2>
                                </div>
                                <div class="p-5">
					                <?php echo do_shortcode('[ninja_form id=2]'); ?>
                                </div>
			                <?php else : ?>
                                <div class="booking-form-container" data-security-deposit="<?php the_field('security_deposit'); ?>" data-cleaning-fee="<?php the_field('cleaning_fee'); ?>">
                                    <div class="p-5 bg-lsg-gold">
                                        <h2 class="text-xl font-thin text-center text-white uppercase"><?php _e('CHECK AVAILABILITY', 'purple'); ?></h2>
                                    </div>
                                    <div class="p-5"><?php echo do_shortcode('[wpbs id="' . get_field('calendar') . '" title="no" legend="yes" legend_position="bottom" display="1" year="0" month="0" language="auto" start="1" dropdown="yes" jump="no" history="1" tooltip="1" highlighttoday="yes" weeknumbers="no" form_id="1" form_position="bottom" auto_pending="no" selection_type="multiple" selection_style="split" minimum_days="0" maximum_days="0" booking_start_day="0" booking_end_day="0" show_date_selection="yes" show_prices="yes"]'); ?></div>
                                    <div class="quote">
                                        <p></p>
                                    </div>
                                </div>
			                <?php endif; ?>
		                <?php endif; ?>
                    </div>
                    <div class="property-content__form__links">
	                    <?php echo get_property_download_links(get_the_ID()); ?>
                        <button id="capture-product">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/display-solid.svg" width="16" height="16" alt="display">
                            <?php _e('Capture' , 'purple'); ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </section>




    <?php endwhile; ?>



<?php else : ?>
    <div class="py-10 text-center">
        <?= get_the_password_form(); ?>
    </div>
<?php endif; ?>
</main>
<?php get_template_part('sections/global/services'); ?>


    <script>

    </script>






<?php
get_footer();
