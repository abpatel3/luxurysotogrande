<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register Avada Builder element (map) + shortcode.
 * Tip: using 'init' ensures other plugins (and taxonomies, if any) are registered before we build dropdowns.
 */
add_action('init', 'wpbs_avada_single_calendar_element_register');
function wpbs_avada_single_calendar_element_register()
{

    // Avada Builder must be active.
    if (! function_exists('fusion_builder_map')) {
        return;
    }

    // Build select options.
    $calendar_options = array('0' => '-');
    if (function_exists('wpbs_get_calendars')) {
        $calendars = wpbs_get_calendars(array('status' => 'active'));
        foreach ($calendars as $calendar) {
            $calendar_options[(string) $calendar->get('id')] = $calendar->get('name');
        }
    }

    $form_options = array('0' => '-');
    if (function_exists('wpbs_get_forms')) {
        $forms = wpbs_get_forms(array('status' => 'active'));
        foreach ($forms as $form) {
            $form_options[(string) $form->get('id')] = $form->get('name');
        }
    }

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
        'name'            => esc_attr__('Single Calendar - WP Booking System', 'wp-booking-system'),
        'shortcode'       => 'wpbs_single_calendar',
        'icon'            => 'fusiona-calendar-plus-o',
        'help_url'        => 'https://www.wpbookingsystem.com/documentation/adding-a-calendar-and-a-form-to-a-page/with-avada-builder/',
        'allow_generator' => true,
        'params'          => array(

            // Calendar (group)
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Calendar', 'wp-booking-system'),
                'param_name' => 'calendar_id',
                'default'    => '0',
                'value'      => $calendar_options,
            ),

            // Form (group)
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Form', 'wp-booking-system'),
                'param_name' => 'form_id',
                'default'    => '0',
                'value'      => $form_options,
            ),

            // Calendar Options (group)
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Display Calendar Title', 'wp-booking-system'),
                'param_name' => 'title',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
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
                'default'    => 'side',
                'value'      => array(
                    'side'   => __('Side', 'wp-booking-system'),
                    'top'    => __('Top', 'wp-booking-system'),
                    'bottom' => __('Bottom', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Months to Display', 'wp-booking-system'),
                'param_name' => 'display',
                'default'    => '1',
                'value'      => array_combine(range(1, 24), range(1, 24)),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Start Year', 'wp-booking-system'),
                'param_name' => 'year',
                'default'    => '0',
                'value'      => $years,
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Start Month', 'wp-booking-system'),
                'param_name' => 'month',
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
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show First Available Month', 'wp-booking-system'),
                'param_name' => 'show_first_available_date',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Display Dropdown', 'wp-booking-system'),
                'param_name' => 'dropdown',
                'default'    => 'yes',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Week Start Day', 'wp-booking-system'),
                'param_name' => 'start',
                'default'    => '1',
                'value'      => array(
                    '1' => __('Monday', 'wp-booking-system'),
                    '2' => __('Tuesday', 'wp-booking-system'),
                    '3' => __('Wednesday', 'wp-booking-system'),
                    '4' => __('Thursday', 'wp-booking-system'),
                    '5' => __('Friday', 'wp-booking-system'),
                    '6' => __('Saturday', 'wp-booking-system'),
                    '7' => __('Sunday', 'wp-booking-system'),
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
                'heading'    => esc_attr__('Highlight Today', 'wp-booking-system'),
                'param_name' => 'highlighttoday',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Week Numbers', 'wp-booking-system'),
                'param_name' => 'weeknumbers',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Calendar Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Prices', 'wp-booking-system'),
                'param_name' => 'show_prices',
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

            // Form Options (group)
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Form Position', 'wp-booking-system'),
                'param_name' => 'form_position',
                'default'    => 'bottom',
                'value'      => array('bottom' => __('Bottom', 'wp-booking-system'), 'side' => __('Side', 'wp-booking-system')),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Automatically Block Dates', 'wp-booking-system'),
                'param_name' => 'auto_pending',
                'default'    => 'yes',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Selection Type', 'wp-booking-system'),
                'param_name' => 'selection_type',
                'default'    => 'multiple',
                'value'      => array('multiple' => __('Date Range', 'wp-booking-system'), 'single' => __('Single Day', 'wp-booking-system')),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Selection Style', 'wp-booking-system'),
                'param_name' => 'selection_style',
                'default'    => 'split',
                'value'      => array('normal' => __('Normal', 'wp-booking-system'), 'split' => __('Split', 'wp-booking-system')),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'textfield',
                'heading'    => esc_attr__('Minimum Days', 'wp-booking-system'),
                'param_name' => 'minimum_days',
                'value'      => '0',
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'textfield',
                'heading'    => esc_attr__('Maximum Days', 'wp-booking-system'),
                'param_name' => 'maximum_days',
                'value'      => '0',
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Booking Start Day', 'wp-booking-system'),
                'param_name' => 'booking_start_day',
                'default'    => '0',
                'value'      => array(
                    '0' => '-',
                    '1' => __('Monday', 'wp-booking-system'),
                    '2' => __('Tuesday', 'wp-booking-system'),
                    '3' => __('Wednesday', 'wp-booking-system'),
                    '4' => __('Thursday', 'wp-booking-system'),
                    '5' => __('Friday', 'wp-booking-system'),
                    '6' => __('Saturday', 'wp-booking-system'),
                    '7' => __('Sunday', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Booking End Day', 'wp-booking-system'),
                'param_name' => 'booking_end_day',
                'default'    => '0',
                'value'      => array(
                    '0' => '-',
                    '1' => __('Monday', 'wp-booking-system'),
                    '2' => __('Tuesday', 'wp-booking-system'),
                    '3' => __('Wednesday', 'wp-booking-system'),
                    '4' => __('Thursday', 'wp-booking-system'),
                    '5' => __('Friday', 'wp-booking-system'),
                    '6' => __('Saturday', 'wp-booking-system'),
                    '7' => __('Sunday', 'wp-booking-system'),
                ),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Date Selection', 'wp-booking-system'),
                'param_name' => 'show_date_selection',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system'), 'no' => __('No', 'wp-booking-system')),
                'group'      => esc_attr__('Form Options', 'wp-booking-system'),
            ),
        ),
    ));
}

/**
 * Shortcode that renders the calendar, mirroring Elementor’s render().
 */
add_shortcode('wpbs_single_calendar', 'wpbs_single_calendar_shortcode');
function wpbs_single_calendar_shortcode($atts)
{

    $defaults = array(
        'calendar_id'              => '0',
        'form_id'                  => '0',
        'title'                    => 'no',
        'legend'                   => 'yes',
        'legend_position'          => 'side',
        'display'                  => '1',
        'year'                     => '0',
        'month'                    => '0',
        'show_first_available_date' => 'no',
        'dropdown'                 => 'yes',
        'start'                    => '1',
        'history'                  => '1',
        'tooltip'                  => '1',
        'highlighttoday'           => 'no',
        'weeknumbers'              => 'no',
        'show_prices'              => 'no',
        'language'                 => 'auto',
        'form_position'            => 'bottom',
        'auto_pending'             => 'yes',
        'selection_type'           => 'multiple',
        'selection_style'          => 'split',
        'minimum_days'             => '0',
        'maximum_days'             => '0',
        'booking_start_day'        => '0',
        'booking_end_day'          => '0',
        'show_date_selection'      => 'no',
    );

    $atts = shortcode_atts($defaults, $atts, 'wpbs_single_calendar');

    if (empty($atts['calendar_id']) || '0' === $atts['calendar_id']) {
        return esc_html__('Please select a calendar to display', 'wp-booking-system');
    }

    // The WPBS renderer expects "id" for the calendar.
    $atts['id'] = $atts['calendar_id'];

    if (class_exists('WPBS_Shortcodes') && is_callable(array('WPBS_Shortcodes', 'single_calendar'))) {
        return WPBS_Shortcodes::single_calendar($atts);
    }

    return '';
}
