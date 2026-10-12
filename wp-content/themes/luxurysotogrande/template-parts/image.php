<?php
/**
 * Outputs an image with an overlay
 */
$url = $args['url'];
$alt = $args['alt'];
$classes = $args['classes'];
?>

<div class="relative flex items-center justify-center">
    <?=
        wp_get_attachment_image( 
            get_field( 'image_overlay', 'options' ), 
            'full',
            null,
            ['class' => 'absolute']
        );
    ?>

    <img 
        src="<?= $url; ?>" 
        alt="<?= $alt; ?>"
        class="<?= $classes; ?>"
    >
</div>