<?php 
/**
 * Renders the services global section
 */

$id = apply_filters( 'wpml_object_id', 150, 'global_section' );
if ( get_sub_field( 'global_section' ) ) {
    $id = apply_filters( 'wpml_object_id', get_sub_field( 'global_section' ), 'global_section' );
}
$title = get_field( 'title', $id );
$subtitle = get_field( 'subtitle', $id );
$box_one = get_field( 'box_one', $id );
$box_two = get_field( 'box_two', $id );
$box_three = get_field( 'box_three', $id );
?>

<section class="bg-lsg-gray services-section">
    <div class="max-w-6xl px-5 py-20 mx-auto lg:px-0">
    
        <div class="space-y-5 text-center uppercase mb-14 ">
            <h2 class="text-3xl font-normal lg:text-4xl"><?php echo $title; ?></h2>
            <h3 class="text-lg font-light lg:text-xl"><?php echo $subtitle; ?></h3>
        </div>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

            <div class="flex flex-col items-center px-5 py-10 text-center bg-white shadow-xl" data-aos="fade-up">
                <?php echo wp_get_attachment_image( $box_one['icon'] ); ?>
                <h2 class="mt-2 mb-5 text-lg font-normal uppercase"><?php echo $box_one['title']; ?></h2>
                <p class="font-light"><?php echo $box_one['content']; ?></p>
            </div>

            <div class="flex flex-col items-center px-5 py-10 text-center bg-white shadow-xl" data-aos="fade-up" data-aos-delay="100">
                <?php echo wp_get_attachment_image( $box_two['icon'] ); ?>
                <h2 class="mt-2 mb-5 text-lg font-normal uppercase"><?php echo $box_two['title']; ?></h2>
                <p class="font-light"><?php echo $box_two['content']; ?></p>
            </div>

            <div class="flex flex-col items-center px-5 py-10 text-center bg-white shadow-xl" data-aos="fade-up" data-aos-delay="200">
                <?php echo wp_get_attachment_image( $box_three['icon'] ); ?>
                <h2 class="mt-2 mb-5 text-lg font-normal uppercase"><?php echo $box_three['title']; ?></h2>
                <p class="font-light"><?php echo $box_three['content']; ?></p>
            </div>

        </div>
    
    </div>
</section>
