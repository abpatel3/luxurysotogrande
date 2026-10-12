<?php 
/**
 * Renders the CTA section template
 */
$id = apply_filters( 'wpml_object_id', get_sub_field( 'global_section' ), 'global_section' );
$backgroundImage = get_field( 'background_image', $id );
$title = get_field( 'title', $id );
$content = get_field( 'content', $id );
$buttonText = get_field( 'button_text', $id );
$buttonUrl = get_field( 'button_url', $id );
?>

<section class="relative text-white h-128 lg:h-148">

    <div class="absolute top-0 left-0 w-full h-full bg-black/50"></div>

    <?php echo wp_get_attachment_image( $backgroundImage, 'full', false, ['class' => 'w-full h-full object-cover object-center'] ); ?>

    <div class="absolute inset-0 flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0">
        <div class="space-y-10 lg:space-y-20 lg:w-1/2">
            <h2 class="text-2xl font-semibold uppercase lg:text-3xl" data-aos="fade-up"><?php echo $title; ?></h2>
            <p class="text-lg font-light lg:text-xl" data-aos="fade-up" data-aos-delay="100"><?php echo $content; ?></p>
            <a href="<?php echo $buttonUrl; ?>" class="inline-block px-10 py-4 font-semibold text-black uppercase bg-lsg-gold hover:bg-lsg-gold-hover" data-aos="fade-up" data-aos-delay="200">
                <?php echo $buttonText?>
            </a>
        </div>
    </div>

</section>