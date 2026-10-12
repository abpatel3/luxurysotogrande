<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Get important notifications
 * 
 * @return array
 * 
 */
function wpbs_get_dashboard_notifications()
{

    if(apply_filters('wpbs_disable_dashboard_notification_count', false) == true){
        return [];
    }

    global $wpdb;

    $notifications = [];

    $today_ymd = current_time('Ymd');

    /**
     * Get due part payments
     */
    $bookings = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT 
                bookings.id,
                bookings.status,
                sent_date.meta_value AS final_payment_email_sent_date
            FROM {$wpdb->prefix}wpbs_bookings AS bookings
            INNER JOIN {$wpdb->prefix}wpbs_booking_meta AS sent_meta
                ON sent_meta.booking_id = bookings.id
                AND sent_meta.meta_key = %s
                AND sent_meta.meta_value = %s
            LEFT JOIN {$wpdb->prefix}wpbs_booking_meta AS sent_date
                ON sent_date.booking_id = bookings.id
                AND sent_date.meta_key = %s
            WHERE bookings.status != %s
            ",
            'final_payment_email_sent',
            '1',
            'final_payment_email_sent_date',
            'trash'
        )
    );

    foreach ($bookings as $booking) {
        $booking_id = absint($booking->id);

        $payment = wpbs_get_payment_by_booking_id($booking_id);

        if (!$payment) {
            continue;
        }

        if (!$payment->is_part_payment()) {
            continue;
        }

        if ($payment->is_final_payment_paid()) {
            continue;
        }

        $notification_date = absint($booking->final_payment_email_sent_date);

        if ($today_ymd > wp_date('Ymd', $notification_date)) {
            $notifications[] = wpbs_get_notification((object) [
                'booking_id'           => $booking_id,
                'notification_type'    => 'payment_due',
                'notification_status'  => 'active',
                'notification_group'   => 'notification',
                'date_created'         => wp_date('Y-m-d H:i:s', $notification_date),
                'dismissable'          => false,
            ]);
        }
    }

    /**
     * Get WooCommerce unfinished payments
     */
    $payments = wpbs_get_payments([
        'gateway'      => 'woocommerce',
        'order_status' => 'pending',
    ]);

    if (!empty($payments)) {
        $booking_ids = [];

        foreach ($payments as $payment) {
            $booking_id = absint($payment->get('booking_id'));

            if ($booking_id) {
                $booking_ids[] = $booking_id;
            }
        }

        $booking_ids = array_values(array_unique($booking_ids));

        $booking_end_dates = [];

        if (!empty($booking_ids)) {
            $placeholders = implode(',', array_fill(0, count($booking_ids), '%d'));

            $booking_rows = $wpdb->get_results(
                $wpdb->prepare(
                    "
                    SELECT id, end_date
                    FROM {$wpdb->prefix}wpbs_bookings
                    WHERE id IN ({$placeholders})
                    ",
                    $booking_ids
                )
            );

            foreach ($booking_rows as $booking_row) {
                $booking_end_dates[absint($booking_row->id)] = $booking_row->end_date;
            }
        }

        foreach ($payments as $payment) {
            $booking_id = absint($payment->get('booking_id'));

            if (!$booking_id || empty($booking_end_dates[$booking_id])) {
                continue;
            }

            if ($today_ymd > wp_date('Ymd', strtotime($booking_end_dates[$booking_id]))) {
                continue;
            }

            $notifications[] = wpbs_get_notification((object) [
                'booking_id'           => $booking_id,
                'notification_type'    => 'woocommerce_payment_due',
                'notification_status'  => 'active',
                'notification_group'   => 'notification',
                'date_created'         => wp_date('Y-m-d H:i:s', strtotime($payment->get('date_created'))),
                'dismissable'          => false,
            ]);
        }
    }

    /**
     * Get manually saved notifications.
     */
    $notifications = array_merge(
        $notifications,
        wpbs_get_notifications([
            'group'  => 'notification',
            'status' => 'active',
        ])
    );

    usort($notifications, function ($a, $b) {
        return strcmp($b->get('date_created'), $a->get('date_created'));
    });

    return $notifications;
}

/**
 * Get the link to the booking modal
 * 
 */
function wpbs_dashboard_get_booking_href($notification, $booking)
{
    $notification_type = wpbs_get_notification_event_type($notification);

    $query_args = array(
        'page' => 'wpbs-calendars',
        'subpage' => 'edit-calendar',
        'calendar_id' => $booking->get('calendar_id'),
        'booking_id' => $booking->get('id')
    );

    if (strtolower($notification_type) == 'email') {
        $query_args['modal-tab'] = 'email-logs';
    }

    if (strtolower($notification_type) == 'payment') {
        $query_args['modal-tab'] = 'payment-details';
    }

    if (strtolower($notification_type) == 'sms') {
        $query_args['modal-tab'] = 'sms';
    }

    return add_query_arg($query_args, admin_url('admin.php'));
}
