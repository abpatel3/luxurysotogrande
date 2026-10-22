<?php
/**
 * Meet Bella Section Template
 *
 * Layout: meet_bella (Flexible Content in Page Builder)
 * Strictly ACF-driven: Image, title, description, button link,
 * quote, and signature are fetched purely from ACF without hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$raw_image   = get_sub_field('image');
$title       = get_sub_field('title');
$description = get_sub_field('description');
$button_link = get_sub_field('button_link');
$quote       = get_sub_field('quote');
$signature   = get_sub_field('signature');

// 2. Image Handling
$image_url = '';
$image_alt = !empty($title) ? esc_attr($title) : 'Bella';

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

// 3. Button Link Handling
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
if (empty($image_url) && empty($title) && empty($description) && empty($btn_url) && empty($quote) && empty($signature)) {
    return;
}
?>

<section class="meet-bella-section">
    <div class="ls-container">
        <div class="meet-bella-grid">

            <?php if (!empty($image_url)) : ?>
                <div class="meet-bella-media">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="meet-bella-image" loading="lazy">
                </div>
            <?php endif; ?>

            <?php if (!empty($title) || !empty($description) || (!empty($btn_url) && !empty($btn_title))) : ?>
                <div class="meet-bella-content <?php echo empty($image_url) ? 'no-media' : ''; ?> <?php echo (empty($quote) && empty($signature)) ? 'no-quote' : ''; ?>">
                    <?php if (!empty($title)) : ?>
                        <h2 class="meet-bella-title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <div class="meet-bella-description">
                            <?php echo nl2br(esc_html($description)); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($btn_url) && !empty($btn_title)) : ?>
                        <div class="meet-bella-action">
                            <a href="<?php echo esc_url($btn_url); ?>" target="<?php echo esc_attr($btn_target); ?>" class="meet-bella-link" <?php echo ($btn_target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
                                <span><?php echo esc_html($btn_title); ?></span>
                                <span class="meet-bella-arrow" aria-hidden="true">
                                    <svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 3.5H22M22 3.5L18.5 0.5M22 3.5L18.5 6.5" stroke="currentColor" stroke-width="1"/>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($quote) || !empty($signature)) : ?>
                <div class="meet-bella-card">
                    <div class="meet-bella-quote-icon" aria-hidden="true">
                        <svg width="29" height="22" viewBox="0 0 29 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.74398 21.1199C5.54398 21.1199 3.69598 20.3719 2.19998 18.8759C0.733315 17.3506 -1.83582e-05 15.1653 -1.83582e-05 12.3199C-1.83582e-05 10.0613 0.601315 8.00794 1.80398 6.15994C3.03598 4.28261 4.78131 2.78661 7.03998 1.67194C9.32798 0.557272 11.704 -6.10352e-05 14.168 -6.10352e-05L15.048 1.23194C14.3146 1.23194 13.376 1.37861 12.232 1.67194C11.088 1.93594 9.95865 2.34661 8.84398 2.90394C7.72932 3.46127 6.80532 4.16527 6.07198 5.01594C4.95731 6.30661 4.39998 7.36261 4.39998 8.18394C4.63465 7.86127 5.16265 7.58261 5.98398 7.34794C6.80532 7.08394 7.68532 6.95194 8.62398 6.95194C10.9706 6.95194 12.76 7.91994 13.992 9.85594C14.432 7.89061 15.312 6.17461 16.632 4.70794C17.9813 3.24127 19.624 2.09727 21.56 1.27594C23.5253 0.425273 25.6373 -6.10352e-05 27.896 -6.10352e-05L28.776 1.23194C28.0426 1.23194 27.104 1.37861 25.96 1.67194C24.816 1.93594 23.6866 2.34661 22.572 2.90394C21.4573 3.46127 20.5333 4.16527 19.8 5.01594C18.6853 6.30661 18.128 7.36261 18.128 8.18394C18.3626 7.86127 18.8906 7.58261 19.712 7.34794C20.5333 7.08394 21.4133 6.95194 22.352 6.95194C23.6427 6.95194 24.772 7.25994 25.74 7.87594C26.708 8.49194 27.4706 9.32794 28.028 10.3839C28.5853 11.4106 28.864 12.5546 28.864 13.8159C28.864 15.9279 28.1893 17.6733 26.84 19.0519C25.52 20.4306 23.7306 21.1199 21.472 21.1199C19.9466 21.1199 18.568 20.7386 17.336 19.9759C16.104 19.2133 15.1946 18.1573 14.608 16.8079C14.0506 18.1573 13.1853 19.2133 12.012 19.9759C10.8386 20.7386 9.41598 21.1199 7.74398 21.1199Z" fill="currentColor"/>
                        </svg>
                    </div>

                    <?php if (!empty($quote)) : ?>
                        <blockquote class="meet-bella-quote">
                            <?php echo nl2br(esc_html($quote)); ?>
                        </blockquote>
                    <?php endif; ?>

                    <?php if (!empty($signature)) : ?>
                        <div class="meet-bella-signature">
                            <?php echo esc_html($signature); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
