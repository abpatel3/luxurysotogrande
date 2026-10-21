<?php
/**
 * Sell With Us - Luxury Real Estate Valuation & Selling Section Template
 *
 * Layout: sell_with_us (Flexible Content in Page Builder)
 * Strictly ACF-driven: Image, tagline, title, description, features,
 * and CTA buttons are fetched purely from ACF without any hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$raw_image       = get_sub_field('image');
$tagline         = get_sub_field('tagline');
$title           = get_sub_field('title');
$description     = get_sub_field('description');
$raw_features    = get_sub_field('features');
$primary_btn     = get_sub_field('primary_button');
$secondary_btn   = get_sub_field('secondary_button');

// Image Handling
$image_url = '';
$image_alt = !empty($title) ? esc_attr($title) : 'Sell With Us';

if (!empty($raw_image)) {
    if (is_array($raw_image) && !empty($raw_image['url'])) {
        $image_url = $raw_image['url'];
        if (!empty($raw_image['alt'])) {
            $image_alt = esc_attr($raw_image['alt']);
        }
    } elseif (is_numeric($raw_image)) {
        $img_src = wp_get_attachment_image_url($raw_image, 'full');
        if ($img_src) {
            $image_url = $img_src;
        }
        $alt_meta = get_post_meta($raw_image, '_wp_attachment_image_alt', true);
        if (!empty($alt_meta)) {
            $image_alt = esc_attr($alt_meta);
        }
    } elseif (is_string($raw_image)) {
        $image_url = $raw_image;
    }
}

// Features Handling
$features = [];
if (!empty($raw_features) && is_array($raw_features)) {
    foreach ($raw_features as $f) {
        $f_title = !empty($f['title']) ? $f['title'] : '';
        $f_text  = !empty($f['text']) ? $f['text'] : '';
        if (!empty($f_title) || !empty($f_text)) {
            $features[] = [
                'title' => $f_title,
                'text'  => $f_text,
            ];
        }
    }
}

// Primary Button Configuration
$btn1_url    = '';
$btn1_title  = '';
$btn1_target = '_self';

if (!empty($primary_btn)) {
    if (is_array($primary_btn)) {
        $btn1_url    = !empty($primary_btn['url']) ? $primary_btn['url'] : '';
        $btn1_title  = !empty($primary_btn['title']) ? $primary_btn['title'] : '';
        $btn1_target = !empty($primary_btn['target']) ? $primary_btn['target'] : '_self';
    } elseif (is_string($primary_btn)) {
        $btn1_url   = $primary_btn;
        $btn1_title = __('REQUEST A FREE VALUATION', 'luxurysotogrande');
    }
}

// Secondary Button Configuration
$btn2_url    = '';
$btn2_title  = '';
$btn2_target = '_self';

if (!empty($secondary_btn)) {
    if (is_array($secondary_btn)) {
        $btn2_url    = !empty($secondary_btn['url']) ? $secondary_btn['url'] : '';
        $btn2_title  = !empty($secondary_btn['title']) ? $secondary_btn['title'] : '';
        $btn2_target = !empty($secondary_btn['target']) ? $secondary_btn['target'] : '_self';
    } elseif (is_string($secondary_btn)) {
        $btn2_url   = $secondary_btn;
        $btn2_title = __('A DISCREET CONVERSATION', 'luxurysotogrande');
    }
}
?>

<section class="sell-with-us-section">
    <div class="ls-container">
        <div class="sell-with-us-grid">

            <!-- Left Column: Visual Media -->
            <?php if (!empty($image_url)) : ?>
                <div class="sell-with-us-media">
                    <div class="sell-media-inner">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="sell-image" loading="lazy">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Right Column: Editorial Selling Content -->
            <div class="sell-with-us-content <?php echo empty($image_url) ? 'sell-full-width' : ''; ?>">
                <?php if (!empty($tagline)) : ?>
                    <span class="sell-tagline"><?php echo esc_html($tagline); ?></span>
                <?php endif; ?>

                <?php if (!empty($title)) : ?>
                    <h2 class="sell-title"><?php echo esc_html($title); ?></h2>
                    <div class="sell-gold-line" aria-hidden="true"></div>
                <?php endif; ?>

                <?php if (!empty($description)) : ?>
                    <p class="sell-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <!-- 2x2 Features Grid -->
                <?php if (!empty($features)) : ?>
                    <div class="sell-features-grid">
                        <?php foreach ($features as $feature) : ?>
                            <div class="sell-feature-item">
                                <?php if (!empty($feature['title'])) : ?>
                                    <h4 class="sell-feature-title">
                                        <span class="feature-dash">—</span> <?php echo esc_html($feature['title']); ?>
                                    </h4>
                                <?php endif; ?>
                                <?php if (!empty($feature['text'])) : ?>
                                    <p class="sell-feature-text"><?php echo esc_html($feature['text']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Dual CTA Action Buttons -->
                <?php if (!empty($btn1_url) || !empty($btn2_url)) : ?>
                    <div class="sell-actions">
                        <?php if (!empty($btn1_url) && !empty($btn1_title)) : ?>
                            <a href="<?php echo esc_url($btn1_url); ?>" class="sell-btn sell-btn-primary" target="<?php echo esc_attr($btn1_target); ?>">
                                <?php echo esc_html($btn1_title); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($btn2_url) && !empty($btn2_title)) : ?>
                            <a href="<?php echo esc_url($btn2_url); ?>" class="sell-btn sell-btn-secondary" target="<?php echo esc_attr($btn2_target); ?>">
                                <?php echo esc_html($btn2_title); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</section>
