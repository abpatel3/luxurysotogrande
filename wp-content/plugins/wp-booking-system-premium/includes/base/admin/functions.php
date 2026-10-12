<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the admin area
 *
 */
function wpbs_include_files_admin()
{

    // Get calendar dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include the db layer classes
    if (file_exists($dir_path . 'class-admin-notices.php')) {
        include $dir_path . 'class-admin-notices.php';
    }
}
add_action('wpbs_include_files', 'wpbs_include_files_admin');

/**
 * Adds a central action hook on the admin_init that the plugin and add-ons
 * can use to do certain actions, like adding a new calendar, editing a calendar, deleting, etc.
 *
 */
function wpbs_register_do_actions()
{

    if (empty($_REQUEST['wpbs_action'])) {
        return;
    }

    $action = sanitize_text_field($_REQUEST['wpbs_action']);

    /**
     * Hook that should be used by all processes that make a certain action
     * withing the plugin, like adding a new calendar, editing a calendar, deleting, etc.
     *
     */
    do_action('wpbs_action_' . $action);
}
add_action('admin_init', 'wpbs_register_do_actions');

/**
 * Builds and returns the HTML with a tooltip for the given message
 *
 * @param string $message
 *
 * @return string
 *
 */
function wpbs_get_output_tooltip($message, $position = '')
{

    $output = '<span class="wpbs-tooltip-wrapper ' . $position . '">';

    // Icon
    $output .= '<span class="wpbs-tooltip-icon">?</span>';

    // Message
    $output .= '<span class="wpbs-tooltip-message">';

    $output .= $message;

    // Arrow
    $output .= '<span class="wpbs-tooltip-arrow"></span>';

    $output .= '</span>';

    $output .= '</span>';

    return $output;
}

/**
 * Split form and settings pages into different screens if this option is disabled.
 *
 */
function wpbs_enhanced_admin_ui()
{
    return apply_filters('wpbs_enhanced_admin_ui', true);
}

/**
 * Localize javascript strings
 * 
 */
function wpbs_get_dashboard_translations()
{
    return array(
        'Remove' => __('Remove', 'wp-booking-system'),
        'Duplicate' => __('Duplicate', 'wp-booking-system'),
        'Reorder' => __('Reorder', 'wp-booking-system'),
        'Field' => __('Field', 'wp-booking-system'),
        'field' => __('field', 'wp-booking-system'),
        'GeneralOptions' => __('General Options', 'wp-booking-system'),
        'AdvancedOptions' => __('Advanced Options', 'wp-booking-system'),
        'Translations' => __('Translations', 'wp-booking-system'),
        'ConditionalLogic' => __('Conditional Logic', 'wp-booking-system'),
        'AddRule' => __('Add Rule', 'wp-booking-system'),
        'FieldType' => __('Field Type', 'wp-booking-system'),
        'Show' => __('Show', 'wp-booking-system'),
        'Hide' => __('Hide', 'wp-booking-system'),
        'All' => __('All', 'wp-booking-system'),
        'Any' => __('Any', 'wp-booking-system'),
        'this_field_if' => __('this field if', 'wp-booking-system'),
        'of_the_following_match' => __('of the following match', 'wp-booking-system'),
        'StayLength' => __('Stay Length', 'wp-booking-system'),
        'DaysFromToday' => __('Days from Today', 'wp-booking-system'),
        'StartDate' => __('Start Date', 'wp-booking-system'),
        'EndDate' => __('End Date', 'wp-booking-system'),
        'StartWeekday' => __('Start Weekday', 'wp-booking-system'),
        'EndWeekday' => __('End Weekday', 'wp-booking-system'),
        'CalendarID' => __('Calendar ID', 'wp-booking-system'),
        'Inventory' => __('Maximum Inventory', 'wp-booking-system'),
        'is' => __('is', 'wp-booking-system'),
        'is_not' => __('is not', 'wp-booking-system'),
        'is_greater_than' => __('is greater than', 'wp-booking-system'),
        'is_lower_than' => __('is lower than', 'wp-booking-system'),
        'contains' => __('contains', 'wp-booking-system'),
        'not_contains' => __('does not contain', 'wp-booking-system'),
        'starts_with' => __('starts with', 'wp-booking-system'),
        'ends_with' => __('ends with', 'wp-booking-system'),
        'Monday' => __('Monday', 'wp-booking-system'),
        'Tuesday' => __('Tuesday', 'wp-booking-system'),
        'Wednesday' => __('Wednesday', 'wp-booking-system'),
        'Thursday' => __('Thursday', 'wp-booking-system'),
        'Friday' => __('Friday', 'wp-booking-system'),
        'Saturday' => __('Saturday', 'wp-booking-system'),
        'Sunday' => __('Sunday', 'wp-booking-system'),
        'Do_not_multiply' => __('Do not multiply', 'wp-booking-system'),
        'Multiply_by_the_value_of_the' => __('Multiply by the value of the', 'wp-booking-system'),
        'AddOption' => __('Add Option', 'wp-booking-system'),
        'Name' => __('Name', 'wp-booking-system'),
        'Price' => __('Price', 'wp-booking-system'),
        'Start_date' => __('Start date', 'wp-booking-system'),
        'End_date' => __('End date', 'wp-booking-system'),
        'Enable' => __('Enable', 'wp-booking-system'),
        'Confirm_remove_rule' => __('Are you sure you want to remove this rule?', 'wp-booking-system'),
        'Confirm_remove_option' => __('Are you sure you want to remove this option?', 'wp-booking-system'),
        'Confirm_remove_field' => __('Are you sure you want to remove this field?', 'wp-booking-system'),

    );
}

/**
 * Output the wp-admin plugin navigation bar
 * 
 */
function wpbs_get_wp_admin_header()
{
    global $submenu;

    echo '<div class="wpbs-plugin-header">';

    echo '<a href="#" class="js-wpbs-mobile-main-menu-button" title="WP Booking System Mobile Menu Button">
            <span class="cross menu--1">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path class="line--1" d="M0 40h62c13 0 6 28-4 18L35 35"></path>
                    <path class="line--2" d="M0 50h70"></path>
                    <path class="line--3" d="M0 60h62c13 0 6-28-4-18L35 65"></path>
                </svg>
                <span class="text">Menu</span>
            </span>
        </a>';

    echo '<nav><ul class="menu">';

    foreach ($submenu['wp-booking-system'] as $menu) {
        echo '<li' . (isset($_GET['page']) && $_GET['page'] == $menu[2] ? ' class="active"' : '') . '><a href="' . add_query_arg(['page' => $menu[2]], admin_url('admin.php')) . '">' . $menu[3] . '</a></li>';
    }

    echo '</ul></nav>';

    echo '<a href="https://www.wpbookingsystem.com/" id="wpbs-logo" title="WP Booking System" target="_blank">
                <img width="317" height="49" src="' . WPBS_PLUGIN_DIR_URL . 'assets/img/logo-white.svg" alt="WP Booking System Logo">
            </a>';

    echo '</div>';
}
