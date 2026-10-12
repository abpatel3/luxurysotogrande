<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Skip update actions if we're just activating the plugin.
 * 
 */
function wpbs_first_activation_skip_updates()
{
    add_option('wpbs_update_action_5_7', true);
    add_option('wpbs_update_action_5_8', true);
    add_option('wpbs_update_action_5_16', true);
    add_option('wpbs_update_action_5_11_0', true);
}
add_action('wpbs_first_activation', 'wpbs_first_activation_skip_updates');


/**
 * In version 5.7, the default Form Strings & Translations were created
 * in the plugin's Settings page. These can be overwritten from each 
 * form's Settings page.
 * 
 * When updating, we enable the overwrite option for all installs so 
 * users won't have to move their translations.
 * 
 */
function wpbs_update_action_5_7()
{

    $update = get_option('wpbs_update_action_5_7');

    if ($update === false) {

        $forms = wpbs_get_forms();

        foreach ($forms as $form) {
            wpbs_update_form_meta($form->get('id'), 'overwrite_strings_and_translations', 'on');
        }

        add_option('wpbs_update_action_5_7', true);
    }
}
add_action('wpbs_update_check', 'wpbs_update_action_5_7');

/**
 * In version 5.8, the Customizer was added. We need to move some settings 
 * from the setings page to the Customizer Settings.
 * 
 */
function wpbs_update_action_5_8()
{

    $update = get_option('wpbs_update_action_5_8');

    if ($update === false) {

        $plugin_settings = get_option('wpbs_settings', array());

        set_theme_mod('form_button_background_color', (isset($plugin_settings['button_background_color']) ? $plugin_settings['button_background_color'] : ''));
        set_theme_mod('form_button_text_color', (isset($plugin_settings['button_text_color']) ? $plugin_settings['button_text_color'] : ''));
        set_theme_mod('form_button_hover_background_color', (isset($plugin_settings['button_background_hover_color']) ? $plugin_settings['button_background_hover_color'] : ''));
        set_theme_mod('form_button_hover_text_color', (isset($plugin_settings['button_text_hover_color']) ? $plugin_settings['button_text_hover_color'] : ''));

        add_option('wpbs_update_action_5_8', true);
    }
}
add_action('wpbs_update_check', 'wpbs_update_action_5_8');

/**
 * In version 5.16.2, custom VAT options were added 
 * and changes to the Invoice add-on were made.
 * 
 * We need to add an option to keep the Invoice working as before.
 * 
 */
function wpbs_update_action_5_16()
{

    $update = get_option('wpbs_update_action_5_16');

    if ($update === false) {

        $plugin_settings = get_option('wpbs_settings', array());

        if (
            in_array('wp-booking-system-premium-invoices/index.php', (array) get_option('active_plugins')) ||
            (is_multisite() && array_key_exists('wp-booking-system-premium-invoices/index.php', (array) get_site_option('active_sitewide_plugins')))
        ) {
            $plugin_settings['invoice_vat_calculation_source'] = 'custom';
            update_option('wpbs_settings', $plugin_settings);
        }

        add_option('wpbs_update_action_5_16', true);
    }
}
add_action('wpbs_update_check', 'wpbs_update_action_5_16');

/**
 * In version 5.11.0, we remade the entire email system
 * 
 * 
 * We have to move all the old data in the new format
 * 
 */
function wpbs_update_action_5_11_0()
{

    $update = get_option('wpbs_update_action_5_11_0');

    if ($update === false) {

        add_option('wpbs_update_action_5_11_0', true);

        $email_types = ['admin', 'user', 'payment', 'payment_success', 'reminder', 'followup'];
        $settings = get_option('wpbs_settings', array());

        $names = ['admin' => 'Admin New Booking Notification', 'user' => 'Customer New Booking Notification', 'payment' => 'Customer Payment Reminder', 'payment_success' => 'Customer Final Payment Confirmation', 'reminder' => 'Customer Reminder Email', 'followup' => 'Customer Follow Up Email'];

        $active_languages = [];
        if (isset($settings['active_languages']) && count($settings['active_languages']) > 0) {
            $active_languages = $settings['active_languages'];
        }

        $forms = wpbs_get_forms();
        foreach ($forms as $form) {

            foreach ($email_types as $email_type) {
                if (wpbs_get_form_meta($form->get('id'), $email_type . '_notification_enable', true) == 'on') {

                    $email_notification_data = array(
                        'name'             => $names[$email_type],
                        'form_id'       => $form->get('id'),
                        'is_enabled'     => 1,
                        'date_created'  => current_time('Y-m-d H:i:s'),
                        'date_modified' => current_time('Y-m-d H:i:s'),
                        'status'        => 'active'
                    );

                    // Insert email notification into the database
                    $email_notification_id = wpbs_insert_email($email_notification_data);

                    $keys = ['notification_subject', 'notification_message', 'notification_ical_summary', 'notification_ical_description', 'notification_send_to', 'notification_send_to_cc', 'notification_send_to_bcc', 'notification_from_name', 'notification_from_email', 'notification_reply_to', 'notification_ical_file'];

                    foreach ($keys as $key) {
                        if (wpbs_get_form_meta($form->get('id'), $email_type . '_' . $key, true)) {
                            wpbs_add_email_meta($email_notification_id, $key, wpbs_get_form_meta($form->get('id'), $email_type . '_' . $key, true));
                        }
                    }

                    $keys = ['notification_subject', 'notification_message', 'notification_ical_summary', 'notification_ical_description'];

                    foreach ($keys as $key) {
                        foreach ($active_languages as $language) {
                            if (wpbs_get_form_meta($form->get('id'), $email_type . '_' . $key . '_translation_' . $language, true)) {
                                wpbs_add_email_meta($email_notification_id, $key . '_translation_' . $language, wpbs_get_form_meta($form->get('id'), $email_type . '_' . $key . '_translation_' . $language, true));
                            }
                        }
                    }

                    if ($email_type == 'admin') {
                        wpbs_update_email_meta($email_notification_id, 'notification_manage_booking_link', 'on');
                        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'email');
                        wpbs_update_email_meta($email_notification_id, 'notification_send_to_email', wpbs_get_form_meta($form->get('id'), $email_type . '_notification_send_to', true));
                    } else {
                        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
                        wpbs_update_email_meta($email_notification_id, 'notification_send_to_field', wpbs_get_form_meta($form->get('id'), $email_type . '_notification_send_to', true));
                    }

                    // offset
                    if (wpbs_get_form_meta($form->get('id'), $email_type . '_notification_when_to_send', true)) {
                        wpbs_add_email_meta($email_notification_id, 'notification_day_offset', wpbs_get_form_meta($form->get('id'), $email_type . '_notification_when_to_send', true));
                    }

                    if ($email_type == 'admin' || $email_type == 'user') {
                        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'booking_created');
                    }

                    if ($email_type == 'payment' || $email_type == 'reminder') {
                        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'before_start');
                    }
                    if ($email_type == 'followup') {
                        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'after_end');
                    }
                    if ($email_type == 'payment_success') {
                        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'final_payment_made');
                    }

                    if ($email_type == 'payment') {
                        wpbs_update_email_meta($email_notification_id, 'notification_enable_conditional_logic', 'on');
                        wpbs_update_email_meta($email_notification_id, 'notification_conditional_logic', json_decode('[[{"condition":"payment_type","form_field":"","comparison":"deposit","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"not_paid","value-booking_type":"customer","value-user-role":"","value-date":""},{"condition":"payment_status","form_field":"","comparison":"equal","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"deposit_paid","value-booking_type":"customer","value-user-role":"","value-date":""},{"condition":"booking_type","form_field":"","comparison":"equal","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"not_paid","value-booking_type":"customer","value-user-role":"","value-date":""}]]', true));
                    }

                    if (wpbs_get_form_meta($form->get('id'), 'contract_attach_to_' . $email_type . '_email', true)) {
                        wpbs_update_email_meta($email_notification_id, 'notification_attach_contract', 'on');
                    }

                    if (wpbs_get_form_meta($form->get('id'), 'invoice_attach_to_' . $email_type . '_email', true)) {
                        wpbs_update_email_meta($email_notification_id, 'notification_attach_invoice', 'on');
                    }
                }
            }
        }

        wpbs_regenerate_scheduled_email_cron_jobs();
    }
}

add_action('wpbs_update_check', 'wpbs_update_action_5_11_0', 99);
