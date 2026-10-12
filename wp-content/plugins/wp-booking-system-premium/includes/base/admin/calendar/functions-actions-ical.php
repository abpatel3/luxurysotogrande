<?php

use Kigkonsult\Icalcreator\Vcalendar;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates the iCal .ics file for a given calendar
 *
 */
function wpbs_action_generate_ical_file()
{

    if (empty($_GET['wp-booking-system-ical']) && empty($_GET['wpbs-ical'])) {
        return;
    }

    $ical_hash = (!empty($_GET['wpbs-ical']) ? sanitize_text_field($_GET['wpbs-ical']) : (!empty($_GET['wp-booking-system-ical']) ? sanitize_text_field($_GET['wp-booking-system-ical']) : ''));

    // Remove .ics, just in case
    $ical_hash = str_replace('.ics', '', $ical_hash);

    // Get the calendar associated with the hash
    $calendars = wpbs_get_calendars(array('ical_hash' => $ical_hash));
    $calendar = (!empty($calendars) ? $calendars[0] : null);

    if (is_null($calendar)) {
        echo __('Invalid calendar ID', 'wp-booking-system');
        die();
    }

    // Include iCal library
    if (!file_exists(WPBS_PLUGIN_DIR . 'includes/libs/iCal/vendor/autoload.php')) {
        echo __('iCalendar library missing', 'wp-booking-system');
        die();
    }

    include_once WPBS_PLUGIN_DIR . 'includes/libs/iCal/vendor/autoload.php';

    /**
     * Prepare iCal file
     *
     */

    // Get timezone
    $tzid = get_option('timezone_string');

    if (empty($tzid)) {
        $date = date_create('now');
        $tz = date_timezone_get($date);
        $tzid = timezone_name_get($tz);
    }

    $vargs = array(
        Vcalendar::UNIQUE_ID => sprintf('WP Booking System - Calendar ID: %d', $calendar->get('id')),
    );

    // Create a new calendar instance
    $v = new Vcalendar($vargs);

    $v->setMethod(Vcalendar::PUBLISH);

    if (apply_filters('wpbs_ical_export_remove_calname', false) == false) {
        $v->setXprop(Vcalendar::X_WR_CALNAME, $calendar->get_name() . ' - WP Booking System');
        $v->setXprop(Vcalendar::X_WR_CALDESC, 'ICS File generated with WP Booking System');
    }

    $v->setXprop(Vcalendar::X_WR_TIMEZONE, $tzid);

    // Get all events
    $events = wpbs_get_events(array('calendar_id' => $calendar->get('id'), 'orderby' => 'date_year, date_month, date_day', 'order' => 'ASC'));

    // Get iCalendar events
    $include_icalendar_events = apply_filters('wpbs_export_calendar_include_icalendar_events', false, $calendar);

    if ($include_icalendar_events == true) {
        $events = array_merge($events, wpbs_get_ical_feeds_as_events($calendar->get('id'), $events));
    }

    // If no events found, return the calendar
    if (empty($events)) {
        $v->returnCalendar();
        die();
    }

    // Get legend item ids that should be exported as booked
    $legend_item_ids = apply_filters('wpbs_ical_export_legend_items', wpbs_get_calendar_meta($calendar->get('id'), 'ical_export_legend_items', true));

    // If no legend item is marked as being booked, return the calendar
    if (empty($legend_item_ids)) {
        $v->returnCalendar();
        die();
    }

    // Export type
    if (wpbs_get_calendar_meta($calendar->get('id'), 'group_events_by_description', true) == 1) {

        /*
         * Grouped export with overlap support.
         *
         * Descriptions separated by "//" are treated as separate bookings.
         */

        $events_by_date = array();

        // Collect valid booked events by date.
        foreach ($events as $event) {

            $year = (int) $event->get('date_year');
            $month = (int) $event->get('date_month');
            $day = (int) $event->get('date_day');

            if (!checkdate($month, $day, $year)) {
                continue;
            }

            if (!in_array($event->get('legend_item_id'), $legend_item_ids)) {
                continue;
            }

            $event_ymd = $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);

            $skip_past_events = apply_filters('wpbs_ical_export_skip_past_events', false, $event);

            if ($skip_past_events && $event_ymd < wp_date('Ymd')) {
                continue;
            }

            // Do not include events older than 1 year.
            if ($event_ymd < wp_date('Ymd', strtotime('1 year ago'))) {
                continue;
            }

            $skip_events = apply_filters('wpbs_ical_export_skip_events', false, $event, $event_ymd, $event_ymd);

            if ($skip_events) {
                continue;
            }

            $events_by_date[$event_ymd] = $event;
        }

        ksort($events_by_date);

        // Split "Aaa // Bbb" into array("Aaa", "Bbb").
        $get_description_parts = function ($description) {

            $parts = preg_split('/\s*\/\/\s*/', (string) $description);

            $parts = array_map('trim', $parts);

            $parts = array_filter($parts, function ($part) {
                return $part !== '';
            });

            $parts = array_values(array_unique($parts));

            // Preserve old behavior for empty descriptions.
            if (empty($parts)) {
                $parts = array('');
            }

            return $parts;
        };

        // Check if two Ymd dates are consecutive.
        $dates_are_consecutive = function ($previous_ymd, $current_ymd) {

            $previous_date = DateTime::createFromFormat('!Ymd', (string) $previous_ymd);

            if ($previous_date === false) {
                return false;
            }

            return $previous_date->modify('+1 day')->format('Ymd') === (string) $current_ymd;
        };

        $open_events = array();
        $groupped_events = array();

        /*
         * Walk through the calendar day by day.
         *
         * An open event is extended only if the same description exists on the next day.
         * If the same description appears later after a gap, it starts a new iCal event.
         */
        foreach ($events_by_date as $event_ymd => $event) {

            $description_parts = $get_description_parts($event->get('description'));

            $today_descriptions = array();

            foreach ($description_parts as $description_part) {
                $description_key = md5($description_part);
                $today_descriptions[$description_key] = $description_part;
            }

            // Close open events that are either missing today or not consecutive.
            foreach ($open_events as $description_key => $open_event) {

                if (
                    !isset($today_descriptions[$description_key]) ||
                    !$dates_are_consecutive($open_event['end_ymd'], $event_ymd)
                ) {
                    $groupped_events[] = $open_event;
                    unset($open_events[$description_key]);
                }
            }

            // Start or extend today's events.
            foreach ($today_descriptions as $description_key => $description) {

                if (isset($open_events[$description_key])) {

                    // Same description on the next consecutive day, so extend the event.
                    $open_events[$description_key]['end'] = $event;
                    $open_events[$description_key]['end_ymd'] = $event_ymd;
                } else {

                    // New description, or same description after a gap.
                    $open_events[$description_key] = array(
                        'description' => $description,
                        'start'       => $event,
                        'end'         => $event,
                        'start_ymd'   => $event_ymd,
                        'end_ymd'     => $event_ymd,
                    );
                }
            }
        }

        // Close any events still open after the final calendar day.
        foreach ($open_events as $open_event) {
            $groupped_events[] = $open_event;
        }

        /*
         * Merge split descriptions that have exactly the same date range.
         *
         * This prevents fully overlapping descriptions from being exported twice.
         */
        $merged_events = array();

        foreach ($groupped_events as $groupped_event) {

            $range_key = $groupped_event['start_ymd'] . '|' . $groupped_event['end_ymd'];

            if (!isset($merged_events[$range_key])) {

                $merged_events[$range_key] = $groupped_event;
                $merged_events[$range_key]['descriptions'] = array();
            }

            if (!in_array($groupped_event['description'], $merged_events[$range_key]['descriptions'], true)) {
                $merged_events[$range_key]['descriptions'][] = $groupped_event['description'];
            }
        }

        $groupped_events = array();

        foreach ($merged_events as $merged_event) {

            $merged_event['description'] = implode(' // ', $merged_event['descriptions']);

            unset($merged_event['descriptions']);

            $groupped_events[] = $merged_event;
        }

        // Sort final grouped events by start date.
        usort($groupped_events, function ($a, $b) {

            if ($a['start_ymd'] === $b['start_ymd']) {
                return strcmp((string) $a['description'], (string) $b['description']);
            }

            return strcmp($a['start_ymd'], $b['start_ymd']);
        });

        // Build the iCalendar events.
        foreach ($groupped_events as $event) {

            $start_year  = (int) $event['start']->get('date_year');
            $start_month = (int) $event['start']->get('date_month');
            $start_day   = (int) $event['start']->get('date_day');

            $end_year  = (int) $event['end']->get('date_year');
            $end_month = (int) $event['end']->get('date_month');
            $end_day   = (int) $event['end']->get('date_day');

            $description = $event['description'];

            // Set event start and end date.
            // DTEND is exclusive, so add 1 day to the final booked date.
            $vevent_start = date('Ymd', mktime(0, 0, 0, $start_month, $start_day, $start_year));
            $vevent_end   = date('Ymd', mktime(0, 0, 0, $end_month, $end_day + 1, $end_year));

            // Create new vevent component.
            $vevent = $v->newVevent();

            $vevent->setDtstart($vevent_start, array(Vcalendar::VALUE => Vcalendar::DATE));
            $vevent->setDtend($vevent_end, array(Vcalendar::VALUE => Vcalendar::DATE));

            /*
             * Include the final merged description in the UID so overlapping bookings
             * with the same dates do not collide incorrectly.
             */
            $uid_source = $calendar->get('id') . $description . $event['start_ymd'] . $event['end_ymd'];

            $vevent->setUid('WPBS-' . base64_encode($uid_source));
            $vevent->setLocation((string) $calendar->get_name());
            $vevent->setDescription($description);
            $vevent->setSummary($description);
            $vevent->setClass(Vcalendar::P_BLIC);
            $vevent->setStatus(Vcalendar::CONFIRMED);
            $vevent->setComment('');
            $vevent->setOrganizer('');
            $vevent->setSequence(0);

            $vevent = apply_filters('wpbs_generate_ical_file_vevent', $vevent, $event, $calendar);
        }
    } else {

        // Set vevents
        foreach ($events as $event) {

            $year  = (int) $event->get('date_year');
            $month = (int) $event->get('date_month');
            $day   = (int) $event->get('date_day');

            if (!checkdate($month, $day, $year)) {
                continue;
            }

            if (!in_array($event->get('legend_item_id'), $legend_item_ids)) {
                continue;
            }

            // Set event start and end date
            $vevent_start = date('Ymd', mktime(0, 0, 0, $month, $day, $year));
            $vevent_end   = date('Ymd', mktime(0, 0, 0, $month, $day + 1, $year));

            $skip_past_events = apply_filters('wpbs_ical_export_skip_past_events', false, $event);

            if ($skip_past_events && $vevent_start < wp_date('Ymd')) {
                continue;
            }

            // Do not include events older than 1 year.
            if ($vevent_start < wp_date('Ymd', strtotime('1 year ago'))) {
                continue;
            }

            $skip_events = apply_filters('wpbs_ical_export_skip_events', false, $event, $vevent_start, $vevent_end);

            if ($skip_events) {
                continue;
            }

            // Create new vevent component
            $vevent = $v->newVevent();

            $vevent->setDtstart($vevent_start, array(Vcalendar::VALUE => Vcalendar::DATE));
            $vevent->setDtend($vevent_end, array(Vcalendar::VALUE => Vcalendar::DATE));

            $id = $event->get('id') ?: $event->get('description');

            $vevent->setUid('WPBS-' . base64_encode($calendar->get('id') . $id . $vevent_start . $vevent_end));
            $vevent->setLocation((string) $calendar->get_name());
            $vevent->setDescription($event->get('description'));
            $vevent->setSummary($event->get('description'));
            $vevent->setClass(Vcalendar::P_BLIC);
            $vevent->setStatus(Vcalendar::CONFIRMED);
            $vevent->setComment('');
            $vevent->setOrganizer('');
            $vevent->setSequence(0);

            $vevent = apply_filters('wpbs_generate_ical_file_vevent', $vevent, $event, $calendar);
        }
    }

    $v = apply_filters('wpbs_generate_ical_file_after', $v, $calendar);

    // Return the calendar
    $v->returnCalendar();
    die();
}
add_action('init', 'wpbs_action_generate_ical_file');

/**
 * Validates and handles the resetting of the Calendar's ical_hash
 *
 */
function wpbs_action_reset_private_link()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_reset_private_link')) {
        return;
    }

    // Verify for the calendar id
    if (empty($_GET['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_calendar_id_missing', '<p>' . __('Something went wrong. Could not reset the private link.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_calendar_id_missing');

        return;
    }

    $calendar_id = absint($_GET['calendar_id']);

    // Prepare calendar data to be updated
    $calendar_data = array(
        'ical_hash' => wpbs_generate_hash(),
    );

    // Update the calendar
    $updated = wpbs_update_calendar($calendar_id, $calendar_data);

    // If the calendar could not be inserted show a message to the user
    if (!$updated) {

        wpbs_admin_notices()->register_notice('calendar_update_false', '<p>' . __('Something went wrong. Could not reset the private link.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('calendar_update_false');

        return;
    }

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_reset_private_link_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_reset_private_link', 'wpbs_action_reset_private_link');

/**
 * Validates and handles the saving of the Calendar Export preferences
 *
 */
function wpbs_action_ical_export_save_preferences()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_ical_export_save_preferences')) {
        return;
    }

    // Verify for the calendar id
    if (empty($_POST['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_calendar_id_missing', '<p>' . __('Something went wrong. Could not save the Export preferences.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_calendar_id_missing');

        return;
    }

    $calendar_id = absint($_POST['calendar_id']);

    // Firstly delete the saved Legend Items that should be exported
    wpbs_delete_calendar_meta($calendar_id, 'ical_export_legend_items');

    // Add the Legend Items to the calendar meta
    if (!empty($_POST['ical_export_legend_items'])) {

        wpbs_add_calendar_meta($calendar_id, 'ical_export_legend_items', _wpbs_array_sanitize_text_field($_POST['ical_export_legend_items']), true);
    }

    // Update group events option
    if (isset($_POST['group_events_by_description']) && !empty($_POST['group_events_by_description'])) {
        wpbs_add_calendar_meta($calendar_id, 'group_events_by_description', sanitize_text_field($_POST['group_events_by_description']), true);
    } else {
        wpbs_delete_calendar_meta($calendar_id, 'group_events_by_description');
    }

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_save_preferences_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_ical_export_save_preferences', 'wpbs_action_ical_export_save_preferences');

/**
 * Validates and handles the importing from an iCal file
 *
 */
function wpbs_action_ical_import_file()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_ical_import_file')) {
        return;
    }

    // Verify for file
    if (empty($_FILES['ical_file_import_file'])) {

        wpbs_admin_notices()->register_notice('ical_file_import_file_missing', '<p>' . __('Please select an iCal file.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_file_missing');

        return;
    }

    // Verify for file format
    if (strpos($_FILES['ical_file_import_file']['name'], '.ics') === false) {

        wpbs_admin_notices()->register_notice('ical_file_import_format_invalid', '<p>' . __("Please make sure the selected file's format is .ics.", 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_format_invalid');

        return;
    }

    // Verify for legend item
    if (empty($_POST['ical_file_import_legend_item_default'])) {

        wpbs_admin_notices()->register_notice('ical_file_import_legend_item_missing', '<p>' . __('Please select the legend item for the iCal file import.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_legend_item_missing');

        return;
    }

    // Verify for overwrite existing
    if (empty($_POST['ical_import_file_overwrite'])) {

        wpbs_admin_notices()->register_notice('ical_file_import_overwrite_missing', '<p>' . __('Please select whether or not to overwrite data for dates that already contain information.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_overwrite_missing');

        return;
    }

    // Verify for the calendar id
    if (empty($_POST['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_file_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not import the iCal file.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_calendar_id_missing');

        return;
    }

    // Get the calendar
    $calendar_id = absint($_POST['calendar_id']);
    $calendar = wpbs_get_calendar($calendar_id);

    // Verify for calendar existance
    if (is_null($calendar)) {

        wpbs_admin_notices()->register_notice('ical_file_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not import the iCal file.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_calendar_id_missing');

        return;
    }

    // Include iCal Reader library
    if (!file_exists(WPBS_PLUGIN_DIR . 'includes/libs/iCalReader/ICal.php')) {
        echo __('iCalendarReader library missing', 'wp-booking-system');
        die();
    }

    include_once WPBS_PLUGIN_DIR . 'includes/libs/iCalReader/Event.php';
    include_once WPBS_PLUGIN_DIR . 'includes/libs/iCalReader/ICal.php';

    // Extract file contents
    $file_contents = file_get_contents($_FILES['ical_file_import_file']['tmp_name']);

    // Extract the file in an array format
    $ical_reader = new WPBS\ICal\ICal();
    $ical_arr = $ical_reader->initString($file_contents);

    if (!isset($ical_arr->cal)) {
        wpbs_admin_notices()->register_notice('ical_file_import_no_events', '<p>' . __('The iCal file has no events to import.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_no_events');

        return;
    }

    $ical_arr = $ical_arr->cal;

    // If no events are present return
    if (empty($ical_arr['VEVENT'])) {

        wpbs_admin_notices()->register_notice('ical_file_import_no_events', '<p>' . __('The iCal file has no events to import.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_file_import_no_events');

        return;
    }

    /**
     * If the skip value was set, remove the events that overlap any data information from the calendar
     *
     */
    if ($_POST['ical_import_file_overwrite'] == 'skip') {

        $events = wpbs_get_events(array('calendar_id' => $calendar_id));

        foreach ($ical_arr['VEVENT'] as $key => $ical_event) {

            foreach ($events as $event) {

                $event_time = strtotime($event->get('date_year') . '-' . $event->get('date_month') . '-' . $event->get('date_day'));

                if ($event_time >= strtotime($ical_event['DTSTART']) && $event_time < strtotime($ical_event['DTEND'])) {
                    unset($ical_arr['VEVENT'][$key]);
                }
            }
        }

        $ical_arr['VEVENT'] = array_values($ical_arr['VEVENT']);
    }

    /**
     * Go through each iCal event and set the
     *
     */
    foreach ($ical_arr['VEVENT'] as $ical_event) {

        $begin = new DateTime($ical_event['DTSTART']);
        if (!isset($ical_event['DTEND'])) {
            $end = clone $begin;
        } else {
            $end = new DateTime($ical_event['DTEND']);
        }

        $end->modify('-1 day');

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {

            $event_data = array(
                'calendar_id' => $calendar_id,
                'legend_item_id' => absint($_POST['ical_file_import_legend_item_default']),
                'date_year' => $i->format('Y'),
                'date_month' => $i->format('m'),
                'date_day' => $i->format('d'),
            );

            // Add description and tooltip only if all conditions are met
            if (!empty($ical_event['SUMMARY']) && in_array($_POST['ical_import_file_description'], array('only_description', 'both'))) {
                $event_data['description'] = wp_kses_post($ical_event['SUMMARY']);
            }

            if (!empty($ical_event['SUMMARY']) && in_array($_POST['ical_import_file_description'], array('only_tooltip', 'both'))) {
                $event_data['tooltip'] = wp_kses_post($ical_event['SUMMARY']);
            }

            // Check if there's already an event present for the date
            $events = wpbs_get_events(array('calendar_id' => $calendar_id, 'date_year' => $i->format('Y'), 'date_month' => $i->format('m'), 'date_day' => $i->format('d')));
            $event = (!empty($events) ? $events[0] : null);

            if (is_null($event)) {
                wpbs_insert_event($event_data);
            } else {
                wpbs_update_event($event->get('id'), $event_data);
            }
        }
    }

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_import_file_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_ical_import_file', 'wpbs_action_ical_import_file');

/**
 * Validates and handles the adding of a new iCal URL
 *
 */
function wpbs_action_ical_import_url()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_ical_import_url')) {
        return;
    }

    // Verify for url
    if (empty($_POST['ical_url_import_name'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_name_missing', '<p>' . __('Please provide a name for your iCal calendar.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_name_missing');

        return;
    }

    // Verify for legend item
    if (empty($_POST['ical_url_import_legend_item_default'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_legend_item_missing', '<p>' . __('Please select the legend item for the iCal URL import.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_legend_item_missing');

        return;
    }

    // Verify for url existing
    if (empty($_POST['ical_url_import_url'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_url_missing', '<p>' . __('Please add the iCal feed URL.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_url_missing');

        return;
    }

    // Verify if Split Day is used and split legends are selected
    if (isset($_POST['ical_url_import_split_days'])) {

        // Verify split start legend
        if (empty($_POST['ical_url_import_legend_item_split_day_start'])) {

            wpbs_admin_notices()->register_notice('ical_url_import_legend_item_split_start_missing', '<p>' . __('Please select the legend item for starting Split Day.', 'wp-booking-system') . '</p>', 'error');
            wpbs_admin_notices()->display_notice('ical_url_import_legend_item_split_start_missing');

            return;
        }

        // Verify split end legend
        if (empty($_POST['ical_url_import_legend_item_split_day_end'])) {

            wpbs_admin_notices()->register_notice('ical_url_import_legend_item_split_end_missing', '<p>' . __('Please select the legend item for the ending Split Day.', 'wp-booking-system') . '</p>', 'error');
            wpbs_admin_notices()->display_notice('ical_url_import_legend_item_split_end_missing');

            return;
        }
    }

    // In case it's a webcal:// url, change the protocol to https.
    $_POST['ical_url_import_url'] = str_replace('webcal://', 'https://', $_POST['ical_url_import_url']);

    // Verify if the import url is an actual url
    if (filter_var($_POST['ical_url_import_url'], FILTER_VALIDATE_URL) === false) {

        wpbs_admin_notices()->register_notice('ical_url_import_url_false', '<p>' . __('The iCal feed URL is not valid.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_url_false');

        return;
    }

    // Verify if the contents of the calendar validates as an iCal
    $ical_contents = wp_remote_get($_POST['ical_url_import_url'], array('timeout' => 30, 'user-agent' => 'Mozilla/5.0 ( Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0'));

    if (wp_remote_retrieve_response_code($ical_contents) != 200) {

        wpbs_admin_notices()->register_notice('ical_url_import_url_false', '<p>' . __('The iCal feed URL is not valid or could not be opened.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_url_false');

        return;
    }

    $ical_contents = wp_remote_retrieve_body($ical_contents);

    $ical_contents = trim($ical_contents);

    if (0 !== strpos($ical_contents, 'BEGIN:VCALENDAR') || false === strpos($ical_contents, 'END:VCALENDAR')) {

        wpbs_admin_notices()->register_notice('ical_url_import_url_false', '<p>' . __('The iCal feed URL is not valid or could not be opened.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_url_false');

        return;
    }

    // Verify for the calendar id
    if (empty($_POST['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not import the iCal URL.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    // Get the calendar
    $calendar_id = absint($_POST['calendar_id']);
    $calendar = wpbs_get_calendar($calendar_id);

    // Verify for calendar existance
    if (is_null($calendar)) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not import the iCal file.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    $ical_feed_id = wpbs_get_ical_feeds_last_id($calendar_id) + 1;

    // Build the new iCal item
    $ical_feed = array(
        'id' => $ical_feed_id,
        'name' => sanitize_text_field($_POST['ical_url_import_name']),
        'url' => trim($_POST['ical_url_import_url']),
        'file_contents' => $ical_contents,
        'legend_item_id' => absint($_POST['ical_url_import_legend_item_default']),
        'split_days' => (isset($_POST['ical_url_import_split_days'])) ? 1 : 0,
        'legend_item_id_split_start' => (isset($_POST['ical_url_import_split_days'])) ? absint($_POST['ical_url_import_legend_item_split_day_start']) : 0,
        'legend_item_id_split_end' => (isset($_POST['ical_url_import_split_days'])) ? absint($_POST['ical_url_import_legend_item_split_day_end']) : 0,
        'last_updated' => current_time('Y-m-d H:i:s'),
    );

    wpbs_add_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed_id, $ical_feed);

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_import_url_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_ical_import_url', 'wpbs_action_ical_import_url');

/**
 * Validates and handles the removal new iCal URL
 *
 */
function wpbs_action_remove_ical_feed()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_remove_ical_feed')) {
        return;
    }

    // Verify for feed id
    if (empty($_GET['ical_feed_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_feed_id_missing', '<p>' . __('Something went wrong. Could not remove the iCal URL.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_feed_id_missing');

        return;
    }

    // Verify for the calendar id
    if (empty($_GET['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not remove the iCal URL.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    // Set ical_feed id
    $ical_feed_id = absint($_GET['ical_feed_id']);

    // Get the calendar
    $calendar_id = absint($_GET['calendar_id']);
    $calendar = wpbs_get_calendar($calendar_id);

    // Verify for calendar existance
    if (is_null($calendar)) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not remove the iCal URL.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    // Remove all ical_feeds
    wpbs_delete_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed_id);

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_import_url_remove_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_remove_ical_feed', 'wpbs_action_remove_ical_feed');

/**
 * Validates and handles the enabling or disabling of an iCal URL
 *
 */
function wpbs_action_toggle_ical_feed()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_toggle_ical_feed')) {
        return;
    }

    // Verify for feed id
    if (empty($_GET['ical_feed_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_feed_id_missing', '<p>' . __('Something went wrong. Could not modify the iCal URL.', 'wp-simple-booking-calendar') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_feed_id_missing');

        return;
    }

    // Verify for the calendar id
    if (empty($_GET['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not modify the iCal URL.', 'wp-simple-booking-calendar') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    // Set ical_feed id
    $ical_feed_id = absint($_GET['ical_feed_id']);

    // Get the calendar
    $calendar_id = absint($_GET['calendar_id']);
    $calendar = wpbs_get_calendar($calendar_id);

    // Verify for calendar existance
    if (is_null($calendar)) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not modify the iCal URL.', 'wp-simple-booking-calendar') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    $ical_feed = wpbs_get_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed_id, true);


    if (isset($ical_feed['disabled']) && $ical_feed['disabled'] == true) {
        $ical_feed['disabled'] = false;
    } else {
        $ical_feed['disabled'] = true;
    }

    wpbs_update_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed_id, $ical_feed);

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_toggle_ical_feed', 'wpbs_action_toggle_ical_feed');

/**
 * Refreshes the ical feeds calendar contents
 *
 */
function wpbs_action_refresh_ical_feeds()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_refresh_ical_feeds')) {
        return;
    }

    // Verify for the calendar id
    if (empty($_GET['calendar_id'])) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not refresh the iCal feeds.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    // Get the calendar
    $calendar_id = absint($_GET['calendar_id']);
    $calendar = wpbs_get_calendar($calendar_id);

    // Verify for calendar existance
    if (is_null($calendar)) {

        wpbs_admin_notices()->register_notice('ical_url_import_calendar_id_missing', '<p>' . __('Something went wrong. Could not refresh the iCal feeds.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('ical_url_import_calendar_id_missing');

        return;
    }

    $ical_feeds = wpbs_get_calendar_meta_ical_feeds($calendar_id);

    foreach ($ical_feeds as $ical_feed) {

        if (empty($ical_feed['id'])) {
            continue;
        }

        if (empty($ical_feed['url'])) {
            continue;
        }

        $ical_contents = wp_remote_get($ical_feed['url'], array('timeout' => 30, 'user-agent' => 'Mozilla/5.0 ( Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0'));

        if (wp_remote_retrieve_response_code($ical_contents) != 200) {
            continue;
        }

        $ical_contents = wp_remote_retrieve_body($ical_contents);

        $ical_contents = trim($ical_contents);

        if (0 !== strpos($ical_contents, 'BEGIN:VCALENDAR') || false === strpos($ical_contents, 'END:VCALENDAR')) {
            continue;
        }

        $ical_contents = addslashes($ical_contents);

        $ical_feed['file_contents'] = $ical_contents;
        $ical_feed['last_updated'] = current_time('Y-m-d H:i:s');

        wpbs_update_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed['id'], $ical_feed);
    }

    // Redirect to the iCal import/export page of the Calendar with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'ical-import-export', 'calendar_id' => $calendar_id, 'wpbs_message' => 'ical_import_url_refresh_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_refresh_ical_feeds', 'wpbs_action_refresh_ical_feeds');

/**
 * Check if a calendar has one or more disabled iCalendar feeds.
 * 
 * @param int $calendar_id
 * 
 * @return bool
 * 
 */
function wpbs_check_for_disabled_ical_feeds($calendar_id)
{

    if (wpbs_get_calendar_meta($calendar_id, 'disable_icalendar_links', true)) {
        return true;
    }

    $ical_feeds = wpbs_get_calendar_meta_ical_feeds($calendar_id);
    foreach ($ical_feeds as $ical_feed) {
        if (isset($ical_feed['disabled']) && $ical_feed['disabled']) {
            return true;
        }
    }
    return false;
}
