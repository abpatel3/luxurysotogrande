<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajax callback when submitting the form
 *
 * This function handles everything when submitting the form: validation, emails, payments screens, auto-pending functionality.
 *
 */
function wpbs_submit_form()
{

    $validate_nonce = apply_filters('wpbs_form_ajax_nonce', true);

    // Nonce
    if ($validate_nonce && (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_form_ajax'))) {
        $response = array('success' => false);
        $response['html'] = '<div class="wpbs-form-general-error">' . sprintf(__('CSRF (Number Used Once) validation failed. %sClick here to find out how to fix this.%s', 'wp-booking-system'), '<a href="https://www.wpbookingsystem.com/documentation/nonce-validation-failed/" target="_blank" title="">', '</a>') . '</div>';
        echo json_encode($response);
        wp_die();
    }

    // Get Form ID
    $form_id = absint(!empty($_POST['form']['id']) ? $_POST['form']['id'] : 0);
    $form = wpbs_get_form($form_id);

    if (is_null($form)) {
        return;
    }

    $calendar_id = absint(!empty($_POST['calendar']['id']) ? $_POST['calendar']['id'] : 0);
    $calendar = wpbs_get_calendar($calendar_id);

    if (is_null($calendar)) {
        return;
    }

    // Set the form arguments
    $form_args = array(
        'minimum_days' => (int) $_POST['form']['minimum_days'],
        'maximum_days' => (int) $_POST['form']['maximum_days'],
        'booking_start_day' => (int) $_POST['form']['booking_start_day'],
        'booking_end_day' => (int) $_POST['form']['booking_end_day'],
        'selection_type' => $_POST['form']['selection_type'],
        'selection_style' => $_POST['form']['selection_style'],
        'auto_pending' => $_POST['form']['auto_pending'],
        'show_date_selection' => (int) $_POST['form']['show_date_selection'],
        'language' => ($_POST['form']['language'] == 'auto' ? wpbs_get_site_language() : $_POST['form']['language']),
        'currency' => $_POST['form']['currency'],
        'manual_booking' => (isset($_POST['form']['manual_booking']) ? $_POST['form']['manual_booking'] : ''),
    );

    // Validate Form
    $form_validator = new WPBS_Form_Validator($form, $calendar, $_POST['form_data'], $_POST['form']['language']);
    $form_validator->sanitize_fields();
    $form_validator->validate_fields($form_args, $_POST['calendar']);
    $form_validator->validate_dates($_POST['form'], $_POST['calendar']);
    $form_validator->set_error_message();

    // Get form fields after sanitization and validation
    $form_fields = $form_validator->get_form_fields();

    if ($form_validator->has_errors() === true) {

        // Errors were found, we show the form again
        $form_outputter = new WPBS_Form_Outputter($form, $form_args, $form_fields, $calendar_id);

        // Response
        $response = array('success' => false);
        $response['html'] = $form_outputter->get_display();
    } else {
        $_POST['payment_screen'] = true;
        $payment_confirmation = apply_filters('wpbs_submit_form_before', false, $_POST, $form, $form_args, $form_fields, $calendar_id);
        if ($payment_confirmation !== false) {
            echo $payment_confirmation;
            wp_die();
        }

        // Process booking, events, emails and confirmation messages.
        $handler = new WPBS_Form_Handler($_POST, $form_id, $form_args, $form_fields, $calendar_id);
        $response = $handler->get_response();
    }

    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_nopriv_wpbs_submit_form', 'wpbs_submit_form');
add_action('wp_ajax_wpbs_submit_form', 'wpbs_submit_form');

/**
 * Ajax callback when selecting a date
 *
 * The function validates the date selection
 *
 */
function wpbs_validate_date_selection()
{

    $validate_nonce = apply_filters('wpbs_form_ajax_nonce', true);

    if ($validate_nonce) {
        // Nonce
        check_ajax_referer('wpbs_form_ajax', 'wpbs_token');
    }

    // Get Form ID
    $form_id = absint(!empty($_POST['form']['id']) ? $_POST['form']['id'] : 0);
    $form = wpbs_get_form($form_id);

    if (is_null($form)) {
        return;
    }

    $calendar_id = absint(!empty($_POST['calendar']['id']) ? $_POST['calendar']['id'] : 0);
    $calendar = wpbs_get_calendar($calendar_id);

    if (is_null($calendar)) {
        return;
    }

    // Validate Form
    $form_validator = new WPBS_Form_Validator($form, $calendar, $_POST['form_data'], $_POST['form']['language']);
    $form_validator->sanitize_fields();
    $form_validator->validate_dates($_POST['form'], $_POST['calendar']);
    $form_validator->set_error_message();

    // Get form fields after sanitization and validation
    $form_fields = $form_validator->get_form_fields();

    // Set the form arguments
    $form_args = array(
        'minimum_days' => (int) $_POST['form']['minimum_days'],
        'maximum_days' => (int) $_POST['form']['maximum_days'],
        'booking_start_day' => (int) $_POST['form']['booking_start_day'],
        'booking_end_day' => (int) $_POST['form']['booking_end_day'],
        'selection_type' => $_POST['form']['selection_type'],
        'selection_style' => $_POST['form']['selection_style'],
        'auto_pending' => $_POST['form']['auto_pending'],
        'show_date_selection' => (int) $_POST['form']['show_date_selection'],
        'language' => ($_POST['form']['language'] == 'auto' ? wpbs_get_site_language() : $_POST['form']['language']),
        'currency' => $_POST['form']['currency'],
        'manual_booking' => (isset($_POST['form']['manual_booking']) ? $_POST['form']['manual_booking'] : ''),
    );

    if ($form_validator->has_errors() === true) {

        // Errors were found, we show the form again
        $form_outputter = new WPBS_Form_Outputter($form, $form_args, $form_fields, $calendar_id);

        // Response
        $response = array('success' => false);
        $response['html'] = $form_outputter->get_display();
    } else {
        $response = array('success' => true);
    }
    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_nopriv_wpbs_validate_date_selection', 'wpbs_validate_date_selection');
add_action('wp_ajax_wpbs_validate_date_selection', 'wpbs_validate_date_selection');

/**
 * Ajax callback for uploading a file
 *
 */
function wpbs_form_upload_file()
{

    if(apply_filters('wpbs_enable_file_upload_field', false) == false){
        return false;
    }

    $validate_nonce = apply_filters('wpbs_form_ajax_nonce', true);

    // Nonce
    if ($validate_nonce && (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_form_ajax'))) {
        wp_send_json_error('<div class="wpbs-form-general-error">' . sprintf(__('CSRF (Number Used Once) validation failed. %sClick here to find out how to fix this.%s', 'wp-booking-system'), '<a href="https://www.wpbookingsystem.com/documentation/nonce-validation-failed/" target="_blank" title="">', '</a>') . '</div>');
    }

    if (empty($_POST['wpbs_upload_token'])) {
        wp_send_json_error('Missing token.');
    }

    $token = sanitize_text_field($_POST['wpbs_upload_token']);

    if (empty($_FILES['wpbs_file'])) {
        wp_send_json_error('No file received.');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $uploaded = wp_handle_upload($_FILES['wpbs_file'], apply_filters('wpbs_handle_upload_overrides', array('test_form' => false), $_POST));

    if (isset($uploaded['error'])) {
        wp_send_json_error($uploaded['error']);
    }

    $data = array(
        'url'  => $uploaded['url'],
        'file' => $uploaded['file'],
        'type' => $uploaded['type'],
        'name' => $_FILES['wpbs_file']['name'],
    );

    set_transient('wpbs_upload_token_' . $token . $_POST['wpbs_form_id'] . $_POST['wpbs_field_id'], $data, HOUR_IN_SECONDS);

    $html = '<span class="wpbs-file-upload-name"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M304 112L192 112C183.2 112 176 119.2 176 128L176 512C176 520.8 183.2 528 192 528L448 528C456.8 528 464 520.8 464 512L464 272L376 272C336.2 272 304 239.8 304 200L304 112zM444.1 224L352 131.9L352 200C352 213.3 362.7 224 376 224L444.1 224zM128 128C128 92.7 156.7 64 192 64L325.5 64C342.5 64 358.8 70.7 370.8 82.7L493.3 205.3C505.3 217.3 512 233.6 512 250.6L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 128z"/></svg><span class="wpbs-file-upload-file-name">' . $data['name'] . '</span></span><a class="wpbs-form-file-remove" href="#"></a></span>';

    wp_send_json_success(array(
        'html' => $html,
    ));
}

add_action('wp_ajax_wpbs_form_upload_file', 'wpbs_form_upload_file');
add_action('wp_ajax_nopriv_wpbs_form_upload_file', 'wpbs_form_upload_file');


/**
 * AJAX call to remove uploaded file from server.
 * 
 */
function wpbs_form_upload_file_remove()
{
    
    if(apply_filters('wpbs_enable_file_upload_field', false) == false){
        return false;
    }

    $validate_nonce = apply_filters('wpbs_form_ajax_nonce', true);

    // Nonce
    if ($validate_nonce && (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_form_ajax'))) {
        wp_die();
    }

    if (empty($_POST['wpbs_upload_token'])) {
        wp_send_json_error('Missing token.');
    }

    $token = sanitize_text_field($_POST['wpbs_upload_token']);

    $file_data = get_transient('wpbs_upload_token_' . $token . $_POST['wpbs_form_id'] . $_POST['wpbs_field_id']);
    if (isset($file_data) && is_array($file_data)) {
        unlink($file_data['file']);
        delete_transient('wpbs_upload_token_' . $token . $_POST['wpbs_form_id'] . $_POST['wpbs_field_id']);
    }

    wp_die();
}
add_action('wp_ajax_wpbs_form_upload_file_remove', 'wpbs_form_upload_file_remove');
add_action('wp_ajax_nopriv_wpbs_form_upload_file_remove', 'wpbs_form_upload_file_remove');
