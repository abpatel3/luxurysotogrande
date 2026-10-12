<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Shortcodes
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {

        // Register the single calendar shortcode
        add_shortcode('wpbs', array(__CLASS__, 'single_calendar'));

        // Register the calendar overview shortcode
        add_shortcode('wpbs-overview', array(__CLASS__, 'calendar_overview'));

        // Register the calendar yearly shortcode
        add_shortcode('wpbs-yearly', array(__CLASS__, 'yearly_calendar'));
    }

    /**
     * Notification if scripts and styles are not enqueued on a page
     *
     */
    public static function scripts_and_styles_not_enqueued_notification()
    {
        return '<div style="display:inline-block; margin:10px auto; background-color:#d63638; color:#fff; padding: 10px 15px;">WP Booking System: Scripts and Styles are not enqueued on this page. <a style="color:#fff;" href="https://www.wpbookingsystem.com/documentation/scripts-and-styles-are-not-enqueued-on-this-page/" target="_blank">Click here to find out how to fix this.</a></div>';
    }

    /**
     * The callback for the WPBS single calendar shortcode
     *
     * @param array $atts
     *
     */
    public static function single_calendar($atts)
    {

        if(!wpbs_check_if_scripts_and_styles_are_enqueued()){
            return self::scripts_and_styles_not_enqueued_notification();
        }

        // Shortcode default attributes
        $default_atts = array(
            // Calendar
            'id' => 0,
            'title' => 'yes',
            'legend' => 'yes',
            'legend_position' => '',
            'dropdown' => 'yes',
            'start' => 1,
            'display' => 1,
            'language' => 'auto',
            'month' => 0,
            'year' => 0,
            'show_first_available_date' => 'no',
            'jump' => 'no',
            'history' => 1,
            'show_tooltip' => 1,
            'weeknumbers' => 'no',
            'highlighttoday' => 'no',
            'show_prices' => 'no',
            'manual_booking' => '',

            // Calendar backwards compatibility
            'tooltip' => null,

            // Form
            'form_id' => 0,
            'minimum_days' => 0,
            'maximum_days' => 0,
            'booking_start_day' => 0,
            'booking_end_day' => 0,
            'selection_type' => 'multiple',
            'selection_style' => 'split',
            'auto_pending' => 'yes',
            'show_date_selection' => 'no',
            'form_position' => 'bottom',
            'currency' => '',

            // Form backwards compatibility
            'form' => null,
            'selection' => null,
            'minimumdays' => null,
            'maximumdays' => null,
            'autopending' => null,

        );

        // Shortcode attributes
        $atts = shortcode_atts($default_atts, $atts);

        /**
         * Calendar Args
         */

        // Calendar outputter default arguments
        $default_calendar_args = wpbs_get_calendar_output_default_args();

        $tooltip = (isset($atts['tooltip']) && $atts['tooltip'] !== null) ? $atts['tooltip'] : $atts['show_tooltip'];

        // Translating values from the shortcode attributes to the calendar arguments
        $calendar_args = array(
            'show_title' => (!empty($atts['title']) && $atts['title'] == 'yes' ? 1 : 0),
            'months_to_show' => (int) $atts['display'],
            'show_legend' => (!empty($atts['legend']) && $atts['legend'] == 'yes' ? 1 : 0),
            'legend_position' => $atts['legend_position'],
            'start_weekday' => (int) $atts['start'],
            'show_selector_navigation' => (!empty($atts['dropdown']) && $atts['dropdown'] == 'yes' ? 1 : 0),
            'show_week_numbers' => (!empty($atts['weeknumbers']) && $atts['weeknumbers'] == 'yes' ? 1 : 0),
            'current_month' => (!empty($atts['month']) ? (int) $atts['month'] : date('n')),
            'current_year' => (!empty($atts['year']) ? (int) $atts['year'] : date('Y')),
            'show_first_available_date' => (!empty($atts['show_first_available_date']) && $atts['show_first_available_date'] == 'yes' ? 1 : 0),
            'jump_months' => (!empty($atts['jump']) && $atts['jump'] == 'yes' ? 1 : 0),
            'highlight_today' => (!empty($atts['highlighttoday']) && $atts['highlighttoday'] == 'yes' ? 1 : 0),
            'history' => (int) $atts['history'],
            'show_tooltip' => (int) $tooltip,
            'show_prices' => (!empty($atts['show_prices']) && $atts['show_prices'] == 'yes' ? 1 : 0),
            'language' => ($atts['language'] == 'auto' ? wpbs_get_site_language() : $atts['language']),
            'currency' => (!empty($atts['currency']) ? strtoupper($atts['currency']) : wpbs_get_currency()),
            'form_position' => (!empty($atts['form_position']) && $atts['form_position'] ? $atts['form_position'] : 'bottom'),
            'manual_booking' => (!empty($atts['manual_booking']) && $atts['manual_booking'] == true ? 1 : 0),
        );

        // Remove legend_position if it's empty
        if (empty($calendar_args['legend_position'])) {
            unset($calendar_args['legend_position']);
        }

        // Calendar id
        $calendar_id = (!empty($atts['id']) ? (int) $atts['id'] : 0);
        $calendar_id = apply_filters('wpbs_calendar_shortcode_calendar_id', $calendar_id);

        // Calendar arguments
        $calendar_args = wp_parse_args($calendar_args, $default_calendar_args);

        $calendar_args = apply_filters('wpbs_calendar_shortcode_args', $calendar_args, $calendar_id);

        // Calendar
        $calendar = wpbs_get_calendar($calendar_id);

        /**
         * Form Args
         */

        // Form outputter default arguments
        $default_form_args = wpbs_get_form_output_default_args();

        // Match shortcode values with old plugin version < 4.2.9
        $selection_type = (isset($atts['selection']) && $atts['selection'] !== null) ? $atts['selection'] : $atts['selection_type'];
        $minimum_days = (isset($atts['minimumdays']) && $atts['minimumdays'] !== null) ? $atts['minimumdays'] : $atts['minimum_days'];
        $maximum_days = (isset($atts['maximumdays']) && $atts['maximumdays'] !== null) ? $atts['maximumdays'] : $atts['maximum_days'];
        $auto_pending = (isset($atts['autopending']) && $atts['autopending'] !== null) ? $atts['autopending'] : $atts['auto_pending'];
        $form_id = (isset($atts['form']) && $atts['form'] !== null) ? $atts['form'] : $atts['form_id'];

        // Translating values from the shortcode attributes to the form arguments
        $form_args = array(
            'minimum_days' => (int) $minimum_days,
            'maximum_days' => (int) $maximum_days,
            'booking_start_day' => (int) $atts['booking_start_day'],
            'booking_end_day' => (int) $atts['booking_end_day'],
            'selection_type' => $selection_type,
            'selection_style' => ($selection_type == 'single' ? 'normal' : $atts['selection_style']),
            'auto_pending' => (!empty($auto_pending) && $auto_pending == 'yes' ? 1 : 0),
            'show_date_selection' => (!empty($atts['show_date_selection']) && $atts['show_date_selection'] == 'yes' ? 1 : 0),
            'language' => ($atts['language'] == 'auto' ? wpbs_get_site_language() : $atts['language']),
            'currency' => (!empty($atts['currency']) ? strtoupper($atts['currency']) : wpbs_get_currency()),
            'manual_booking' => (!empty($atts['manual_booking']) && $atts['manual_booking'] == true ? 1 : 0),
        );

        // Form arguments
        $form_args = wp_parse_args($form_args, $default_form_args);

        // Form id
        $form_id = (!empty($form_id) ? (int) $form_id : 0);

        if (is_null($calendar)) {
            return '<p>' . __('Calendar does not exist.', 'wp-booking-system') . '</p>';
        }

        if ($calendar->get('status') != 'active') {
            return '<p>' . __('Calendar does not exist.', 'wp-booking-system') . '</p>';
        }

        $output = '<div class="wpbs-main-wrapper wpbs-main-wrapper-calendar-' . $calendar_id . ' wpbs-main-wrapper-form-' . $form_id . '">';

        $output .= '<a class="wpbs-visually-hidden wpbs-skip-to-content-link" title="'.__('Skip Booking Form', 'wp-booking-system').'" href="#wpbs-booking-form-end">'.__('Skip Booking Form', 'wp-booking-system').'</a>';

        // Initialize the calendar outputter
        $calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);

        $output .= $calendar_outputter->get_display();

        if ($form_id !== 0) {

            // Form
            $form = wpbs_get_form($form_id);

            if (is_null($form)) {

                $output .= '<p>' . __('Form does not exist.', 'wp-booking-system') . '</p>';
            } else {

                // Initialize the form outputter
                $form_outputter = new WPBS_Form_Outputter($form, $form_args, array(), $calendar_id);
                $output .= $form_outputter->get_display();
            }
        }

        $output .= '<span class="wpbs-visually-hidden" id="wpbs-booking-form-end"></span>';
        $output .= '</div>';

        return apply_filters('wpbs_shortcode_single_calendar_output', $output);
    }

    /**
     * The callback for the WPBS calendar overview shortcode
     *
     * @param array $atts
     *
     */
    public static function calendar_overview($atts)
    {

        if(!wpbs_check_if_scripts_and_styles_are_enqueued()){
            return self::scripts_and_styles_not_enqueued_notification();
        }

        // Shortcode default attributes
        $default_atts = array(
            'calendars' => 'all',
            'legend' => 'yes',
            'legend_position' => 'side',
            'language' => 'auto',
            'start_month' => 0,
            'start_year' => 0,
            'history' => 1,
            'show_tooltip' => 1,
            'weeknumbers' => 'no',

            // Calendar backwards compatibility
            'tooltip' => false,
        );

        // Shortcode attributes
        $atts = shortcode_atts($default_atts, $atts);

        // Calendar outputter default arguments
        $default_args = wpbs_get_calendar_overview_output_default_args();

        $tooltip = (isset($atts['tooltip']) && $atts['tooltip'] !== null) ? $atts['tooltip'] : $atts['show_tooltip'];

        // Translating values from the shortcode attributes to the calendar arguments
        $args = array(
            'show_legend' => (!empty($atts['legend']) && $atts['legend'] == 'yes' ? 1 : 0),
            'legend_position' => $atts['legend_position'],
            'show_day_abbreviation' => (!empty($atts['weeknumbers']) && $atts['weeknumbers'] == 'yes' ? 1 : 0),
            'current_month' => (!empty($atts['start_month']) ? (int) $atts['start_month'] : date('n')),
            'current_year' => (!empty($atts['start_year']) ? (int) $atts['start_year'] : date('Y')),
            'history' => (int) $atts['history'],
            'show_tooltip' => (int) $tooltip,
            'language' => ($atts['language'] == 'auto' ? wpbs_get_site_language() : $atts['language']),
        );

        // Calendar arguments
        $calendar_args = wp_parse_args($args, $default_args);

        $calendar_args = apply_filters('wpbs_overview_calendar_shortcode_args', $calendar_args);

        // Calendars
        if (empty($atts['calendars']) || $atts['calendars'] == 'all') {

            $args = apply_filters('wpbs_calendar_overview_shortcode_all_calendars_args', array('status' => 'active'));

            $calendars = wpbs_get_calendars($args);
        } else {

            $calendar_ids = array_filter(array_map('trim', explode(',', $atts['calendars'])));

            $args = array(
                'include' => $calendar_ids,
                'orderby' => 'FIELD( id, ' . implode(',', $calendar_ids) . ')',
                'order' => '',
            );

            $calendars = wpbs_get_calendars($args);
        }

        if (empty($calendars)) {

            $output = '<p>' . __('No calendars found.', 'wp-booking-system') . '</p>';
        } else {

            // Initialize the calendar overview outputter
            $calendar_overview_outputter = new WPBS_Calendar_Overview_Outputter($calendars, $calendar_args);

            $output = $calendar_overview_outputter->get_display();
        }

        return apply_filters('wpbs_shortcode_overview_calendar_output', $output);
    }

    /**
     * The callback for the WPBS yearly calendar shortcode
     *
     * @param array $atts
     *
     */
    public static function yearly_calendar($atts)
    {   

        if(!wpbs_check_if_scripts_and_styles_are_enqueued()){
            return self::scripts_and_styles_not_enqueued_notification();
        }

        // Shortcode default attributes
        $default_atts = array(
            // Calendar
            'id' => 0,
            'title' => 'yes',
            'legend' => 'yes',
            'legend_position' => '',
            'dropdown' => 'yes',
            'start' => 1,
            'display' => 1,
            'language' => 'auto',
            'month' => 0,
            'year' => 0,
            'jump' => 'no',
            'history' => 1,
            'show_tooltip' => 1,
            'weeknumbers' => 'no',
            'highlighttoday' => 'no',
            'show_prices' => 'no',
            'manual_booking' => '',

            // Calendar backwards compatibility
            'tooltip' => null,

            // Form
            'form_id' => 0,
            'minimum_days' => 0,
            'maximum_days' => 0,
            'booking_start_day' => 0,
            'booking_end_day' => 0,
            'selection_type' => 'multiple',
            'selection_style' => 'split',
            'auto_pending' => 'yes',
            'show_date_selection' => 'no',
            'form_position' => 'bottom',
            'currency' => '',

            // Form backwards compatibility
            'form' => null,
            'selection' => null,
            'minimumdays' => null,
            'maximumdays' => null,
            'autopending' => null,

        );

        // Shortcode attributes
        $atts = shortcode_atts($default_atts, $atts);

        /**
         * Calendar Args
         */

        // Calendar outputter default arguments
        $default_calendar_args = wpbs_get_calendar_output_default_args();

        $tooltip = (isset($atts['tooltip']) && $atts['tooltip'] !== null) ? $atts['tooltip'] : $atts['show_tooltip'];

        // Translating values from the shortcode attributes to the calendar arguments
        $calendar_args = array(
            'show_title' => (!empty($atts['title']) && $atts['title'] == 'yes' ? 1 : 0),
            'show_legend' => (!empty($atts['legend']) && $atts['legend'] == 'yes' ? 1 : 0),
            'legend_position' => $atts['legend_position'],
            'show_selector_navigation' => (!empty($atts['dropdown']) && $atts['dropdown'] == 'yes' ? 1 : 0),
            'current_year' => (!empty($atts['year']) ? (int) $atts['year'] : date('Y')),
            'jump_months' => (!empty($atts['jump']) && $atts['jump'] == 'yes' ? 1 : 0),
            'highlight_today' => (!empty($atts['highlighttoday']) && $atts['highlighttoday'] == 'yes' ? 1 : 0),
            'history' => (int) $atts['history'],
            'show_tooltip' => (int) $tooltip,
            'show_prices' => (!empty($atts['show_prices']) && $atts['show_prices'] == 'yes' ? 1 : 0),
            'language' => ($atts['language'] == 'auto' ? wpbs_get_site_language() : $atts['language']),
            'currency' => (!empty($atts['currency']) ? strtoupper($atts['currency']) : wpbs_get_currency()),
            'form_position' => (!empty($atts['form_position']) && $atts['form_position'] ? $atts['form_position'] : 'bottom'),
            'manual_booking' => (!empty($atts['manual_booking']) && $atts['manual_booking'] == true ? 1 : 0),
        );

        // Remove legend_position if it's empty
        if (empty($calendar_args['legend_position'])) {
            unset($calendar_args['legend_position']);
        }

        // Calendar id
        $calendar_id = (!empty($atts['id']) ? (int) $atts['id'] : 0);
        $calendar_id = apply_filters('wpbs_calendar_shortcode_calendar_id', $calendar_id);

        // Calendar arguments
        $calendar_args = wp_parse_args($calendar_args, $default_calendar_args);

        $calendar_args = apply_filters('wpbs_calendar_shortcode_args', $calendar_args, $calendar_id);

        // Calendar
        $calendar = wpbs_get_calendar($calendar_id);

        /**
         * Form Args
         */

        // Form outputter default arguments
        $default_form_args = wpbs_get_form_output_default_args();

        // Match shortcode values with old plugin version < 4.2.9
        $selection_type = (isset($atts['selection']) && $atts['selection'] !== null) ? $atts['selection'] : $atts['selection_type'];
        $minimum_days = (isset($atts['minimumdays']) && $atts['minimumdays'] !== null) ? $atts['minimumdays'] : $atts['minimum_days'];
        $maximum_days = (isset($atts['maximumdays']) && $atts['maximumdays'] !== null) ? $atts['maximumdays'] : $atts['maximum_days'];
        $auto_pending = (isset($atts['autopending']) && $atts['autopending'] !== null) ? $atts['autopending'] : $atts['auto_pending'];
        $form_id = (isset($atts['form']) && $atts['form'] !== null) ? $atts['form'] : $atts['form_id'];

        // Translating values from the shortcode attributes to the form arguments
        $form_args = array(
            'minimum_days' => (int) $minimum_days,
            'maximum_days' => (int) $maximum_days,
            'booking_start_day' => (int) $atts['booking_start_day'],
            'booking_end_day' => (int) $atts['booking_end_day'],
            'selection_type' => $selection_type,
            'selection_style' => ($selection_type == 'single' ? 'normal' : $atts['selection_style']),
            'auto_pending' => (!empty($auto_pending) && $auto_pending == 'yes' ? 1 : 0),
            'show_date_selection' => (!empty($atts['show_date_selection']) && $atts['show_date_selection'] == 'yes' ? 1 : 0),
            'language' => ($atts['language'] == 'auto' ? wpbs_get_site_language() : $atts['language']),
            'currency' => (!empty($atts['currency']) ? strtoupper($atts['currency']) : wpbs_get_currency()),
            'manual_booking' => (!empty($atts['manual_booking']) && $atts['manual_booking'] == true ? 1 : 0),
        );

        // Form arguments
        $form_args = wp_parse_args($form_args, $default_form_args);

        // Form id
        $form_id = (!empty($form_id) ? (int) $form_id : 0);

        if (is_null($calendar)) {
            return '<p>' . __('Calendar does not exist.', 'wp-booking-system') . '</p>';
        }

        if ($calendar->get('status') != 'active') {
            return '<p>' . __('Calendar does not exist.', 'wp-booking-system') . '</p>';
        }

        $output = '<div class="wpbs-main-wrapper wpbs-main-wrapper-yearly wpbs-main-wrapper-calendar-' . $calendar_id . ' wpbs-main-wrapper-form-' . $form_id . '">';

        // Initialize the calendar outputter
        $calendar_outputter = new WPBS_Calendar_Yearly_Outputter($calendar, $calendar_args);

        $output .= $calendar_outputter->get_display();

        if ($form_id !== 0) {

            // Form
            $form = wpbs_get_form($form_id);

            if (is_null($form)) {

                $output .= '<p>' . __('Form does not exist.', 'wp-booking-system') . '</p>';
            } else {

                // Initialize the form outputter
                $form_outputter = new WPBS_Form_Outputter($form, $form_args, array(), $calendar_id);
                $output .= $form_outputter->get_display();
            }
        }

        $output .= '</div>';

        return apply_filters('wpbs_shortcode_yearly_calendar_output', $output);
    }
}

// Init shortcodes
new WPBS_Shortcodes();
