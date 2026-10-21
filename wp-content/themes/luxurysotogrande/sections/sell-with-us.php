<?php
/**
 * Sell With Us - Luxury Real Estate Valuation & Selling Section Template
 *
 * Layout: sell_with_us (Flexible Content in Page Builder)
 * Matching luxury editorial design: Left pool/architecture image, right editorial selling copy,
 * 4 USP features (2x2 grid), and dual CTA buttons.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$raw_image       = get_sub_field('image');
$raw_tagline     = get_sub_field('tagline');
$raw_title       = get_sub_field('title');
$raw_description = get_sub_field('description');
$raw_features    = get_sub_field('features');
$primary_btn     = get_sub_field('primary_button');
$secondary_btn   = get_sub_field('secondary_button');

// Defaults if left empty in WP-Admin
$tagline     = !empty($raw_tagline) ? $raw_tagline : __('SELL WITH US', 'luxurysotogrande');
$title       = !empty($raw_title) ? $raw_title : __('The Art of Selling Luxury Homes', 'luxurysotogrande');
$description = !empty($raw_description) ? $raw_description : __("From expert valuation and bespoke marketing to skilled negotiation and completion, we provide a personalised service designed to maximise your home’s value and achieve the very best result.", 'luxurysotogrande');

// Image Handling
$image_url = '';
$image_alt = esc_attr($title);

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

// Fallback image if none uploaded
if (empty($image_url)) {
    $image_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.jpg';
}

// Features Handling (Fallback to 4 editorial points)
$default_features = [
    [
        'title' => __('EXPERT VALUATION', 'luxurysotogrande'),
        'text'  => __('Honest advice, backed by local expertise', 'luxurysotogrande'),
    ],
    [
        'title' => __('BESPOKE MARKETING', 'luxurysotogrande'),
        'text'  => __('Every home deserves its own strategy', 'luxurysotogrande'),
    ],
    [
        'title' => __('GLOBAL REACH', 'luxurysotogrande'),
        'text'  => __('Connecting with qualified buyers worldwide', 'luxurysotogrande'),
    ],
    [
        'title' => __('EXCEPTIONAL NEGOTIATION', 'luxurysotogrande'),
        'text'  => __('Delivering the strongest possible result', 'luxurysotogrande'),
    ],
];

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

if (empty($features)) {
    $features = $default_features;
}

// Primary Button Configuration
$btn1_url    = home_url('/contact/');
$btn1_title  = __('REQUEST A FREE VALUATION', 'luxurysotogrande');
$btn1_target = '_self';

if (!empty($primary_btn)) {
    if (is_array($primary_btn)) {
        $btn1_url    = !empty($primary_btn['url']) ? $primary_btn['url'] : $btn1_url;
        $btn1_title  = !empty($primary_btn['title']) ? $primary_btn['title'] : $btn1_title;
        $btn1_target = !empty($primary_btn['target']) ? $primary_btn['target'] : '_self';
    } elseif (is_string($primary_btn)) {
        $btn1_url = $primary_btn;
    }
}

// Secondary Button Configuration
$btn2_url    = home_url('/contact/');
$btn2_title  = __('A DISCREET CONVERSATION', 'luxurysotogrande');
$btn2_target = '_self';

if (!empty($secondary_btn)) {
    if (is_array($secondary_btn)) {
        $btn2_url    = !empty($secondary_btn['url']) ? $secondary_btn['url'] : $btn2_url;
        $btn2_title  = !empty($secondary_btn['title']) ? $secondary_btn['title'] : $btn2_title;
        $btn2_target = !empty($secondary_btn['target']) ? $secondary_btn['target'] : '_self';
    } elseif (is_string($secondary_btn)) {
        $btn2_url = $secondary_btn;
    }
}
?>

<section class="sell-with-us-section">
    <div class="ls-container">
        <div class="sell-with-us-grid">

            <!-- Left Column: Visual Media -->
            <div class="sell-with-us-media">
                <div class="sell-media-inner">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="sell-image" loading="lazy">
                </div>
            </div>

            <!-- Right Column: Editorial Selling Content -->
            <div class="sell-with-us-content">
                <?php if (!empty($tagline)) : ?>
                    <span class="sell-tagline"><?php echo esc_html($tagline); ?></span>
                <?php endif; ?>

                <h2 class="sell-title"><?php echo esc_html($title); ?></h2>

                <div class="sell-gold-line" aria-hidden="true"></div>

                <?php if (!empty($description)) : ?>
                    <p class="sell-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <!-- 2x2 Features Grid -->
                <div class="sell-features-grid">
                    <?php foreach ($features as $feature) : ?>
                        <div class="sell-feature-item">
                            <h4 class="sell-feature-title">
                                <span class="feature-dash">—</span> <?php echo esc_html($feature['title']); ?>
                            </h4>
                            <?php if (!empty($feature['text'])) : ?>
                                <p class="sell-feature-text"><?php echo esc_html($feature['text']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dual CTA Action Buttons -->
                <div class="sell-actions">
                    <?php if (!empty($btn1_url)) : ?>
                        <a href="<?php echo esc_url($btn1_url); ?>" class="sell-btn sell-btn-primary" target="<?php echo esc_attr($btn1_target); ?>">
                            <?php echo esc_html($btn1_title); ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($btn2_url)) : ?>
                        <a href="<?php echo esc_url($btn2_url); ?>" class="sell-btn sell-btn-secondary" target="<?php echo esc_attr($btn2_target); ?>">
                            <?php echo esc_html($btn2_title); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</section>
