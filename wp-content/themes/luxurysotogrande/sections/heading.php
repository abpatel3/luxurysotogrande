<?php
/**
 * Renders the heading section template part
 */

$heading = get_sub_field( 'heading' );

$text_alignment = match ( get_sub_field( 'text_alignment' ) ) {
    'left' => 'text-left',
    'center' => 'text-center',
    'right' => 'text-right'
};

$heading_html = match ( get_sub_field( 'level' ) ) {
    'h1' => "<h1 class='mb-5 text-5xl font-bold " . $text_alignment . "'>$heading</h1>",
    'h2' => "<h2 class='mb-5 text-4xl font-bold " . $text_alignment . "'>$heading</h2>",
    'h3' => "<h3 class='mb-5 text-3xl font-bold " . $text_alignment . "'>$heading</h3>",
    'h4' => "<h4 class='mb-5 text-2xl font-bold " . $text_alignment . "'>$heading</h4>",
    'h5' => "<h5 class='mb-5 text-xl font-bold " . $text_alignment . "'>$heading</h5>",
    'h6' => "<h6 class='mb-5 text-lg font-bold " . $text_alignment . "'>$heading</h6>"
};
?>

<?php if ( get_sub_field( 'container' ) ) : ?>
    <div class="max-w-6xl mx-auto">
        <?php echo $heading_html; ?>
    </div>
<?php else: ?>
    <?php echo $heading_html; ?>
<?php endif; ?>