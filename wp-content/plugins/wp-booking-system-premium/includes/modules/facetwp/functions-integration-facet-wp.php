<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add WP Booking System as a Facet source
 *
 * @param array $sources
 *
 * @return array
 *
 */
function wpbs_facetwp_facet_sources($sources)
{
    $sources['wpbs'] = array(
        'label' => __('WP Booking System', 'wp-booking-system'),
        'choices' => [
            'wpbs_date' => __('Calendar Dates', 'wp-booking-system'),
        ],
        'weight' => 10,
    );

    return $sources;
}
add_filter('facetwp_facet_sources', 'wpbs_facetwp_facet_sources', 10, 1);

/**
 * Filter results
 *
 * @param bool|array $return
 * @param array $params
 *
 * @return bool|array
 *
 */

function wpbs_facetwp_facet_filter_posts($return, $params)
{

    // Check if Facet type is "Date Range" and source is "WP Booking System", and both fields are present
    if ($params['facet']['type'] != 'date_range' || $params['facet']['source'] != 'wpbs_date' || $params['facet']['fields'] != 'both') {
        return $return;
    }

    // Check if both start and end date are selected
    if (empty($params['selected_values'][0]) || empty($params['selected_values'][1])) {
        return 'continue';
    }

    // Search through calendars
    $date = DateTime::createFromFormat('Y-m-d', $params['selected_values'][0]);
    $search_start_date = clone $date;
    $original_search_start_date = clone $date;
    $start_date = $date->format('Ymd');

    // Format end date
    $date = DateTime::createFromFormat('Y-m-d', $params['selected_values'][1]);
    $search_end_date = clone $date;
    $end_date = $date->format('Ymd');

    // Add custom search dates with +/- 2 days to properly handle ical events
    $search_start_date->modify('-2 days');
    $search_end_date->modify('+2 days');

    $matches = [];

    $all_events = wpbs_get_events(array('custom_query' => 'AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) >= ' . $search_start_date->format('Ymd') . ' AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) <= ' . $search_end_date->format('Ymd'))); // Calendar Events

    $all_groupped_events = [];
    foreach ($all_events as $event) {
        $all_groupped_events[$event->get('calendar_id')][] = $event;
    }

    // Get all calendars
    $calendars = wpbs_get_calendars(array('status' => 'active', 'orderby' => 'name', 'order' => 'asc'));

    foreach ($calendars as $calendar) {

        $available = false;

        wpbs_maybe_refresh_icalendar_feed($calendar->get('id'));

        // Check if calendar has a page attached to it
        $calendar_link = wpbs_get_translated_calendar_meta($calendar->get('id'), 'calendar_link_internal', wpbs_get_site_language());

        if (empty($calendar_link)) {
            continue;
        }

        $events = isset($all_groupped_events[$calendar->get('id')]) ? $all_groupped_events[$calendar->get('id')] : [];

        // If date is available, add it to $matches
        if (wpbs_check_if_date_range_is_bookable($calendar->get('id'), $start_date, $end_date, $events) === true) {
            $available = true; 
        }

        if(apply_filters('wpbs_facetwp_filter_by_minumum_stay', false) == true){
            foreach($events as $event){
                if ($event->get('date_year') == $original_search_start_date->format('Y') && $event->get('date_month') == $original_search_start_date->format('m') && $event->get('date_day') == $original_search_start_date->format('d')) {
                    if(absint($event->get('minimum_stay')) > ($end_date - $start_date) ){
                        $available = false;
                        break;
                    }
                }
            }
        }
        

        if($available) {
            $matches[] = $calendar_link;
        }
    }

    if (!empty($matches)) {
        return $matches;
    }

    return $return;
}
add_filter('facetwp_facet_filter_posts', 'wpbs_facetwp_facet_filter_posts', 10, 2);

//Add current date to permalink

function wpbs_facetwp_query_string($permalink, $post, $leavename)
{
    // Check if a Date Facet is selected
    if (!isset($_GET['fwp_date']) && !isset($_POST['data']['http_params']['get']['fwp_date'])) {
        return $permalink;
    }

    $fwp_date = explode(',', (isset($_GET['fwp_date'])) ? $_GET['fwp_date'] : urldecode($_POST['data']['http_params']['get']['fwp_date']));

    $fwp_date = array_filter($fwp_date);

    //Check if start and end date are both present
    if (count($fwp_date) != 2) {
        return $permalink;
    }

    // Get start and end dates
    list($start_date, $end_date) = $fwp_date;

    $date = DateTime::createFromFormat("Y-m-d", $start_date);

    // Add them to the permalink
    $permalink = add_query_arg(array(
        'wpbs-start-month' => $date->format('n'),
        'wpbs-start-year' => $date->format('Y'),
        'wpbs-selection-start' => $start_date,
        'wpbs-selection-end' => $end_date,
    ), $permalink);

    return $permalink;
}
add_filter('post_link', 'wpbs_facetwp_query_string', 10, 3);
add_filter('page_link', 'wpbs_facetwp_query_string', 10, 3);
add_filter('post_type_link', 'wpbs_facetwp_query_string', 10, 3);


/**
 * Trick the indexer to properly display the date selector
 * 
 */
add_filter('facetwp_index_row', function ($params, $class) {
    $dates = range(date('Y'), date('Y') + 10);
    if ('wpbs_date' == $params['facet_source']) {
        foreach ($dates as $date) {
            $params['facet_value'] = $date;
            $params['facet_display_value'] = $date;
            $class->insert($params);
        }
    }

    return $params;
}, 10, 2);

add_filter('facetwp_indexer_row_data', function ($rows, $params) {
    if ('wpbs_date' == $params['facet']['source']) {
        $new_row = $params['defaults'];
        $new_row['facet_value'] = '';
        $new_row['facet_display_value'] = '';
        $rows[] = $new_row;
    }
    return $rows;
}, 10, 2);
