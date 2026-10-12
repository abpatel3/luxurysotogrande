<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function wpbs_is_security_deposit_enabled()
{
    $settings = get_option('wpbs_settings');

    if (isset($settings['payment_security_deposit_enable']) && $settings['payment_security_deposit_enable'] == 'on') {
        return true;
    }

    return false;
}

/**
 * Add the Security Deposit as a form extra
 * 
 */
function wpbs_add_security_deposit_as_extra($prices, $payment, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date, $post_data)
{

    $settings = get_option('wpbs_settings');

    if (!wpbs_is_security_deposit_enabled()) {
        return $prices;
    }

    if ($settings['payment_security_deposit_taxable'] != 'yes') {
        return $prices;
    }

    $security_deposit = wpbs_get_security_deposit_form_field($form_fields);

    if (!$security_deposit) {
        return $prices;
    }

    // Explode and get key value pairs
    list($price, $value) = explode('|', $security_deposit['user_value']);

    if (empty($price) && $price !== '0') {
        return $prices;
    }

    $price = apply_filters('wpbs_pricing_item_modifier', $price, $prices, 'security_deposit');

    // Multiply by another field
    $multiplication_id = isset($security_deposit['values']['default']['multiplication']) ? absint($security_deposit['values']['default']['multiplication']) : false;
    $multiplication = false;

    if ($multiplication_id !== false) {
        foreach ($form_fields as $multiplication_form_field) {
            if ($multiplication_form_field['id'] != $multiplication_id) {
                continue;
            }

            $multiplication_value = $payment->get_multiplication_field_value($multiplication_form_field);

            $multiplication = max(0, round(abs($multiplication_value), 2));

            $price = $price * $multiplication;
        }
    }



    $custom_vat = (isset($security_deposit['values']['default']['custom_vat']) && (!empty($security_deposit['values']['default']['custom_vat']) || $security_deposit['values']['default']['custom_vat'] !== "") ? floatval($security_deposit['values']['default']['custom_vat']) : false);

    $price = apply_filters('wpbs_security_deposit_value', $price, $prices, $calendar_id, $form_fields, $payment);

    // Filter extra fields
    $extra = apply_filters('wpbs_get_checkout_price_extra', array(
        'field_id' => $security_deposit['id'],
        'label' => $value,
        'original_label' => $value,
        'price' => $payment->vat->deduct_vat($price, false, $custom_vat),
        'price_with_vat' => $price,
        'vat_percentage' => $custom_vat !== false ? $custom_vat : $prices['vat_percentage'],
        'multiplication' => $multiplication,
        'addition' => 'per_booking',
        'total' => $price,
        'type' => 'security_deposit',
    ), $prices, $form_fields, $calendar_id, $form_args, $form, $start_date, $end_date, $post_data);

    $extra['total_with_vat'] = $extra['total'];
    $extra['total'] = $payment->vat->deduct_vat($extra['total'], true, $custom_vat);

    $extra['total'] = round($extra['total'], 2);

    // Add to final output
    $prices['extras'][] = $extra;

    $prices['total'] += $extra['total'];

    $prices['has_security_deposit'] = true;
    $prices['security_deposit_taxable'] = true;

    return $prices;
}
add_filter('wpbs_get_checkout_price_before_subtotal', 'wpbs_add_security_deposit_as_extra', 20, 9);

/**
 * Add the Security Deposit as a tax
 * 
 */

function wpbs_add_security_deposit_as_tax($prices, $post_data, $calendar_id, $form_args, $form, $form_fields, $payment)
{
    $settings = get_option('wpbs_settings');

    if (!isset($settings['payment_security_deposit_enable']) || $settings['payment_security_deposit_enable'] != 'on') {
        return $prices;
    }

    if ($settings['payment_security_deposit_taxable'] != 'no') {
        return $prices;
    }

    $security_deposit = wpbs_get_security_deposit_form_field($form_fields);

    if (!$security_deposit) {
        return $prices;
    }

    // Explode and get key value pairs
    list($price, $value) = explode('|', $security_deposit['user_value']);

    if (empty($price) && $price !== '0') {
        return $prices;
    }

    $price = apply_filters('wpbs_pricing_item_modifier', $price, $prices, 'security_deposit');

    // Multiply by another field
    $multiplication_id = isset($security_deposit['values']['default']['multiplication']) ? absint($security_deposit['values']['default']['multiplication']) : false;
    $multiplication = false;

    if ($multiplication_id !== false) {
        foreach ($form_fields as $multiplication_form_field) {
            if ($multiplication_form_field['id'] != $multiplication_id) {
                continue;
            }

            $multiplication_value = $payment->get_multiplication_field_value($multiplication_form_field);

            $multiplication = max(0, round(abs($multiplication_value), 2));

            $price = $price * $multiplication;
        }
    }

    $price = apply_filters('wpbs_security_deposit_value', $price, $prices, $calendar_id, $form_fields, $payment);

    $deposit = array(
        'name' => $value,
        'percentage' => false,
        'fixed_amount' => $price,
        'calculation' => 'per_booking',
        'value' => $price,
        'type' => 'security_deposit',
    );

    $prices['total'] += $price;

    $prices['has_security_deposit'] = true;
    $prices['security_deposit_taxable'] = false;

    $prices['taxes'][] =  $deposit;

    return $prices;
}
add_filter('wpbs_get_checkout_price_after_total', 'wpbs_add_security_deposit_as_tax', 20, 7);

/**
 * Get the Security Deposit form field
 * 
 */
function wpbs_get_security_deposit_form_field($form_fields)
{
    foreach ($form_fields as $form_field) {
        if ($form_field['type'] != 'security_deposit') {
            continue;
        }

        if (empty($form_field['user_value'])) {
            continue;
        }

        return $form_field;
    }

    return false;
}

/**
 * Add the Security Deposit submenu to the Payments Tab
 *
 */
function wpbs_security_deposit_settings_page_tab($tabs)
{

    $tabs['security_deposit'] = 'Security Deposit';
    return $tabs;
}
add_filter('wpbs_submenu_page_settings_payment_tabs', 'wpbs_security_deposit_settings_page_tab', 20, 1);

/**
 * Add the Security Deposit Settings to the Security Deposit tab
 *
 */
function wpbs_security_deposit_settings_page_tab_content()
{

    include 'settings/views/view-payment-settings-security-deposit.php';
}
add_action('wpbs_submenu_page_payment_settings_tab_security_deposit', 'wpbs_security_deposit_settings_page_tab_content');

/**
 * Hook in the form handler and create a cron job to schedule the refund 
 * 
 * @param int $booking_id
 * 
 */
add_action('wpbs_submit_form_end', function ($booking_id) {

    $payment = wpbs_get_payment_by_booking_id($booking_id);

    if (!$payment) {
        return false;
    }

    if (!$payment->has_security_deposit()) {
        return false;
    }

    $details = $payment->get('details');

    if (!wpbs_payment_gateway_supports_refunds($payment)) {

        $details['security_deposit']['status'] = 'not_supported';

        wpbs_update_payment($payment->get('id'), [
            'details' => $details
        ]);

        return false;
    }

    $booking = wpbs_get_booking($booking_id);

    $settings = get_option('wpbs_settings', array());

    $end_date = strtotime($booking->get('end_date'));

    $days_after = isset($settings['payment_security_deposit_when_to_refund']) && $settings['payment_security_deposit_when_to_refund'] ? $settings['payment_security_deposit_when_to_refund'] : 7;

    $when_to_refund = $end_date + ($days_after * DAY_IN_SECONDS);

    wp_schedule_single_event($when_to_refund, 'wpbs_refund_security_deposit_cron', array($booking_id));

    $details['security_deposit']['status'] = 'scheduled';

    wpbs_update_payment($payment->get('id'), [
        'details' => $details
    ]);
});

/**
 * CRON callback for refunding the security deposit
 * 
 * @param int $booking_id
 * 
 */
function wpbs_refund_security_deposit_cron($booking_id)
{

    $refund = wpbs_refund_security_deposit($booking_id);

    // Prevent cron from running the refund function multiple times.
    if ($refund === false) {
        return false;
    }

    $payment = wpbs_get_payment_by_booking_id($booking_id);

    $details = $payment->get('details');

    if (isset($refund['success'])) {
        $details['security_deposit']['status'] = 'refunded';
        $details['security_deposit']['marked_as_refunded'] = true;
        $details['security_deposit']['action_date'] = current_time('timestamp');
        $details['security_deposit']['action_type'] = 'automatic';
    } else {
        $details['security_deposit']['status'] = 'error';
        $details['security_deposit']['error'] = (isset($refund['error_message']) ? $refund['error_message'] : __('No error message given.', 'wp-booking-system'));
    }

    wpbs_update_payment($payment->get('id'), [
        'details' => $details
    ]);

    add_action('wpbs_security_deposit_automatically_refunded', $booking_id);
}
add_action('wpbs_refund_security_deposit_cron', 'wpbs_refund_security_deposit_cron');

/**
 * Function to refund the security deposit
 * 
 * @param int $booking_id
 * 
 */
function wpbs_refund_security_deposit($booking_id)
{

    $payment = wpbs_get_payment_by_booking_id($booking_id);

    if (!$payment) {
        return false;
    }

    // Check if it was already refunded
    if (wpbs_get_booking_meta($booking_id, 'security_deposit_refunded', true)) {
        return false;
    }

    $details = $payment->get('details');

    $status = isset($details['security_deposit']['status']) && $details['security_deposit']['status'] ? $details['security_deposit']['status'] : false;

    // Exit if status is not "scheduled"
    if ($status != 'scheduled') {
        return false;
    }

    if ($payment->is_part_payment() && !$payment->is_final_payment_paid()) {
        return ['error' => true, 'error_message' => __('Security deposit cannot be refunded for part payments until the final payment is paid.', 'wp-booking-system')];
    }

    $charges = wpbs_payment_get_charges($payment);

    if (empty($charges)) {
        return ['error' => true, 'error_message' => __('No charges found.', 'wp-booking-system')];
    }

    $amount_to_refund = wpbs_get_security_deposit_value($payment->get('prices'));

    $charge = end($charges);

    if ($amount_to_refund > $charge['available']) {
        return ['error' => true, 'error_message' => __('Not enough balance to issue the refund.', 'wp-booking-system')];
    }

    wpbs_add_booking_meta($booking_id, 'security_deposit_refunded', true);

    $refund = wpbs_process_refund($booking_id, $charge['id'], $amount_to_refund, __('Security deposit refund.', 'wp-booking-system'), 'automatic');

    // Refresh charges just to update the refund status
    $payment = wpbs_get_payment($payment->get('id'));
    wpbs_payment_get_charges($payment);

    return $refund;
}


/**
 * Get the security deposit value
 * 
 * @param array $prices
 * 
 * @return int
 * 
 */
function wpbs_get_security_deposit_value($prices)
{
    if (isset($prices['extras'])) foreach ($prices['extras'] as $line_item) {
        if (isset($line_item['type']) && $line_item['type'] == 'security_deposit') {
            return $line_item['total'];
        }
    }

    if (isset($prices['taxes'])) foreach ($prices['taxes'] as $line_item) {
        if (isset($line_item['type']) && $line_item['type'] == 'security_deposit') {
            return $line_item['value'];
        }
    }

    return 0;
}

/**
 * Get the status of a security deposit
 * 
 * @param WPBS_Payment $payment
 * 
 * @return string
 * 
 */
function wpbs_get_security_deposit_status($payment)
{
    $details = $payment->get('details');

    $status = isset($details['security_deposit']['status']) && $details['security_deposit']['status'] ? $details['security_deposit']['status'] : false;

    if (in_array($payment->get('order_status'), ['authorized', 'requires_capture'])) {
        return 'awaiting_capture';
    }

    if ($status == 'scheduled') {

        if ($payment->is_part_payment() && !$payment->is_final_payment_paid()) {
            return 'awaiting_final_payment';
        }

        $crons = _get_cron_array();

        foreach ($crons as $cron) {
            if (isset($cron['wpbs_refund_security_deposit_cron'])) {
                foreach ($cron['wpbs_refund_security_deposit_cron'] as $job) {
                    if ($job['args'][0] == $payment->get('booking_id')) {
                        return 'scheduled';
                    }
                }
            }
        }

        return 'scheduled_error';
    }

    return $status;
}

/**
 * Get the refunded status of a security deposit
 * 
 * @param WPBS_Payment $payment
 * 
 */
function wpbs_get_security_deposit_refund_status($payment)
{
    $details = $payment->get('details');
    $status = isset($details['security_deposit']['marked_as_refunded']) && $details['security_deposit']['marked_as_refunded'] == 'refunded' ? true : false;
    return $status;
}

/**
 * Get the string representation of a security deposit status
 * 
 * @param WPBS_Payment
 * 
 */
function wpbs_get_security_deposit_nice_status($payment)
{

    $status = wpbs_get_security_deposit_status($payment);

    $details = $payment->get('details');

    if ($status == 'refunded') {
        return sprintf(__('Refunded on %s.', 'wp-booking-system'), '<strong>' . wpbs_date_i18n(get_option('date_format'), $details['security_deposit']['action_date']) . '</strong>');
    }

    if ($status == 'cancelled') {
        return sprintf(__('Refund cancelled on %s.', 'wp-booking-system'), '<strong>' . wpbs_date_i18n(get_option('date_format'), $details['security_deposit']['action_date']) . '</strong>');
    }

    if ($status == 'not_supported') {
        return __('The payment method used does not support automatic refunds.', 'wp-booking-system');
    }

    if ($status == 'awaiting_final_payment') {
        return __('Awaiting final payment.', 'wp-booking-system');
    }

    if ($status == 'awaiting_capture') {
        return  __('Awaiting payment capture.', 'wp-booking-system');
    }

    if ($status == 'error') {
        return __('The automatic attempt to issue the refund has failed. Reason: ', 'wp-booking-system') . '<strong>' . (isset($details['security_deposit']['error']) ? $details['security_deposit']['error'] : '-') . '</strong>';
    }

    if ($status == 'scheduled') {

        $crons = _get_cron_array();

        foreach ($crons as $timestamp => $cron) {
            if (isset($cron['wpbs_refund_security_deposit_cron'])) {
                foreach ($cron['wpbs_refund_security_deposit_cron'] as $job) {
                    if ($job['args'][0] == $payment->get('booking_id')) {
                        return sprintf(__('Scheduled to be refunded on %s.', 'wp-booking-system'), '<strong>' . wpbs_date_i18n(get_option('date_format'), $timestamp) . '</strong>');
                    }
                }
            }
        }

        return __('Not scheduled. Can only be refunded manually.', 'wp-booking-system');
    }

    return __('No status available.', 'wp-booking-system');
}



/**
 * Remove scheduled event
 * 
 */
function wpbs_action_ajax_wpbs_security_deposit_toggle_refunded_status()
{

    // Nonce
    check_ajax_referer('wpbs_security_deposit_action', 'wpbs_token');

    if (!isset($_POST['payment_id'])) {
        wp_die();
    }

    $payment_id = absint($_POST['payment_id']);

    $payment = wpbs_get_payment($payment_id);

    if (!$payment) {
        wp_die();
    }

    $booking = wpbs_get_booking($payment->get('booking_id'));

    if(!wpbs_current_user_can_access_calendar($booking->get('calendar_id'))){
        wp_die();
    }

    $details = $payment->get('details');

    if (isset($details['security_deposit']['marked_as_refunded']) && $details['security_deposit']['marked_as_refunded'] == true) {
        $details['security_deposit']['marked_as_refunded'] = false;
    } else {
        $details['security_deposit']['marked_as_refunded'] = true;
    }

    wpbs_update_payment($payment->get('id'), [
        'details' => $details
    ]);

    wpbs_booking_details_modal_security_deposits(wpbs_get_payment($payment_id));

    wp_die();
}
add_action('wp_ajax_wpbs_security_deposit_toggle_refunded_status', 'wpbs_action_ajax_wpbs_security_deposit_toggle_refunded_status');

/**
 * Cancel automatic refund
 * 
 */
function wpbs_action_ajax_wpbs_security_deposit_cancel_automatic_refund()
{

    // Nonce
    check_ajax_referer('wpbs_security_deposit_action', 'wpbs_token');


    if (!isset($_POST['payment_id'])) {
        wp_die();
    }

    $payment_id = absint($_POST['payment_id']);

    $payment = wpbs_get_payment($payment_id);

    if (!$payment) {
        wp_die();
    }

    $booking = wpbs_get_booking($payment->get('booking_id'));

    if(!wpbs_current_user_can_access_calendar($booking->get('calendar_id'))){
        wp_die();
    }

    $details = $payment->get('details');

    $details['security_deposit']['status'] = 'cancelled';
    $details['security_deposit']['action_date'] = current_time('timestamp');

    wpbs_update_payment($payment->get('id'), [
        'details' => $details
    ]);

    wpbs_booking_details_modal_security_deposits(wpbs_get_payment($payment_id));

    wp_die();
}
add_action('wp_ajax_wpbs_security_deposit_cancel_automatic_refund', 'wpbs_action_ajax_wpbs_security_deposit_cancel_automatic_refund');

/**
 * Mark security deposit as manually paid
 * 
 */
function wpbs_action_ajax_wpbs_security_deposit_manual_refund()
{

    // Nonce
    check_ajax_referer('wpbs_security_deposit_action', 'wpbs_token');

    if (!isset($_POST['payment_id'])) {
        wp_die();
    }

    $payment_id = absint($_POST['payment_id']);

    $payment = wpbs_get_payment($payment_id);

    if (!$payment) {
        wp_die();
    }

    $booking_id = $payment->get('booking_id');

    $booking = wpbs_get_booking($booking_id);

    if(!wpbs_current_user_can_access_calendar($booking->get('calendar_id'))){
        wp_die();
    }

    $refund = wpbs_refund_security_deposit($booking_id);
    $payment = wpbs_get_payment($payment_id);

    $details = $payment->get('details');

    if (isset($refund['success'])) {
        $details['security_deposit']['status'] = 'refunded';
        $details['security_deposit']['marked_as_refunded'] = true;
        $details['security_deposit']['action_date'] = current_time('timestamp');
        $details['security_deposit']['action_type'] = 'manual';

        wpbs_update_payment($payment->get('id'), [
            'details' => $details
        ]);
    } else {

        $details['security_deposit']['status'] = 'error';
        $details['security_deposit']['error'] = $refund['error_message'];

        wpbs_update_payment($payment->get('id'), [
            'details' => $details
        ]);
    }

    $payment = wpbs_get_payment($payment_id);

    ob_start();
    wpbs_booking_details_modal_security_deposits($payment);
    $html = ob_get_contents();
    ob_end_clean();
    $response['security_deposit'] = $html;

    ob_start();
    wpbs_modal_payment_details_refunds_view($payment);
    $html = ob_get_contents();
    ob_end_clean();
    $response['refunds'] = $html;

    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_wpbs_security_deposit_manual_refund', 'wpbs_action_ajax_wpbs_security_deposit_manual_refund');

/**
 * Booking modal template file
 * 
 */
function wpbs_booking_details_modal_security_deposits($payment)
{
    $status = wpbs_get_security_deposit_status($payment);
    $refunded = wpbs_get_security_deposit_refund_status($payment);
?>
    <table>
        <tr>
            <td><strong><?php echo __('Amount', 'wp-booking-system') ?>:</strong></td>
            <td>
                <p class="wpbs-security-deposit-<?php echo $refunded ? 'refunded' : 'not-refunded'; ?>"><strong><?php echo wpbs_get_formatted_price(wpbs_get_security_deposit_value($payment->get('prices')), $payment->get_currency()); ?></strong> - <?php echo $refunded ? 'Refunded' : 'Not refunded'; ?></p>
            </td>
        </tr>

        <tr>
            <td><strong><?php echo __('Status', 'wp-booking-system') ?>:</strong></td>
            <td>
                <p><?php echo wpbs_get_security_deposit_nice_status($payment); ?></p>
            </td>
        </tr>

        <?php if (in_array($status, ['error', 'cancelled', 'scheduled_error', 'not_supported', 'scheduled'])) : ?>
            <tr class="wpbs-booking-modal-security-deposit-actions">
                <td><strong><?php echo __('Actions', 'wp-booking-system') ?>:</strong></td>
                <td>
                    <p>

                        <?php if (in_array($status, ['error', 'cancelled', 'scheduled_error', 'not_supported'])) : ?>
                            <a href="#" class="wpbs-secondary-button wpbs-security-deposit-toggle-refunded-status" data-payment-id="<?php echo $payment->get('id'); ?>"><?php echo $refunded ? __('Mark as not refunded', 'wp-booking-system') : __('Mark as refunded', 'wp-booking-system') ?></a>
                        <?php endif; ?>

                        <?php if (in_array($status, ['scheduled'])) : ?>
                            <a href="#" class="wpbs-secondary-button wpbs-security-deposit-cancel-automatic-refund" data-payment-id="<?php echo $payment->get('id'); ?>"><?php echo  __('Cancel automatic refund', 'wp-booking-system') ?></a>
                            <a href="#" class="wpbs-secondary-button wpbs-security-deposit-manual-refund" data-payment-id="<?php echo $payment->get('id'); ?>"><?php echo  __('Refund now', 'wp-booking-system') ?></a>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
        <?php endif; ?>
    </table>
<?php
}

/**
 * Add {Security Deposit} Email Tag
 *
 * @param string $output
 *
 * @return string
 *
 */
function wpbs_email_tags_security_deposit($tags)
{

    $settings = get_option('wpbs_settings', array());

    if (!isset($settings['payment_security_deposit_enable']) || empty($settings['payment_security_deposit_enable'])) {
        return $tags;
    }

    $tags['payment']['security_deposit'] = 'Security Deposit';

    return $tags;
}
add_filter('wpbs_email_tags', 'wpbs_email_tags_security_deposit', 30, 1);

/**
 * Replace {Security Deposit} Email Tag with proper value
 *
 * @param string        $text
 * @param string        $tag
 * @param WPBS_Payment  $payment
 * @param string        $language
 *
 * @return string
 *
 */
function wpbs_form_mailer_custom_tag_security_deposit($text, $tag, $payment, $language)
{

    if ($tag != '{Security Deposit}') {
        return $text;
    }

    if (!$payment) {
        return $text;
    }

    $security_deposit = wpbs_get_security_deposit_value($payment->get('prices'));

    $text = str_replace($tag, $security_deposit, $text);

    return $text;
}
add_filter('wpbs_form_mailer_custom_tag', 'wpbs_form_mailer_custom_tag_security_deposit', 10, 4);
