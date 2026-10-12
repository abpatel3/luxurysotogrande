<?php
/**
 * Template part to display SEO related meta tags
 */

$post_id = $post->ID;

/**
 * Set the SEO description
 */
$description = get_field( 'description' );

/**
 * Set the SEO title
 */
$social_media_title = get_field( 'social_media_title', $post_id );
if ( ! $social_media_title ) {
    $social_media_title = get_the_title( $post_id );
}

/**
 * Set the SEO image
 */
$social_media_image = get_field( 'social_media_image', $post_id );
if ( ! $social_media_image && has_post_thumbnail( $post_id ) ) {
    $social_media_image = get_the_post_thumbnail_url( $post_id, 'large' );
}
?>

<!-- Meta Tags -->
<meta name="description" content="<?= $description; ?>">

<!-- Open Graph -->
<meta name="og:description" content="<?= $description; ?>">
<meta name="og:title" content="<?= $social_media_title; ?>">
<meta name="og:image" content="<?= $social_media_image; ?>">
<meta name="og:url" content="<?= get_permalink( $post->ID ); ?>">
<meta name="og:type" content="article">

<!-- Twitter -->
<meta name="twitter:description" content="<?= $description; ?>">
<meta name="twitter:title" content="<?= $social_media_title; ?>">
<meta name="twitter:image" content="<?= $social_media_image; ?>">
<meta name="twitter:card" content="summary_large_image">


