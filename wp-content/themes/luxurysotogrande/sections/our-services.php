<?php
/**
 * Our Services - Clean Luxury Grid Section
 *
 * Layout: our_services (Flexible Content in Page Builder)
 * Strictly ACF-driven: Title, subtitle, and service cards
 * are fetched purely from ACF without any hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

$title    = get_sub_field('title');
$subtitle = get_sub_field('subtitle');
$services = get_sub_field('services');
?>

<section class="our-services-section">
    <div class="ls-container">

        <!-- Header -->
        <?php if (!empty($title) || !empty($subtitle)) : ?>
            <div class="our-services-header">
                <?php if (!empty($title)) : ?>
                    <h2 class="our-services-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitle)) : ?>
                    <p class="our-services-subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Services Grid -->
        <?php if (!empty($services) && is_array($services)) : ?>
            <div class="our-services-grid">
                <?php foreach ($services as $service) : 
                    $s_title = !empty($service['title']) ? $service['title'] : '';
                    $s_desc  = !empty($service['description']) ? $service['description'] : '';
                    $s_link  = !empty($service['link']) ? $service['link'] : null;

                    // Skip if completely empty
                    if (empty($s_title) && empty($s_desc)) {
                        continue;
                    }

                    // Resolve link
                    $link_url    = '';
                    $link_title  = __('LEARN MORE', 'luxurysotogrande');
                    $link_target = '_self';

                    if (!empty($s_link)) {
                        if (is_array($s_link)) {
                            $link_url    = !empty($s_link['url']) ? $s_link['url'] : '';
                            $link_title  = !empty($s_link['title']) ? $s_link['title'] : __('LEARN MORE', 'luxurysotogrande');
                            $link_target = !empty($s_link['target']) ? $s_link['target'] : '_self';
                        } elseif (is_string($s_link)) {
                            $link_url = $s_link;
                        }
                    }
                ?>
                    <div class="service-card">
                        <div class="service-gold-line"></div>
                        <?php if (!empty($s_title)) : ?>
                            <h3 class="service-card-title"><?php echo esc_html($s_title); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($s_desc)) : ?>
                            <p class="service-card-description"><?php echo esc_html($s_desc); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($link_url)) : ?>
                            <a href="<?php echo esc_url($link_url); ?>" class="service-card-link" target="<?php echo esc_attr($link_target); ?>">
                                <span class="service-card-link-text"><?php echo esc_html($link_title); ?></span>
                                <span class="service-card-link-arrow">→</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
