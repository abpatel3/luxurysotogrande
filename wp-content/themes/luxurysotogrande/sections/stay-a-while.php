<?php
/**
 * Stay A While - Luxury Holiday Rentals Section Template
 *
 * Layout: stay_a_while (Flexible Content in Page Builder)
 * Strictly ACF-driven: Title, subtitle, button link and selected properties
 * are fetched purely from ACF without any hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$title               = get_sub_field('title');
$subtitle            = get_sub_field('subtitle');
$button_link         = get_sub_field('button_link');
$selected_properties = get_sub_field('properties');

// Button Link Configuration
$button_url    = '';
$button_title  = '';
$button_target = '_self';

if (!empty($button_link)) {
    if (is_array($button_link)) {
        $button_url    = !empty($button_link['url']) ? $button_link['url'] : '';
        $button_title  = !empty($button_link['title']) ? $button_link['title'] : '';
        $button_target = !empty($button_link['target']) ? $button_link['target'] : '_self';
    } elseif (is_string($button_link)) {
        $button_url   = $button_link;
        $button_title = __('PLAN YOUR STAY', 'luxurysotogrande');
    }
}

// 2. Fetch Selected Properties strictly from ACF
$property_ids = [];

if (!empty($selected_properties) && is_array($selected_properties)) {
    foreach ($selected_properties as $prop) {
        if (is_object($prop) && isset($prop->ID)) {
            $property_ids[] = (int) $prop->ID;
        } elseif (is_numeric($prop)) {
            $property_ids[] = (int) $prop;
        }
    }
}

// Exit if nothing to display
if (empty($title) && empty($subtitle) && empty($property_ids)) {
    return;
}
?>

<section class="stay-a-while-section">
    <div class="ls-container">

        <!-- Header Group -->
        <?php if (!empty($title) || !empty($subtitle) || !empty($button_url)) : ?>
            <div class="stay-a-while-header">
                <div class="stay-a-while-title-group">
                    <?php if (!empty($title)) : ?>
                        <h2 class="stay-a-while-title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($subtitle)) : ?>
                        <p class="stay-a-while-subtitle"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($button_url) && !empty($button_title)) : ?>
                    <div class="stay-a-while-action">
                        <a href="<?php echo esc_url($button_url); ?>" class="stay-a-while-link" target="<?php echo esc_attr($button_target); ?>">
                            <span><?php echo esc_html($button_title); ?></span>
                            <svg class="link-arrow" width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 1L15 5M15 5L11 9M15 5H1" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Selected Rentals Grid -->
        <?php if (!empty($property_ids)) : ?>
            <div class="stay-a-while-grid">
                <?php
                foreach ($property_ids as $property_id) :
                    // Property Title
                    $raw_title  = get_the_title($property_id);
                    $card_title = !empty($raw_title) ? html_entity_decode($raw_title, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';

                    // Area Taxonomy: Hierarchy (Main Area > Sub-area)
                    $area_name_html = '';
                    $terms = get_the_terms($property_id, 'property_area');
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $valid_terms = array_filter($terms, function($t) {
                            return stripos($t->name, 'All ') === false && $t->slug !== 'all-sotogrande';
                        });

                        if (!empty($valid_terms)) {
                            $child_term  = null;
                            $parent_term = null;

                            $clean_area_name = function($name) {
                                $decoded = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $cleaned = preg_replace('/^Sotogrande\s+La\s+Reserva/i', 'La Reserva', $decoded);
                                $cleaned = preg_replace('/^Sotogrande\s+/i', '', $cleaned);
                                $cleaned = preg_replace('/\s*-\s*Sotogrande/i', '', $cleaned);
                                return strtoupper(trim($cleaned));
                            };

                            foreach ($valid_terms as $t) {
                                if (!empty($t->parent)) {
                                    $child_term = $t;
                                    $p_obj = get_term($t->parent, 'property_area');
                                    if ($p_obj && !is_wp_error($p_obj) && stripos($p_obj->name, 'All ') === false) {
                                        $parent_term = $p_obj;
                                    }
                                    break;
                                }
                            }

                            if (!$parent_term) {
                                foreach ($valid_terms as $t) {
                                    if (empty($t->parent)) {
                                        $parent_term = $t;
                                        break;
                                    }
                                }
                            }

                            if ($parent_term && $child_term && $parent_term->term_id !== $child_term->term_id) {
                                $p_name = $clean_area_name($parent_term->name);
                                $c_name = $clean_area_name($child_term->name);
                                if ($p_name === $c_name) {
                                    $area_name_html = esc_html($p_name);
                                } else {
                                    $area_name_html = '<span class="area-main">' . esc_html($p_name) . '</span><span class="area-sep"> &gt; </span><span class="area-sub">' . esc_html($c_name) . '</span>';
                                }
                            } elseif ($child_term) {
                                $area_name_html = esc_html($clean_area_name($child_term->name));
                            } elseif ($parent_term) {
                                $area_name_html = esc_html($clean_area_name($parent_term->name));
                            } else {
                                $first = reset($valid_terms);
                                $area_name_html = esc_html($clean_area_name($first->name));
                            }
                        }
                    }

                    // Reference ID
                    $ref_code = get_field('property_id', $property_id);

                    // Rates / Pricing for Stays (Default to Step Inside)
                    $is_poa    = get_field('poa', $property_id);
                    $raw_price = get_field('property_price', $property_id);

                    if (!$is_poa && !empty($raw_price) && is_numeric($raw_price) && (float)$raw_price < 100000) {
                        $rate_text = '€' . number_format((float)$raw_price) . ' / ' . __('week', 'luxurysotogrande');
                    } else {
                        $rate_text = __('Step Inside', 'luxurysotogrande');
                    }

                    // Specs: Internal Area, Plot Size, Bedrooms & Bathrooms
                    $internal_raw = get_field('internal_size', $property_id);
                    $plot_raw     = get_field('plot_size', $property_id);
                    $bedrooms     = get_field('bedrooms', $property_id);
                    $bathrooms    = get_field('bathrooms', $property_id);

                    // Internal Area formatting
                    $internal_size = '';
                    if (!empty($internal_raw)) {
                        if (is_array($internal_raw) && !empty($internal_raw['size'])) {
                            $val  = trim((string)$internal_raw['size']);
                            $post = !empty($internal_raw['postfix_text']) ? trim((string)$internal_raw['postfix_text']) : 'm²';
                            if (preg_match('/m(?:2|²)|metro|sq/i', $post)) {
                                $post = 'm²';
                            }
                            $internal_size = $val . ' ' . $post;
                        } elseif (is_string($internal_raw) && trim($internal_raw) !== '') {
                            $internal_size = trim($internal_raw) . ' m²';
                        }
                    }

                    // Plot Size formatting
                    $plot_size = '';
                    if (!empty($plot_raw)) {
                        if (is_array($plot_raw) && !empty($plot_raw['size'])) {
                            $val  = trim((string)$plot_raw['size']);
                            $post = !empty($plot_raw['postfix_text']) ? trim((string)$plot_raw['postfix_text']) : 'm²';
                            if (preg_match('/m(?:2|²)|metro|sq/i', $post)) {
                                $post = 'm²';
                            }
                            $plot_size = $val . ' ' . $post;
                        } elseif (is_string($plot_raw) && trim($plot_raw) !== '') {
                            $plot_size = trim($plot_raw) . ' m²';
                        }
                    }

                    // Password Protected Check
                    $post_password = get_post_field('post_password', $property_id);
                    $is_protected  = !empty($post_password) || post_password_required($property_id);

                    // Featured Image
                    $image_url = '';
                    $image_alt = esc_attr($card_title);

                    if ($is_protected) {
                        $image_url = get_stylesheet_directory_uri() . '/assets/images/POCKET-LISTING.jpg';
                        $image_alt = __('Password Protected', 'luxurysotogrande');
                    } else {
                        if (has_post_thumbnail($property_id)) {
                            $thumb_id  = get_post_thumbnail_id($property_id);
                            $image_url = wp_get_attachment_image_url($thumb_id, 'large');
                            $meta_alt  = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
                            if (!empty($meta_alt)) {
                                $image_alt = esc_attr($meta_alt);
                            }
                        }

                        if (empty($image_url)) {
                            $gallery = get_field('gallery', $property_id);
                            if (!empty($gallery[0])) {
                                if (is_array($gallery[0]) && !empty($gallery[0]['ID'])) {
                                    $image_url = wp_get_attachment_image_url($gallery[0]['ID'], 'large');
                                } elseif (is_numeric($gallery[0])) {
                                    $image_url = wp_get_attachment_image_url($gallery[0], 'large');
                                } elseif (is_array($gallery[0]) && !empty($gallery[0]['url'])) {
                                    $image_url = $gallery[0]['url'];
                                }
                            }
                        }
                    }
                ?>
                    <article class="stay-a-while-card">
                        <a href="<?php echo esc_url(get_permalink($property_id)); ?>" class="stay-card-link">

                            <!-- Card Media -->
                            <div class="stay-card-media">
                                <?php if (!empty($image_url)) : ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="stay-card-img" loading="lazy">
                                <?php endif; ?>
                                <?php if ($is_protected) : ?>
                                    <span class="portfolio-badge badge-protected"><?php echo esc_html(__('PROTECTED', 'luxurysotogrande')); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Card Body -->
                            <div class="stay-card-body">
                                <!-- Top Meta: Area & Ref -->
                                <?php if (!empty($area_name_html) || !empty($ref_code)) : ?>
                                    <div class="stay-card-meta-top">
                                        <?php if (!empty($area_name_html)) : ?>
                                            <span class="stay-card-area"><?php echo $area_name_html; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($ref_code)) : ?>
                                            <span class="stay-card-ref"><?php echo esc_html('REF: ' . ltrim($ref_code, 'REF: ')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Property Title -->
                                <?php if (!empty($card_title)) : ?>
                                    <h3 class="stay-card-title"><?php echo esc_html($card_title); ?></h3>
                                <?php endif; ?>

                                <!-- Bottom Meta: Rate on request & Specs -->
                                <div class="stay-card-meta-bottom">
                                    <span class="stay-card-rate"><?php echo esc_html($rate_text); ?></span>

                                    <div class="stay-card-specs">
                                        <?php if (!empty($internal_size)) : ?>
                                            <span class="stay-spec-item" title="<?php echo esc_attr(__('Internal Area: ', 'luxurysotogrande') . $internal_size); ?>">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path d="M1.875 6.875L7.5 2.5L13.125 6.875" stroke="currentColor" stroke-width="1.0625" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M3.125 6.25V12.5C3.125 12.8452 3.40482 13.125 3.75 13.125H11.25C11.5952 13.125 11.875 12.8452 11.875 12.5V6.25" stroke="currentColor" stroke-width="1.0625" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M6.25 13.125V9.375H8.75V13.125" stroke="currentColor" stroke-width="1.0625" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span class="spec-count"><?php echo esc_html($internal_size); ?></span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($plot_size)) : ?>
                                            <span class="stay-spec-item" title="<?php echo esc_attr(__('Plot Size: ', 'luxurysotogrande') . $plot_size); ?>">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path d="M7.5 1.875L4.0625 6.25H5.9375L2.8125 10.625H6.5625V13.125H8.4375V10.625H12.1875L9.0625 6.25H10.9375L7.5 1.875Z" stroke="currentColor" stroke-width="1.0625" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span class="spec-count"><?php echo esc_html($plot_size); ?></span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($bedrooms) : ?>
                                            <span class="stay-spec-item" title="<?php echo esc_attr($bedrooms . ' ' . __('Bedrooms', 'luxurysotogrande')); ?>">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.875 11.25V7.5C1.875 7.16848 2.0067 6.85054 2.24112 6.61612C2.47554 6.3817 2.79348 6.25 3.125 6.25H11.875C12.2065 6.25 12.5245 6.3817 12.7589 6.61612C12.9933 6.85054 13.125 7.16848 13.125 7.5V11.25" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                    <path d="M1.5625 11.25H13.4375" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                    <path d="M3.125 6.25V4.375C3.125 4.04348 3.2567 3.72554 3.49112 3.49112C3.72554 3.2567 4.04348 3.125 4.375 3.125H10.625C10.9565 3.125 11.2745 3.2567 11.5089 3.49112C11.7433 3.72554 11.875 4.04348 11.875 4.375V6.25" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                    <path d="M5 6.25V5.3125H10V6.25" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                </svg>
                                                <span class="spec-count"><?php echo esc_html($bedrooms); ?></span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($bathrooms) : ?>
                                            <span class="stay-spec-item" title="<?php echo esc_attr($bathrooms . ' ' . __('Bathrooms', 'luxurysotogrande')); ?>">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.5 7.5H12.5V9.0625C12.5 9.43184 12.4273 9.79757 12.2859 10.1388C12.1446 10.48 11.9374 10.7901 11.6762 11.0512C11.4151 11.3124 11.105 11.5196 10.7638 11.6609C10.4226 11.8023 10.0568 11.875 9.6875 11.875H5.3125C4.56658 11.875 3.85121 11.5787 3.32376 11.0512C2.79632 10.5238 2.5 9.80842 2.5 9.0625V7.5Z" stroke-width="1.0625" stroke-linejoin="round" stroke="currentColor"/>
                                                    <path d="M3.75 7.5V4.0625C3.75 3.6481 3.91462 3.25067 4.20765 2.95765C4.50067 2.66462 4.8981 2.5 5.3125 2.5C5.7269 2.5 6.12433 2.66462 6.41735 2.95765C6.71038 3.25067 6.875 3.6481 6.875 4.0625" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                    <path d="M4.0625 11.875L3.4375 13.125M10.9375 11.875L11.5625 13.125" stroke-width="1.0625" stroke-linecap="round" stroke="currentColor"/>
                                                </svg>
                                                <span class="spec-count"><?php echo esc_html($bathrooms); ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
