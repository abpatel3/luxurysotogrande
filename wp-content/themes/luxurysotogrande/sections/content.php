<?php
/**
 * Renders the content section template
 */

$content = get_sub_field( 'content' );

$text_alignment = match ( get_sub_field( 'text_alignment' ) ) {
    'left' => 'text-left',
    'center' => 'text-center',
    'right' => 'text-right'
};

?>

<section class="mb-5 space-y-5 <?php echo $text_alignment; ?>">
    <?php if ( get_sub_field( 'container' ) ) : ?>
        <div class="max-w-6xl mx-auto"><?php echo $content; ?></div>
    <?php else: ?>
        <?php echo $content; ?>
    <?php endif; ?>
</section>