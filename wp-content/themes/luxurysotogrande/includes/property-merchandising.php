<?php
/**
 * Property Merchandising & Ordering System
 * Added on: 12-10-2026
 * 
 * - Ensures all properties for a category/area show on the right side (selected/order list) in admin
 * - Automatically prepends new properties to the top of the right-side list
 * - Synchronizes properties order with frontend templates
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper to get term IDs from a page's ACF field or post meta.
 */
function lsg_get_page_term_ids($page_id, $field_name) {
    $field = function_exists('get_field') ? get_field($field_name, $page_id) : null;
    if (!$field) {
        $field = get_post_meta($page_id, $field_name, true);
        $field = maybe_unserialize($field);
    }
    if (!$field) {
        return [];
    }

    $ids = [];
    if (is_array($field)) {
        foreach ($field as $item) {
            if (is_object($item) && isset($item->term_id)) {
                $ids[] = (int) $item->term_id;
            } elseif (is_numeric($item)) {
                $ids[] = (int) $item;
            }
        }
    } elseif (is_object($field) && isset($field->term_id)) {
        $ids[] = (int) $field->term_id;
    } elseif (is_numeric($field)) {
        $ids[] = (int) $field;
    }
    return array_unique($ids);
}

/**
 * Get all published property IDs matching the page's area, status, and type.
 */
function lsg_get_properties_for_page($page_id) {
    $status_ids = lsg_get_page_term_ids($page_id, 'property_status');
    $area_ids   = lsg_get_page_term_ids($page_id, 'property_area');
    $type_ids   = lsg_get_page_term_ids($page_id, 'property_types');

    // If both are empty, fallback to checking slug in property_area taxonomy
    if (empty($area_ids)) {
        $post = get_post($page_id);
        if ($post) {
            $term = get_term_by('slug', $post->post_name, 'property_area');
            if ($term) {
                $area_ids = [(int) $term->term_id];
            }
        }
    }

    if (empty($area_ids) && empty($status_ids)) {
        return [];
    }

    $tax_query = ['relation' => 'AND'];
    if (!empty($status_ids)) {
        $tax_query[] = [
            'taxonomy' => 'property_status',
            'field'    => 'term_id',
            'terms'    => $status_ids,
        ];
    }
    if (!empty($area_ids)) {
        $tax_query[] = [
            'taxonomy' => 'property_area',
            'field'    => 'term_id',
            'terms'    => $area_ids,
            'include_children' => true,
        ];
    }
    if (!empty($type_ids)) {
        $tax_query[] = [
            'taxonomy' => 'property_type',
            'field'    => 'term_id',
            'terms'    => $type_ids,
        ];
    }

    // Support WPML if active
    $lang_code = null;
    if (function_exists('apply_filters')) {
        $lang_details = apply_filters('wpml_post_language_details', null, $page_id);
        if (!empty($lang_details['language_code'])) {
            $lang_code = $lang_details['language_code'];
        }
    }

    $query_args = [
        'post_type'              => 'property',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'tax_query'              => $tax_query,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ];

    if ($lang_code && function_exists('do_action')) {
        do_action('wpml_switch_language', $lang_code);
    }

    $query = new WP_Query($query_args);
    $property_ids = !empty($query->posts) ? array_map('intval', $query->posts) : [];

    return $property_ids;
}

/**
 * Check if a page template is an allowed property listing template.
 */
function lsg_is_property_listing_page($page_id) {
    $template = get_page_template_slug($page_id);
    $allowed = [
        'templates/template-sale_property.php',
        'templates/template-rental_property.php',
        'templates/template-long_rentals.php',
        'templates/template-prorepty.php'
    ];
    return in_array($template, $allowed, true);
}

/**
 * 1. Filter ACF load_value for properties_order:
 * Automatically populates all matching properties on the RIGHT SIDE in admin,
 * and ensures any newly created properties are placed at the TOP on the right side.
 */
add_filter('acf/load_value/name=properties_order', function($value, $post_id, $field) {
    if (!is_admin()) {
        return $value;
    }

    if (!is_numeric($post_id) || (int) $post_id <= 0) {
        return $value;
    }

    if (!lsg_is_property_listing_page($post_id)) {
        return $value;
    }

    $all_matching_ids = lsg_get_properties_for_page($post_id);
    if (empty($all_matching_ids)) {
        return $value;
    }

    $saved_ids = is_array($value) ? array_map('intval', array_filter($value)) : [];

    // If currently empty, load ALL matching properties into the right side
    if (empty($saved_ids)) {
        return $all_matching_ids;
    }

    // Keep existing custom-ordered properties that are still valid and published
    $existing_ordered = array_values(array_intersect($saved_ids, $all_matching_ids));

    // Identify newly added properties not yet in the ordered list
    $new_property_ids = array_values(array_diff($all_matching_ids, $saved_ids));

    // Place newly added properties at the TOP on the right side
    $final_order = array_merge($new_property_ids, $existing_ordered);

    return $final_order;
}, 20, 3);

/**
 * 2. Sync property with listing pages:
 * - Automatically adds property to TOP of matching area pages
 * - Immediately REMOVES property from non-matching area pages
 */
function lsg_sync_property_with_pages($post_id) {
    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish' || $post->post_type !== 'property') {
        return;
    }

    $prop_area_terms   = wp_get_post_terms($post_id, 'property_area', ['fields' => 'ids']);
    $prop_status_terms = wp_get_post_terms($post_id, 'property_status', ['fields' => 'ids']);

    if (is_wp_error($prop_area_terms)) $prop_area_terms = [];
    if (is_wp_error($prop_status_terms)) $prop_status_terms = [];

    // Find all listing pages
    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => '_wp_page_template',
                'value'   => [
                    'templates/template-sale_property.php',
                    'templates/template-rental_property.php',
                    'templates/template-long_rentals.php',
                    'templates/template-prorepty.php'
                ],
                'compare' => 'IN'
            ]
        ],
        'suppress_filters' => false,
    ]);

    foreach ($pages as $page) {
        $page_id = $page->ID;
        $page_areas  = lsg_get_page_term_ids($page_id, 'property_area');
        $page_status = lsg_get_page_term_ids($page_id, 'property_status');

        $status_match = empty($page_status) || !empty(array_intersect($prop_status_terms, $page_status));
        $area_match   = empty($page_areas)  || !empty(array_intersect($prop_area_terms, $page_areas));

        $current_order = get_post_meta($page_id, 'properties_order', true);
        $current_order = maybe_unserialize($current_order);
        if (!is_array($current_order)) {
            $current_order = [];
        } else {
            $current_order = array_map('intval', array_filter($current_order));
        }

        if ($status_match && $area_match) {
            // Belongs to this page -> add to TOP if not present
            if (!in_array((int) $post_id, $current_order, true)) {
                array_unshift($current_order, (int) $post_id);
                update_post_meta($page_id, 'properties_order', $current_order);
                if (function_exists('update_field')) {
                    update_field('properties_order', $current_order, $page_id);
                }
            }
        } else {
            // Does NOT belong to this page -> remove immediately if present!
            if (in_array((int) $post_id, $current_order, true)) {
                $current_order = array_values(array_filter($current_order, fn($id) => (int)$id !== (int)$post_id));
                update_post_meta($page_id, 'properties_order', $current_order);
                if (function_exists('update_field')) {
                    update_field('properties_order', $current_order, $page_id);
                }
            }
        }
    }
}

add_action('save_post_property', function($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    lsg_sync_property_with_pages($post_id);
}, 20, 3);

add_action('set_object_terms', function($object_id, $terms, $tt_ids, $taxonomy) {
    if (!in_array($taxonomy, ['property_area', 'property_status'], true)) {
        return;
    }
    lsg_sync_property_with_pages($object_id);
}, 20, 4);

/**
 * Filter acf/update_value so when an area page is updated, non-matching properties are purged
 * and properties for newly selected areas are included.
 */
add_filter('acf/update_value/name=properties_order', function($value, $post_id, $field) {
    if (!lsg_is_property_listing_page($post_id)) {
        return $value;
    }

    $all_matching = lsg_get_properties_for_page($post_id);
    if (empty($all_matching)) {
        return [];
    }

    $submitted_ids = is_array($value) ? array_map('intval', array_filter($value)) : [];
    $valid_ordered = array_values(array_intersect($submitted_ids, $all_matching));
    $newly_added   = array_values(array_diff($all_matching, $valid_ordered));

    return array_merge($newly_added, $valid_ordered);
}, 10, 3);

/**
 * 3. Remove property from orders when trashed or deleted.
 */
add_action('wp_trash_post', function($post_id) {
    if (get_post_type($post_id) !== 'property') return;
    lsg_remove_property_from_orders($post_id);
});

add_action('before_delete_post', function($post_id) {
    if (get_post_type($post_id) !== 'property') return;
    lsg_remove_property_from_orders($post_id);
});

function lsg_remove_property_from_orders($property_id) {
    global $wpdb;
    $meta_rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'properties_order' AND meta_value LIKE %s",
            '%' . $wpdb->esc_like((string)$property_id) . '%'
        )
    );

    foreach ($meta_rows as $row) {
        $order = maybe_unserialize($row->meta_value);
        if (is_array($order) && in_array((int)$property_id, array_map('intval', $order), true)) {
            $order = array_values(array_filter($order, fn($id) => (int)$id !== (int)$property_id));
            update_post_meta($row->post_id, 'properties_order', $order);
        }
    }
}

/**
 * 4. Filter the relationship query so available choices on the left side
 * correctly match the page's status and area if searching.
 */
add_filter('acf/fields/relationship/query/name=properties_order', function($args, $field, $post_id) {
    if (!$post_id) {
        return $args;
    }

    $status_ids = lsg_get_page_term_ids($post_id, 'property_status');
    $area_ids   = lsg_get_page_term_ids($post_id, 'property_area');

    $tax_query = $args['tax_query'] ?? [];
    if (!empty($status_ids)) {
        $tax_query[] = [
            'taxonomy' => 'property_status',
            'field'    => 'term_id',
            'terms'    => $status_ids,
        ];
    }
    if (!empty($area_ids)) {
        $tax_query[] = [
            'taxonomy' => 'property_area',
            'field'    => 'term_id',
            'terms'    => $area_ids,
            'include_children' => true,
        ];
    }

    if (!empty($status_ids) || !empty($area_ids)) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

    $args['orderby'] = 'date';
    $args['order']   = 'DESC';

    return $args;
}, 10, 3);
