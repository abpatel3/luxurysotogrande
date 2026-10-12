<?php

/**
 * Single property template file
 */
get_header();
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

?>

<?php if (!post_password_required()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <section class="relative flex items-center justify-center h-96 lg:h-128">
            <?=
            wp_get_attachment_image(
                get_field('image_overlay', 'options'),
                'full',
                null,
                ['class' => 'absolute w-72 !opacity-50']
            );
            ?>
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
            <?php endif; ?>
        </section>

        <article class="max-w-6xl px-5 mx-auto my-10 lg:px-0">
            <section class="mb-10 space-y-5 text-center">
                <h1 class="text-3xl font-semibold uppercase md:text-5xl property-title"><?php the_title(); ?></h1>
                <h2 class="text-2xl font-thin uppercase md:text-4xl text-lsg-gold"><?php the_field('subtitle'); ?></h2>
                <div class="grid items-center justify-center grid-cols-2 text-lg font-thin uppercase gap-x-5 lg:gap-0 lg:flex md:flex-row md:divide-x-2 md:text-3xl divide-lsg-gold">
                    <h3 class="px-0 py-4 border-b-2 lg:px-4 lg:py-0 lg:border-0 border-lsg-gold">REF: <?php the_field('property_id'); ?></h3>
                    <h3 class="px-0 py-4 border-b-2 lg:px-4 lg:py-0 lg:border-0 border-lsg-gold"><?php the_field('bedrooms'); ?> <?= label('bedrooms_label'); ?></h3>
                    <h3 class="px-0 py-4 lg:px-4 lg:py-0"><?php the_field('bathrooms'); ?> <?= label('bathrooms_label'); ?></h3>
                    <div class="flex items-center justify-center px-0 py-4 space-x-2 lg:px-4 lg:py-0">
                        <?php if (!$is_holiday_rental) : ?>
                            <?php if ($poa) : ?>
                                <h3>POA</h3>
                            <?php else : ?>
                                <?php if ($price && is_numeric($price)) : ?>
                                    <h3 class="<?= $reduced_price ? 'line-through text-gray-400' : ''; ?>">
                                        <?= number_format($price) . $price_postfix; ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if ($reduced_price && is_numeric($reduced_price)) : ?>
                                    <h3>
                                        <?= number_format($reduced_price) . $price_postfix; ?>
                                    </h3>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php if ($images) : ?>
                <section <?= count($images) > 1 ? 'class="relative mt-20 glide slider lg:mt-0"' : ''; ?> data-aos="fade-up">
                    <?php if (count($images) > 1) : ?>
                        <div class="absolute right-0 flex items-center gap-2 -top-6 glide__arrows lg:hidden" data-glide-el="controls">
                            <button class="relative p-1 border-0 shadow-none -left-0 glide__arrow glide__arrow--left bg-lsg-blue hover:bg-lsg-blue/50" data-glide-dir="<">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z" />
                                </svg>
                            </button>
                            <button class="relative p-1 border-0 shadow-none -right-0 glide__arrow glide__arrow--right bg-lsg-blue hover:bg-lsg-blue/50" data-glide-dir=">">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="glide__track" data-glide-el="track">
                        <ul class="h-72 lg:h-128 glide__slides">
                            <?php foreach ($images as $image) : ?>
                                <li class="relative flex items-center justify-center glide__slide">
                                    <?=
                                    wp_get_attachment_image(
                                        get_field('image_overlay', 'options'),
                                        'full',
                                        null,
                                        ['class' => 'absolute w-72 !opacity-50']
                                    );
                                    ?>
                                    <img src="<?= $image['sizes']['large']; ?>" alt="<?= $image['alt']; ?>" class="object-cover w-full h-full">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if (count($images) > 1) : ?>

                        <!-- Controls -->
                        <div class="absolute items-center justify-between hidden w-full gap-2 lg:flex glide__arrows property-card__controls" data-glide-el="controls">
                            <button class="p-1 border-0 shadow-none left-5 glide__arrow glide__arrow--left bg-lsg-blue" data-glide-dir="<">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z" />
                                </svg>
                            </button>
                            <button class="p-1 border-0 shadow-none right-5 glide__arrow glide__arrow--right bg-lsg-blue" data-glide-dir=">">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z" />
                                </svg>
                            </button>
                        </div>

                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="grid gap-10 mt-10 lg:grid-cols-3">
                <section class="order-2 space-y-10 text-lg lg:col-span-2 lg:order-1">

                    <div class="prose content max-w-none"><?php the_content(); ?></div>

                    <!-- <div id="map" class="w-full h-96" data-lat="<?php // echo get_field('property_location')['lat']; 
                                                                        ?>" data-lng="<?php // echo get_field('property_location')['lng']; 
                                                                                        ?>"></div> -->

                    <section class="py-5 features">
                        <?php $features = get_the_terms(get_the_ID(), 'property_feature'); ?>
                        <h2 class="mb-5 text-lg font-semibold uppercase lg:text-xl"><?php the_field('property_features', 'options'); ?></h2>
                        <ul class="mb-3 columns-1 xl:columns-2">
                            <?php if ($features) : ?>
                                <?php foreach ($features as $feature) : ?>
                                    <li class="flex items-center space-x-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>

                                        <p><?php echo $feature->name; ?></p>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </section>
                </section>

                <aside class="order-1 space-y-10 uppercase lg:order-2">
                    <?php if (isset($terms[0]) && in_array($terms[0]->slug, ['for-sale', 'for-sale-es'])) : ?>
                        <div class="w-full bg-white shadow-lg">
                            <div>
                                <div class="p-5 bg-lsg-gold">
                                    <h2 class="text-xl font-thin text-center text-white uppercase">Important Information</h2>
                                </div>
                                <ul class="grid grid-cols-2 gap-5 p-5">
                                    <li class="flex flex-col">
                                        <span class="font-medium uppercase"><?= label('bedrooms_label'); ?></span>
                                        <span><?php the_field('bedrooms'); ?></span>
                                    </li>
                                    <li class="flex flex-col">
                                        <span class="font-medium uppercase"><?= label('bathrooms_label'); ?></span>
                                        <span><?php the_field('bathrooms'); ?></span>
                                    </li>
                                    <li class="flex flex-col">
                                        <span class="font-medium uppercase"><?= label('plot_label'); ?></span>
                                        <span><?php echo get_field('plot_size')['size'] ?? ''; ?> <?php echo get_field('plot_size')['postfix_text'] ?? ''; ?></span>
                                    </li>
                                    <li class="flex flex-col">
                                        <span class="font-medium uppercase"><?= label('internal_label'); ?></span>
                                        <span><?php echo get_field('internal_size')['size'] ?? ''; ?> <?php echo get_field('internal_size')['postfix_text'] ?? ''; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if (have_rows('expenses')) : ?>
                            <div class="w-full bg-white shadow-lg">
                                <div class="p-5 bg-lsg-gold">
                                    <h2 class="text-xl font-thin text-center text-white uppercase">Expenses</h2>
                                </div>
                                <ul class="grid grid-cols-2 gap-5 p-5">

                                    <?php while (have_rows('expenses')) : the_row(); ?>
                                        <li class="flex flex-col">
                                            <span class="font-semibold uppercase"><?php the_sub_field('title'); ?></span> <span><?php the_sub_field('value'); ?> &euro;</span>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="w-full bg-white shadow-lg">
                        <?php if (isset($terms[0])) : ?>
                            <?php
                            if (
                                in_array($terms[0]->slug, ['for-sale', 'for-sale-es'])
                                ||  in_array($terms[0]->slug, ['long-term-rental', 'long-term-rental-es'])
                            ) :
                            ?>
                                <div class="p-5 bg-lsg-gold">
                                    <h2 class="text-xl font-thin text-center text-white uppercase">Enquire Now</h2>
                                </div>
                                <div class="p-5">
                                    <?php echo do_shortcode('[ninja_form id=2]'); ?>
                                </div>
                            <?php else : ?>
                                <div class="booking-form-container" data-security-deposit="<?php the_field('security_deposit'); ?>" data-cleaning-fee="<?php the_field('cleaning_fee'); ?>">
                                    <div class="p-5 bg-lsg-gold">
                                        <h2 class="text-xl font-thin text-center text-white uppercase">Book Now</h2>
                                    </div>
                                    <div class="p-5"><?php echo do_shortcode('[wpbs id="' . get_field('calendar') . '" title="no" legend="yes" legend_position="bottom" display="1" year="0" month="0" language="auto" start="1" dropdown="yes" jump="no" history="1" tooltip="1" highlighttoday="yes" weeknumbers="no" form_id="1" form_position="bottom" auto_pending="no" selection_type="multiple" selection_style="split" minimum_days="0" maximum_days="0" booking_start_day="0" booking_end_day="0" show_date_selection="yes" show_prices="yes"]'); ?></div>
                                    <div class="quote">
                                        <p></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </aside>
            </section>

        </article>

    <?php endwhile; ?>

    <?php get_template_part('sections/global/services'); ?>

    <?php if ($similar_properties) : ?>
        <section class="max-w-6xl px-5 mx-auto my-10 space-y-10 lg:px-0">
            <div class="space-y-5 text-center" data-aos="fade-up">
                <?php $similarSectionId = apply_filters('wpml_object_id', 1181, 'global_section'); ?>
                <h1 class="text-3xl font-bold uppercase lg:text-4xl"><?php the_field('title', $similarSectionId);  ?></h1>
                <h2 class="text-2xl font-thin uppercase lg:text-3xl text-lsg-gold"><?php the_field('subtitle', $similarSectionId); ?></h2>
            </div>
            <?php get_template_part('template-parts/property-carousel', null, ['properties' => $similar_properties]); ?>
        </section>
    <?php endif; ?>

<?php else : ?>
    <div class="py-10 text-center">
        <?= get_the_password_form(); ?>
    </div>
<?php endif; ?>

<?php
get_footer();
