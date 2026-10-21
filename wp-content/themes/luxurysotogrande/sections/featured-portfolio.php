<?php
/**
 * Featured Portfolio Section
 *
 * Displays a curated luxury property portfolio showcase with:
 * - Editorial header (Title, Subtitle, "Explore the Portfolio" action)
 * - 3-column responsive luxury cards grid
 * - Integrated ACF Relationship field with intelligent fallback to featured/latest properties
 * - Pure luxury typography (Playfair Display & Roboto)
 *
 * @package LuxurySotogrande
 * @date 21-10-2026
 */

// Retrieve ACF sub fields
$title_field    = get_sub_field('title');
$subtitle_field = get_sub_field('subtitle');
$button_link    = get_sub_field('button_link');
$selected_props = get_sub_field('properties');

// Title & Subtitle with defaults
$title    = !empty($title_field) ? $title_field : "LET'S FIND THE ONE";
$subtitle = ($subtitle_field !== null && $subtitle_field !== false && $subtitle_field !== '') ? $subtitle_field : 'Homes chosen, not listed.';

// Explore Link / Button
$button_url    = !empty($button_link['url']) ? $button_link['url'] : home_url('/sotogrande-villas-for-sale/all-sotogrande/');
$button_title  = !empty($button_link['title']) ? $button_link['title'] : __('EXPLORE THE PORTFOLIO', 'luxurysotogrande');
$button_target = !empty($button_link['target']) ? $button_link['target'] : '_self';

// Properties Resolution
$portfolio_properties = [];

if (!empty($selected_props) && is_array($selected_props)) {
    foreach ($selected_props as $p_item) {
        $p_id = is_object($p_item) ? $p_item->ID : (int)$p_item;
        if ($p_id && get_post_status($p_id) === 'publish') {
            $portfolio_properties[] = $p_id;
        }
    }
}

// Fallback: If no properties are selected or published, fetch 3 curated properties with thumbnails
if (empty($portfolio_properties)) {
    $fallback_query = get_posts([
        'post_type'      => 'property',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'meta_query'     => [
            [
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS',
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ]);

    if (!empty($fallback_query)) {
        $portfolio_properties = $fallback_query;
    }
}
?>

<section class="featured-portfolio-section">
    <div class="ls-container">
        
        <!-- Section Header -->
        <div class="featured-portfolio-header">
            <div class="featured-portfolio-title-group">
                <?php if ($title) : ?>
                    <h2 class="featured-portfolio-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ($subtitle) : ?>
                    <p class="featured-portfolio-subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($button_url && $button_title) : ?>
                <div class="featured-portfolio-action">
                    <a href="<?php echo esc_url($button_url); ?>" target="<?php echo esc_attr($button_target); ?>" class="featured-portfolio-link">
                        <span class="link-label"><?php echo esc_html($button_title); ?></span>
                        <svg class="link-arrow" width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 1L17 6M17 6L12 11M17 6H1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3-Column Luxury Cards Grid -->
        <?php if (!empty($portfolio_properties)) : ?>
            <div class="featured-portfolio-grid">
                <?php foreach ($portfolio_properties as $property_id) :
                    $prop_post = get_post($property_id);
                    if (!$prop_post) continue;

                    $permalink = get_permalink($property_id);

                    // Title Formatting: clean, editorial title
                    $raw_title = html_entity_decode($prop_post->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (preg_match('/(?:–|-)\s*(.+)$/u', $raw_title, $matches)) {
                        $card_title = ucwords(strtolower(trim($matches[1])));
                    } else {
                        $card_title = ucwords(strtolower(trim($raw_title)));
                    }

                    // Location / Area: Hierarchy without "All "
                    $area_terms = get_the_terms($property_id, 'property_area');
                    $area_name = '';
                    if ($area_terms && !is_wp_error($area_terms)) {
                        $valid_terms = array_filter($area_terms, function($t) {
                            return stripos($t->name, 'All ') === false;
                        });

                        // Prioritize specific subareas/areas
                        usort($valid_terms, function($a, $b) {
                            return (int)$b->parent - (int)$a->parent;
                        });

                        if (!empty($valid_terms)) {
                            $primary_term = reset($valid_terms);
                            $decoded_tname = html_entity_decode($primary_term->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $clean_tname = trim(str_ireplace('Sotogrande ', '', $decoded_tname));
                            $area_name = strtoupper($clean_tname);
                        }
                    }
                    if (empty($area_name)) {
                        $area_name = 'SOTOGRANDE';
                    }

                    // Reference ID
                    $ref_code = get_field('property_id', $property_id);
                    if (empty($ref_code)) {
                        $ref_code = 'LS' . $property_id;
                    }

                    // Pricing
                    $is_poa    = get_field('poa', $property_id);
                    $raw_price = get_field('property_price', $property_id);
                    $reduced   = get_field('property_reduced_price', $property_id);

                    if ($is_poa || (empty($raw_price) && empty($reduced))) {
                        $price_text = __('POA', 'luxurysotogrande');
                    } elseif (!empty($reduced) && is_numeric($reduced)) {
                        $price_text = '€' . number_format((float)$reduced);
                    } elseif (!empty($raw_price) && is_numeric($raw_price)) {
                        $price_text = '€' . number_format((float)$raw_price);
                    } else {
                        $price_text = __('POA', 'luxurysotogrande');
                    }

                    // Specs: Bedrooms & Bathrooms
                    $bedrooms  = get_field('bedrooms', $property_id);
                    $bathrooms = get_field('bathrooms', $property_id);

                    // Check if property is password protected / pocket listing
                    $post_password = get_post_field('post_password', $property_id);
                    $is_protected  = !empty($post_password) || post_password_required($property_id);

                    // Featured Image
                    $image_url = '';
                    $image_alt = esc_attr($card_title);

                    if ($is_protected) {
                        // Password Protected: Display the default key image (POCKET-LISTING.jpg)
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

                        if (empty($image_url)) {
                            $image_url = get_stylesheet_directory_uri() . '/assets/images/placeholder.jpg';
                        }
                    }

                    // Badges (Protected / Sold / Rented / Price Reduced)
                    $is_sold   = get_field('sold', $property_id);
                    $is_rented = get_field('rented', $property_id);
                ?>
                    <article class="featured-portfolio-card">
                        <a href="<?php echo esc_url($permalink); ?>" class="portfolio-card-link" aria-label="<?php echo esc_attr($card_title); ?>">
                            
                            <!-- Card Media / Image -->
                            <div class="portfolio-card-media">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo $image_alt; ?>" loading="lazy" class="portfolio-card-img" />
                                
                                <?php if ($is_protected) : ?>
                                    <span class="portfolio-badge badge-protected"><?php echo esc_html(__('PROTECTED', 'luxurysotogrande')); ?></span>
                                <?php elseif ($is_sold) : ?>
                                    <span class="portfolio-badge badge-sold"><?php echo esc_html(label('sold_label') ?: __('SOLD', 'luxurysotogrande')); ?></span>
                                <?php elseif ($is_rented) : ?>
                                    <span class="portfolio-badge badge-rented"><?php echo esc_html(label('rented_label') ?: __('RENTED', 'luxurysotogrande')); ?></span>
                                <?php elseif ($reduced) : ?>
                                    <span class="portfolio-badge badge-reduced"><?php echo esc_html(label('reduced_price_label') ?: __('PRICE REDUCED', 'luxurysotogrande')); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Card Body -->
                            <div class="portfolio-card-body">
                                
                                <!-- Top Meta: Area & Reference -->
                                <div class="portfolio-card-meta-top">
                                    <span class="portfolio-card-area"><?php echo esc_html($area_name); ?></span>
                                    <span class="portfolio-card-ref">REF: <?php echo esc_html($ref_code); ?></span>
                                </div>

                                <!-- Property Title -->
                                <h3 class="portfolio-card-title"><?php echo esc_html($card_title); ?></h3>

                                <!-- Bottom Meta: Price & Specs -->
                                <div class="portfolio-card-meta-bottom">
                                    <div class="portfolio-card-price">
                                        <?php echo esc_html($price_text); ?>
                                    </div>
                                    <div class="portfolio-card-specs">
                                        <?php if ($bedrooms) : ?>
                                            <span class="portfolio-spec-item" title="<?php echo esc_attr($bedrooms . ' ' . __('Bedrooms', 'luxurysotogrande')); ?>">
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
                                            <span class="portfolio-spec-item" title="<?php echo esc_attr($bathrooms . ' ' . __('Bathrooms', 'luxurysotogrande')); ?>">
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
