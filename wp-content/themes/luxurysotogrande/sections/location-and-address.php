<?php 
/**
 * Renders the location and address section
 */
$banner = get_sub_field( 'banner' );
?>

<!-- Banner Image -->
<section class="relative w-full h-96 lg:h-128">
    <?php echo wp_get_attachment_image( $banner['image'], 'full', null, ['class' => 'w-full h-full object-cover'] ); ?>

    <div class="absolute inset-x-0 p-5 text-center text-white bottom-10 lg:bottom-20">
        <div class="space-y-4 lg:space-y-8">
            <div>
                <h2 class="inline-block p-3 text-xl font-bold uppercase md:p-5 md:text-4xl bg-black/25"><?php echo $banner['title']; ?></h2>
            </div>
            <div>
                <h3 class="inline-block max-w-screen-md p-3 text-2xl uppercase md:p-5 md:text-3xl bg-black/25"><?php echo $banner['subtitle']; ?></h3>
            </div>
            <a href="tel:<?php echo $banner['telephone']; ?>" class="inline-block w-auto px-8 py-4 text-lg uppercase lg:text-3xl bg-lsg-gold"><?php echo $banner['cta']; ?></a>
        </div>
    </div>
</section>
<!-- /Banner Image -->

<!-- Location -->
<section class="grid max-w-6xl gap-10 p-5 mx-auto lg:py-20 lg:grid-cols-3" data-aos="fade-up">
    <div class="lg:col-span-2 contact-form" data-aos="fade-up">
        <?= do_shortcode( '[ninja_form id=1]' ); ?>
    </div>
    <div class="space-y-4 text-center lg:text-left">
        <?= get_sub_field( 'address' ); ?>
    </div>
</section>
<!-- /Location -->