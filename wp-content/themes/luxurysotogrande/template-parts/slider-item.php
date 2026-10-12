<?php
/**
 * Renders a slider item
 */
$layout = $args['layout'];
$fields = $args['fields'];
?>

<!-- Image Slide -->
<?php if ( $layout === 'image' ) : ?>
    <li class="glide__slide">
        <?=
            wp_get_attachment_image( 
                $fields['image'], 
                'full', 
                false, 
                [ 'class' => 'h-full w-full object-cover' ] 
            ); 
        ?>
    </li>
<?php endif; ?>

<!-- Image and Content Slide -->
<?php if ( $layout === 'image_and_content' ) : ?>
    <li class="relative flex items-center justify-center glide__slide">
        <?=
            wp_get_attachment_image(
                $fields['image'],
                'full',
                false,
                [ 'class' => 'h-full w-full object-cover' ]
            );
        ?>

        <div class="absolute px-4 space-y-4 text-center text-white md:space-y-8">
            <div class="space-y-4">
                <div>
                    <h2 class="inline-block p-3 text-xl font-bold uppercase md:p-5 md:text-4xl bg-black/25">
                        <?= $fields['content']['title']; ?>
                    </h2>
                </div>
                <h3 class="inline-block p-3 text-2xl uppercase md:p-5 md:text-3xl bg-black/25">
                    <?= $fields['content']['subtitle']; ?>
                </h3>
            </div>
            <h4 class="text-ld md:text-2xl">
                <?= $fields['content']['features']; ?>
            </h4>
            <a 
                href="<?= $fields['content']['button_url']; ?>" 
                class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold"
            ><?= $fields['content']['button_text']; ?></a>
        </div>
    </li>
<?php endif; ?>