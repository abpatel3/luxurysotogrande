<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register Avada Builder element (map) + shortcode for Overview Calendar.
 */
add_action('init', 'wpbs_avada_overview_calendar_element_register');
function wpbs_avada_overview_calendar_element_register()
{

    // Avada Builder must be active.
    if (! function_exists('fusion_builder_map')) {
        return;
    }

    // Build calendars dropdown (active only).
    $calendar_options = array();
    if (function_exists('wpbs_get_calendars')) {
        $calendars = wpbs_get_calendars(array('status' => 'active'));
        foreach ($calendars as $calendar) {
            $calendar_options[(string) $calendar->get('id')] = $calendar->get('name');
        }
    }

    // Languages dropdown.
    $languages_dropdown = array('auto' => __('Auto', 'wp-booking-system'));
    $settings = get_option('wpbs_settings');
    if (! empty($settings['active_languages']) && function_exists('wpbs_get_languages')) {
        $languages = wpbs_get_languages();
        foreach ($settings['active_languages'] as $code) {
            if (! empty($languages[$code])) {
                $languages_dropdown[$code] = $languages[$code];
            }
        }
    }

    // Years list (current year + 10).
    $years = array('0' => __('Current Year', 'wp-booking-system'));
    $y = (int) date('Y');
    for ($i = 0; $i <= 10; $i++) {
        $years[(string) ($y + $i)] = (string) ($y + $i);
    }

    // Register the element with Avada Builder.
    fusion_builder_map(array(
        'name'            => esc_attr__('Overview Calendar - WP Booking System', 'wp-booking-system'),
        'shortcode'       => 'wpbs_overview_calendar',
        'icon'            => 'fusiona-calendar-plus-o',
        'help_url'        => 'https://www.wpbookingsystem.com/documentation/',
        'allow_generator' => true,

        'params'          => array(

            // Calendars group
            array(
                'type'        => 'select',
                'heading'     => esc_attr__('Display', 'wp-booking-system'),
                'param_name'  => 'calendars_type',
                'default'     => 'all',
                'value'       => array(
                    'all'      => __('All Calendars', 'wp-booking-system'),
                    'selected' => __('Selected Calendars', 'wp-booking-system'),
                ),
            ),
            array(
                // Avada supports a multiple select via type 'multiple_select'
                'type'        => 'multiple_select',
                'heading'     => esc_attr__('Calendars', 'wp-booking-system'),
                'param_name'  => 'calendars',
                'value'       => $calendar_options,
                'dependency'  => array(
                    array(
                        'element'  => 'calendars_type',
                        'value'    => 'selected',
                        'operator' => '==',
                    ),
                ),
                'description' => esc_html__('Choose one or more calendars when “Selected Calendars” is active.', 'wp-booking-system'),
            ),

            // Calendar Options group
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Display Legend', 'wp-booking-system'),
                'param_name' => 'legend',
                'default'    => 'yes',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Legend Position', 'wp-booking-system'),
                'param_name' => 'legend_position',
                'default'    => 'top',
                'value'      => array('top' => __('Top', 'wp-booking-system'), 'bottom' => __('Bottom', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Start Year', 'wp-booking-system'),
                'param_name' => 'start_year',
                'default'    => '0',
                'value'      => $years,
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Start Month', 'wp-booking-system'),
                'param_name' => 'start_month',
                'default'    => '0',
                'value'      => array(
                    '0'  => __('Current Month', 'wp-booking-system'),
                    '1'  => __('January', 'wp-booking-system'),
                    '2'  => __('February', 'wp-booking-system'),
                    '3'  => __('March', 'wp-booking-system'),
                    '4'  => __('April', 'wp-booking-system'),
                    '5'  => __('May', 'wp-booking-system'),
                    '6'  => __('June', 'wp-booking-system'),
                    '7'  => __('July', 'wp-booking-system'),
                    '8'  => __('August', 'wp-booking-system'),
                    '9'  => __('September', 'wp-booking-system'),
                    '10' => __('October', 'wp-booking-system'),
                    '11' => __('November', 'wp-booking-system'),
                    '12' => __('December', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Show History', 'wp-booking-system'),
                'param_name' => 'history',
                'default'    => '1',
                'value'      => array(
                    '1' => __('Display booking history', 'wp-booking-system'),
                    '2' => __('Replace booking history with the default legend item', 'wp-booking-system'),
                    '3' => __('Use the Booking History Color from the Settings', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Display Tooltips', 'wp-booking-system'),
                'param_name' => 'tooltip',
                'default'    => '1',
                'value'      => array(
                    '1' => __('No', 'wp-booking-system'),
                    '2' => __('Yes', 'wp-booking-system'),
                    '3' => __('Yes, with red indicator', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Weekday Abbreviations', 'wp-booking-system'),
                'param_name' => 'weeknumbers',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Language', 'wp-booking-system'),
                'param_name' => 'language',
                'default'    => 'auto',
                'value'      => $languages_dropdown,
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
        ),
    ));
}

/**
 * Shortcode that renders the overview calendar, mirroring Elementor’s render().
 */
add_shortcode('wpbs_overview_calendar', 'wpbs_overview_calendar_shortcode');
function wpbs_overview_calendar_shortcode($atts)
{

    $defaults = array(
        'calendars_type'   => 'all',
        'calendars'        => array(), // may arrive as array (from multiple_select) or string.
        'legend'           => 'yes',
        'legend_position'  => 'top',
        'start_year'       => '0',
        'start_month'      => '0',
        'history'          => '1',
        'tooltip'          => '1',
        'weeknumbers'      => 'no',
        'language'         => 'auto',
    );

    $atts = shortcode_atts($defaults, $atts, 'wpbs_overview_calendar');

    // Normalize calendars field to a comma-separated string when needed.
    if ('all' === $atts['calendars_type']) {
        $atts['calendars'] = 'all';
    } else {
        // If builder provided an array (multiple_select), implode it.
        if (is_array($atts['calendars'])) {
            $atts['calendars'] = implode(',', array_filter(array_map('trim', $atts['calendars'])));
        }
        // If empty after normalization, default to 'all'.
        if (empty($atts['calendars'])) {
            $atts['calendars'] = 'all';
        }
    }

    if (class_exists('WPBS_Shortcodes') && is_callable(array('WPBS_Shortcodes', 'calendar_overview'))) {
        return WPBS_Shortcodes::calendar_overview($atts);
    }

    return '';
}
