<?php /* Template Name: Demo-page */ ?>
<?php
get_header();
?>

<?php 
$banner_section = get_field('banner_section'); // Get the group field

if ($banner_section && isset($banner_section['banner_video'])) {
    // Check if the video field returns an array (ACF File field returns an array by default)
    if (is_array($banner_section['banner_video'])) {
        $banner_video = $banner_section['banner_video']['url']; // Get the URL
    } else {
        $banner_video = $banner_section['banner_video']; // If it's already a URL
    }

    if (!empty($banner_video)) { ?>
        <section class="home-video">
            <div class="home-bg__block">
                <div class="home-bg__block-video">
                    <div class="videoHolder" data-ratio="<?php echo esc_attr(get_post_meta(get_the_ID(), 'video_aspect_ratio', true) ?: '16:9'); ?>">
                        <video src="<?php echo esc_url($banner_video); ?>" autoplay playsinline preload="none" muted loop class="video video--local"></video>
                    </div>
      <div class="video-content">
    <?php  
    if (!empty($banner_section['video_logo'])) {
        // Check if the image field returns an array (ACF Image field returns an array by default)
        if (is_array($banner_section['video_logo'])) {
            $video_logo_url = $banner_section['video_logo']['url']; // Get the image URL
            $video_logo_alt = $banner_section['video_logo']['alt']; // Get alt text
        } else {
            $video_logo_url = $banner_section['video_logo']; // If it's already a URL
            $video_logo_alt = ''; // Set alt as empty
        }
    ?>
        <img src="<?php echo esc_url($video_logo_url); ?>" alt="<?php echo esc_attr($video_logo_alt); ?>" class="w-full h-full object-cover object-center">
    <?php } ?>
</div>
                </div>
            </div>
        </section>
    <?php } 
} ?>



<section class="max-w-6xl px-5 mx-auto lg:px-0 featured-categories">
    <div class="grid-2">
        <?php 
        $properties_section = get_field('properties_section'); 
        if ($properties_section && !empty($properties_section['add_properties_data'])) { 
            foreach ($properties_section['add_properties_data'] as $property) { 
                if (!empty($property['properties_name'])) {
                    $title = esc_html($property['properties_name']); 
                    $property_image = isset($property['properties_image']) ? $property['properties_image'] : null; // Check if property_image exists
                    $properties_link = isset($property['properties_link']) ? $property['properties_link'] : null; // Check if properties_link exists
      
        ?>
                <a target="_self" title="<?php echo $title; ?>" href="<?php echo !empty($properties_link['url']) ? esc_url($properties_link['url']) : '#'; ?>">
                    <h3 class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold text-center title-gold">
                        <?php echo $title; ?>
                    </h3>
                    <?php if (!empty($property_image)): ?> 
                            <img src="<?php echo esc_url($property_image); ?>" class="w-full h-full object-cover object-center" alt="<?php echo esc_attr($title); ?>" loading="lazy">
                    <?php endif; ?>
                </a>
        <?php
                }
            }
        }
        ?>
    </div>
</section>








<?php
get_footer();
?>