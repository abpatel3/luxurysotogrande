<?php
/**
 * The concierge page template file
 */
get_header();

$concierge_services = new WP_Query( [
    'post_type' => 'concierge_service',
    'posts_per_page' => -1,
    'order' => 'ASC'
] );

get_section_templates();
?>


<div class="max-w-6xl p-5 mx-auto my-20 space-y-10 text-center lg:p-0" data-aos="fade-up">
    <h1 class="text-3xl font-semibold">Concierge Services</h1>
    <p>We encourage our clients to take advantage of our exclusive concierge services while staying in our holiday rental villas. We offer high quality additional services, due to our partnerships with all the sought-after companies located within the area. Here at Luxury Sotogrande, we want to ensure that our guests have the best possible experience during their stay.</p>
</div>

<?php if ( $concierge_services->have_posts() ) : ?>
    <div class="grid max-w-6xl gap-10 p-5 mx-auto my-10 lg:p-0 lg:grid-cols-3">
        <?php $i = 0; while ( $concierge_services->have_posts() ) : $concierge_services->the_post(); ?>
            
            <div class="bg-white shadow-xl" data-aos="fade-up" data-aos-delay="<?php echo $i * 100; ?>">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="w-full h-40">
                        <?php the_post_thumbnail( 'medium', ['class' => 'w-full h-full object-cover'] ); ?>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col items-center px-5 py-10 text-center">
                    <h2 class="mt-2 mb-5 text-lg font-semibold uppercase"><?php the_title(); ?></h2>
                    <p class="font-light"><?php the_content(); ?></p>
                </div>
            </div>
        <?php $i++; endwhile; ?>
    </div>
<?php endif; ?>

<div class="max-w-6xl p-5 mx-auto my-20 text-center lg:p-0" data-aos="fade-up">
    <p>If you would like to enquire further on any of these services, please do not hesitate to contact us at <a href="mailto:bella@luxurysotogrande.com">bella@luxurysotogrande.com</a></p>
</div>

<?php
get_footer();