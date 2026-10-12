<?php
/**
 * Displays the feautured properties slider
 */

$title = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$property_status = get_sub_field( 'property_status' );

$properties = get_posts( [
    'post_type'  => 'property',
    'post_status' => 'publish',
    'tax_query'  => [
        [
            'taxonomy' => 'property_status',
            'field'    => 'slug',
            'terms'    => $property_status ?? 'rented'
        ]
    ],
    'meta_key'   => 'featured',
    'meta_value' => true
] );
?>

<section class="max-w-6xl px-5 py-20 mx-auto lg:px-0 featured-properties">

    <div class="space-y-5 text-center uppercase mb-14 ">
        <h2 class="text-3xl font-normal lg:text-4xl"><?php echo $title; ?></h2>
        <h3 class="text-lg font-light lg:text-xl  bg-lsg-gold featured-properties-subtitle"><?php echo $subtitle; ?></h3>
    </div>
    
    <?php get_template_part( 'template-parts/property-carousel', null, [ 'properties' => $properties ] ); ?>
</section>