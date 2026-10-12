<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Export Bookings in CSV Format
 *
 */
function wpbs_action_export_bookings()
{
    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_export_bookings')) {
        return;
    }

    // Get the calendar ID
    $calendar_id = $_GET['calendar_id'];

    // Get the bookings
    $args = apply_filters('wpbs_export_bookings_args', array('status' => array('pending', 'accepted'), 'calendar_id' => $calendar_id));
    $bookings = wpbs_get_bookings($args);



    // Output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="wpbs-bookings-export-calendar-' . $calendar_id . '-' . time() . '.csv"');

    // Do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');

    $fh = fopen('php://output', 'w');
    wpbs_get_csv_file_contents($bookings, $fh, true);
    exit;
}

add_action('wpbs_action_export_bookings', 'wpbs_action_export_bookings');

/**
 * Export Single Booking in CSV Format
 *
 */
function wpbs_action_export_booking_csv()
{
    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_export_bookings')) {
        return;
    }

    // Get the calendar ID
    $calendar_id = $_GET['calendar_id'];

    // Get the bookings
    $booking = wpbs_get_booking($_GET['booking_id']);

    if (!$booking) {
        return;
    }

    // Output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="wpbs-bookings-export-calendar-' . $calendar_id . '-booking-' . $booking->get('id') . '-' . time() . '.csv"');

    // Do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    $fh = fopen('php://output', 'w');
    wpbs_get_csv_file_contents([$booking], $fh, true);
    exit;
}

add_action('wpbs_action_export_booking_csv', 'wpbs_action_export_booking_csv');


/**
 * Generate the CSV file contents
 * 
 * @param array $bookings
 * 
 * @return array
 * 
 */
/**
 * Stream WPBS bookings as CSV (low memory, avoids timeouts).
 *
 * @param array         $bookings
 * @param resource|null $fh         File handle. If null, uses php://output
 * @param bool          $include_bom Add UTF-8 BOM for Excel compatibility
 */
function wpbs_get_csv_file_contents($bookings, $fh = null, $include_bom = true)
{
    if (empty($bookings) || !is_array($bookings)) {
        return;
    }

    if (function_exists('set_time_limit')) {
        @set_time_limit(0);
    }
    if (function_exists('wp_raise_memory_limit')) {
        wp_raise_memory_limit('admin');
    }

    $settings        = (array) get_option('wpbs_settings');
    $payment_methods = wpbs_get_payment_methods(); // cache once
    $excluded_types  = wpbs_get_excluded_fields(array('hidden')); // cache once

    // Output handle
    $close_fh = false;
    if (!$fh) {
        $fh = fopen('php://output', 'w');
        $close_fh = true;
    }
    if (!$fh) {
        return;
    }

    // Add BOM for Excel (optional)
    if ($include_bom) {
        fprintf($fh, "\xEF\xBB\xBF");
    }

    /**
     * PASS 1: Build header + cache payments 
     */
    $csv_header = array(
        'Booking ID'             => '-',
        'Booking Status'         => '-',
        'Calendar ID'            => '-',
        'Calendar Name'          => '-',
        'Start Date'             => '-',
        'End Date'               => '-',
        'Stay Length - Days'     => '-',
        'Stay Length - Nights'   => '-',
        'Date Created'           => '-',
    );

    $payment_exists = false;
    $payments_cache = array(); 

    foreach ($bookings as $booking) {

        $booking_id = (int) $booking->get('id');

        // Cache payment once per booking 
        if (!array_key_exists($booking_id, $payments_cache)) {
            $payments_cache[$booking_id] = wpbs_get_payment_by_booking_id($booking_id);
        }
        $payment = $payments_cache[$booking_id];

        if (!empty($payment)) {
            $payment_exists = true;
        }

        $fields = (array) $booking->get('fields');
        $field_names = array();

        foreach ($fields as $field) {

            if (!isset($field['id'])) {
                continue;
            }
            if (in_array($field['type'], $excluded_types, true)) {
                continue;
            }

            if (empty($field['values']['default']['label'])) {
                $field['values']['default']['label'] = '(no label)';
            }

            $label = $field['values']['default']['label'] . ' (ID:' . $field['id'] . ')';
            $field_names[$field['id']] = $label;

            // Products: add "- Value" column
            if (wpbs_form_field_is_product($field['type'])) {
                $label = $label . ' - Value';
            }

            $csv_header[$label] = '-';
        }

        // Add payment columns to header (if this booking has payment)
        if (!empty($payment)) {

            if (!empty($settings['payment_product_name'])) {
                $csv_header[$settings['payment_product_name']] = '-';
            }

            foreach ((array) $payment->get_line_items() as $line_item_key => $line_item) {

                if (in_array((string) $line_item_key, array('events', 'total'), true)) {
                    continue;
                }

                if (isset($line_item['field_id'], $field_names[$line_item['field_id']])) {
                    $csv_header[$field_names[$line_item['field_id']] . ' - Price'] = '-';
                } else {
                    $label = (isset($line_item['label_raw']) ? $line_item['label_raw'] : wpbs_format_html_string($line_item['label']));
                    $csv_header[$label . ' - Price'] = '-';
                }
            }
        }
    }

    if ($payment_exists) {
        $csv_header['Payment Status']  = '-';
        $csv_header['Total Amount']    = '-';
        $csv_header['Currency']        = '-';
        $csv_header['Transaction ID']  = '-';
    }

    $csv_header = apply_filters('wpbs_csv_export_header', $csv_header);
    $headers = array_keys($csv_header);

    // Write CSV header row
    $delimiter = apply_filters('wpbs_csv_delimiter', ',');
    $enclosure  = '"';
    $escape     = "\\";
    fputcsv($fh, $headers, $delimiter, $enclosure, $escape);

    /**
     * PASS 2: Stream rows
     */
    $calendars_cache = array(); 

    $row_count = 0;
    $flush_every = 200; 

    foreach ($bookings as $booking) {

        $line = $csv_header;
        $field_names = array();

        $booking_id  = (int) $booking->get('id');
        $calendar_id = (int) $booking->get('calendar_id');

        // Calendar name caching
        if (!isset($calendars_cache[$calendar_id])) {
            $cal = wpbs_get_calendar($calendar_id);
            $calendars_cache[$calendar_id] = $cal ? $cal->get_name() : '';
        }

        $start_ts = strtotime($booking->get('start_date'));
        $end_ts   = strtotime($booking->get('end_date'));
        $difference = ($end_ts - $start_ts) / DAY_IN_SECONDS;

        // Standard fields
        $line['Booking ID']           = $booking_id;
        $line['Booking Status']       = (string) $booking->get('status');
        $line['Calendar ID']          = $calendar_id;
        $line['Calendar Name']        = $calendars_cache[$calendar_id];
        $line['Start Date']           = wpbs_date_i18n('Y-m-d', $start_ts);
        $line['End Date']             = wpbs_date_i18n('Y-m-d', $end_ts);
        $line['Stay Length - Days']   = $difference + 1;
        $line['Stay Length - Nights'] = $difference;
        $line['Date Created']         = wpbs_date_i18n('Y-m-d', strtotime($booking->get('date_created')));

        // Fields
        $fields = (array) $booking->get('fields');

        foreach ($fields as $field) {

            if (!isset($field['id'])) {
                continue;
            }
            if (in_array($field['type'], $excluded_types, true)) {
                continue;
            }

            if (empty($field['values']['default']['label'])) {
                $field['values']['default']['label'] = '(no label)';
            }

            $base_label = $field['values']['default']['label'] . ' (ID:' . $field['id'] . ')';
            $field_names[$field['id']] = $base_label;

            $label = $base_label;
            $value = isset($field['user_value']) ? $field['user_value'] : '';

            if (wpbs_form_field_is_product($field['type'])) {
                $label .= ' - Value';
                $value = wpbs_get_form_field_product_values($field);
                if ($value === '' || $value === null) {
                    $value = 0;
                }
            }

            // Only write if this column exists in header
            if (!array_key_exists($label, $line)) {
                continue;
            }

            $value = wpbs_get_field_display_user_value($value);

            // Payment method display label
            if ($field['type'] === 'payment_method' && isset($payment_methods[$value])) {
                $value = $payment_methods[$value];
            }

            $line[$label] = $value;
        }

        // Payments 
        if ($payment_exists) {

            $payment = isset($payments_cache[$booking_id]) ? $payments_cache[$booking_id] : null;

            if (!empty($payment)) {

                foreach ((array) $payment->get_line_items() as $line_item_key => $line_item) {
                    if ($line_item_key === 'total') {
                        continue;
                    }

                    if ($line_item_key === 'events') {
                        if (!empty($settings['payment_product_name'])) {
                            $line[$settings['payment_product_name']] = $line_item['price'];
                        }
                        continue;
                    }

                    if (isset($line_item['field_id'], $field_names[$line_item['field_id']])) {
                        $line[$field_names[$line_item['field_id']] . ' - Price'] = $line_item['price'];
                    } else {
                        $label = (isset($line_item['label_raw']) ? $line_item['label_raw'] : wpbs_format_html_string($line_item['label']));
                        $line[$label . ' - Price'] = $line_item['price'];
                    }
                }

                $line['Payment Status'] = strip_tags(WPBS_Bookings_Outputter::payment_status($booking));
                $line['Total Amount']   = $payment->get_total();
                $line['Currency']       = $payment->get_currency();
                $line['Transaction ID'] = $payment->get('order_id');
            }
        }

        $line = apply_filters('wpbs_csv_export_line', $line, $booking);

        // Write row in header order
        $row = array();
        foreach ($headers as $h) {
            $row[] = isset($line[$h]) ? $line[$h] : '';
        }
        fputcsv($fh, $row, $delimiter, $enclosure, $escape);

        // Periodic flush helps long downloads
        $row_count++;
        if (($row_count % $flush_every) === 0) {
            if (function_exists('flush')) {
                @flush();
            }
        }
    }

    if ($close_fh) {
        fclose($fh);
    }
}
