<?php
$property = $args['property'];
$terms = $args['terms'];
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

<a href="<?= get_the_permalink($property->ID); ?>" class="relative flex flex-col overflow-hidden">
    <!-- banner -->
    <?php if ($is_sold) : ?>
        <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold">
            <p class="text-center"><?= label('sold_label'); ?></p>
        </div>
    <?php elseif ($is_rented) : ?>
        <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold">
            <p class="text-center"><?= label('rented_label'); ?></p>
        </div>
    <?php elseif ($reduced_price) : ?>
        <div class="absolute z-10 w-64 p-2 text-lg font-semibold text-center text-white uppercase -rotate-45 -left-14 top-12 bg-lsg-gold label-size-14">
            <p class="text-center"><?= label('reduced_price_label'); ?></p>
        </div>
    <?php endif; ?>
    <!-- /banner -->

    <?php if (has_post_thumbnail($property->ID)) : ?>
        <?php
        $thumbnail_id = get_post_thumbnail_id($property->ID);
        $url = wp_get_attachment_image_url($thumbnail_id, 'medium_large');
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        ?>
        <div class="relative flex items-center justify-center">
            <?=
            wp_get_attachment_image(
                get_field('image_overlay', 'options'),
                'full',
                null,
                ['class' => 'absolute w-72 !opacity-50']
            );
            ?>
            <img src="<?= $url; ?>" alt="<?= $alt; ?>" class="object-cover w-full h-60 lg:h-96">
        </div>
    <?php endif; ?>

    <div class="flex flex-col justify-between flex-1 p-4 space-y-2 bg-lsg-gray">
        <div>
            <h2 class="text-2xl font-medium uppercase title-card-single-property"><?= $property->post_title; ?></h2>
            <h3 class="text-xl font-light uppercase"><?= $property->subtitle; ?></h3>
        </div>
        <div class="flex items-center justify-between font-light">
            <div class="text-xl text-center uppercase">
                <p>Ref: <?= $property->property_id; ?></p>
            </div>
            <div class="flex items-center space-x-2">
                <?php if (!$is_holiday_rental) : ?>
                    <?php if ($property->poa) : ?>
                        <h3 class="text-xl text-right uppercase">POA</h3>
                    <?php else : ?>
                        <?php if ($property->property_price && is_numeric($property->property_price)) : ?>
                            <h3 class="text-xl text-right uppercase <?= $reduced_price ? 'line-through text-gray-400' : ''; ?>">
                                <?= number_format($property->property_price) . $property->price_postfix_text; ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ($reduced_price && is_numeric($reduced_price)) : ?>
                            <h3 class="text-xl text-right uppercase ">
                                <?= number_format($property->property_reduced_price) . $property->price_postfix_text; ?>
                            </h3>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="p-4 space-y-2 text-white bg-lsg-gold">
        <div class="grid justify-between  <?= $is_for_sale ? 'grid-cols-4' : 'grid-cols-2'; ?> gap-5 text-sm font-medium uppercase">
            <?php if ($is_for_sale) : ?>
                <div class="text-center">
                    <span class="block h-6 text-white">
                        <?php get_template_part('template-parts/icons/house', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                    </span>
                    <p class="text-center">
                        <?= get_field('internal_size', $property->ID)['size'] ?? ''; ?> <?= get_field('internal_size', $property->ID)['postfix_text'] ?? ''; ?>
                    </p>
                </div>
                <div class="text-center">
                    <span class="block h-6 text-white">
                        <?php get_template_part('template-parts/icons/tree', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                    </span>
                    <p class="text-center">
                        <?= get_field('plot_size', $property->ID)['size'] ?? ''; ?> <?= get_field('plot_size', $property->ID)['postfix_text'] ?? ''; ?>
                    </p>
                </div>
            <?php endif; ?>
            <div class="text-center">
                <span class="block h-6 text-white">
                    <?php get_template_part('template-parts/icons/bed', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                </span>
                <p class="text-center"><?= $property->bedrooms . ' ' . label('bedrooms_label'); ?></p>
            </div>
            <div class="text-center">
                <span class="block h-6 text-white">
                    <?php get_template_part('template-parts/icons/bathrooms', null, ['classes' => 'w-5 mx-auto fill-current']); ?>
                </span>
                <p class="text-center"><?= $property->bathrooms . ' ' . label('bathrooms_label'); ?></p>
            </div>
        </div>
    </div>
</a>