<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Adds a new tab to the Settings page of the plugin
 *
 * @param array $tabs
 *
 * @return $tabs
 *
 */
function wpbs_submenu_page_settings_tabs_tools($tabs)
{

	$tabs['tools'] = __('Tools', 'wp-booking-system');

	return $tabs;
}
add_filter('wpbs_submenu_page_settings_tabs', 'wpbs_submenu_page_settings_tabs_tools', 100);


/**
 * Adds the HTML for the Tools tab
 *
 */
function wpbs_submenu_page_settings_tab_tools()
{

	include 'views/view-tools.php';
}
add_action('wpbs_submenu_page_settings_tab_tools', 'wpbs_submenu_page_settings_tab_tools');

/**
 * Returns an array with the sub tabs for the Tools page
 *
 * @return array
 *
 */
function wpbs_get_tools_tabs()
{
	$tabs = array(
		'data-deletion' => __('Data Deletion', 'wp-booking-system'),
		'cron' => __('Emails & Cron', 'wp-booking-system'),
		'calendar' => __('Calendar Tools', 'wp-booking-system'),
	);

	/**
	 * Filter the tabs before returning
	 *
	 * @param array $tabs
	 *
	 */
	return apply_filters('wpbs_submenu_page_settings_tools_tabs', $tabs);
}

/**
 * Action that uninstalls the plugin
 *
 */
function wpbs_action_uninstall_plugin()
{

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_uninstall_plugin'))
		return;

	/**
	 * Drop db tables
	 *
	 */
	global $wpdb;

	$registered_tables = wp_booking_system()->db;

	foreach ($registered_tables as $table)
		$wpdb->query("DROP TABLE IF EXISTS {$table->table_name}");

	/**
	 * Remove options
	 *
	 */
	delete_option('wpbs_version');
	delete_option('wpbs_first_activation');
	delete_option('wpbs_upgrade_5_0_0');
	delete_option('wpbs_upgrade_5_0_0_skipped');
	delete_option('wpbs_serial_key');
	delete_option('wpbs_registered_website_id');
	delete_option('wpbs_settings');


	/**
	 * Deactivate the plugin and redirect to Plugins
	 *
	 */
	deactivate_plugins(WPBS_BASENAME);

	wp_redirect(admin_url('plugins.php'));
	exit;
}
add_action('wpbs_action_uninstall_plugin', 'wpbs_action_uninstall_plugin');

/**
 * Action that wipes all booking data
 *
 */
function wpbs_action_wipe_booking_data()
{

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_wipe_booking_data'))
		return;

	/**
	 * Truncate db tables
	 *
	 */
	global $wpdb;

	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_bookings");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_booking_meta");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_payments");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_notifications");

	// Remove existing cron jobs
	$crons = _get_cron_array();

	foreach ($crons as $timestamp => $cron) {

		if (isset($cron['wpbs_sms_reminder_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_reminder_notification']);
		}
		if (isset($cron['wpbs_sms_follow_up_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_follow_up_notification']);
		}
		if (isset($cron['wpbs_sms_payment_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_payment_notification']);
		}
		if (isset($cron['wpbs_refund_security_deposit_cron'])) {
			unset($crons[$timestamp]['wpbs_refund_security_deposit_cron']);
		}
		if (isset($cron['wpbs_send_scheduled_email_notification'])) {
			unset($crons[$timestamp]['wpbs_send_scheduled_email_notification']);
		}
	}

	foreach ($crons as $timestamp => $cron) {
		if (!$cron) {
			unset($crons[$timestamp]);
		}
	}

	_set_cron_array($crons);

	wp_redirect(add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'tools', 'wpbs_message' => 'booking_wipe_successful'), admin_url('admin.php')));
	exit;
}
add_action('wpbs_action_wipe_booking_data', 'wpbs_action_wipe_booking_data');


/**
 * Action that regenerates cron jobs
 *
 */
function wpbs_action_regenerate_scheduled_email_cron_jobs()
{

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_regenerate_scheduled_email_cron_jobs')) {
		return;
	}

	wpbs_regenerate_scheduled_email_cron_jobs();

	// Redirect to the current page
	wp_redirect(add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'tools', 'wpbs_message' => 'payment_reminder_cron_regenerate_success'), admin_url('admin.php')));
	exit;
}
add_action('wpbs_action_regenerate_scheduled_email_cron_jobs', 'wpbs_action_regenerate_scheduled_email_cron_jobs');

/**
 * Function that regenerated cron jobs
 * 
 */
function wpbs_regenerate_scheduled_email_cron_jobs()
{
	// Delete crons
	$crons = _get_cron_array();

	foreach ($crons as $timestamp => $cron) {
		// Delete old email crons
		if (isset($cron['wpbs_part_payments_payment_reminder_email'])) {
			unset($crons[$timestamp]['wpbs_part_payments_payment_reminder_email']);
		}
		if (isset($cron['wpbs_er_reminder_email'])) {
			unset($crons[$timestamp]['wpbs_er_reminder_email']);
		}
		if (isset($cron['wpbs_er_follow_up_email'])) {
			unset($crons[$timestamp]['wpbs_er_follow_up_email']);
		}

		// Delete crons
		if (isset($cron['wpbs_send_scheduled_email_notification'])) {
			unset($crons[$timestamp]['wpbs_send_scheduled_email_notification']);
		}
	}

	foreach ($crons as $timestamp => $cron) {
		if (!$cron) {
			unset($crons[$timestamp]);
		}
	}

	_set_cron_array($crons);

	$bookings = wpbs_get_bookings();

	foreach ($bookings as $booking) {
		$emails = new WPBS_Email_Notifications($booking->get('id'));
		$emails->send_past_emails = false;
		$emails->schedule_emails();
	}
}

/**
 * Copy prices from one calendar to the other
 *
 */
function wpbs_action_copy_calendar_prices()
{

	// Let only admins do this procedure
	if (! current_user_can('manage_options'))
		return;

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_copy_calendar_prices'))
		return;

	if (!isset($_GET['source_calendar'])) {
		return;
	}

	$source_calendar = absint($_GET['source_calendar']);

	if (empty($source_calendar)) {
		return;
	}

	if (!isset($_GET['destination_calendar'])) {
		return;
	}

	$destination_calendar = absint($_GET['destination_calendar']);

	if (empty($destination_calendar)) {
		return;
	}

	// Remove event prices from Destination Calendar
	$destination_events = wpbs_get_events(['calendar_id' => $destination_calendar]);
	foreach ($destination_events as $destination_event) {
		$event_data = array(
			'calendar_id' => $destination_calendar,
			'price' => null,
			'date_year' => $destination_event->get('date_year'),
			'date_month' => $destination_event->get('date_month'),
			'date_day' => $destination_event->get('date_day'),
		);
		wpbs_update_event($destination_event->get('id'), $event_data);
	}

	// Copy prices
	$source_events = wpbs_get_events(['calendar_id' => $source_calendar]);
	foreach ($source_events as $source_event) {
		$event_data = array(
			'calendar_id' => $destination_calendar,
			'price' => $source_event->get('price'),
			'date_year' => $source_event->get('date_year'),
			'date_month' => $source_event->get('date_month'),
			'date_day' => $source_event->get('date_day'),
		);

		// Insert the events in the database
		$event = wpbs_get_events(array('calendar_id' => $destination_calendar, 'date_day' => $event_data['date_day'], 'date_month' => $event_data['date_month'], 'date_year' => $event_data['date_year']));

		if (!empty($event[0])) {
			wpbs_update_event($event[0]->get('id'), $event_data);
		} else {
			wpbs_insert_event($event_data);
		}
	}

	// Update Default Price
	wpbs_update_calendar_meta($destination_calendar, 'default_price', wpbs_get_calendar_meta($source_calendar, 'default_price', true));

	// Redirect to the current page
	wp_redirect(add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'tools', 'wpbs_message' => 'copy_prices_success'), admin_url('admin.php')));
	exit;
}
add_action('wpbs_action_copy_calendar_prices', 'wpbs_action_copy_calendar_prices');


/**
 * Copy prices from one calendar to the other
 *
 */
function wpbs_action_copy_calendar_year_prices()
{

	// Let only admins do this procedure
	if (! current_user_can('manage_options'))
		return;

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_copy_calendar_year_prices'))
		return;

	if (!isset($_GET['calendar'])) {
		return;
	}

	$calendar = absint($_GET['calendar']);

	if (empty($calendar)) {
		return;
	}

	if (!isset($_GET['source_year'])) {
		return;
	}

	$source_year = absint($_GET['source_year']);

	if (empty($source_year)) {
		return;
	}

	if (!isset($_GET['destination_year'])) {
		return;
	}

	$destination_year = absint($_GET['destination_year']);

	if (empty($destination_year)) {
		return;
	}

	$price_increase = isset($_GET['price_increase']) && (!empty($_GET['price_increase']) || $_GET['price_increase'] === '0') ? absint($_GET['price_increase']) : false;

	// Get current year prices
	$source_prices = [];
	$events = wpbs_get_events(['calendar_id' => $calendar, 'date_year' => $source_year]);
	foreach ($events as $event) {
		if ($event->get('price')) {
			$source_prices[$event->get('date_year') . str_pad($event->get('date_month'), 2, '0', STR_PAD_LEFT) . str_pad($event->get('date_day'), 2, '0', STR_PAD_LEFT)] = $event->get('price');
		}
	}



	if ($price_increase === false) {
		// If there is no price increase, just copy over existing prices and ignore the default price.
		foreach ($source_prices as $event_date => $price) {

			$date = DateTime::createFromFormat('Ymd', $event_date);

			$event_data = array(
				'calendar_id' => $calendar,
				'price' => $price,
				'date_year' => $destination_year,
				'date_month' => $date->format('m'),
				'date_day' => $date->format('d'),
			);

			// Insert the events in the database
			$event = wpbs_get_events(array('calendar_id' => $calendar, 'date_day' => $event_data['date_day'], 'date_month' => $event_data['date_month'], 'date_year' => $event_data['date_year']));

			if (!empty($event[0])) {
				wpbs_update_event($event[0]->get('id'), $event_data);
			} else {
				wpbs_insert_event($event_data);
			}
		}
	} else {
		// If there is a price increase, overwrite all prices, including increasing the default price for empty dates.
		$year_start = DateTime::createFromFormat('Ymd H:i:s', $destination_year . '0101 00:00:00');
		$year_end = DateTime::createFromFormat('Ymd H:i:s', $destination_year . '1231 00:00:00');

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($year_start, $interval, $year_end);

		$increased_default_price = round(wpbs_get_calendar_meta($calendar, 'default_price', true) * (1 + ($price_increase / 100)));

		// Loop through dates
		foreach ($period as $date) {

			$source_date = $source_year . $date->format('m') .  $date->format('d');

			if (isset($source_prices[$source_date])) {
				$price = round($source_prices[$source_date] * (1 + ($price_increase / 100)));
			} else {
				$price = $increased_default_price;
			}

			$event_data = array(
				'calendar_id' => $calendar,
				'price' => $price,
				'date_year' => $date->format('Y'),
				'date_month' => $date->format('m'),
				'date_day' => $date->format('d'),
			);

			// Insert the events in the database
			$event = wpbs_get_events(array('calendar_id' => $calendar, 'date_day' => $event_data['date_day'], 'date_month' => $event_data['date_month'], 'date_year' => $event_data['date_year']));

			if (!empty($event[0])) {
				wpbs_update_event($event[0]->get('id'), $event_data);
			} else {
				wpbs_insert_event($event_data);
			}
		}
	}

	// Redirect to the current page
	wp_redirect(add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'tools', 'wpbs_message' => 'copy_prices_success'), admin_url('admin.php')));
	exit;
}
add_action('wpbs_action_copy_calendar_year_prices', 'wpbs_action_copy_calendar_year_prices');
