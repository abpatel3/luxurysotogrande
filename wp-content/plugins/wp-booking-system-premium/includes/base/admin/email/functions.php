<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Form Emails
 *
 */
function wpbs_include_files_email()
{

    // Get form dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include main Email class
    if (file_exists($dir_path . 'class-email.php')) {
        include $dir_path . 'class-email.php';
    }

    // Include main Table List class
    if (file_exists($dir_path . 'class-list-table-emails.php')) {
        include $dir_path . 'class-list-table-emails.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-emails.php')) {
        include $dir_path . 'class-object-db-emails.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-emails.php')) {
        include $dir_path . 'class-object-meta-db-emails.php';
    }

    // Include the Email Notifications class
    if (file_exists($dir_path . 'class-email-notifications.php')) {
        include $dir_path . 'class-email-notifications.php';
    }

     // Include the Email Notifications class
    if (file_exists($dir_path . 'class-evaluate-email-logic.php')) {
        include $dir_path . 'class-evaluate-email-logic.php';
    }

    // Include email actions
    if (file_exists($dir_path . 'functions-actions-emails.php')) {
        include $dir_path . 'functions-actions-emails.php';
    }
}
add_action('wpbs_include_files', 'wpbs_include_files_email');

/**
 * Register the class that handles database queries for the Form Emails
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_emails($classes)
{

    $classes['emails'] = 'WPBS_Object_DB_Emails';
    $classes['emailmeta'] = 'WPBS_Object_Meta_DB_Emails';

    return $classes;
}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_emails');

/**
 * Returns an array with WPBS_Email objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_emails($args = array(), $count = false)
{

    $emails = wp_booking_system()->db['emails']->get_emails($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $emails
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_emails', $emails, $args, $count);
}

/**
 * Gets a email from the database
 *
 * @param mixed int|object      - email id or object representing the email
 *
 * @return WPBS_Email|false
 *
 */
function wpbs_get_email($email)
{

    return wp_booking_system()->db['emails']->get_object($email);
}

/**
 * Inserts a new email into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_email($data)
{

    return wp_booking_system()->db['emails']->insert($data);
}

/**
 * Updates a email from the database
 *
 * @param int     $email_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_email($email_id, $data)
{

    return wp_booking_system()->db['emails']->update($email_id, $data);
}

/**
 * Deletes a email from the database
 *
 * @param int $email_id
 *
 * @return bool
 *
 */
function wpbs_delete_email($email_id)
{

    return wp_booking_system()->db['emails']->delete($email_id);
}

/**
 * Inserts a new meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_email_meta($email_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['emailmeta']->add($email_id, $meta_key, $meta_value, $unique);
}

/**
 * Updates a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_email_meta($email_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['emailmeta']->update($email_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Returns a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_email_meta($email_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['emailmeta']->get($email_id, $meta_key, $single);
}

/**
 * Returns the translated meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $language_code
 *
 * @return mixed
 *
 */
function wpbs_get_translated_email_meta($email_id, $meta_key, $language_code)
{
    $settings           = get_option('wpbs_settings', array());
    $active_languages = (! empty($settings['active_languages']) ? $settings['active_languages'] : array());

    if (in_array($language_code, $active_languages)) {

        $translated_meta = wpbs_get_email_meta($email_id, $meta_key . '_translation_' . $language_code, true);

        if (!empty($translated_meta)) {
            return $translated_meta;
        }
    }

    return wpbs_get_email_meta($email_id, $meta_key, true);
}

/**
 * Removes a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_email_meta($email_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['emailmeta']->delete($email_id, $meta_key, $meta_value, $delete_all);
}

/**
 * Send Scheduled Email
 * 
 * @param int $booking_id
 * @param int $email_id
 * 
 */
function wpbs_send_scheduled_email_notification($booking_id, $email_id)
{

    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return false;
    }

    if ($booking->get('status') == 'trash') {
        return false;
    }

    $email = wpbs_get_email($email_id);

    if (is_null($email)) {
        return false;
    }

    if ($email->get('status') == 'trash') {
        return false;
    }

    if (!$email->get('is_enabled')) {
        return false;
    }

    // // If Booking is still Pending, reschedule email.
    if ($booking->get('status') == 'pending') {

        // Reschedule the event to be sent after an hour
        if (wpbs_get_email_delivery_date($booking, $email) < current_time('timestamp')) {
            return false;
        }

        $delivery_date = current_time('timestamp') + HOUR_IN_SECONDS;
        wp_schedule_single_event($delivery_date, 'wpbs_send_scheduled_email_notification', array($booking_id, $email_id));

        return false;
    }

    $emails = new WPBS_Email_Notifications($booking_id);
    $emails->send_email($email_id);

}
add_action('wpbs_send_scheduled_email_notification', 'wpbs_send_scheduled_email_notification', 10, 2);

/**
 * Get scheduled email delivery date
 * 
 * @param WPBS_Booking $booking
 * @param WPBS_Email $email
 * 
 * @return int
 * 
 */
function wpbs_get_email_delivery_date($booking, $email)
{

    $when_to_send = wpbs_get_email_meta($email->get('id'), 'notification_when_to_send', true);
    $day_offset = absint(wpbs_get_email_meta($email->get('id'), 'notification_day_offset', true));
    $booking_created_date = strtotime($booking->get('date_created'));
    $booking_start_date = strtotime($booking->get('start_date'));
    $booking_end_date = strtotime($booking->get('end_date'));

    // When do we send it.
    switch ($when_to_send) {
        case 'after_created':
            $delivery_date = $booking_created_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'before_start':
            $delivery_date = $booking_start_date - intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'after_start':
            $delivery_date = $booking_start_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'before_end':
            $delivery_date = $booking_end_date - intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'after_end':
            $delivery_date = $booking_end_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        default:
            $delivery_date = $booking_start_date;
    }

    if (function_exists('wpbs_scheduled_email_delivery_hour')) {
        $delivery_date += wpbs_scheduled_email_delivery_hour();
    }

    return $delivery_date;
}

/**
 * Helper function for outputting a logic rule row.
 * 
 */
function wpbs_email_conditional_logic_rule_row($form_id, $rule = false, $group_index = false)
{

    $form = wpbs_get_form($form_id);

    $logic_rules = array(
        'form-field' => __('Form Field', 'wp-booking-system'),
        'payment_type' => __('Payment Type', 'wp-booking-system'),
        'payment_method' => __('Payment Method', 'wp-booking-system'),
        'payment_status' => __('Payment Status', 'wp-booking-system'),
        'booking_type' => __('Booking Type', 'wp-booking-system'),
        'calendar_id' => __('Calendar ID', 'wp-booking-system'),
        'number-of-booked-days' => __('Number of booked days', 'wp-booking-system'),
        'calendar-value' => __('Calendar value', 'wp-booking-system'),
        'total-order-value' => __('Total order value', 'wp-booking-system'),
        'booking-weekday' => __('Booking weekday', 'wp-booking-system'),
        'booking-day' => __('Booking day', 'wp-booking-system'),
        'booking-period' => __('Number of days from current day', 'wp-booking-system'),
        'price-per-day' => __('Price per day', 'wp-booking-system'),
        'user' => __('User', 'wp-booking-system'),
        'user_role' => __('User Role', 'wp-booking-system'),
    );

    $logic_rules = apply_filters('wpbs_email_conditional_rules', $logic_rules);

    $comparison_values = array(
        'number-of-booked-days' => array(
            "equal" =>  __('is equal to', 'wp-booking-system'),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            )
        ),

        'total-order-value' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            )
        ),

        'calendar-value' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            )
        ),

        'form-field' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            ),
            "contains" => __(
                'contains',
                'wp-booking-system'
            ),
            "not_contains" => __(
                'does not contain',
                'wp-booking-system'
            )
        ),

        'booking-weekday' => array(
            "starts" =>  __(
                'starts on a',
                'wp-booking-system'
            ),
            "does-not-start" => __(
                'does not start on a',
                'wp-booking-system'
            ),
            "ends" =>  __(
                'ends on a',
                'wp-booking-system'
            ),
            "does-not-end" => __(
                'does not end on a',
                'wp-booking-system'
            )
        ),

        'booking-day' => array(
            "starts" =>  __(
                'starts on',
                'wp-booking-system'
            ),
            "does-not-start" => __(
                'does not start on',
                'wp-booking-system'
            ),
            "ends" =>  __(
                'ends on',
                'wp-booking-system'
            ),
            "does-not-end" => __(
                'does not end on',
                'wp-booking-system'
            )
        ),

        'booking-period' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            )
        ),

        'price-per-day' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            ),
            "greater" => __(
                'is greater than',
                'wp-booking-system'
            ),
            "lower" => __(
                'is lower than',
                'wp-booking-system'
            )
        ),

        'user' => array(
            "logged_in" =>  __(
                'is logged in',
                'wp-booking-system'
            ),
            "not_logged_in" => __(
                'is not logged in',
                'wp-booking-system'
            )
        ),

        'payment_type' => array(
            "full" =>  __(
                'is full amount',
                'wp-booking-system'
            ),
            "deposit" => __(
                'is deposit',
                'wp-booking-system'
            )
        ),

        'booking_type' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

        'calendar_id' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

        'payment_status' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

        'payment_method' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

        'user_role' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

        'currency' => array(
            "equal" =>  __(
                'is equal to',
                'wp-booking-system'
            ),
            "not-equal" => __(
                'is not equal to',
                'wp-booking-system'
            )
        ),

    );

    $comparison_values = apply_filters('wpbs_email_conditional_logic_rules_comparison_values', $comparison_values);

?>
    <div class="wpbs-logic-rule <?php if ($rule) : ?>wpbs-logic-rule-<?php echo isset($rule['condition']) ? $rule['condition'] : ''; ?><?php endif; ?>">
        <select class="logic-rule-condition" data-name="notification_conditional_logic[index][condition][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][condition][]" <?php endif; ?>>
            <?php foreach ($logic_rules as $rule_key => $rule_name) : ?>
                <option <?php isset($rule['condition']) ? selected($rule['condition'], $rule_key) : ''; ?> value="<?php echo $rule_key; ?>" data-comparison-rules='<?php echo json_encode($comparison_values[$rule_key]); ?>'><?php echo $rule_name; ?></option>
            <?php endforeach; ?>

        </select>

        <select class="logic-rule-form-fields" data-name="notification_conditional_logic[index][form_field][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][form_field][]" <?php endif; ?>>
            <option value=""></option>
            <?php $fields = $form->get('fields'); ?>
            <?php foreach ($fields as $field) : ?>
                <?php
                if (in_array($field['type'], wpbs_get_excluded_fields(['hidden'])) || $field['type'] == 'payment_method') {
                    continue;
                }
                ?>
                <option <?php isset($rule['form_field']) ? selected($rule['form_field'], $form->get('id') . '-' . $field['id']) : ''; ?> value="<?php echo $form->get('id'); ?>-<?php echo $field['id']; ?>"><?php echo isset($field['values']['default']['label']) && $field['values']['default']['label'] ? $field['values']['default']['label'] : '(no label)' ?></option>
            <?php endforeach; ?>
        </select>

        <select class="logic-rule-comparison" data-name="notification_conditional_logic[index][comparison][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][comparison][]" <?php endif; ?>>
            <?php if ($rule) : ?>
                <?php foreach ($comparison_values[$rule['condition']] as $comparison_value_key => $comparison_value) : ?>
                    <option <?php selected($comparison_value_key, $rule['comparison']) ?> value="<?php echo $comparison_value_key; ?>"><?php echo $comparison_value ?></option>
                <?php endforeach; ?>
            <?php endif ?>
        </select>

        <input type="text" class="logic-rule-value logic-rule-value-default" value="<?php if (isset($rule['value'])) : ?><?php echo $rule['value']; ?><?php endif; ?>" data-name="notification_conditional_logic[index][value][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value][]" <?php endif; ?>>

        <select class="logic-rule-value logic-rule-value-weekdays" data-name="notification_conditional_logic[index][value-weekday][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-weekday][]" <?php endif; ?>>
            <option value="1" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 1) : ''; ?>><?php echo __('Monday', 'wp-booking-system'); ?></option>
            <option value="2" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 2) : ''; ?>><?php echo __('Tuesday', 'wp-booking-system'); ?></option>
            <option value="3" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 3) : ''; ?>><?php echo __('Wednesday', 'wp-booking-system'); ?></option>
            <option value="4" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 4) : ''; ?>><?php echo __('Thursday', 'wp-booking-system'); ?></option>
            <option value="5" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 5) : ''; ?>><?php echo __('Friday', 'wp-booking-system'); ?></option>
            <option value="6" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 6) : ''; ?>><?php echo __('Saturday', 'wp-booking-system'); ?></option>
            <option value="7" <?php isset($rule['value-weekday']) ? selected($rule['value-weekday'], 7) : ''; ?>><?php echo __('Sunday', 'wp-booking-system'); ?></option>
        </select>

        <select class="logic-rule-value logic-rule-value-payment_method" data-name="notification_conditional_logic[index][value-payment_method][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-payment_method][]" <?php endif; ?>>
            <?php $payment_methods = wpbs_get_payment_methods(); ?>
            <?php foreach (wpbs_get_active_payment_methods() as $payment_method_slug): ?>
                <option value="<?php echo $payment_method_slug; ?>" <?php isset($rule['value-payment_method']) ? selected($rule['value-payment_method'], $payment_method_slug) : ''; ?>><?php echo $payment_methods[$payment_method_slug] ?></option>
            <?php endforeach; ?>
        </select>

        <select class="logic-rule-value logic-rule-value-payment_status" data-name="notification_conditional_logic[index][value-payment_status][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-payment_status][]" <?php endif; ?>>
            <option value="not_paid" <?php isset($rule['value-payment_status']) ? selected($rule['value-payment_status'], 'not_paid') : ''; ?>><?php echo __('Not Paid', 'wp-booking-system'); ?></option>
            <option value="authorized" <?php isset($rule['value-payment_status']) ? selected($rule['value-payment_status'], 'authorized') : ''; ?>><?php echo __('Authorized', 'wp-booking-system'); ?></option>
            <option value="deposit_paid" <?php isset($rule['value-payment_status']) ? selected($rule['value-payment_status'], 'deposit_paid') : ''; ?>><?php echo __('Deposit Paid', 'wp-booking-system'); ?></option>
            <option value="paid" <?php isset($rule['value-payment_status']) ? selected($rule['value-payment_status'], 'paid') : ''; ?>><?php echo __('Paid', 'wp-booking-system'); ?></option>
            <option value="error" <?php isset($rule['value-payment_status']) ? selected($rule['value-payment_status'], 'error') : ''; ?>><?php echo __('Error', 'wp-booking-system'); ?></option>
        </select>

        <select class="logic-rule-value logic-rule-value-booking_type" data-name="notification_conditional_logic[index][value-booking_type][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-booking_type][]" <?php endif; ?>>
            <option value="customer" <?php isset($rule['value-booking_type']) ? selected($rule['value-booking_type'], 'customer') : ''; ?>><?php echo __('Customer Created Booking', 'wp-booking-system'); ?></option>
            <option value="manual" <?php isset($rule['value-booking_type']) ? selected($rule['value-booking_type'], 'manual') : ''; ?>><?php echo __('Manually Added Booking', 'wp-booking-system'); ?></option>
        </select>

        <?php $roles = get_editable_roles();  ?>

        <select class="logic-rule-user-roles" data-name="notification_conditional_logic[index][value-user-role][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-user-role][]" <?php endif; ?>>
            <option value=""></option>
            <?php foreach ($roles as $role_key => $role_data) : ?>
                <option <?php isset($rule['value-user-role']) ? selected($rule['value-user-role'], $role_key) : ''; ?> value="<?php echo $role_key ?>"><?php echo $role_data['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" class="logic-rule-value logic-rule-datepicker" value="<?php if (isset($rule['value-date'])) : ?><?php echo $rule['value-date']; ?><?php endif; ?>" data-name="notification_conditional_logic[index][value-date][]" <?php if ($group_index !== false) : ?>name="notification_conditional_logic[<?php echo $group_index; ?>][value-date][]" <?php endif; ?>>

        <button class="wpbs-secondary-button logic-rule-add-and"><?php echo __('and', 'wp-booking-system'); ?></button>

        <a href="#" class="wpbs-primary-button wpbs-remove-button wpbs-logic-rule-remove" title="Remove"><i class="wpbs-icon-trash"></i></a>
    </div>
<?php
}
