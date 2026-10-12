<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;


/**
 * Validates and handles the adding of the new email notification in the database
 *
 */
function wpbs_action_add_email_notification()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || ! wp_verify_nonce($_POST['wpbs_token'], 'wpbs_add_email_notification'))
        return;

    // Verify for email notification name
    if (empty($_POST['notification_email_name'])) {

        wpbs_admin_notices()->register_notice('email_notification_name_missing', '<p>' . __('Please add a name for your new email notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_name_missing');

        return;
    }

    // Verify for email notification name
    if (empty($_POST['email_notification_form_id'])) {
        return;
    }

    $form_id = absint($_POST['email_notification_form_id']);
    $form = wpbs_get_form($form_id);

    // Prepare email notification data to be inserted
    $email_notification_data = array(
        'name'             => sanitize_text_field(stripslashes($_POST['notification_email_name'])),
        'form_id'       => $form_id,
        'is_enabled'     => 1,
        'date_created'  => current_time('Y-m-d H:i:s'),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'status'        => 'active'
    );

    // Insert email notification into the database
    $email_notification_id = wpbs_insert_email($email_notification_data);

    // If the email notification could not be inserted show a message to the user
    if (! $email_notification_id) {

        wpbs_admin_notices()->register_notice('email_notification_insert_false', '<p>' . __('Something went wrong. Could not create the email notification. Please try again.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_insert_false');

        return;
    }

    $email_fields = wpbs_form_get_email_fields($form->get('fields'));


    // Set some defaults
    wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
    wpbs_update_email_meta($email_notification_id, 'notification_send_to_email', get_option('admin_email'));
    if ($email_fields) {
        $label = isset($email_fields[0]['values']['default']['label']) && !empty($email_fields[0]['values']['default']['label']) ? $email_fields[0]['values']['default']['label'] : 'no-label';
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_field', '{' . $email_fields[0]['id'] . ':' . $label . '}');
        wpbs_update_email_meta($email_notification_id, 'notification_send_to', '{' . $email_fields[0]['id'] . ':' . $label . '}');
    } else {
        wpbs_update_email_meta($email_notification_id, 'notification_send_to', get_option('admin_email'));
    }


    if ($_POST['email_notification_template'] == 'user_notification') {
        // User Notification Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', __('Thank you for your booking!', 'wp-booking-system'));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>Thank you for your booking! We're excited to welcome you. <br><br>Here are your booking details:<br><br>{All Fields}<br><br>Looking forward to your stay!<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_ical_file', 'on');
        wpbs_update_email_meta($email_notification_id, 'notification_ical_summary', sprintf(__('Stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'booking_created');
    } elseif ($_POST['email_notification_template'] == 'reminder_email') {
        // Reminder Email Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', sprintf(__('Your upcoming stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>We're looking forward to welcoming you on {Start Date}! Here are the details of your booking for your convenience:<br><br>{All Fields}<br><br>If there's anything you need before your stay, feel free to reply to this email.<br><br>Safe travels, and see you soon!<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_ical_file', 'on');
        wpbs_update_email_meta($email_notification_id, 'notification_ical_summary', sprintf(__('Stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'before_start');
        wpbs_update_email_meta($email_notification_id, 'notification_day_offset', 3);
    } elseif ($_POST['email_notification_template'] == 'followup_email') {
        // Followup Email Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', sprintf(__('We hope you enjoyed your stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>We hope you had a wonderful stay with us! Your feedback is very important to us, and we'd love to hear about your experience.<br><br>If you have a moment, please let us know what you enjoyed or how we can improve by replying to this email.<br><br>Should you plan another trip in the future, we'd be delighted to welcome you back. Feel free to reach out anytime!<br><br>Thank you for choosing us.<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'after_end');
        wpbs_update_email_meta($email_notification_id, 'notification_day_offset', 3);
    } elseif ($_POST['email_notification_template'] == 'payment_email') {
        // Payment Reminder Email Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', sprintf(__('Payment Reminder for your stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>We hope this message finds you well. This is a friendly reminder regarding your outstanding payment for your booking.<br><br>Here are the details:<br><br>{Order Details}<br><br>To complete your payment, please use the secure link below:<br>{Final Payment Link}<br><br>If you've already made the payment, kindly disregard this email. Should you have any questions or require assistance, don't hesitate to reply to this message.<br><br>Thank you for your prompt attention to this matter.<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'before_start');
        wpbs_update_email_meta($email_notification_id, 'notification_day_offset', 7);
        wpbs_update_email_meta($email_notification_id, 'notification_enable_conditional_logic', 'on');
        wpbs_update_email_meta($email_notification_id, 'notification_conditional_logic', json_decode('[[{"condition":"payment_type","form_field":"","comparison":"deposit","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"not_paid","value-booking_type":"customer","value-user-role":"","value-date":""},{"condition":"payment_status","form_field":"","comparison":"equal","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"deposit_paid","value-booking_type":"customer","value-user-role":"","value-date":""},{"condition":"booking_type","form_field":"","comparison":"equal","value":"","value-weekday":"1","value-payment_method":"payment_on_arrival","value-payment_status":"not_paid","value-booking_type":"customer","value-user-role":"","value-date":""}]]', true));
    } elseif ($_POST['email_notification_template'] == 'payment_confirmation') {
        // Payment Reminder Email Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', sprintf(__('Payment Confirmation for your stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>Thank you for completing your payment! We're pleased to confirm that your booking is now fully paid.<br><br>If you have any questions or need further assistance before your arrival, feel free to reach out to us.<br><br>We look forward to welcoming you soon!<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'final_payment_made');
    } else {
        // No template
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'booking_created');
    }

    // Redirect to the edit page of the email notification with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications', 'email_notification_id' => $email_notification_id, 'wpbs_message' => 'email_notification_insert_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_add_email_notification', 'wpbs_action_add_email_notification', 50);


/**
 * Validates and handles the editing of an existing email notification
 *
 */
function wpbs_action_edit_email_notification()
{
    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_edit_email_notification')) {
        return;
    }

    $_POST = stripslashes_deep($_POST);

    if (empty($_POST['form_id'])) {

        wpbs_admin_notices()->register_notice('email_notification_update_failed', '<p>' . __('Something went wrong. Could not update the email_notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_update_failed');

        return;
    }

    if (empty($_POST['email_id'])) {

        wpbs_admin_notices()->register_notice('email_notification_update_failed', '<p>' . __('Something went wrong. Could not update the email_notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_update_failed');

        return;
    }

    $settings = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    /**
     * Prepare variables
     *
     */
    $form_id = absint($_POST['form_id']);
    $email_id = absint($_POST['email_id']);
    $email_name = sanitize_text_field($_POST['notification_email_name']);


    /**
     * Handle email notification object data
     *
     */

    // Get email
    $email = wpbs_get_email($email_id);

    // Update email
    $update_data = array(
        'name' => (!empty($email_name) ? $email_name : $email->get('name')),
        'date_modified' => current_time('Y-m-d H:i:s'),
    );



    wpbs_update_email($email_id, $update_data);

    /**
     * Handle email meta
     *
     */


    // Email Notification Meta Fields
    $meta_fields = array(
        'notification_send_to_type' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_field' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_email' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_cc' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_bcc' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_from_name' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_from_email' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_reply_to' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_subject' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_message' => array('translations' => true, 'sanitization' => 'wp_kses_post'),
        'notification_ical_file' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
        'notification_ical_summary' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_ical_description' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_when_to_send' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_day_offset' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_manage_booking_link' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
        'notification_enable_conditional_logic' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
    );

    $meta_fields = apply_filters('wpbs_edit_emails_meta_fields', $meta_fields);

    // Store the "send to" email address in a single field.
    if (isset($_POST['notification_send_to_type']) && $_POST['notification_send_to_type'] == 'field') {
        if (isset($_POST['notification_send_to_field'])) {
            wpbs_update_email_meta($email_id, 'notification_send_to', sanitize_text_field($_POST['notification_send_to_field']));
        }
    } elseif (isset($_POST['notification_send_to_type']) && $_POST['notification_send_to_type'] == 'email') {
        if (isset($_POST['notification_send_to_email'])) {
            wpbs_update_email_meta($email_id, 'notification_send_to', sanitize_text_field($_POST['notification_send_to_email']));
        }
    }

    // Update meta fields
    foreach ($meta_fields as $meta_field => $options) {

        if (!isset($_POST[$meta_field])) {
            continue;
        }

        if (!wpbs_is_empty($_POST[$meta_field])) {
            wpbs_update_email_meta($email_id, $meta_field, $options['sanitization']($_POST[$meta_field]));
        } else {
            wpbs_delete_email_meta($email_id, $meta_field);
        }

        if ($options['translations'] == true) {
            foreach ($active_languages as $code) {

                if (!empty($_POST[$meta_field . '_translation_' . $code])) {
                    wpbs_update_email_meta($email_id, $meta_field . '_translation_' . $code, $options['sanitization']($_POST[$meta_field . '_translation_' . $code]));
                } else {
                    wpbs_delete_email_meta($email_id, $meta_field . '_translation_' . $code);
                }
            }
        }
    }

    // Update conditional Logic
    if (isset($_POST['notification_conditional_logic'])) {
        $conditional_logic_options = [];
        foreach ($_POST['notification_conditional_logic'] as $i => $conditional_logic_rule_group) {
            for ($j = 0; $j < count($conditional_logic_rule_group['condition']); $j++) {
                $conditional_logic_options[$i][] = array(
                    'condition' => (isset($conditional_logic_rule_group['condition'][$j]) ? $conditional_logic_rule_group['condition'][$j] : ''),
                    'form_field' => (isset($conditional_logic_rule_group['form_field'][$j]) ? $conditional_logic_rule_group['form_field'][$j] : ''),
                    'comparison' => (isset($conditional_logic_rule_group['comparison'][$j]) ? $conditional_logic_rule_group['comparison'][$j] : ''),
                    'value' => (isset($conditional_logic_rule_group['value'][$j]) ? $conditional_logic_rule_group['value'][$j] : ''),
                    'value-weekday' => (isset($conditional_logic_rule_group['value-weekday'][$j]) ? $conditional_logic_rule_group['value-weekday'][$j] : ''),
                    'value-payment_method' => (isset($conditional_logic_rule_group['value-payment_method'][$j]) ? $conditional_logic_rule_group['value-payment_method'][$j] : ''),
                    'value-payment_status' => (isset($conditional_logic_rule_group['value-payment_status'][$j]) ? $conditional_logic_rule_group['value-payment_status'][$j] : ''),
                    'value-booking_type' => (isset($conditional_logic_rule_group['value-booking_type'][$j]) ? $conditional_logic_rule_group['value-booking_type'][$j] : ''),
                    'value-user-role' => (isset($conditional_logic_rule_group['value-user-role'][$j]) ? $conditional_logic_rule_group['value-user-role'][$j] : ''),
                    'value-date' => (isset($conditional_logic_rule_group['value-date'][$j]) ? $conditional_logic_rule_group['value-date'][$j] : ''),
                );
            }
        }

        wpbs_update_email_meta($email_id, 'notification_conditional_logic', $conditional_logic_options);
    }


    /**
     * Action hook to save extra email data
     *
     * @param array $_POST
     *
     */
    do_action('wpbs_save_email_data', $_POST);

    /**
     * Success redirect
     *
     */
    if (isset($_POST['_wp_http_referer'])) {
        wp_redirect(add_query_arg(array('wpbs_message' => 'email_notification_edit_success'), $_POST['_wp_http_referer']));
        exit;
    }

    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications', 'email_id' => $email_id, 'wpbs_message' => 'email_notification_edit_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_edit_email_notification', 'wpbs_action_edit_email_notification', 50);

/**
 * Handles the trash email action, which changes the status of the email from active to trash
 *
 */
function wpbs_action_trash_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_trash_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    if (empty($_GET['form_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    $email_data = array(
        'status' => 'trash'
    );

    $updated = wpbs_update_email($email_notification_id, $email_data);

    if (! $updated)
        return;

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'wpbs_message' => 'email_trash_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_trash_email', 'wpbs_action_trash_email', 50);


/**
 * Handles the restore email action, which changes the status of the email from trash to active
 *
 */
function wpbs_action_restore_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_restore_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    $email_data = array(
        'status' => 'active'
    );

    $updated = wpbs_update_email($email_notification_id, $email_data);

    if (! $updated)
        return;

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'email_status' => 'trash', 'wpbs_message' => 'email_notification_restore_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_restore_email', 'wpbs_action_restore_email', 50);


/**
 * Handles the delete email action, which removes all email data, legend items and events data
 * associated with the email
 *
 */
function wpbs_action_delete_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_delete_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    /**
     * Delete the email
     *
     */
    $deleted = wpbs_delete_email($email_notification_id);

    if (! $deleted)
        return;

    /**
     * Delete email meta
     *
     */
    $email_meta = wpbs_get_email_meta($email_notification_id);

    if (! empty($email_meta)) {

        foreach ($email_meta as $key => $value) {

            wpbs_delete_email_meta($email_notification_id, $key);
        }
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'email_status' => 'trash', 'wpbs_message' => 'email_notification_delete_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_delete_email', 'wpbs_action_delete_email', 50);


/**
 * Handles the duplication of a email
 *
 */
function wpbs_action_duplicate_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_duplicate_email')) {
        return;
    }

    if (empty($_GET['email_notification_id'])) {
        return;
    }

    $email_notification_id = absint($_GET['email_notification_id']);

    $email = wpbs_get_email($email_notification_id);

    // Prepare email notification data to be inserted
    $email_notification_data = array(
        'name' => __('Duplicate of', 'wp-booking-system') . ' ' . $email->get('name'),
        'form_id'       =>  $email->get('form_id'),
        'is_enabled'     =>  $email->get('is_enabled'),
        'date_created'  => current_time('Y-m-d H:i:s'),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'status' => $email->get('status'),
    );

    // Insert email notification into the database
    $new_email_notification_id = wpbs_insert_email($email_notification_data);

    $meta_fields = wpbs_get_email_meta($email_notification_id);

    foreach ($meta_fields as $meta_key => $meta_value) {
        if (is_serialized($meta_value[0], false)) {
            $meta_value[0] = unserialize($meta_value[0]);
        }
        wpbs_add_email_meta($new_email_notification_id, $meta_key, $meta_value[0]);
    }


    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'wpbs_message' => 'email_duplicate_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_duplicate_email', 'wpbs_action_duplicate_email', 50);


/**
 * AJAX Callback function for enabling/disabling emails
 * 
 */
function wpbs_action_ajax_email_status_toggle()
{

    if (!isset($_POST['email_id'])) {
        return false;
    }

    $email_id = absint($_POST['email_id']);

    wpbs_update_email($email_id, [
        'is_enabled' => ($_POST['value'] == 'true' ? 1 : 0),
        'date_modified' => current_time('Y-m-d H:i:s')
    ]);

    wp_die();
}
add_action('wp_ajax_wpbs_email_status_toggle', 'wpbs_action_ajax_email_status_toggle');
