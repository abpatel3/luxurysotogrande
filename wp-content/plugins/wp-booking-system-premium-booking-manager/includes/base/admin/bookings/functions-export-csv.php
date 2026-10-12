<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Generate export CSV file.
 */

function wpbs_action_bm_export_bookings()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_bm_export_bookings')) {
        return;
    }

    $status = isset($_GET['booking_status']) && in_array($_GET['booking_status'], array('accepted', 'pending', 'trash')) ? [$_GET['booking_status']] : '';

    $bookings = wpbs_bm_get_bookings(array('number' => 99999, 'status' => $status, 'search' => (!empty($_GET['s']) ? sanitize_text_field($_GET['s']) : ''), 'start_date' => (!empty($_GET['wpbs_bm_start_date']) ? sanitize_text_field($_GET['wpbs_bm_start_date']) : ''), 'end_date' => (!empty($_GET['wpbs_bm_end_date']) ? sanitize_text_field($_GET['wpbs_bm_end_date']) : '')));

    if (empty($bookings)) {
        return;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=wpbs-bookings-' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $fh = fopen('php://output', 'w');
    wpbs_get_csv_file_contents($bookings, $fh, true);
    exit;
}

add_action('wpbs_action_bm_export_bookings', 'wpbs_action_bm_export_bookings');
