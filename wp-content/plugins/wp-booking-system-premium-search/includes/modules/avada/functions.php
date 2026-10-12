<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register Avada Builder element (map) + shortcode for Search Widget.
 */
add_action('init', 'wpbs_avada_search_widget_register');
function wpbs_avada_search_widget_register()
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
    $languages_dropdown = array('auto' => __('Auto', 'wp-booking-system-search'));
    $settings = get_option('wpbs_settings');
    if (! empty($settings['active_languages']) && function_exists('wpbs_get_languages')) {
        $languages = wpbs_get_languages();
        foreach ($settings['active_languages'] as $code) {
            if (! empty($languages[$code])) {
                $languages_dropdown[$code] = $languages[$code];
            }
        }
    }

    // Register the element with Avada Builder.
    fusion_builder_map(array(
        'name'            => esc_attr__('Search Widget - WP Booking System', 'wp-booking-system-search'),
        'shortcode'       => 'wpbs_search_widget',
        'help_url'        => 'https://www.wpbookingsystem.com/documentation/',
        'icon'             => 'fusiona-search',
        'allow_generator' => true,

        'params'          => array(

            // Calendars group
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Search in', 'wp-booking-system-search'),
                'param_name' => 'calendars_type',
                'default'    => 'all',
                'value'      => array(
                    'all'      => __('All Calendars', 'wp-booking-system-search'),
                    'selected' => __('Selected Calendars', 'wp-booking-system-search'),
                ),
            ),
            array(
                'type'        => 'multiple_select', // allows multi-select in Avada
                'heading'     => esc_attr__('Calendars', 'wp-booking-system-search'),
                'param_name'  => 'calendars',
                'value'       => $calendar_options,
                'dependency'  => array(
                    array(
                        'element'  => 'calendars_type',
                        'value'    => 'selected',
                        'operator' => '==',
                    ),
                ),
                'description' => esc_html__('Choose one or more calendars when “Selected Calendars” is active.', 'wp-booking-system-search'),
            ),

            // Widget Options group
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Language', 'wp-booking-system-search'),
                'param_name' => 'language',
                'default'    => 'auto',
                'value'      => $languages_dropdown,
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Widget Title', 'wp-booking-system-search'),
                'param_name' => 'title',
                'default'    => 'yes',
                'value'      => array('yes' => __('Yes', 'wp-booking-system-search'), 'no' => __('No', 'wp-booking-system-search')),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Week Start Day', 'wp-booking-system-search'),
                'param_name' => 'start_day',
                'default'    => '1',
                'value'      => array(
                    '1' => __('Monday', 'wp-booking-system-search'),
                    '2' => __('Tuesday', 'wp-booking-system-search'),
                    '3' => __('Wednesday', 'wp-booking-system-search'),
                    '4' => __('Thursday', 'wp-booking-system-search'),
                    '5' => __('Friday', 'wp-booking-system-search'),
                    '6' => __('Saturday', 'wp-booking-system-search'),
                    '7' => __('Sunday', 'wp-booking-system-search'),
                ),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Automatically Mark Selection', 'wp-booking-system-search'),
                'param_name' => 'mark_selection',
                'default'    => 'yes',
                'value'      => array('yes' => __('Yes', 'wp-booking-system-search'), 'no' => __('No', 'wp-booking-system-search')),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Selection Type', 'wp-booking-system-search'),
                'param_name' => 'selection_type',
                'default'    => 'multiple',
                'value'      => array(
                    'multiple' => __('Date Range', 'wp-booking-system-search'),
                    'single'   => __('Single Day', 'wp-booking-system-search'),
                ),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'textfield',
                'heading'    => esc_attr__('Minimum Stay', 'wp-booking-system-search'),
                'param_name' => 'minimum_stay',
                'value'      => '0',
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Featured Image', 'wp-booking-system-search'),
                'param_name' => 'featured_image',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system-search'), 'no' => __('No', 'wp-booking-system-search')),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Starting Price', 'wp-booking-system-search'),
                'param_name' => 'starting_price',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system-search'), 'no' => __('No', 'wp-booking-system-search')),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'radio_button_set',
                'heading'    => esc_attr__('Show Results on Load', 'wp-booking-system-search'),
                'param_name' => 'show_results_on_load',
                'default'    => 'no',
                'value'      => array('yes' => __('Yes', 'wp-booking-system-search'), 'no' => __('No', 'wp-booking-system-search')),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'select',
                'heading'    => esc_attr__('Results Layout', 'wp-booking-system-search'),
                'param_name' => 'results_layout',
                'default'    => 'list',
                'value'      => array(
                    'list' => __('List', 'wp-booking-system-search'),
                    'grid' => __('Grid', 'wp-booking-system-search'),
                ),
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'textfield',
                'heading'    => esc_attr__('Results per Page', 'wp-booking-system-search'),
                'param_name' => 'results_per_page',
                'value'      => '10',
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
            ),
            array(
                'type'       => 'textfield',
                'heading'    => esc_attr__('Redirect', 'wp-booking-system-search'),
                'param_name' => 'redirect',
                'value'      => '',
                'group'      => esc_attr__('Widget Options', 'wp-booking-system-search'),
                'description' => esc_html__('Optional URL to redirect to after search.', 'wp-booking-system-search'),
            ),
        ),
    ));
}

/**
 * Shortcode that renders the search widget, mirroring Elementor’s render().
 */
add_shortcode('wpbs_search_widget', 'wpbs_search_widget_shortcode');
function wpbs_search_widget_shortcode($atts)
{

    $defaults = array(
        'calendars_type'     => 'all',
        'calendars'          => array(), // may arrive as array (from multiple_select) or string

        'language'           => 'auto',
        'title'              => 'yes',
        'start_day'          => '1',
        'mark_selection'     => 'yes',
        'selection_type'     => 'multiple',
        'minimum_stay'       => '0',
        'featured_image'     => 'no',
        'starting_price'     => 'no',
        'show_results_on_load' => 'no',
        'results_layout'     => 'list',
        'results_per_page'   => '10',
        'redirect'           => '',
    );

    $atts = shortcode_atts($defaults, $atts, 'wpbs_search_widget');

    // Normalize calendars field to a comma-separated string when needed.
    if ('all' === $atts['calendars_type']) {
        $atts['calendars'] = 'all';
    } else {
        if (is_array($atts['calendars'])) {
            $atts['calendars'] = implode(',', array_filter(array_map('trim', $atts['calendars'])));
        }
        if (empty($atts['calendars'])) {
            $atts['calendars'] = 'all';
        }
    }

    if (class_exists('WPBS_S_Shortcodes') && is_callable(array('WPBS_S_Shortcodes', 'search_widget'))) {
        return WPBS_S_Shortcodes::search_widget($atts);
    }

    return '';
}
