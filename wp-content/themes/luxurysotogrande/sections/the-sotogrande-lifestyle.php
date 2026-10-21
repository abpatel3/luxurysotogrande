<?php
/**
 * The Sotogrande Lifestyle Section
 *
 * Layout: the_sotogrande_lifestyle (Flexible Content in Page Builder)
 * Strictly ACF-driven: Title, subtitle, button link and lifestyle cards
 * are fetched purely from ACF without any hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

$title       = get_sub_field('title');
$subtitle    = get_sub_field('subtitle');
$button_link = get_sub_field('button_link');
$items       = get_sub_field('items');

// Button link resolution strictly from ACF
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
        $btn_title = __('EXPLORE LIFESTYLE', 'luxurysotogrande');
    }
}
?>

<section class="lifestyle-section">
    <div class="ls-container">

        <!-- Header -->
        <?php if (!empty($title) || !empty($subtitle) || !empty($btn_title)) : ?>
            <div class="lifestyle-header">
                <div class="lifestyle-header-content">
                    <?php if (!empty($title)) : ?>
                        <h2 class="lifestyle-title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($subtitle)) : ?>
                        <p class="lifestyle-subtitle"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($btn_title) && !empty($btn_url)) : ?>
                    <div class="lifestyle-header-action">
                        <a href="<?php echo esc_url($btn_url); ?>" class="lifestyle-link" target="<?php echo esc_attr($btn_target); ?>">
                            <span class="lifestyle-link-text"><?php echo esc_html($btn_title); ?></span>
                            <span class="lifestyle-link-arrow"><svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.776 6.24012H18.864C19.376 5.10411 19.904 4.24012 20.4 3.68011H-3.99947e-05V2.54412H20.4C19.888 1.98412 19.376 1.12012 18.864 0.000114918H19.776C20.896 1.31211 22.08 2.27211 23.296 2.89611V3.34412C22.08 3.96811 20.896 4.92811 19.776 6.24012Z" fill="currentColor" />
                                </svg>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Lifestyle Grid -->
        <?php if (!empty($items) && is_array($items)) : ?>
            <div class="lifestyle-grid">
                <?php foreach ($items as $item) : 
                    $i_title = !empty($item['title']) ? $item['title'] : '';
                    $i_image = !empty($item['image']) ? $item['image'] : '';
                    $i_link  = !empty($item['link']) ? $item['link'] : null;

                    // Image handling
                    $img_url = '';
                    $img_alt = !empty($i_title) ? $i_title : 'Sotogrande Lifestyle';

                    if (!empty($i_image)) {
                        if (is_array($i_image)) {
                            $img_url = !empty($i_image['url']) ? $i_image['url'] : '';
                            $img_alt = !empty($i_image['alt']) ? $i_image['alt'] : $img_alt;
                        } elseif (is_numeric($i_image)) {
                            $img_url = wp_get_attachment_image_url($i_image, 'large');
                            $alt     = get_post_meta($i_image, '_wp_attachment_image_alt', true);
                            if (!empty($alt)) {
                                $img_alt = $alt;
                            }
                        } elseif (is_string($i_image)) {
                            $img_url = $i_image;
                        }
                    }

                    // Link handling
                    $card_url    = '';
                    $card_target = '_self';
                    if (!empty($i_link)) {
                        if (is_array($i_link)) {
                            $card_url    = !empty($i_link['url']) ? $i_link['url'] : '';
                            $card_target = !empty($i_link['target']) ? $i_link['target'] : '_self';
                        } elseif (is_string($i_link)) {
                            $card_url = $i_link;
                        }
                    }

                    $tag = !empty($card_url) ? 'a' : 'div';
                ?>
                    <<?php echo $tag; ?> <?php if (!empty($card_url)) : ?>href="<?php echo esc_url($card_url); ?>" target="<?php echo esc_attr($card_target); ?>"<?php endif; ?> class="lifestyle-card">
                        <div class="lifestyle-card-media">
                            <?php if (!empty($img_url)) : ?>
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="lifestyle-card-img" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($i_title)) : ?>
                            <span class="lifestyle-card-title"><?php echo esc_html($i_title); ?></span>
                        <?php endif; ?>
                    </<?php echo $tag; ?>>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
