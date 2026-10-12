<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_S_Search_Widget_Outputter
{

    /**
     * The shortcode attributes
     *
     */
    public $args;

    /**
     * The start date
     *
     */
    public $start_date;

    /**
     * The end date
     *
     */
    public $end_date;

    /**
     * Additional data
     *
     */
    public $additional_data;
    public $additional_search_fields;

    /**
     * Variable that holds the errors
     *
     */
    private $has_error = false;

    /**
     * The error message
     *
     */
    private $error;

    /**
     * A unique string
     *
     * @access protected
     * @var    array
     *
     */
    public $unique;


    /**
     * Constructor
     *
     */
    public function __construct($args, $start_date = null, $end_date = null, $additional_data = [])
    {

        /**
         * Set the attributes
         *
         */
        $this->args = $args;

        /**
         * Set the start date
         */
        if ($start_date === null && isset($_GET['wpbs-search-start-date'])) {
            $this->start_date = sanitize_text_field($_GET['wpbs-search-start-date']);
        } else {
            $this->start_date = $start_date;
        }

        /**
         * Set the end date
         *
         */
        if ($end_date === null && isset($_GET['wpbs-search-end-date'])) {
            $this->end_date = sanitize_text_field($_GET['wpbs-search-end-date']);
        } else {
            $this->end_date = $end_date;
        }

        /**
         * Set the language
         * 
         */
        if($this->args['language'] == 'auto'){
            $this->args['language'] = wpbs_get_site_language();
        }

        /**
         * Set the additional data
         * 
         */
        $this->additional_data = $additional_data;

        $this->additional_search_fields = wpbs_s_get_additional_search_fields();

        if ($this->additional_search_fields) {

            foreach ($this->additional_search_fields as $field) {

                if (isset($_GET[$field['slug']]) && !empty($_GET[$field['slug']])) {
                    $this->additional_data[$field['slug']] = sanitize_text_field($_GET[$field['slug']]);
                }
            }
        }


        /**
         * Set the unique string to prevent conflicts if the same form is embedded twice on the same page
         *
         */
        $this->unique = hash('crc32', microtime(), false);

        /**
         * Check for errors
         *
         */
        $this->check_errors();
    }

    /**
     * Constructs and returns the HTML for the Search Widget
     *
     * @return string
     *
     */
    public function get_display()
    {
        // Add the shortcode attributes
        $search_widget_html_data = '';
        foreach ($this->args as $att => $val) {
            $search_widget_html_data .= 'data-' . $att . '="' . esc_attr($val) . '" ';
        }

        $output = '<div class="wpbs_s-search-widget wpbs_s-search-widget-' . $this->get_search_type() . '-date-search" ' . $search_widget_html_data . '>';

        // Get the form
        $output .= '<div class="wpbs_s-search-widget-form-wrap">';
        $output .= $this->get_display_search_form();
        $output .= '</div>';

        // Get the errors
        $output .= $this->get_display_error();

        // Get the results
        $output .= '<div class="wpbs_s-search-widget-results-wrap" data-label-previous="' . $this->get_search_widget_string('label_previous') . '" data-label-next="' . $this->get_search_widget_string('label_next') . '">';
        $output .= $this->get_search_results();
        $output .= '</div>';

        $output .= '<div class="wpbs-search-container-loaded" data-just-loaded="1"></div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Constructs the HTML for the form errors
     *
     * @return string
     *
     */
    private function get_display_error()
    {

        // Check if we have errors
        if ($this->has_error === false) {
            return false;
        }

        // If we do, return them
        return '<div class="wpbs_s-search-widget-error-field">' . $this->error . '</div>';
    }

    /**
     * Constructs the HTML for the search form
     *
     * @return string
     *
     */
    private function get_display_search_form()
    {
        ob_start();
        include 'views/view-search-form.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Constructs the HTML for the 'no results found' message
     *
     * @return string
     *
     */
    private function get_display_no_results()
    {

        $output = '<p class="wpbs_s-search-widget-no-results">' . do_shortcode($this->get_search_widget_string('no_results')) . '</p>';

        return apply_filters('wpbs_search_no_results_html', $output, $this->args, $this->start_date, $this->end_date);

    }

    /**
     * Constructs the HTML for the search results
     *
     * @return string
     *
     */
    private function get_search_results()
    {
        // Check if the form was submitted
        if ($this->is_form_submitted() === false) {

            if ($this->args['show_results_on_load'] == 'no') {
                return false;
            }

            $available_calendars = $this->get_all_results_calendar_data();
        } else {
            if ($this->has_error === true) {
                return false;
            }
            // Get search data
            $available_calendars = $this->get_search_results_calendar_data();
        }

        // Search results title
        $output = '<h2>' . $this->get_search_widget_string('results_title') . '</h2>';

        $available_calendars = apply_filters('wpbs_search_results', $available_calendars, $this);

        // Check if there are results
        if (empty($available_calendars)) {
            $output .= $this->get_display_no_results();
            return $output;
        }

        // Display results
        $output .= '<div class="wpbs_s-search-widget-results">';
        foreach ($available_calendars as $calendar) {
            $output .= $this->get_display_search_result($calendar);
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Constructs the HTML for each search result row
     *
     * @param array $data
     * @return string
     *
     */
    private function get_display_search_result($data)
    {

        $data['button_label'] = $this->get_search_widget_string('view_button_label');

        $data['starting_from'] = $data['price'] ? str_replace('%', wpbs_get_formatted_price($data['price']['per_night'], wpbs_get_currency()), $this->get_search_widget_string('starting_from')) : '';

        $data = apply_filters('wpbs_search_result_data', $data, $this);

        $link_attributes = apply_filters('wpbs_search_result_link_attributes', array(
            'href' => $data['link'],
            'title' => $data['calendar_name'],
        ), $data, $this);

        $link_attributes_html = $this->array_to_html_attributes($link_attributes);

        $output = '<div class="wpbs_s-search-widget-result' . ($data['featured_image'] ? ' wpbs_s-has-thumb' : '') . '">';

        if ($data['featured_image']) {
            if (empty($data['link'])) {
                $output .= $data['featured_image'];
            } else {
                $output .= '<a class="wpbs_s-search-widget-result-link" ' . $link_attributes_html . '>' . $data['featured_image'] . '</a>';
            }
        }

        $output .= '<div class="wpbs_s-search-widget-result-title">';

        $output .= apply_filters('wpbs_search_result_before_title', '', $data, $this);

        if (empty($data['link'])) {
            $output .= '<h3>' . $data['calendar_name'] . '</h3>';
        } else {
            $output .= '<h3><a class="wpbs_s-search-widget-result-link" ' . $link_attributes_html . '>' . $data['calendar_name'] . '</a></h3>';
        }

        $output .= apply_filters('wpbs_search_result_after_title', '', $data, $this);

        if ($data['price']) {
            $output .= '<span>' . $data['starting_from'] . '</span>';
        }

        $output .= '</div>';

        $output .= apply_filters('wpbs_search_result_before_button', '', $data, $this);

        if (!empty($data['link'])) {
            $output .= '<a class="wpbs_s-search-widget-result-button" ' . $link_attributes_html . '>' . $data['button_label'] . '</a>';
        }

        $output .= apply_filters('wpbs_search_result_after_button', '', $data, $this);

        $output .= '</div>';

        $output = apply_filters('wpbs_search_resuts_html', $output, $data, $this);

        return $output;
    }

    /**
     * Checks if the form was submitted
     *
     * @return bool
     *
     */
    private function is_form_submitted()
    {
        if ($this->start_date === null) {
            return false;
        }
        return true;
    }

    /**
     * Returns all calendars in the correct format, used when show_resutls_on_load is true
     *
     */
    private function get_all_results_calendar_data()
    {
        $available_calendars = [];

        if (empty($this->args['calendars']) || $this->args['calendars'] == 'all') {
            // All calendars
            $args = array('status' => 'active', 'orderby' => 'name', 'order' => 'asc');
        } else {
            // Specific calendars
            $calendar_ids = array_filter(array_map('trim', explode(',', $this->args['calendars'])));
            $args = array(
                'include' => $calendar_ids,
                'orderby' => 'FIELD( id, ' . implode(',', $calendar_ids) . ')',
                'order' => '',
            );
        }

        $args = apply_filters('wpbs_search_calendar_query_args', $args);

        $calendars = wpbs_get_calendars($args);

        foreach ($calendars as $calendar) {

            wpbs_maybe_refresh_icalendar_feed($calendar->get('id'));

            $name = $calendar->get_name($this->args['language']);

            // Get calendar Links
            $calendar_link_post_id = WPBS_Calendar_Overview_Outputter::get_calendar_link_post_id($calendar->get('id'), $this->args['language']);
            $calendar_link = WPBS_Calendar_Overview_Outputter::get_calendar_link($calendar->get('id'), $this->args['language']);

            // Pricing
            $calendar_price = false;

            if ($this->args['starting_price'] == 'yes' && wpbs_is_pricing_enabled()) {
                $default_price = (float) wpbs_get_calendar_meta($calendar->get('id'), 'default_price', true);
                $calendar_price['total'] = $default_price;
                $calendar_price['per_night'] = $default_price;
            }

            // Featured image
            $featured_image = false;
            if ($this->args['featured_image'] == 'yes' && $calendar_link_post_id && has_post_thumbnail($calendar_link_post_id)) {
                $featured_image = get_the_post_thumbnail($calendar_link_post_id, apply_filters('wpbs_search_wiget_featured_image_size', 'large'));
            }

            $available_calendars[] = array('calendar_name' => $name, 'calendar_id' => $calendar->get('id'), 'link' => $calendar_link, 'post_id' => $calendar_link_post_id, 'price' => $calendar_price, 'featured_image' => $featured_image, 'additional_data' => $this->additional_data);
        }

        return $available_calendars;
    }

    /**
     * Does the actual search for available dates in calendars
     *
     */
    private function get_search_results_calendar_data()
    {
        // Format start date
        $start_datetime = DateTime::createFromFormat('Y-m-d', $this->start_date);
        $start_date = $start_datetime->format('Ymd');

        // Format end date
        if ($this->get_search_type() == 'multiple') {
            $end_datetime = DateTime::createFromFormat('Y-m-d', $this->end_date);
            $end_date = $end_datetime->format('Ymd');
        } else {
            $end_datetime = clone $start_datetime;
            $end_date = $start_date;
        }

        // Add custom search dates with +/- 2 days to properly handle ical events
        $search_start_date = clone $start_datetime;
        $search_end_date = clone $end_datetime;

        $search_start_date->modify('-2 days');
        $search_end_date->modify('+2 days');

        $all_events = wpbs_get_events(array('custom_query' => 'AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) >= ' . $search_start_date->format('Ymd') . ' AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) <= ' . $search_end_date->format('Ymd'))); // Calendar Events

        $all_groupped_events = [];
        foreach ($all_events as $event) {
            $all_groupped_events[$event->get('calendar_id')][] = $event;
        }

        // Empty array with calendars
        $available_calendars = [];

        if (empty($this->args['calendars']) || $this->args['calendars'] == 'all') {
            // All calendars
            $args = array('status' => 'active', 'orderby' => 'name', 'order' => 'asc');
        } else {
            // Specific calendars
            $calendar_ids = array_filter(array_map('trim', explode(',', $this->args['calendars'])));
            $args = array(
                'include' => $calendar_ids,
                'orderby' => 'FIELD( id, ' . implode(',', $calendar_ids) . ')',
                'order' => '',
            );
        }

        $args = apply_filters('wpbs_search_calendar_query_args', $args);

        $calendars = wpbs_get_calendars($args);

        // Loop through calendars
        foreach ($calendars as $calendar) {

            // Update ical data
            wpbs_maybe_refresh_icalendar_feed($calendar->get('id'));

            // Assume calendar dates are available
            $is_available = true;
            $default_legend_is_unbookable = false;

            // Get non bookable legend items
            $non_bookable_legend_items = array();
            $legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar->get('id')));

            $changeover_start = $changeover_end = false;

            foreach ($legend_items as $legend_item) {
                if (!$legend_item->get('is_bookable')) {
                    $non_bookable_legend_items[] = $legend_item->get('id');

                    if ($legend_item->get('is_default')) {
                        $default_legend_is_unbookable = true;
                    }
                }

                if ($legend_item->get('auto_pending') == 'changeover_start') {
                    $changeover_start = $legend_item->get('id');
                }

                if ($legend_item->get('auto_pending') == 'changeover_end') {
                    $changeover_end = $legend_item->get('id');
                }
            }

            $changeover_start_found = $changeover_end_found = 0;

            // Loop through events
            $calendar_events = isset($all_groupped_events[$calendar->get('id')]) ? $all_groupped_events[$calendar->get('id')] : [];
            $ical_events = wpbs_get_ical_feeds_as_events($calendar->get('id'), $calendar_events, $search_start_date->format('Ymd'), $search_end_date->format('Ymd')); // iCalendar Events

            $events = array_merge($calendar_events, $ical_events);

            $sorted_events = array();
            $events_prices = array();

            foreach ($events as $event) {
                if ($event->get('price')) {
                    $events_prices[$event->get('date_year') . str_pad($event->get('date_month'), 2, '0', STR_PAD_LEFT) . str_pad($event->get('date_day'), 2, '0', STR_PAD_LEFT)] = $event->get('price');
                }

                $sorted_events[$event->get('date_year') . str_pad($event->get('date_month'), 2, '0', STR_PAD_LEFT) . str_pad($event->get('date_day'), 2, '0', STR_PAD_LEFT)] = $event;
            }

            ksort($sorted_events);

            $matching_events = 0;

            $interval = DateInterval::createFromDateString('1 day');
            $start = DateTime::createFromFormat('Ymd', $start_date);
            $end = DateTime::createFromFormat('Ymd', $end_date);
            $end->modify('+1 day');
            $period = new DatePeriod($start, $interval, $end);

            $selection = [];

            foreach ($period as $date) {
                $selection[$date->format('Ymd')] = false;
            }

            foreach ($sorted_events as $event_date => $event) {

                // If event date is outside search range, continue;
                if ($event_date < $start_date || $event_date > $end_date) {
                    continue;
                }

                $event_legend_item_id = $event->get('legend_item_id') == null ? -1 : $event->get('legend_item_id');

                $matching_events++;

                // Check if we found a default legend item and if it's unbookable
                if ($event_legend_item_id === -1 && $default_legend_is_unbookable) {
                    $is_available = false;
                    break;
                }

                // Check if the event found is not bookable
                if (in_array($event_legend_item_id, $non_bookable_legend_items)) {
                    $is_available = false;
                    break;
                }

                // Check for changeovers. The rule is that if a start changeover exists in an array, we shouln't an end changeover

                // We found a starting changeover date
                if ($event_legend_item_id == $changeover_start) {
                    $changeover_start_found++;
                }

                if ($event_legend_item_id == $changeover_end) {
                    $changeover_end_found++;
                }

                // Now if we find an ending changeover date and a starting changeover date was previously found, we mark the date as not available.
                if ($event_legend_item_id == $changeover_end && $changeover_start_found !== 0) {
                    $is_available = false;
                    break;
                }

                // If more than 2 starting changeovers are found, selection is invalid
                if ($changeover_start_found !== 0 && $changeover_start_found > 1) {
                    $is_available = false;
                    break;
                }

                // If more than 2 ending changeovers are found, selection is invalid
                if ($changeover_end_found !== 0 && $changeover_end_found > 1) {
                    $is_available = false;
                    break;
                }

                $selection[$event_date] = $event_legend_item_id;
            }

            foreach ($selection as $i => $legend_id) {

                if ($legend_id === false) {
                    continue;
                }

                // Check if the selection starts with a starting changeover
                if ($i == $start_date && $legend_id == $changeover_start) {
                    $is_available = false;
                    break;
                }

                // Check if the selection ends with an ending changeover.
                if ($i == $end_date && $legend_id == $changeover_end) {
                    $is_available = false;
                    break;
                }

                // Check if we have any changeover in the middle of our selection range
                if ($i != $start_date && $i != $end_date && ($legend_id == $changeover_start || $legend_id == $changeover_end)) {
                    $is_available = false;
                    break;
                }
            }

            // If no events are found but the default legend is unbookable, it means that the date is not available.
            if (($matching_events === 0 || $matching_events != count($selection)) && $default_legend_is_unbookable === true) {
                $is_available = false;
            }

            // Get calendar post id
            $calendar_link_post_id = WPBS_Calendar_Overview_Outputter::get_calendar_link_post_id($calendar->get('id'), $this->args['language']);

            // Filter by additional field values
            if ($this->additional_search_fields && $is_available === true) {

                foreach ($this->additional_search_fields as $field) {

                    $value = $this->additional_data[$field['slug']];

                    $result_data = [
                        'calendar_id' => $calendar->get('id'),
                        'post_id' => $calendar_link_post_id,
                        'language' => $this->args['language']
                    ];

                    $field_validation_result = $field['validation']($value, $result_data);

                    if ($is_available === true && $field_validation_result === false) {
                        $is_available = false;
                    }
                }
            }

            if ($is_available === true) {

                $name = $calendar->get_name($this->args['language']);

                // Get calendar Links
                $calendar_link_type = wpbs_get_calendar_meta($calendar->get('id'), 'calendar_link_type', true);
                $calendar_link = WPBS_Calendar_Overview_Outputter::get_calendar_link($calendar->get('id'), $this->args['language']);

                if (isset($calendar_link) && !empty($calendar_link) && $calendar_link_type == 'internal' && $this->args['mark_selection'] == 'yes') {

                    $calendar_link_query_args = array(
                        'wpbs-start-year' => $start_datetime->format('Y'),
                        'wpbs-start-month' => $start_datetime->format('n'),
                        'wpbs-selection-start' => $start_datetime->format('Y-m-d'),
                        'wpbs-selection-end' => $end_datetime->format('Y-m-d'),
                    );

                    foreach ($this->additional_data as $additional_field_key => $additional_field_value) {
                        if ($additional_field_value) {
                            $calendar_link_query_args[$additional_field_key] =  esc_attr($additional_field_value);
                        }
                    }

                    $calendar_link = add_query_arg($calendar_link_query_args, $calendar_link);
                }

                // Pricing
                $calendar_price = false;

                if ($this->args['starting_price'] == 'yes' && wpbs_is_pricing_enabled()) {

                    $default_price = (float) wpbs_get_calendar_meta($calendar->get('id'), 'default_price', true);

                    // Set loop interval
                    $interval = DateInterval::createFromDateString('1 day');
                    $end_datetime_clone = clone $end_datetime;
                    if ($this->args['selection_type'] == 'single' || $start_datetime == $end_datetime_clone) {
                        $end_datetime_clone->modify('+1 day');
                    }

                    $period = new DatePeriod($start_datetime, $interval, $end_datetime_clone);
                    $days = 0;

                    $calendar_price['total'] = 0;

                    // Loop through dates
                    foreach ($period as $date) {
                        $days++;
                        if (isset($events_prices[$date->format('Ymd')]) && $events_prices[$date->format('Ymd')]) {
                            $calendar_price['total'] += $events_prices[$date->format('Ymd')];
                        } else {
                            $calendar_price['total'] += $default_price;
                        }
                    }

                    $calendar_price['per_night'] = round($calendar_price['total'] / $days, 2);
                }

                // Featured image
                $featured_image = false;
                if ($this->args['featured_image'] == 'yes' && $calendar_link_post_id && has_post_thumbnail($calendar_link_post_id)) {
                    $featured_image = get_the_post_thumbnail($calendar_link_post_id, apply_filters('wpbs_search_wiget_featured_image_size', 'large'));
                }

                $calendar_data = array('calendar_name' => $name, 'calendar_id' => $calendar->get('id'), 'link' => $calendar_link, 'post_id' => $calendar_link_post_id, 'price' => $calendar_price, 'featured_image' => $featured_image, 'additional_data' => $this->additional_data);

                $calendar_data = apply_filters('wpbs_search_outputter_available_calendars', $calendar_data, $this);

                if ($calendar_data) {
                    $available_calendars[] = $calendar_data;
                }
            }
        }

        return $available_calendars;
    }

    /**
     * Check for form errors
     *
     */
    private function check_errors()
    {

        // If form wasn't submitted, there is nothing to check
        if ($this->is_form_submitted() === false) {
            return false;
        }

        // Check if a starting day was entered
        if (empty($this->start_date)) {
            $this->has_error = true;
            $this->error = $this->get_search_widget_string('no_start_date');
            return;
        }

        // Check if an ending day was entered
        if ($this->get_search_type() == 'multiple' && empty($this->end_date)) {
            $this->has_error = true;
            $this->error = $this->get_search_widget_string('no_end_date');
            return;
        }

        foreach ($this->additional_search_fields as $field) {
            if ($field['required'] == true && empty($this->additional_data[$field['slug']])) {
                $this->has_error = true;
                $this->error = $field['required_message'];
                return;
            }
        }

        // Check if the starting day is valid
        if (DateTime::createFromFormat('Y-m-d', $this->start_date) === false) {
            $this->has_error = true;
            $this->error = $this->get_search_widget_string('invalid_start_date');
            return;
        }

        // Check if the ending day is valid
        if ($this->get_search_type() == 'multiple' && DateTime::createFromFormat('Y-m-d', $this->end_date) === false) {
            $this->has_error = true;
            $this->error = $this->get_search_widget_string('invalid_end_date');
            return;
        }
    }

    /**
     * Helper function to get custom or translated strings
     *
     */
    private function get_search_widget_string($key)
    {

        $settings = get_option('wpbs_settings', array());

        // Check for translation
        if (!empty($settings['search_addon'][$key . '_translation_' . $this->args['language']])) {
            return $settings['search_addon'][$key . '_translation_' . $this->args['language']];
        }

        // Check for default
        if (!empty($settings['search_addon'][$key])) {
            return $settings['search_addon'][$key];
        }

        return wpbs_s_search_widget_default_strings()[$key];
    }

    /**
     * Get the "selection_type" shortcode parameter.
     *
     */
    private function get_search_type()
    {

        if (isset($this->args['selection_type']) && $this->args['selection_type'] == 'single') {
            return 'single';
        }

        return 'multiple';
    }

    /**
     * Convert array to html attributes
     *
     */
    private function array_to_html_attributes($array)
    {
        return implode(' ', array_map(
            function ($k, $v) {
                return $k . '="' . esc_attr($v) . '"';
            },
            array_keys($array),
            $array
        ));
    }
}
