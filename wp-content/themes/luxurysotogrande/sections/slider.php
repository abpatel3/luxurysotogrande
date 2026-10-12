<?php

/**
 * Renders the slider template block
 */

$sliderId = apply_filters('wpml_object_id', get_sub_field('slider'), 'slider');
$options = get_field('options', $sliderId);
$has_multiple_slides = count(get_field('slides', $sliderId)) > 1;
?>

<?php if (have_rows('slides', $sliderId)) : ?>
    <section <?= $has_multiple_slides ? 'class="slider glide"' : ''; ?>>
        <div class="glide__track" data-glide-el="track">
            <ul class="h-96 xl:h-192 glide__slides">
                <?php while (have_rows('slides', $sliderId)) : the_row(); ?>
                    <?php
                    $layout = get_row_layout();
                    $fields = match ($layout) {
                        'image' => ['image' => get_sub_field('image')],
                        'image_and_content' => [
                            'image' => get_sub_field('image'),
                            'content' => get_sub_field('content')
                        ],
                        default => ''
                    };
                    get_template_part(
                        'template-parts/slider-item',
                        null,
                        [
                            'layout' => get_row_layout(),
                            'fields' => $fields
                        ]
                    );
                    ?>
                <?php endwhile; ?>
            </ul>
        </div>

        <?php if ($has_multiple_slides) : ?>

            <!-- Controls -->
            <?php if ($options['controls']) : ?>
                <div class="hidden glide__arrows md:block" data-glide-el="controls">
                    <button class="!border-0 !shadow-none glide__arrow glide__arrow--left" data-glide-dir="<">
                        <span class="sr-only">Previous slide</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z" />
                        </svg>
                    </button>
                    <button class="!border-0 !shadow-none glide__arrow glide__arrow--right" data-glide-dir=">">
                        <span class="sr-only">Next slide</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z" />
                        </svg>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Bullets -->
            <?php if ($options['bullets']) : ?>
                <div class="glide__bullets" data-glide-el="controls[nav]">
                    <?php while (have_rows('slides', $sliderId)) : the_row(); ?>
                        <button class="glide__bullet" data-glide-dir="=<?php echo get_row_index() - 1; ?>"></button>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </section>
<?php endif; ?>