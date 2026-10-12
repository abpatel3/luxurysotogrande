<?php
/**
 * Properties page template
 */
get_header();
get_section_templates();
?>

<section class="max-w-6xl px-5 py-20 mx-auto lg:px-0">
    <?php get_template_part( 'template-parts/filtered-properties' ); ?>
</section>

<?php 
get_footer();