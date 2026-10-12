<?php
/**
 * Renders the banner image section
 */
$image = get_sub_field( 'image' );
?>

<section class="w-full h-96">
    <?php echo wp_get_attachment_image( $image, 'full', null, ['class' => 'w-full h-full object-cover'] ); ?>
</section>