<?php
// Get the current post
global $post;

// Get post data
$post_id = $post->ID;
$post_title = get_the_title($post_id);
$post_description = get_the_excerpt($post_id); // Assuming you have an excerpt set for the post
$post_image = get_the_post_thumbnail_url($post_id);
$post_author = get_the_author_meta('display_name', $post->post_author);
$post_date = get_the_date('Y-m-d', $post_id);

// Publisher data
$publisher_name = "LUXURY SOTOGRANDE SL";
$publisher_logo_url = "https://luxurysotogrande.com/wp-content/uploads/2022/11/lsg-logo-1.png";

?>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?php the_permalink($post_id); ?>"
        },
        "headline": "<?php echo esc_html($post_title); ?>",
        "description": "<?php echo esc_html($post_description); ?>",
        "image": "<?php echo esc_url($post_image); ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo esc_html($post_author); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo esc_html($publisher_name); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo esc_url($publisher_logo_url); ?>"
            }
        },
        "datePublished": "<?php echo esc_html($post_date); ?>"
    }
</script>
