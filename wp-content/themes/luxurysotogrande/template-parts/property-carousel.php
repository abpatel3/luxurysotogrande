<?php if (count($args['properties']) > 0) : ?>
    <div <?= count($args['properties']) > 1 ? 'class="mt-20 glide property-carousel lg:mt-0"' : ''; ?> data-aos="fade-up">
        <?php if (count($args['properties']) > 1) : ?>
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
            <ul class="glide__slides">
                <?php foreach ($args['properties'] as $property) : ?>
                    <li class="glide__slide max-w-[50%]">
                        <?php $terms = get_the_terms($property, 'property_status'); ?>
                        <?php
                        get_template_part(
                            'template-parts/property-card',
                            null,
                            ['terms' => $terms, 'property' => $property]
                        );
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (count($args['properties']) > 1) : ?>
            <div class="hidden glide__arrows lg:block" data-glide-el="controls">
                <button class="border-0 shadow-none lg:-left-24 glide__arrow glide__arrow--left" data-glide-dir="<">
                    <span class="sr-only">Previous slide</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z" />
                    </svg>
                </button>
                <button class="border-0 !shadow-none lg:-right-24 glide__arrow glide__arrow--right" data-glide-dir=">">
                    <span class="sr-only">Next slide</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>