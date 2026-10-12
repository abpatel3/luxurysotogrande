<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Booking admin area
 *
 */
function wpbs_bm_include_files_admin_bookings()
{

    // Get legend admin dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include submenu page
    if (file_exists($dir_path . 'class-submenu-page-bookings.php')) {
        include $dir_path . 'class-submenu-page-bookings.php';
    }

    // Include bookings list table
    if (file_exists($dir_path . 'class-list-table-bookings.php')) {
        include $dir_path . 'class-list-table-bookings.php';
    }

    // Include bookings list table
    if (file_exists($dir_path . 'class-calendar-view-bookings.php')) {
        include $dir_path . 'class-calendar-view-bookings.php';
    }

    // Include csv export functions file
    if (file_exists($dir_path . 'functions-export-csv.php')) {
        include $dir_path . 'functions-export-csv.php';
    }
}
add_action('wpbs_bm_include_files', 'wpbs_bm_include_files_admin_bookings');

/**
 * Register the Bookings admin submenu page
 *
 */
function wpbs_register_submenu_page_booking_manager($submenu_pages)
{

    if (!is_array($submenu_pages)) {
        return $submenu_pages;
    }

    $submenu_pages['bookings'] = array(
        'class_name' => 'WPBS_Submenu_Page_Bookings',
        'data' => array(
            'page_title' => __('Bookings', 'wp-booking-system-booking-manager'),
            'menu_title' => __('Bookings', 'wp-booking-system-booking-manager'),
            'capability' => apply_filters('wpbs_submenu_page_capability_bookings', 'manage_options'),
            'menu_slug' => 'wpbs-bookings',
        ),
    );

    return $submenu_pages;
}
add_filter('wpbs_register_submenu_page', 'wpbs_register_submenu_page_booking_manager', 30);

/**
 * Get the default view of the dashboard
 *
 */
function wpbs_bm_get_dashboard_view()
{
    $view = get_user_meta(get_current_user_id(), 'wpbs_bm_dashboard_view', true);

    return $view ?: 'list';
}

/**
 * Save the current view of the dashboard
 *
 */
function wpbs_bm_save_dashboard_view()
{

    if (!isset($_GET['view'])) {
        return false;
    }

    if (!isset($_GET['page']) || $_GET['page'] != 'wpbs-bookings') {
        return false;
    }

    if (!in_array($_GET['view'], array('list', 'calendar'))) {
        return false;
    }

    update_user_meta(get_current_user_id(), 'wpbs_bm_dashboard_view', $_GET['view']);
}
add_action('init', 'wpbs_bm_save_dashboard_view');

/**
 * Get the hide bookings filter
 *
 */
function wpbs_bm_get_past_bookings_filter()
{
    $past_bookings = get_user_meta(get_current_user_id(), 'wpbs_bm_past_bookings_filter', true);

    return $past_bookings ?: '';
}

/**
 * Save the hide bookings filter
 *
 */
function wpbs_set_hide_bookings_filter()
{

    if (empty($_POST['action']) || $_POST['action'] != 'wpbs_set_hide_bookings_filter') {
        echo __('', 'wp-booking-system');
        wp_die();
    }

    if (!isset($_POST['hide_past_bookings']) || $_POST['hide_past_bookings'] != 'on') {
        update_user_meta(get_current_user_id(), 'wpbs_bm_past_bookings_filter', '');
    } else {
        update_user_meta(get_current_user_id(), 'wpbs_bm_past_bookings_filter', $_POST['hide_past_bookings']);
    }

    wp_die();
}
add_action('wp_ajax_wpbs_set_hide_bookings_filter', 'wpbs_set_hide_bookings_filter');

/**
 * Function for getting filtered bookings
 *
 */
function wpbs_bm_get_bookings($args = [], $count = false)
{
    $original_args = $args;

    $search = isset($args['search']) ? $args['search'] : '';
    unset($args['search']);

    $number = isset($args['number']) ? max(0, (int)$args['number']) : 20;
    unset($args['number']);

    $offset = isset($args['offset']) ? max(0, (int)$args['offset']) : 0;
    unset($args['offset']);

    // Normalize orderby
    if (isset($args['orderby']) && strpos($args['orderby'], 'column_') !== false) {
        $args['orderby'] = 'id';
    }

    $args['custom_query'] = $args['custom_query'] ?? '';

    // Build allowed calendar IDs
    $excluded = apply_filters('wpbs_booking_manager_excluded_calendar_ids', []);
    $calendar_ids = [];

    foreach (wpbs_get_calendars() as $calendar) {
        $id = (int)$calendar->get('id');
        if (in_array($id, $excluded, true)) continue;
        $calendar_ids[] = $id;
    }

    if (!empty($calendar_ids)) {
        $args['custom_query'] .= ' AND calendar_id IN (' . implode(',', array_map('intval', $calendar_ids)) . ')';
    } else {
        return $count ? 0 : [];
    }

    // Filter past bookings
    if (wpbs_bm_get_past_bookings_filter() === 'on') {
        $args['custom_query'] .= ' AND end_date >= DATE(NOW())';
    }

    // Filter start date
    if (!empty($args['start_date'])) {
        $start_date = DateTime::createFromFormat('Y-m-d', $args['start_date']);
        if ($start_date) {
            $start_date->setTime(0, 0, 0, 0);
            $args['custom_query'] .= " AND start_date >= '" . esc_sql($start_date->format('Y-m-d 00:00:00')) . "'";
        }
        unset($args['start_date']);
    }

    // Filter end date
    if (!empty($args['end_date'])) {
        $end_date = DateTime::createFromFormat('Y-m-d', $args['end_date']);
        if ($end_date) {
            $end_date->setTime(0, 0, 0, 0);
            $args['custom_query'] .= " AND end_date <= '" . esc_sql($end_date->format('Y-m-d 00:00:00')) . "'";
        }
        unset($args['end_date']);
    }

    // Decide if we can paginate in DB (best case)
    $orderby_raw = $original_args['orderby'] ?? ($args['orderby'] ?? '');
    $needs_full_set =
        (!empty($search)) ||
        ($orderby_raw === 'calendar_id') ||
        (is_string($orderby_raw) && strpos($orderby_raw, 'column_') === 0);

    if (!$needs_full_set && !$count) {
        $args['number'] = $number;
        $args['offset'] = $offset;
    }

    $bookings = wpbs_get_bookings($args);

    if (!empty($search)) {
        $keywords = preg_split('/\s+/', strtolower(sanitize_text_field($search)), -1, PREG_SPLIT_NO_EMPTY);
        $filtered = [];

        foreach ($bookings as $booking) {
            $haystack = '';
            $haystack .= date('j F Y', strtotime($booking->get('start_date'))) . ' ';
            $haystack .= date('j F Y', strtotime($booking->get('end_date'))) . ' ';
            $haystack .= '#' . $booking->get('id') . ' ';

            foreach ((array)$booking->get('fields') as $field) {
                $uv = $field['user_value'] ?? '';
                if ($uv === '' || $uv === null) continue;

                $haystack .= is_array($uv) ? implode(' ', $uv) : $uv;
                $haystack .= ' ';
            }

            $haystack = strtolower($haystack);

            $ok = true;
            foreach ($keywords as $kw) {
                if ($kw === '') continue;
                if (strpos($haystack, $kw) === false) { $ok = false; break; }
            }

            if ($ok) $filtered[] = $booking;
        }

        $bookings = $filtered;
        unset($filtered);
    }

    if (($original_args['orderby'] ?? '') === 'calendar_id') {
        $calendar_names = [];
        foreach (wpbs_get_calendars() as $calendar) {
            $calendar_names[(int)$calendar->get('id')] = $calendar->get('name');
        }

        usort($bookings, function($a, $b) use ($calendar_names, $args) {
            $nameA = $calendar_names[(int)$a->get('calendar_id')] ?? '';
            $nameB = $calendar_names[(int)$b->get('calendar_id')] ?? '';
            $cmp = strcmp($nameA, $nameB);
            if ($cmp === 0) $cmp = ((int)$a->get('id') <=> (int)$b->get('id'));
            return (isset($args['order']) && $args['order'] === 'DESC') ? -$cmp : $cmp;
        });
    }

    if ($needs_full_set) {
        if ($count) return count($bookings);
        $bookings = array_slice($bookings, $offset, $number);
    }

    // Dynamic columns mapping
    $forms = wpbs_get_forms();
    $dynamic_columns = [];
    foreach ($forms as $form) {
        $fields = wpbs_get_form_meta($form->get('id'), 'booking_manager_fields', true);
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $field) {
                $dynamic_columns[$form->get('id')][$field] = $field;
            }
        }
    }

    $dynamic_fields_count = 0;
    foreach ($dynamic_columns as $cols) {
        $dynamic_fields_count = max($dynamic_fields_count, count($cols));
    }

    $payment_cache = [];

    foreach ($bookings as $b => $booking) {
        $booking_id = (int)$booking->get('id');

        $fields_by_id = [];
        foreach ((array)$booking->get('fields') as $f) {
            if (isset($f['id'])) $fields_by_id[$f['id']] = $f;
        }

        $dynamic_fields = [];
        $form_id = $booking->get('form_id');
        $form_fields = isset($dynamic_columns[$form_id]) ? array_keys($dynamic_columns[$form_id]) : [];

        for ($i = 0; $i < $dynamic_fields_count; $i++) {
            $field_id = $form_fields[$i] ?? null;

            if ($field_id === null) { $dynamic_fields[$i] = '-'; continue; }

            // Special payment fields
            if (!is_numeric($field_id)) {
                if (!isset($payment_cache[$booking_id])) {
                    $payment_cache[$booking_id] = wpbs_get_payment_by_booking_id($booking_id);
                }
                $payment = $payment_cache[$booking_id];

                if ($field_id === 'deposit_amount') {
                    $dynamic_fields[$i] = ($payment && $payment->is_part_payment())
                        ? wpbs_get_formatted_price($payment->get_total_first_payment(), $payment->get_currency())
                        : '-';
                    continue;
                }

                if ($field_id === 'final_payment_amount') {
                    $dynamic_fields[$i] = ($payment && $payment->is_part_payment())
                        ? wpbs_get_formatted_price($payment->get_total_second_payment(), $payment->get_currency())
                        : '-';
                    continue;
                }

                if ($field_id === 'total_amount') {
                    $dynamic_fields[$i] = ($payment)
                        ? wpbs_get_formatted_price($payment->get_total(), $payment->get_currency())
                        : '-';
                    continue;
                }

                $dynamic_fields[$i] = '-';
                continue;
            }

            // Regular form field
            $field = $fields_by_id[(int)$field_id] ?? null;
            if (!$field) { $dynamic_fields[$i] = '-'; continue; }

            $form_value = $field['user_value'] ?? '-';

            // normalize value
            if (is_array($form_value)) {
                $vals = [];
                foreach ($form_value as $val) {
                    if (strpos($val, '|') !== false) {
                        $parts = explode('|', $val, 2);
                        $val = $parts[1] ?? $val;
                    }
                    $vals[] = $val;
                }
                $dynamic_fields[$i] = trim(implode(', ', $vals));
            } else {
                if (!empty($form_value) && strpos($form_value, '|') !== false) {
                    $parts = explode('|', $form_value, 2);
                    $dynamic_fields[$i] = $parts[1] ?? $form_value;
                } else {
                    $dynamic_fields[$i] = $form_value;
                }
            }

            if ($dynamic_fields[$i] === '' || $dynamic_fields[$i] === null) $dynamic_fields[$i] = '-';
        }

        $booking->dynamic_fields = $dynamic_fields;
        $bookings[$b] = $booking;
    }

    if (isset($original_args['orderby']) && strpos($original_args['orderby'], 'column_') === 0) {
        $column_index = ((int) str_replace('column_', '', $original_args['orderby'])) - 1;

        usort($bookings, function($a, $b) use ($column_index, $args) {
            $va = $a->dynamic_fields[$column_index] ?? '';
            $vb = $b->dynamic_fields[$column_index] ?? '';
            $va = ($va === '-') ? '' : $va;
            $vb = ($vb === '-') ? '' : $vb;

            $cmp = strcmp($va, $vb);
            if ($cmp === 0) $cmp = ((int)$a->get('id') <=> (int)$b->get('id'));
            return (isset($args['order']) && $args['order'] === 'DESC') ? -$cmp : $cmp;
        });
    }

    $bookings = apply_filters('wpbs_booking_manager_results', $bookings, $args);

    if ($count) return count($bookings);

    return $bookings;
}
