<?php
/**
 * The main template file
 */

get_header();
?>

<!-- Standard WordPress Editor Loop -->
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <?php the_title(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();