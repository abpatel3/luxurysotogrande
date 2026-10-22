<?php
/**
 * Valuation Banner Section Template
 *
 * Layout: valuation_banner (Flexible Content in Page Builder)
 * Strictly ACF-driven: Subtitle, title, and button link
 * are fetched purely from ACF without hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$subtitle    = get_sub_field('subtitle');
$title       = get_sub_field('title');
$button_link = get_sub_field('button_link');

// 2. Button Link Handling
$btn_url    = '';
$btn_title  = '';
$btn_target = '_self';

if (!empty($button_link)) {
    if (is_array($button_link)) {
        $btn_url    = !empty($button_link['url']) ? $button_link['url'] : '';
        $btn_title  = !empty($button_link['title']) ? $button_link['title'] : '';
        $btn_target = !empty($button_link['target']) ? $button_link['target'] : '_self';
    } elseif (is_string($button_link)) {
        $btn_url   = $button_link;
        $btn_title = $button_link;
    }
}

// Exit if entire section is empty
if (empty($subtitle) && empty($title) && empty($btn_url)) {
    return;
}
?>

<section class="valuation-banner-section">
    <div class="ls-container">
        <div class="valuation-banner-content">

            <?php if (!empty($subtitle)) : ?>
                <div class="valuation-banner-subtitle"><?php echo esc_html($subtitle); ?></div>
            <?php endif; ?>

            <?php if (!empty($title)) : ?>
                <h2 class="valuation-banner-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($btn_url) && !empty($btn_title)) : ?>
                <div class="valuation-banner-action">
                    <a href="<?php echo esc_url($btn_url); ?>" target="<?php echo esc_attr($btn_target); ?>" class="valuation-banner-btn" <?php echo ($btn_target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                        <?php echo esc_html($btn_title); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
