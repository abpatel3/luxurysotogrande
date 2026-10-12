<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if part payments are enabled
 *
 * @return bool
 *
 */
function wpbs_part_payments_enabled()
{
    $settings = get_option('wpbs_settings', array());
    if (isset($settings['payment_part_payments_enable']) && $settings['payment_part_payments_enable'] == 'on') {
        return true;
    }

    return false;
}

function wpbs_part_payments_settings_page($settings)
{

    $calendars = wpbs_get_calendars(array('status' => 'active'));
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
    $languages = wpbs_get_languages();

?>

    <div class="wpbs-divider"></div>
    <h2><?php echo __('Part Payments (Deposit/Advance Payment)', 'wp-booking-system'); ?></h2>

    <!-- Enable Part Payments -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_part_payments_enable">
            <?php echo __('Enable Part Payments', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_part_payments_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_part_payments_enable]" value="0">
                <input data-target="#wpbs-part-payments-wrapper" name="wpbs_settings[payment_part_payments_enable]" type="checkbox" id="payment_part_payments_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['payment_part_payments_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-part-payments-wrapper" class="wpbs-payment-part-payments-wrapper wpbs-form-fields-indent wpbs-settings-wrapper <?php echo (!empty($settings['payment_part_payments_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

        <div class="wpbs-part-payments-wrapper">

            <!-- Amount Type -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_amount_type">
                    <?php echo __('Deposit type', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <select name="wpbs_settings[payment_part_payments_amount_type]" id="payment_part_payments_amount_type">
                        <option <?php selected((isset($settings['payment_part_payments_amount_type']) ? $settings['payment_part_payments_amount_type'] : ''), 'percentage') ?> value="percentage"><?php echo __('Percentage', 'wp-booking-system') ?></option>
                        <option <?php selected((isset($settings['payment_part_payments_amount_type']) ? $settings['payment_part_payments_amount_type'] : ''), 'fixed_amount') ?> value="fixed_amount"><?php echo __('Fixed Amount', 'wp-booking-system') ?></option>
                    </select>
                </div>
            </div>

            <!-- Amount -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_amount">
                    <?php echo __('Deposit amount', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input wpbs-deposit-value-field-inner">
                    <div class="wpbs-input-wrapper">
                        <span class="wpbs-input-symbol wpbs-input-symbol-left deposit-type-fixed_amount"><span><?php echo wpbs_get_currency(); ?></span></span>
                        <span class="wpbs-input-symbol wpbs-input-symbol-left deposit-type-percentage"><span>%</span></span>
                        <input name="wpbs_settings[payment_part_payments_amount]" id="payment_part_payments_amount" type="number" step="0.01" min="0" value="<?php echo (!empty($settings['payment_part_payments_amount']) ? esc_attr($settings['payment_part_payments_amount']) : ''); ?>" />
                    </div>
                </div>

                <div class="wpbs-label-description"><?php echo __('The amount that the customer will be charged when making the booking.', 'wp-booking-system'); ?></div>
            </div>

            <!-- Amount Source -->
            <div class="wpbs-form-field wpbs-part-payments-source">
                <label class="wpbs-label" for="payment_part_payments_source">
                    <?php echo __('Deposit source', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <select name="wpbs_settings[payment_part_payments_source]" id="payment_part_payments_source">
                        <option <?php selected((isset($settings['payment_part_payments_source']) ? $settings['payment_part_payments_source'] : ''), 'all') ?> value="all"><?php echo __('Calendar and Form Prices', 'wp-booking-system') ?></option>
                        <option <?php selected((isset($settings['payment_part_payments_source']) ? $settings['payment_part_payments_source'] : ''), 'calendar') ?> value="calendar"><?php echo __('Calendar Price Only', 'wp-booking-system') ?></option>
                    </select>
                </div>
            </div>

            <!-- Applicable Period -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_applicable_period">
                    <?php echo __('Applicable period', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <div class="wpbs-input-wrapper">
                        <input name="wpbs_settings[payment_part_payments_applicable_period]" id="payment_part_payments_applicable_period" min="0" type="number" value="<?php echo (!empty($settings['payment_part_payments_applicable_period']) ? esc_attr($settings['payment_part_payments_applicable_period']) : '0'); ?>" />
                        <span class="wpbs-input-symbol wpbs-input-symbol-right"><span><?php echo __('days from the current date', 'wp-booking-system') ?></span></span>
                    </div>
                </div>

                <div class="wpbs-label-description"><?php echo __('This option allows you to charge a deposit only if the booking is X days in the future. For example, if you set the applicable period to 14 days, and someone makes a booking starting one week from today, they will be charged the full amount. If they make a booking starting from 14 or more days in the future, they will be asked to pay the deposit. Zero days means the option is disabled, and deposit will always be charged.', 'wp-booking-system'); ?></div>

            </div>

            <!-- Applicability -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_applicability">
                    <?php echo __('Applicability', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <select name="wpbs_settings[payment_part_payments_applicability]" id="payment_part_payments_applicability">
                        <option <?php selected((isset($settings['payment_part_payments_applicability']) ? $settings['payment_part_payments_applicability'] : ''), 'mandatory') ?> value="mandatory"><?php echo __('Mandatory - Always use part payments', 'wp-booking-system') ?></option>
                        <option <?php selected((isset($settings['payment_part_payments_applicability']) ? $settings['payment_part_payments_applicability'] : ''), 'optional') ?> value="optional"><?php echo __('Optional - Allow customer to choose whether to pay a deposit or the full amount', 'wp-booking-system') ?></option>
                    </select>
                </div>

                <div class="wpbs-label-description"><?php echo __('Give the customer the possibility to choose if they want to pay a deposit or not. Applicable Period option is still applied in both cases.', 'wp-booking-system'); ?></div>
            </div>

            <!-- Applicability -->
            <div class="wpbs-form-field payment_part_payments_applicability_default-wrap wpbs-form-fields-indent">
                <label class="wpbs-label" for="payment_part_payments_applicability_default">
                    <?php echo __('Default Option', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <select name="wpbs_settings[payment_part_payments_applicability_default]" id="payment_part_payments_applicability_default">
                        <option <?php selected((isset($settings['payment_part_payments_applicability_default']) ? $settings['payment_part_payments_applicability_default'] : ''), 'deposit') ?> value="deposit"><?php echo __('Deposit', 'wp-booking-system') ?></option>
                        <option <?php selected((isset($settings['payment_part_payments_applicability_default']) ? $settings['payment_part_payments_applicability_default'] : ''), 'full') ?> value="full"><?php echo __('Full Amount', 'wp-booking-system') ?></option>
                    </select>
                </div>

                <div class="wpbs-label-description"><?php echo __('Which Applicability part payment option should be selected by default.', 'wp-booking-system'); ?></div>
            </div>


            <!-- Calendars -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_calendars">
                    <?php echo __('Calendars', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input wpbs-chosen-wrapper">
                    <select name="wpbs_settings[payment_part_payments_calendars][]" id="payment_part_payments_calendars" class="wpbs-chosen" multiple>
                        <?php foreach ($calendars as $calendar): ?>
                            <option value="<?php echo $calendar->get('id'); ?>" <?php echo isset($settings['payment_part_payments_calendars']) && in_array($calendar->get('id'), $settings['payment_part_payments_calendars']) ? 'selected' : ''; ?>><?php echo $calendar->get('name'); ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="wpbs-label-description"><?php echo __('Select the calendars the part payments apply to. If no calendars are selected, the part payments will be applied to all calendars.', 'wp-booking-system') ?></div>
            </div>

            <!-- Final Payment Method -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="payment_part_payments_method">
                    <?php echo __('Final Payment Method', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <select name="wpbs_settings[payment_part_payments_method]" id="payment_part_payments_method">
                        <option <?php selected((isset($settings['payment_part_payments_method']) ? $settings['payment_part_payments_method'] : ''), 'cash') ?> value="cash"><?php echo __('Cash on arrival', 'wp-booking-system') ?></option>
                        <option <?php selected((isset($settings['payment_part_payments_method']) ? $settings['payment_part_payments_method'] : ''), 'initial') ?> value="initial"><?php echo __('Same as initial payment', 'wp-booking-system') ?></option>
                    </select>
                </div>

                <div class="wpbs-label-description"><?php echo __('You can charge a deposit and receive the rest of the amount in cash, or ask for the final payment to be done online using the same method (eg. PayPal or Stripe) as the initial payment, for which you will have some extra options below.', 'wp-booking-system'); ?></div>
            </div>

            <div id="wpbs-final-payment-options" class="wpbs-form-fields-indent">

                <!-- Email Notification -->
                <div class="wpbs-form-field">
                    <label class="wpbs-label" for="payment_part_payments_email_notification">
                        <?php echo __('Email Reminder', 'wp-booking-system'); ?>
                    </label>

                    <div class="wpbs-page-notice notice-info">
                        <p><?php echo __('You can set up an email notification in your Form to include the <strong>{Final Payment Link}</strong>. You can use the <strong>Customer Final Payment Reminder</strong> template for easy set up.', 'wp-booking-system'); ?></p>
                    </div>

                </div>

                <div class="wpbs-input">
                    <div class="wpbs-page-notice notice-warning">
                        <p><?php echo __("Don't forget to set up the Secondary Payment Page options below.", 'wp-booking-system'); ?></p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="wpbs-divider"></div>
    <h2>
        <?php echo __('Secondary Payment Page', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __('Used for Part Payments and for payment links created when manually adding a booking.', 'wp-booking-system'); ?></small>
    </h2>

    <!-- Final Payment Page -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_part_payments_page">
            <?php echo __('Page', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <select name="wpbs_settings[payment_part_payments_page]" id="payment_part_payments_page">
                <option value="">-</option>
                <?php $items = get_posts(array('post_type' => 'page', 'numberposts' => -1));
                foreach ($items as $item): ?>
                    <option <?php selected((isset($settings['payment_part_payments_page']) ? $settings['payment_part_payments_page'] : ''), $item->ID) ?> value="<?php echo $item->ID; ?>"><?php echo $item->post_title ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="wpbs-label-description"><?php echo __('This is the page which will hold the payment form. You will need to add the [wpbs-payment-form] shortcode to this page.', 'wp-booking-system'); ?></div>

    </div>

    <div class="wpbs-page-notice notice-warning">
        <p><?php echo __('Make sure to include the <code>[wpbs-payment-form]</code> shortcode on this page.', 'wp-booking-system') ?></p>
    </div>

    <!-- Success Message -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_part_payments_confirmation">
            <?php echo __('Confirmation Message', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <?php wp_editor((!empty($settings['payment_part_payments_confirmation']) ? wp_kses_post($settings['payment_part_payments_confirmation']) : __('The form was successfully submitted.', 'wp-booking-system')), 'payment_part_payments_confirmation', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'wpbs_settings[payment_part_payments_confirmation]')) ?>
        </div>

        <div class="wpbs-label-description"><?php echo __('The confirmation message that appears after the final payment has succeeded.', 'wp-booking-system'); ?></div>

        <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

        <?php if (wpbs_translations_active()): ?>
            <!-- Required Field Translations -->
            <div class="wpbs-form-translations wpbs-form-fields-indent">
                <?php foreach ($active_languages as $language): ?>
                    <div class="wpbs-form-field wpbs-absolute-label">
                        <label class="wpbs-label"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                        <div class="wpbs-input">
                            <?php wp_editor((!empty($settings['payment_part_payments_confirmation_translation_' . $language]) ? wp_kses_post($settings['payment_part_payments_confirmation_translation_' . $language]) : ''), 'payment_part_payments_confirmation_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'wpbs_settings[payment_part_payments_confirmation_translation_' . $language . ']')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="wpbs-form-field">
        <label class="wpbs-label" for="payment_part_payments_show_booking_details">
            <?php echo __('Show Booking Details', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="payment_part_payments_show_booking_details" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[payment_part_payments_show_booking_details]" value="0">
                <input name="wpbs_settings[payment_part_payments_show_booking_details]" type="checkbox" id="payment_part_payments_show_booking_details" class="wpbs-settings-toggle" <?php echo (!empty($settings['payment_part_payments_show_booking_details'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>

        <div class="wpbs-label-description"><?php echo __('Show the booking details on the Final Payment page.', 'wp-booking-system'); ?></div>
    </div>
<?php
}
add_action('wpbs_submenu_page_settings_tab_payment_general_bottom', 'wpbs_part_payments_settings_page', 20, 1);

/**
 * Calculate the applicable period
 *
 * @return timestamp
 *
 */
function wpbs_part_payments_get_applicable_period($calendar_id, $form_id)
{
    $settings = get_option('wpbs_settings', array());

    if (!isset($settings['payment_part_payments_applicable_period']) || empty($settings['payment_part_payments_applicable_period'])) {
        $applicable_period = 0;
    } else {
        $applicable_period = $settings['payment_part_payments_applicable_period'];
    }

    $applicable_period = apply_filters('wpbs_part_payments_applicable_period', $applicable_period, $calendar_id, $form_id);

    return mktime(0, 0, 0, current_time('n'), current_time('j'), current_time('Y')) + ($applicable_period * DAY_IN_SECONDS);
}

/**
 * Check if this is a part payment
 *
 * @param array      $prices
 * @param int        $calendar_id
 * @param array      $form_args
 * @param WPBS_Form  $form
 * @param array      $form_fields
 * @param string     $start_date
 * @param string     $end_date
 * @param array      $post_data
 *
 * @return array
 *
 */

function wpbs_check_if_is_part_payment($prices, $payment, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date, $post_data)
{

    // Check if part payments is enabled
    if (wpbs_part_payments_enabled() == false) {
        return $prices;
    }

    // Check if Payment method is not Payment on Arrival
    if (apply_filters('wpbs_disable_deposit_for_payment_on_arrival', true) && $prices['payment_method'] == 'payment_on_arrival') {
        return $prices;
    }

    $settings = get_option('wpbs_settings', array());

    // Check if part payments is applicable to this calendar
    if (isset($settings['payment_part_payments_calendars']) && !empty($settings['payment_part_payments_calendars']) && !in_array($calendar_id, $settings['payment_part_payments_calendars'])) {
        return $prices;
    }

    // Get applicable period
    $applicable_period = wpbs_part_payments_get_applicable_period($calendar_id, $form->get('id'));


    // Get booking start date
    $start_date = wpbs_convert_js_to_php_timestamp($post_data['calendar']['start_date']);

    // Check if booking starts after applicable period
    if ($start_date < $applicable_period) {
        return $prices;
    }

    $prices['part_payment_applicable'] = true;

    // Check if optional part payments are enabled
    if (isset($post_data['post_data'])) {
        parse_str($post_data['post_data'], $post);
    } elseif (isset($post_data['form_data'])) {
        parse_str($post_data['form_data'], $post);
    }

    if (isset($post['wpbs-input-part-payment-applicability']) && $post['wpbs-input-part-payment-applicability'] == 'full') {
        return $prices;
    }

    $prices['is_part_payment'] = true;

    return $prices;
}
add_filter('wpbs_get_checkout_price_before_subtotal', 'wpbs_check_if_is_part_payment', 1, 9);


/**
 * Calculate the part payments and add it to the pricing array.
 *
 * @param array         $prices
 * @param array         $post_data
 * @param int           $calendar_id
 * @param array         $form_args
 * @param WPBS_Form     $form
 * @param array         $form_fields
 *
 * @return array
 *
 */
function wpbs_get_checkout_price_part_payments($prices, $post_data, $calendar_id, $form_args, $form, $form_fields)
{

    if (!isset($prices['is_part_payment']) || $prices['is_part_payment'] !== true) {
        return $prices;
    }

    $settings = get_option('wpbs_settings', array());

    // Calculate first and second payments
    if (isset($settings['payment_part_payments_amount_type']) && $settings['payment_part_payments_amount_type'] == 'fixed_amount') {
        $amount = absint($settings['payment_part_payments_amount']);
        $amount = apply_filters('wpbs_pricing_item_modifier', $amount, $prices, 'deposit');

        $first_payment = $amount;
    } else {
        $payment_source = (isset($settings['payment_part_payments_source']) && !empty($settings['payment_part_payments_source'])) ? $settings['payment_part_payments_source'] : 'all';

        if ($payment_source == 'calendar') {
            $total = $prices['events']['price_with_vat'];
        } else {
            $total = $prices['total'];

            // Remove security deposit from the total
            if (isset($prices['has_security_deposit'])) {
                $total -= wpbs_get_security_deposit_value($prices);
            }
        }

        $amount = absint($settings['payment_part_payments_amount']) / 100;

        $round_initial_payment = apply_filters('wpbs_part_payments_round_initial_payment', true);

        if ($round_initial_payment) {
            $first_payment = ceil($total * $amount);
        } else {
            $first_payment = round($total * $amount, 2);
        }
    }

    $first_payment = apply_filters('wpbs_part_payments_deposit', $first_payment, $prices, $post_data, $calendar_id, $form_args, $form, $form_fields);

    $second_payment = $prices['total'] - $first_payment;

    // If deposit is larger than total amount, don't enable part payments for this
    if ($first_payment > $prices['total']) {
        return $prices;
    }

    // If it's a 100% discounted bookings, don't enable part payments
    if ($prices['total'] == 0) {
        return $prices;
    }

    // Apply part payment prices
    $prices['part_payments'] = array(
        'total' => round($prices['total'], 2),
        'first_payment' => round($first_payment, 2),
        'second_payment' => round($second_payment, 2),
        'method' => apply_filters('wpbs_part_payments_final_payment_method', $settings['payment_part_payments_method'], $calendar_id, $form->get('id')),
    );

    return $prices;
}
add_filter('wpbs_get_checkout_price_after_total', 'wpbs_get_checkout_price_part_payments', 50, 6);

/**
 * Add Part Payments to Checkout Line Items
 *
 * @param string $line_items
 * @param WPBS_Payment $payment
 *
 */
function wpbs_checkout_pricing_table_part_payments($line_items, $payment)
{
    if (!$payment->is_part_payment()) {
        return $line_items;
    }

    if (apply_filters('wpbs_disable_deposit_for_payment_on_arrival', true) && $payment->get('gateway') == 'payment_on_arrival') {
        return $line_items;
    }

    $line_items['first_payment'] = array(
        'label' => __('Deposit', 'wp-booking-system'),
        'value' => wpbs_get_formatted_price($payment->get_total_first_payment(), $payment->get_display_currency(), true),
        'price' => $payment->get_total_first_payment(),
        'class' => 'wpbs-line-item-part-payments wpbs-first-payment',
        'type' => 'part-payment-first-payment',
        'editable' => true,
    );

    $line_items['second_payment'] = array(
        'label' => __('Final Payment', 'wp-booking-system'),
        'value' => wpbs_get_formatted_price($payment->get_total_second_payment(), $payment->get_display_currency(), true),
        'price' => $payment->get_total_second_payment(),
        'class' => 'wpbs-line-item-part-payments wpbs-second-payment',
        'type' => 'part-payment-second-payment',
        'editable' => true,
    );

    return $line_items;
}
add_filter('wpbs_line_items_after_total', 'wpbs_checkout_pricing_table_part_payments', 10, 2);

/**
 * Overwrite the the part payments line items in the booking details popup, adding links to mark as paid and the final payment link.
 *
 * @param array $line_items
 * @param WPBS_Payment $payment
 *
 * @return array
 *
 */
function wpbs_booking_details_order_information_part_payments($line_items, $payment)
{

    if (!$payment->is_part_payment()) {
        return $line_items;
    }

    if (apply_filters('wpbs_disable_deposit_for_payment_on_arrival', true) && $payment->get('gateway') == 'payment_on_arrival') {
        return $line_items;
    }

    $settings = get_option('wpbs_settings', array());

    $first_payment_status = wpbs_booking_details_order_information_part_payments_actions($payment, 'deposit');
    $second_payment_status = wpbs_booking_details_order_information_part_payments_actions($payment, 'final_payment');

    $booking_id = $payment->get('booking_id');

    if (wpbs_get_booking_meta($booking_id, 'manual_booking', 'true') && !apply_filters('wpbs_payment_method_supports_manual_deposit_' . $payment->get_payment_gateway(), false)) {

        unset($line_items['first_payment']);

        $line_items['second_payment'] = array(
            'label' => __('Remaining Payment', 'wp-booking-system'),
            'value' => wpbs_get_formatted_price($payment->get_total_second_payment(), $payment->get_currency(), true) . '<span class="wpbs-order-information-payment-actions wpbs-order-information-part-payment-actions" data-booking-payment="final_payment" data-booking-id="' . $payment->get('id') . '">' . $second_payment_status . '</span>',
            'price' => $payment->get_total_second_payment(),
            'type' => 'second_payment',
            'editable' => true,
        );

    } else {

        // Part Payments
        $line_items['first_payment'] = array(
            'label' => __('Deposit', 'wp-booking-system'),
            'value' => wpbs_get_formatted_price($payment->get_total_first_payment(), $payment->get_currency(), true) . '<span class="wpbs-order-information-payment-actions wpbs-order-information-part-payment-actions" data-booking-payment="deposit" data-booking-id="' . $payment->get('id') . '">' . $first_payment_status . '</span>',
            'price' => $payment->get_total_first_payment(),
            'type' => 'first_payment',
            'editable' => true,
        );
        $line_items['second_payment'] = array(
            'label' => __('Final Payment', 'wp-booking-system'),
            'value' => wpbs_get_formatted_price($payment->get_total_second_payment(), $payment->get_currency(), true) . '<span class="wpbs-order-information-payment-actions wpbs-order-information-part-payment-actions" data-booking-payment="final_payment" data-booking-id="' . $payment->get('id') . '">' . $second_payment_status . '</span>',
            'price' => $payment->get_total_second_payment(),
            'type' => 'second_payment',
            'editable' => true,
        );

        $line_items[] = array(
            'label' => __(' &#8211; Final Payment Method', 'wp-booking-system'),
            'value' => ($payment->get_final_payment_method() == 'cash' ? __('Cash on arrival', 'wp-booking-system') : __('Same as initial payment', 'wp-booking-system')),
        );
        
    }

    $payment_link_label = __(' &#8211; Payment Link', 'wp-booking-system');

    // Final Payment Link
    if (
        !$payment->is_final_payment_paid() &&
        $payment->get_final_payment_method() == 'initial' &&
        $payment->get('gateway') != 'bank_transfer' &&
        !in_array($payment->get('order_status'), ['authorized'])
    ) {
        $url = add_query_arg(['wpbs-payment-id' => $payment->get('order_id')], get_permalink($settings['payment_part_payments_page']));
        $value = '<a target="_blank" href="' . $url . '">' . $url . '</a>';
        $line_items[] = array('label' => $payment_link_label, 'value' => $value);
    }

    return $line_items;
}
add_filter('wpbs_booking_details_order_information', 'wpbs_booking_details_order_information_part_payments', 10, 2);

/**
 * Overwrite the the part payments line items in the booking details popup, adding links to mark as paid and the final payment link.
 *
 * @param array $line_items
 * @param WPBS_Payment $payment
 *
 * @return array
 *
 */
function wpbs_booking_details_order_information_manual_bookings($line_items, $payment)
{


    if (!wpbs_get_booking_meta($payment->get('booking_id'), 'manual_booking', true)) {
        return $line_items;
    }

    if ($payment->is_part_payment()) {
        return $line_items;
    }

    $settings = get_option('wpbs_settings', array());


    $payment_link_label = __(' &#8211; Final Payment Link', 'wp-booking-system');

    // Final Payment Link
    if (
        !$payment->is_paid() &&
        $payment->get('gateway') != 'bank_transfer'
    ) {
        $url = add_query_arg(['wpbs-payment-id' => $payment->get('order_id')], get_permalink($settings['payment_part_payments_page']));
        $value = '<a target="_blank" href="' . $url . '">' . $url . '</a>';
        $line_items[] = array('label' => $payment_link_label, 'value' => $value);
    }

    return $line_items;
}
add_filter('wpbs_booking_details_order_information', 'wpbs_booking_details_order_information_manual_bookings', 10, 2);

/**
 * Get payment statuses for part payments and add the "mark as paid" buttons
 *
 * @param WPBS_Payment $payment
 * @param string $payment_type
 *
 * @return string
 *
 */
function wpbs_booking_details_order_information_part_payments_actions($payment, $payment_type)
{
    $html = '<span>';

    $is_paid = 'is_' . $payment_type . '_paid';

    $html .= $payment->$is_paid() ? '<strong> (' . __('paid', 'wp-booking-system') . ')</strong>' : false;

    // Handle bank transfer
    if ($payment->get('gateway') == 'bank_transfer' || wpbs_get_booking_meta($payment->get('booking_id'), 'manual_booking', true)) {

        // exit if payment = final & deposit is not paid
        if ($payment_type == 'final_payment' && !$payment->is_deposit_paid()) {
            return $html;
        }

        // exit if payment = deposit & final payment is paid
        if ($payment_type == 'deposit' && $payment->is_final_payment_paid()) {
            return $html;
        }

        $html .= $payment->$is_paid()
            ? '<a class="wpbs-part-payment-change-status wpbs-payment-status-unpaid" href="#">' . __('mark as unpaid', 'wp-booking-system') . '</a>'
            : '<a class="wpbs-part-payment-change-status" href="#">' . __('mark as paid', 'wp-booking-system') . '</a>';
    }

    // Handle other payment methods
    if (
        $payment_type == 'final_payment' &&
        !wpbs_get_booking_meta($payment->get('booking_id'), 'manual_booking', true) &&
        !in_array($payment->get('gateway'), array('payment_on_arrival', 'bank_transfer'))
    ) {
        $html .= $payment->$is_paid()
            ? '<a class="wpbs-part-payment-change-status wpbs-payment-status-unpaid" href="#">' . __('mark as unpaid', 'wp-booking-system') . '</a>'
            : '<a class="wpbs-part-payment-change-status" href="#">' . __('mark as paid', 'wp-booking-system') . '</a>';
    }

    $html .= '</span>';

    return $html;
}

/**
 * Make strings translatable - add default strings for part payments
 *
 * @param array $strings
 *
 * @return array
 *
 */
function wpbs_part_payments_payment_default_strings($strings)
{
    $strings['amount_billed'] = __('Amount Billed Today', 'wp-booking-system');
    $strings['part_payments_deposit'] = __('Deposit', 'wp-booking-system');
    $strings['part_payments_final_payment_cash'] = __('Final Payment (on arrival)', 'wp-booking-system');
    $strings['part_payments_final_payment_initial'] = __('Final Payment (online, before arrival)', 'wp-booking-system');

    return $strings;
}
add_filter('wpbs_payment_default_strings', 'wpbs_part_payments_payment_default_strings');

/**
 * Make strings translatable - add form fields strings for part payments
 *
 * @param array $strings
 *
 * @return array
 *
 */
function wpbs_part_payments_payment_default_strings_labels($strings)
{
    $strings['amount_billed'] = array(
        'label' => __('Amount Billed Label', 'wp-booking-system'),
        'tooltip' => __("The label for the Amount Billed in the payment form.", 'wp-booking-system'),
    );

    $strings['part_payments_deposit'] = array(
        'label' => __('Deposit Label', 'wp-booking-system'),
        'tooltip' => __("Appears in the Pricing Table", 'wp-booking-system'),
    );

    $strings['part_payments_final_payment_cash'] = array(
        'label' => __('Final Payment (on arrival) Label', 'wp-booking-system'),
        'tooltip' => __("Appears in the Pricing Table when the Final Payment method is set to 'Cash on Arrival'", 'wp-booking-system'),
    );

    $strings['part_payments_final_payment_initial'] = array(
        'label' => __('Final Payment (online) Label', 'wp-booking-system'),
        'tooltip' => __("Appears in the Pricing Table when the Final Payment method is set to 'Same as initial payment'", 'wp-booking-system'),
    );

    return $strings;
}
add_filter('wpbs_payment_default_strings_labels', 'wpbs_part_payments_payment_default_strings_labels');

/**
 * Final Payment Shortcode
 *
 */
function wpbs_final_payment_shortcode($atts, $content = null)
{

    if (!wpbs_check_if_scripts_and_styles_are_enqueued()) {
        return WPBS_Shortcodes::scripts_and_styles_not_enqueued_notification();
    }

    // Get Payment ID
    if (!isset($_GET['wpbs-payment-id']) || empty($_GET['wpbs-payment-id'])) {
        $message = __('Missing Payment ID.', 'wp-booking-system');
        if (current_user_can('manage_options')) {
            $message = __('The shortcode is working correctly! The payment form will appear here if a valid payment ID is present in the URL. This message is only visible to you, the website administrator.', 'wp-booking-system');
        }
        return $message;
    }

    if ($_GET['wpbs-payment-id'] == 'N/A') {
        return __('Invalid Payment ID.', 'wp-booking-system');
    }

    $order_id = sanitize_text_field($_GET['wpbs-payment-id']);

    // Get Order
    $payments = wpbs_get_payments(array('order_id' => $order_id));

    if (empty($payments)) {
        return __('Invalid Payment ID.', 'wp-booking-system');
    }

    $payment = array_shift($payments);

    // Exit early if we need to display a payment confirmation if the order was already updated (eg. GoPay or WC)
    $output = '';
    $output = apply_filters('wpbs_final_payment_output', $output);
    if (!empty($output)) {
        return $output;
    }

    // Check if it's a valid order that requires second payment
    if ($payment->is_part_payment() && $payment->is_final_payment_paid()) {
        return __('Payment was already made for this order.', 'wp-booking-system');
    }

    if (!$payment->is_part_payment() && $payment->is_paid()) {
        return __('Payment was already made for this order.', 'wp-booking-system');
    }

    // Check second payment method
    if ($payment->get_final_payment_method() == 'cash') {
        return false;
    }

    // Get the language the form was submitted in
    $language = wpbs_get_booking_meta($payment->get('booking_id'), 'submitted_language', true);

    $settings = get_option('wpbs_settings', array());

    /**
     * Prepare Output
     *
     */

    ob_start();
?>
    <div class="wpbs-main-wrapper">
        <div class="wpbs-payment-confirmation wpbs-final-payment-confirmation">

            <form id="wpbs-final-payment-form">
                <input type="hidden" name="order_id" value="<?php echo $payment->get('id'); ?>">
                <input type="hidden" name="language" value="<?php echo $language; ?>">
                <?php echo wp_nonce_field('wpbs_final_payment_ajax', 'wpbs_token', true, false); ?>
            </form>

            <div class="wpbs-payment-confirmation-inner wpbs-<?php echo $payment->get('gateway'); ?>-payment-confirmation-inner">

                <?php if (isset($settings['payment_part_payments_show_booking_details']) && !empty($settings['payment_part_payments_show_booking_details'])): ?>
                    <?php $booking = wpbs_get_booking($payment->get('booking_id')); ?>
                    <table class="wpbs-pricing-table">
                        <thead>
                            <tr>
                                <th colspan="2"><?php echo wpbs_get_payment_default_string('booking_details', $language) ?></th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td><?php echo wpbs_get_form_default_string($booking->get('form_id'), 'booking_id', $language) ?></td>
                                <td>#<?php echo $booking->get('id') ?></td>
                            </tr>

                            <tr>
                                <td><?php echo  wpbs_get_form_default_string($booking->get('form_id'), 'start_date', $language) ?></td>
                                <td><?php echo wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date'))) ?></td>
                            </tr>

                            <tr>
                                <td><?php echo  wpbs_get_form_default_string($booking->get('form_id'), 'end_date', $language) ?></td>
                                <td><?php echo wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date'))) ?></td>
                            </tr>

                            <?php foreach ($booking->get('fields') as $field):

                                if (in_array($field['type'], wpbs_get_excluded_fields())) {
                                    continue;
                                }

                                // Get value
                                $value = (isset($field['user_value'])) ? $field['user_value'] : '';

                                // Handle Pricing options differently
                                if (wpbs_form_field_is_product($field['type'])) {
                                    $value = wpbs_get_form_field_product_values($field);
                                }

                                $value = wpbs_get_field_display_user_value($value);

                                if ($field['type'] == 'textarea') {
                                    $value = nl2br($value);
                                }

                                if ($field['type'] == 'payment_method') {
                                    continue;
                                }

                                if ($value == '-' || !$value) {
                                    continue;
                                }

                                // Get Translations
                                if (isset($field['values'][$language]['label']) && !empty($field['values'][$language]['label'])) {
                                    $field_name = $field['values'][$language]['label'];
                                } else if (isset($field['values']['default']['label']) && !empty($field['values']['default']['label'])) {
                                    $field_name = $field['values']['default']['label'];
                                } else {
                                    $field_name = '(no label, ID: #' . $field['id'] . ')';
                                }
                            ?>
                                <tr>
                                    <td><?php echo $field_name ?></td>
                                    <td><?php echo $value ?></td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo $payment->get_pricing_table($language); ?>
                <?php echo apply_filters('wpbs_final_payment_' . $payment->get('gateway'), '', $payment, $language); ?>
            </div>

        </div>
    </div>

<?php
    $output = ob_get_contents();
    ob_end_clean();

    return apply_filters('wpbs_shortcode_final_payment_page_output', $output);
}
add_shortcode('wpbs-payment-form', 'wpbs_final_payment_shortcode');
add_shortcode('wpbs-final-payment', 'wpbs_final_payment_shortcode');

/**
 * AJAX Callback function for saving the final payment
 *
 */
function wpbs_save_final_payment()
{
    parse_str($_POST['post_data'], $post_data);

    // Nonce
    if (!wp_verify_nonce($post_data['wpbs_token'], 'wpbs_final_payment_ajax')) {
        return false;
    }

    // Get Payment ID
    if (!isset($post_data['order_id']) || empty($post_data['order_id'])) {
        return false;
    }

    $order_id = absint($post_data['order_id']);

    // Get Order
    $payment = wpbs_get_payment($order_id);

    if (empty($payment)) {
        return false;
    }

    $settings = get_option('wpbs_settings', array());

    do_action('wpbs_save_final_payment_' . $payment->get('gateway'), $post_data, $payment);

    do_action('wpbs_save_final_payment', $payment);

    $confirmation_message = (!empty($settings['payment_part_payments_confirmation_translation_' . $post_data['language']])) ? $settings['payment_part_payments_confirmation_translation_' . $post_data['language']] : (!empty($settings['payment_part_payments_confirmation']) ? $settings['payment_part_payments_confirmation'] : __('The form was successfully submitted.', 'wp-booking-system'));

    echo apply_filters('the_content', $confirmation_message);

    wp_die();
}

add_action('wp_ajax_nopriv_wpbs_save_final_payment', 'wpbs_save_final_payment');
add_action('wp_ajax_wpbs_save_final_payment', 'wpbs_save_final_payment');

/**
 * Add {Final Payment Link} email tag
 *
 * @param string $output
 *
 * @return string
 *
 */
function wpbs_email_tags_final_payment_link($tags)
{

    if (!wpbs_is_pricing_enabled()) {
        return $tags;
    }

    $settings = get_option('wpbs_settings', array());

    $tags['payment']['final-payment-deposit-first'] = 'Deposit - First Payment';
    $tags['payment']['final-payment-deposit-second'] = 'Deposit - Second Payment';

    if (!isset($settings['payment_part_payments_method']) || $settings['payment_part_payments_method'] != 'initial') {
        return $tags;
    }

    $tags['payment']['outstanding-amount'] = 'Outstanding Amount';
    $tags['payment']['final-payment-link'] = 'Final Payment Link';

    return $tags;
}
add_filter('wpbs_email_tags', 'wpbs_email_tags_final_payment_link', 40, 1);


/**
 * Ajax Callback for changing the payment status on part payments
 *
 */
function wpbs_action_ajax_booking_part_payment_change_status()
{
    // Nonce
    check_ajax_referer('wpbs_change_payment_status', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $payment_id = absint($_POST['id']);

    if (!isset($_POST['payment_type'])) {
        return false;
    }

    $payment_type = $_POST['payment_type'];

    // Get payment
    $payment = wpbs_get_payment($payment_id);

    if (is_null($payment)) {
        return;
    }

    $details = $payment->get('details');

    $details['part_payments'][$payment_type] = (isset($details['part_payments']) && isset($details['part_payments'][$payment_type]) && $details['part_payments'][$payment_type] == true) ? false : true;

    wpbs_update_payment($payment_id, array(
        'details' => $details,
        'order_status' => ((isset($details['part_payments']['final_payment']) && $details['part_payments']['final_payment'] == true) ? 'completed' : '-'),
    ));

    // Send payment success emails for bank transfers
    if (isset($details['part_payments']['final_payment']) && $details['part_payments']['final_payment'] === true && $payment->get('gateway') == 'bank_transfer') {
        wpbs_final_payment_success_email(wpbs_get_payment($payment_id));
    }

    wp_die();
}
add_action('wp_ajax_wpbs_booking_part_payment_change_status', 'wpbs_action_ajax_booking_part_payment_change_status');

/**
 * Ajax Callback for updating the HTML for the payment status on part payments
 *
 */
function wpbs_action_ajax_booking_part_payment_update_status()
{
    // Nonce
    check_ajax_referer('wpbs_change_payment_status', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $payment_id = absint($_POST['id']);

    if (!isset($_POST['payment_type'])) {
        return false;
    }

    $payment_type = $_POST['payment_type'];

    $payment = wpbs_get_payment($payment_id);



    if ($payment->is_part_payment() && !$payment->is_final_payment_paid() && $payment_type == 'deposit' && !wpbs_get_booking_meta($payment->get('booking_id'), 'deposit_paid_email_sent', true)) {
        // Send the email
        $emails = new WPBS_Email_Notifications($payment->get('booking_id'));
        $emails->send_final_payment_emails();
        wpbs_update_booking_meta($payment->get('booking_id'), 'deposit_paid_email_sent', true);
    }

    echo wpbs_booking_details_order_information_part_payments_actions($payment, $payment_type);

    wp_die();
}
add_action('wp_ajax_wpbs_booking_part_payment_update_status', 'wpbs_action_ajax_booking_part_payment_update_status');

/**
 * Send an email after the completion of the final payment
 *
 * @param WPBS_Payment $payment
 *
 */
function wpbs_final_payment_success_email($payment)
{
    if ($payment->get('order_status') != 'completed') {
        return false;
    }

    // Get booking ID
    $booking_id = $payment->get('booking_id');

    // Send the email
    $emails = new WPBS_Email_Notifications($booking_id);
    $emails->send_final_payment_emails();
};
add_action('wpbs_save_final_payment', 'wpbs_final_payment_success_email', 10, 1);

/**
 * Save the date when the final payment reminder was sent
 * 
 */
add_action('wpbs_email_notifications_before', function ($email, $booking, $language) {

    $message = wpbs_get_translated_email_meta($email->get('id'), 'notification_message', $language);

    if (stripos($message, '{Final Payment Link}') === false) {
        return false;
    }

    $trigger = wpbs_get_email_meta($email->get('id'), 'when_to_send', true);

    if ($trigger == 'booking_created') {
        return false;
    }

    wpbs_update_booking_meta($booking->get('id'), 'final_payment_email_sent', true);
    wpbs_update_booking_meta($booking->get('id'), 'final_payment_email_sent_date', current_time('timestamp'));
}, 10, 3);

/**
 * Add the form strings for the optional part payment form field
 *
 */
function wpbs_form_default_strings_settings_part_payment_applicability($strings)
{

    if (!wpbs_part_payments_enabled()) {
        return $strings;
    }

    $strings['part-payment-applicability'] = array(
        'label' => __('Part Payment Applicability Field', 'wp-booking-system'),
        'strings' => array(
            'part_payment_applicability_label' => array(
                'label' => __('Label', 'wp-booking-system'),
            ),
            'part_payment_applicability_option_full' => array(
                'label' => __('Full Payment Option', 'wp-booking-system'),
            ),
            'part_payment_applicability_option_deposit' => array(
                'label' => __('Deposit Only Option', 'wp-booking-system'),
            ),
        ),
    );

    return $strings;
}
add_filter('wpbs_form_default_strings_settings_page', 'wpbs_form_default_strings_settings_part_payment_applicability', 10, 1);

function wpbs_form_default_strings_part_payment_applicability($strings)
{
    $strings['part_payment_applicability_label'] = __('Payment Type', 'wp-booking-system');
    $strings['part_payment_applicability_option_full'] = __('Pay the full amount now', 'wp-booking-system');
    $strings['part_payment_applicability_option_deposit'] = __('Pay a deposit now and the remaining amount later', 'wp-booking-system');
    return $strings;
}
add_filter('wpbs_form_default_strings', 'wpbs_form_default_strings_part_payment_applicability', 10, 1);
