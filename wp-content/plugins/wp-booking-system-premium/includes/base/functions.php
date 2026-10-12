<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the Base files
 *
 */
function wpbs_include_files_base()
{

    // Get legend dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include languages functions
    if (file_exists($dir_path . 'functions-languages.php')) {
        include $dir_path . 'functions-languages.php';
    }

    // Include utils functions
    if (file_exists($dir_path . 'functions-utils.php')) {
        include $dir_path . 'functions-utils.php';
    }

    // Include update functions
    if (file_exists($dir_path . 'functions-update.php')) {
        include $dir_path . 'functions-update.php';
    }

    // Include the shortcodes class
    if (file_exists($dir_path . 'class-shortcodes.php')) {
        include $dir_path . 'class-shortcodes.php';
    }

    // Include the widget class
    if (file_exists($dir_path . 'class-widget-calendar.php')) {
        include $dir_path . 'class-widget-calendar.php';
    }
    if (file_exists($dir_path . 'class-widget-overview-calendar.php')) {
        include $dir_path . 'class-widget-overview-calendar.php';
    }

    if (file_exists($dir_path . 'class-widget-yearly-calendar.php')) {
        include $dir_path . 'class-widget-yearly-calendar.php';
    }

    // Include the email template class
    if (file_exists($dir_path . 'class-email-template.php')) {
        include $dir_path . 'class-email-template.php';
    }

    // Include the base mailer
    if (file_exists($dir_path . 'class-mailer.php')) {
        include $dir_path . 'class-mailer.php';
    }

    // Include the Customizer class
    if (file_exists($dir_path . 'class-customizer.php')) {
        include $dir_path . 'class-customizer.php';
    }
}
add_action('wpbs_include_files', 'wpbs_include_files_base');

/**
 * Returns an array with the weekdays
 *
 * @return array
 *
 */
function wpbs_get_weekdays()
{

    $weekdays = array(
        __('Monday', 'wp-booking-system'),
        __('Tuesday', 'wp-booking-system'),
        __('Wednesday', 'wp-booking-system'),
        __('Thursday', 'wp-booking-system'),
        __('Friday', 'wp-booking-system'),
        __('Saturday', 'wp-booking-system'),
        __('Sunday', 'wp-booking-system'),
    );

    return $weekdays;
}

/**
 * Returns an array with the localized weekdays
 *
 * @return array
 *
 */
function wpbs_get_translated_weekdays()
{

    $weekdays = array(
        wpbs_date_i18n('l', strtotime('Monday')),
        wpbs_date_i18n('l', strtotime('Tuesday')),
        wpbs_date_i18n('l', strtotime('Wednesday')),
        wpbs_date_i18n('l', strtotime('Thursday')),
        wpbs_date_i18n('l', strtotime('Friday')),
        wpbs_date_i18n('l', strtotime('Saturday')),
        wpbs_date_i18n('l', strtotime('Sunday')),
    );

    return $weekdays;
}

/**
 * Returns true if there are any active languages in settings, false if not.
 *
 * @return bool
 *
 */
function wpbs_translations_active($language = false)
{

    $settings = get_option('wpbs_settings', array());

    if (!isset($settings['active_languages'])) {
        return false;
    }

    if (empty($settings['active_languages'])) {
        return false;
    }

    if ($language !== false && !in_array($language, $settings['active_languages'])) {
        return false;
    }

    return true;
}

/**
 * Check if a variable is empty, but not zero
 * 
 * @param int $var
 * 
 * @return bool
 * 
 */
function wpbs_is_empty(&$var)
{
    return !(isset($var) && ($var === '0' || !empty($var)));
}

/**
 * Add 'st' 'nd' 'rd' 'th' to a number
 * 
 * @param int $number
 * 
 * @return string
 * 
 */
function wpbs_ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}

/**
 * Check if the scripts and styles are enququed on the current page
 * 
 * @return bool
 * 
 */
function wpbs_check_if_scripts_and_styles_are_enqueued()
{

    if (apply_filters('wpbs_load_scripts_and_styles', true) == false) {
        return true;
    }

    global $post;

    $settings = get_option('wpbs_settings', array());

    if (isset($settings['custom_enqueue_scripts']) && $settings['custom_enqueue_scripts'] == 'on') {
        global $post;

        $pages = isset($settings['custom_enqueue_scripts_pages']) ? $settings['custom_enqueue_scripts_pages'] : array();
        if (isset($post->ID) && !in_array($post->ID, $pages)) {
            return false;
        }
    }

    return true;
}
