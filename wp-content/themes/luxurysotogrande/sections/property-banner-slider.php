<?php
/**
 * Template: Property Banner Slider Section
 * Displays a luxury property hero slider with glassmorphism info card
 * and custom bottom-right controls pill matching the design mockup.
 */

$raw_slides = get_sub_field('slides');
$raw_autoplay = get_sub_field('autoplay');
$autoplay = true; // Enabled by default as requested: auto-slide 3s
$raw_speed = get_sub_field('autoplay_speed');
$autoplay_speed = !empty($raw_speed) ? ((int) $raw_speed * 1000) : 3000;
if ($autoplay_speed <= 0) {
    $autoplay_speed = 3000;
}

$slides = [];
if (!empty($raw_slides) && is_array($raw_slides)) {
    $slides = $raw_slides;
}

// Fallback if no slides added yet
if (empty($slides)) {
    $fallback_query = get_posts([
        'post_type'      => 'property',
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'post__in'       => [6104, 6106, 6107],
        'orderby'        => 'post__in',
    ]);
    if (empty($fallback_query)) {
        $fallback_query = get_posts([
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => 4,
            'meta_key'       => 'featured',
            'meta_value'     => true,
        ]);
    }
    if (!empty($fallback_query)) {
        foreach ($fallback_query as $fp) {
            $slides[] = [
                'property'     => $fp->ID,
                'button_text'  => __('VIEW THIS PROPERTY', 'luxurysotogrande'),
                'enquire_text' => __('ENQUIRE', 'luxurysotogrande'),
            ];
        }
    }
}

if (empty($slides)) {
    return;
}

$slider_id = 'prop-hero-slider-' . wp_unique_id();
$default_tel = get_field('telephone', 'options') ?: '+34 956 123 456';
$clean_wa = preg_replace('/^00/', '', preg_replace('/[^0-9]/', '', $default_tel));
?>

<section class="property-hero-slider-wrap" 
         id="<?php echo esc_attr($slider_id); ?>"
         data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
         data-autoplay-speed="<?php echo esc_attr($autoplay_speed); ?>">

    <!-- Main Slider Track -->
    <div class="property-hero-slider-main">
        <?php foreach ($slides as $index => $slide) : 
            $raw_prop_id = !empty($slide['property']) ? (is_object($slide['property']) ? $slide['property']->ID : (int) $slide['property']) : null;
            // WPML support: translate property ID to active language
            $prop_id = $raw_prop_id ? apply_filters('wpml_object_id', $raw_prop_id, 'property', true) : null;
            $property = $prop_id ? get_post($prop_id) : null;

            // Background Image
            $bg_image_url = '';
            if (!empty($slide['custom_image'])) {
                if (is_array($slide['custom_image']) && !empty($slide['custom_image']['url'])) {
                    $bg_image_url = $slide['custom_image']['url'];
                } elseif (is_numeric($slide['custom_image'])) {
                    $bg_image_url = wp_get_attachment_image_url((int)$slide['custom_image'], 'full');
                } elseif (is_string($slide['custom_image'])) {
                    $bg_image_url = $slide['custom_image'];
                }
            }
            if (!$bg_image_url && $prop_id) {
                $thumb_id = get_post_thumbnail_id($prop_id);
                if ($thumb_id) {
                    $bg_image_url = wp_get_attachment_image_url($thumb_id, 'full');
                } else {
                    $gallery = get_field('gallery', $prop_id);
                    if (!empty($gallery[0]['ID'])) {
                        $bg_image_url = wp_get_attachment_image_url($gallery[0]['ID'], 'full');
                    } elseif (!empty($gallery[0]['url'])) {
                        $bg_image_url = $gallery[0]['url'];
                    }
                }
            }

            // Title
            if (!empty($slide['custom_title'])) {
                $title = $slide['custom_title'];
            } elseif ($property) {
                $raw_title = html_entity_decode($property->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if (preg_match('/(?:–|-)\s*(.+)$/u', $raw_title, $matches)) {
                    $title = ucwords(strtolower(trim($matches[1])));
                } else {
                    $title = ucwords(strtolower(trim($raw_title)));
                }
            } else {
                $title = 'Luxury Sotogrande Villa';
            }

            // Add elegant ending period if not present
            if ($title && !in_array(substr(rtrim($title), -1), ['.', '!', '?'])) {
                $title_display = rtrim($title) . '.';
            } else {
                $title_display = $title;
            }

            // Reference (supports both custom_reference and custom_ref)
            $ref = !empty($slide['custom_reference']) ? $slide['custom_reference'] : (!empty($slide['custom_ref']) ? $slide['custom_ref'] : ($prop_id ? get_field('property_id', $prop_id) : ''));

            // Location / Area breadcrumb
            if (!empty($slide['custom_location'])) {
                $location = strtoupper($slide['custom_location']);
            } elseif ($prop_id) {
                $area_terms = get_the_terms($prop_id, 'property_area');
                if ($area_terms && !is_wp_error($area_terms)) {
                    $valid_terms = array_filter($area_terms, function($t) {
                        return stripos($t->name, 'All ') === false;
                    });
                    usort($valid_terms, function($a, $b) {
                        return (int)$a->parent - (int)$b->parent;
                    });
                    $clean_names = [];
                    foreach ($valid_terms as $t) {
                        $name = trim(str_ireplace('Sotogrande ', '', $t->name));
                        if (!in_array(strtoupper($name), array_map('strtoupper', $clean_names))) {
                            $clean_names[] = strtoupper($name);
                        }
                    }
                    $location = !empty($clean_names) ? implode(' › ', $clean_names) : 'SOTOGRANDE';
                } else {
                    $location = 'SOTOGRANDE';
                }
            } else {
                $location = 'SOTOGRANDE';
            }

            // Specs
            if (!empty($slide['custom_specs'])) {
                $specs_text = $slide['custom_specs'];
            } elseif ($prop_id) {
                $specs = [];
                $beds = get_field('bedrooms', $prop_id);
                if ($beds) {
                    $specs[] = $beds . ' ' . __('BEDROOMS', 'luxurysotogrande');
                }
                $baths = get_field('bathrooms', $prop_id);
                if ($baths) {
                    $specs[] = $baths . ' ' . __('BATHROOMS', 'luxurysotogrande');
                }
                $plot = get_field('plot_size', $prop_id);
                if (!empty($plot['size'])) {
                    $p_size = str_replace([' ', ','], '', (string)$plot['size']);
                    $p_formatted = is_numeric($p_size) ? number_format((float)$p_size) : $plot['size'];
                    $postfix = !empty($plot['postfix_text']) ? str_ireplace('m2', 'M²', $plot['postfix_text']) : 'M²';
                    $specs[] = __('PLOT', 'luxurysotogrande') . ' ' . esc_html($p_formatted) . ' ' . esc_html(strtoupper($postfix));
                }
                $internal = get_field('internal_size', $prop_id);
                if (!empty($internal['size'])) {
                    $i_size = str_replace([' ', ','], '', (string)$internal['size']);
                    $i_formatted = is_numeric($i_size) ? number_format((float)$i_size) : $internal['size'];
                    $postfix = !empty($internal['postfix_text']) ? str_ireplace('m2', 'M²', $internal['postfix_text']) : 'M²';
                    $specs[] = __('INTERNAL', 'luxurysotogrande') . ' ' . esc_html($i_formatted) . ' ' . esc_html(strtoupper($postfix));
                }
                $specs_text = implode(' · ', $specs);
            } else {
                $specs_text = '';
            }

            // Price (fixed so no double €)
            if (!empty($slide['custom_price'])) {
                $price_display = $slide['custom_price'];
            } elseif ($prop_id) {
                $is_poa = get_field('poa', $prop_id);
                $raw_price = get_field('property_price', $prop_id);
                if ($is_poa || empty($raw_price)) {
                    $price_display = __('Price on Application', 'luxurysotogrande');
                } else {
                $raw_postfix = trim((string) get_field('price_postfix_text', $prop_id));
                $decoded_postfix = trim(html_entity_decode($raw_postfix, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (in_array($decoded_postfix, ['€', 'EUR', 'eur', '&euro;'], true)) {
                    $postfix = '';
                } else {
                    $postfix = $decoded_postfix;
                }
                $price_display = '€' . number_format((float) $raw_price) . ($postfix ? ' ' . $postfix : '');
                }
            } else {
                $price_display = __('Price on Application', 'luxurysotogrande');
            }

            // Links
            $permalink = $prop_id ? get_permalink($prop_id) : '#';
            $button_text = !empty($slide['button_text']) ? $slide['button_text'] : __('VIEW THIS PROPERTY', 'luxurysotogrande');
            $enquire_text = !empty($slide['enquire_text']) ? $slide['enquire_text'] : __('ENQUIRE', 'luxurysotogrande');
            $enquire_url = home_url('/contact/?property_ref=' . urlencode($ref) . '&property_id=' . ($prop_id ?: ''));
            $wa_message = urlencode("Hello, I am interested in property {$title_display} (Ref: {$ref}).");
            $wa_url = "https://wa.me/{$clean_wa}?text={$wa_message}";
        ?>
            <div class="property-hero-slide">
                
                <!-- Background Image & Gradient Overlay -->
                <div class="property-hero-bg">
                    <?php if ($bg_image_url) : ?>
                        <img src="<?php echo esc_url($bg_image_url); ?>" 
                             alt="<?php echo esc_attr($title); ?>">
                    <?php endif; ?>
                    <div class="property-hero-overlay"></div>
                </div>

                <!-- Floating Glassmorphism Property Card -->
                <div class="property-hero-container ls-container">
                    <div class="property-hero-card">
                        
                        <!-- Top Gold Accent Indicator -->
                        <div class="property-hero-card-accent"></div>

                        <!-- Top Meta: Location & Ref -->
                        <div class="property-hero-card-meta">
                            <span class="property-hero-location"><?php echo esc_html($location); ?></span>
                            <?php if ($ref) : ?>
                                <span class="property-hero-ref"><?php echo esc_html(__('REF:', 'luxurysotogrande') . ' ' . $ref); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Property Title (Editorial Serif) -->
                        <h2 class="property-hero-title">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <?php echo esc_html($title_display); ?>
                            </a>
                        </h2>

                        <!-- Specs Line -->
                        <?php if ($specs_text) : ?>
                            <div class="property-hero-specs">
                                <?php echo esc_html($specs_text); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Price Line -->
                        <div class="property-hero-price">
                            <?php echo esc_html($price_display); ?>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="property-hero-actions">
                            <!-- View Property Button -->
                            <a href="<?php echo esc_url($permalink); ?>" class="property-hero-btn-view">
                                <span><?php echo esc_html($button_text); ?></span>
                                <span class="property-hero-btn-arrow"><svg width="19" height="6" viewBox="0 0 19 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.068 5.06999H15.327C15.743 4.14699 16.172 3.44499 16.575 2.98999H5.65127e-06V2.06699H16.575C16.159 1.61199 15.743 0.909986 15.327 -1.34408e-05H16.068C16.978 1.06599 17.94 1.84599 18.928 2.35299V2.71699C17.94 3.22399 16.978 4.00399 16.068 5.06999Z" fill="#7E692F"/>
                                </svg>
                                </span>
                            </a>

                            <!-- Enquire Link -->
                            <a href="<?php echo esc_url($enquire_url); ?>" class="property-hero-btn-enquire">
                                <?php echo esc_html($enquire_text); ?>
                            </a>

                            <!-- WhatsApp Circular Button -->
                            <a href="<?php echo esc_url($wa_url); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               aria-label="<?php esc_attr_e('Chat on WhatsApp', 'luxurysotogrande'); ?>"
                               class="property-hero-btn-wa">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- Floating Custom Slider Navigation (Bottom Right Pill within ls-container) -->
    <div class="property-hero-controls-wrap ls-container">
        <div class="property-hero-controls-pill">
            <!-- Prev Arrow -->
            <button type="button" class="property-hero-nav-btn property-hero-prev" aria-label="<?php esc_attr_e('Previous Slide', 'luxurysotogrande'); ?>">
                <svg width="15" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 5H1M1 5L5 1M1 5L5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Slide Dash Indicators -->
            <div class="property-hero-dashes">
                <?php for ($i = 0; $i < count($slides); $i++) : ?>
                    <button type="button" 
                            class="property-hero-dash <?php echo $i === 0 ? 'is-active' : ''; ?>" 
                            data-slide-index="<?php echo $i; ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Go to slide %d', 'luxurysotogrande'), $i + 1)); ?>">
                    </button>
                <?php endfor; ?>
            </div>

            <!-- Next Arrow -->
            <button type="button" class="property-hero-nav-btn property-hero-next" aria-label="<?php esc_attr_e('Next Slide', 'luxurysotogrande'); ?>">
                <svg width="15" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 5H15M15 5L11 1M15 5L11 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Pause / Play Toggle Button -->
            <button type="button" class="property-hero-pause-btn" aria-label="<?php esc_attr_e('Pause slider autoplay', 'luxurysotogrande'); ?>" data-state="playing">
                <!-- Pause Icon (Default) -->
                <svg class="property-hero-icon-pause" width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                </svg>
                <!-- Play Icon (When Paused) -->
                <svg class="property-hero-icon-play hidden" width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
        </div>
    </div>

</section>
