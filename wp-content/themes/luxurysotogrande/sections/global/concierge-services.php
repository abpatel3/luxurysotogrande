<?php
/**
 * Renders the grid of concierge services
 */

$concierge_services = new WP_Query( [
    'post_type' => 'concierge_service',
    'posts_per_page' => -1,
    'order' => 'ASC'
] );
?>

<?php if ( $concierge_services->have_posts() ) : ?>
    <div class="grid max-w-6xl grid-cols-3 gap-10 py-20 mx-auto">
        <?php $i = 0; while ( $concierge_services->have_posts() ) : $concierge_services->the_post(); ?>
            <div class="flex flex-col items-center px-5 py-10 text-center bg-white shadow-xl" data-aos="fade-up" data-aos-delay="<?php echo $i * 50; ?>">
                <h2 class="mt-2 mb-5 text-lg font-normal uppercase"><?php the_title(); ?></h2>
                <p class="font-light"><?php the_content(); ?></p>
            </div>
        <?php $i++; endwhile; ?>
    </div>
<?php endif; ?>