<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$settings = get_option('wpbs_settings', array());

$default_strings = wpbs_payment_default_strings();

$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$languages = wpbs_get_languages();

$strings = array(
    'payment_confirmation' => array(
        'label' => __('"Payment Confirmation" Heading', 'wp-booking-system'),
        'tooltip'  => __('Appears on the payment screen.', 'wp-booking-system'),
    ),
    'your_order' => array(
        'label' => __('"Your Order" Heading', 'wp-booking-system'),
        'tooltip'  => __('Appears above the pricing table.', 'wp-booking-system'),
    ),
    'booking_details' => array(
        'label' => __('"Booking Details" Heading', 'wp-booking-system'),
        'tooltip'  => __('Appears in the Final Payment page.', 'wp-booking-system'),
    ),
    'edit_order' => array(
        'label' => __('"Edit Order" Link', 'wp-booking-system'),
        'tooltip'  => __('Appears above the pricing table as a link which takes you back to the booking form.', 'wp-booking-system'),
    ),
    'item' => array(
        'label' => __('"Item" Table Column', 'wp-booking-system'),
        'tooltip'  => __('Appears in the pricing table.', 'wp-booking-system'),
    ),
    'total' => array(
        'label' => __('"Total" Table Column', 'wp-booking-system'),
        'tooltip'  => __('Appears in the pricing table.', 'wp-booking-system'),
    ),
    'subtotal' => array(
        'label' => __('"Subtotal" Table Column', 'wp-booking-system'),
        'tooltip'  => __('Appears in the pricing table.', 'wp-booking-system'),
    ),
    'processing_payment' => array(
        'label' => __('Processing Payment Message', 'wp-booking-system'),
        'tooltip'  => __('The loading message while the payment is being processed.', 'wp-booking-system'),
    ),
    'failed_payment' => array(
        'label' => __('Failed Payment Message', 'wp-booking-system'),
        'tooltip'  => __('A general error message shown when the payment fails or was cancelled.', 'wp-booking-system'),
    ),
    'select_dates' => array(
        'label' => __('No Dates Selected', 'wp-booking-system'),
        'tooltip'  => __('The message that appears in the pricing table if no dates are selected.', 'wp-booking-system'),
    ),
    'pricing_table_dates' => array(
        'label' => __('Pricing Table Dates', 'wp-booking-system'),
        'tooltip'  => __('This appears in the Pricing Table under the Product Name. The %s variables will be replaced with the start and end dates.', 'wp-booking-system'),
    ),
    'price_breakdown' => array(
        'label' => __('Price Breakdown Toggle', 'wp-booking-system'),
        'tooltip'  => __('This appears in the Pricing Table under the Product Name if the Price Breakdown option is enabled.', 'wp-booking-system'),
    ),
    'payment_submit' => array(
        'label' => __('Payment Submit Button Label', 'wp-booking-system'),
        'tooltip' => __("The button label when submitting a payment form.", 'wp-booking-system'),
    )
);

$strings = apply_filters('wpbs_payment_default_strings_labels', $strings);
?>

<div class="wpbs-tab-inner">

    <h2><?php echo __('Payment Strings', 'wp-booking-system') ?></h2>
    <?php foreach ($strings as $key => $string): ?>
        <!-- Required Field -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="wpbs_payment_string_<?php echo $key; ?>">
                <?php echo $string['label'] ?>
            </label>

            <div class="wpbs-input">
                <input name="wpbs_settings[payment_strings][<?php echo $key; ?>]" type="text" id="wpbs_payment_string_<?php echo $key; ?>" value="<?php echo (!empty($settings['payment_strings'][$key])) ? esc_attr($settings['payment_strings'][$key]) : $default_strings[$key]; ?>">
            </div>

            <?php if (isset($string['tooltip'])): ?>
                <div class="wpbs-label-description"><?php echo $string['tooltip']; ?></div>
            <?php endif ?>

            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

            <?php if (wpbs_translations_active()): ?>
                <!-- Required Field Translations -->
                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <?php foreach ($active_languages as $language): ?>
                        <div class="wpbs-form-field wpbs-small-margin">
                            <div class="wpbs-input">
                                <div class="wpbs-input-wrapper">
                                    <span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
                                        <span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo strtoupper($language); ?></span>
                                    </span>
                                    <input name="wpbs_settings[payment_strings][<?php echo $key; ?>_translation_<?php echo $language; ?>]" type="text" id="wpbs_payment_string_<?php echo $key; ?>_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_strings'][$key . '_translation_' . $language])) ? esc_attr($settings['payment_strings'][$key . '_translation_' . $language]) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

</div>