<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizes the values of an array recursively using sanitize_text_field
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_sanitize_text_field($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_sanitize_text_field($value);
        } else {
            $array[$key] = sanitize_text_field($value);
        }
    }

    return $array;
}

/**
 * Sanitizes the values of an array recursively using sanitize_textarea_field
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_sanitize_textarea_field($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_sanitize_textarea_field($value);
        } else {
            $array[$key] = sanitize_textarea_field($value);
        }
    }

    return $array;
}

function _wpbs_recursive_array_replace($find, $replace, $array)
{
    if (!is_array($array)) {
        return str_replace($find, $replace, $array);
    }
    $newArray = array();
    foreach ($array as $key => $value) {
        $newArray[$key] = _wpbs_recursive_array_replace($find, $replace, $value);
    }
    return $newArray;
}

/**
 * Sanitizes the values of an array recursively and allows HTML tags
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_wp_kses_post($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_wp_kses_post($value);
        } else {
            $array[$key] = wp_kses_post($value);
        }
    }

    return $array;
}

/**
 * Escapes the values of an array recursively using esc_attr
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_text_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_text_field($value);
        } else {
            $array[$key] = esc_attr($value);
        }
    }

    return $array;
}

/**
 * Escapes the values of an array recursively using esc_textarea
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_textarea_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_textarea_field($value);
        } else {
            $array[$key] = esc_textarea($value);
        }
    }

    return $array;
}

/**
 * Helper function to search an array
 *
 * @param string
 * @param array
 *
 * @return bool
 *
 */
function _wpbs_recursive_array_search($needle, $haystack)
{
    foreach ($haystack as $key => $value) {
        if ($key === 'user_value') {
            continue;
        }

        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && _wpbs_recursive_array_search($needle, $value) !== false)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Generates and returns a random 32 character long string
 *
 * @return string
 *
 */
function wpbs_generate_hash()
{

    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length = strlen($chars);
    $hash = '';

    for ($i = 0; $i < 19; $i++) {

        $hash .= $chars[rand(0, $chars_length - 1)];
    }

    return $hash . uniqid();
}

/**
 * Replace date_i18n with wp_date, but fallback to date_i18n if WP < 5.3
 *
 * @param string $format
 * @param string $timestamp
 * @param string $language
 *
 * @return string
 *
 */
function wpbs_date_i18n($format, $timestamp, $language = false)
{

    if ($language !== false) {
        $original_locale = get_locale();
        switch_to_locale(wpbs_language_to_locale($language));
    }

    if (function_exists('wp_date')) {
        $zone = apply_filters('wpbs_date_timezone', new DateTimeZone('UTC'));
        $date = wp_date($format, $timestamp, $zone);
    } else {
        $date = date_i18n($format, $timestamp);
    }

    if ($language !== false) {
        switch_to_locale($original_locale);
    }

    return $date;
}

/**
 * Get users IP Address
 *
 */
function wpbs_get_user_ip_address()
{
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
            $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Check if a date is contained within a range of dates
 * Works for recurring dates.
 * 
 * @param DateTime $start
 * @param DateTime $end
 * @param DateTime $compare
 * 
 * @return bool
 * 
 */
function wpbs_is_date_in_range($start, $end, $compare)
{
    if ($start < $end) {
        return $start <= $compare && $compare <= $end;
    }

    return $compare >= $start || $compare <= $end;
}

/**
 * Get the time when scheduled emails should go out
 * 
 * @return int
 * 
 */
function wpbs_scheduled_email_delivery_hour()
{
    $settings = get_option('wpbs_settings');

    if (!isset($settings['when_to_send_hour'])) {
        $hour = 12;
    } else {
        $hour = absint($settings['when_to_send_hour']);
    }

    $hour = $hour - get_option('gmt_offset');

    return $hour * HOUR_IN_SECONDS;
}


/**
 * Get the "permalink"  of the current page,
 * regardless of post type, archive, search, front page, etc.
 *
 * @return string 
 */
function wpbs_get_current_permalink()
{

    if (is_singular()) {
        return get_permalink();
    }

    if (is_front_page() || is_home()) {
        return home_url('/');
    }

    if (is_post_type_archive()) {
        $pt = get_query_var('post_type');
        return get_post_type_archive_link($pt);
    }

    if (is_date()) {
        $year     = get_query_var('year');
        $monthnum = get_query_var('monthnum');
        $day      = get_query_var('day');

        if ($day) {
            return get_day_link($year, $monthnum, $day);
        } elseif ($monthnum) {
            return get_month_link($year, $monthnum);
        } else {
            return get_year_link($year);
        }
    }

    if (is_category()) {
        return get_category_link(get_queried_object_id());
    }
    if (is_tag()) {
        return get_tag_link(get_queried_object_id());
    }
    if (is_tax()) {
        return get_term_link(get_queried_object());
    }

    if (is_author()) {
        return get_author_posts_url(get_queried_object_id());
    }

    if (is_search()) {
        return home_url('/?s=' . rawurlencode(get_search_query(false)));
    }

    if (is_404()) {
        return home_url('/404');
    }

    $scheme = is_ssl() ? 'https://' : 'http://';
    $host   = $_SERVER['HTTP_HOST'];
    $uri    = $_SERVER['REQUEST_URI'];

    return esc_url_raw($scheme . $host . $uri);
}
/**
 * Safe sprintf function that doesn't throw an error for mismatched arguments
 * 
 */
function wpbs_sprintf(string $format, ...$args): string
{
    $sentinel = "\x1A";
    $format = str_replace('%%', $sentinel, $format);

    preg_match_all('/%(\d+)\$s/', $format, $numMatches);
    $maxIndex = !empty($numMatches[1]) ? max(array_map('intval', $numMatches[1])) : 0;

    if (strpos($format, '%s') !== false) {
        $i = $maxIndex;
        $format = preg_replace_callback('/%s/', function () use (&$i) {
            $i++;
            return '%' . $i . '$s';
        }, $format);
        $maxIndex = max($maxIndex, $i);
    }

    if (strpos($format, '%') !== false) {
        $i = $maxIndex;
        $format = preg_replace_callback('/%(?!\d+\$s)/', function () use (&$i) {
            $i++;
            return '%' . $i . '$s';
        }, $format);
        $maxIndex = max($maxIndex, $i);
    }

    if ($maxIndex === 0) {
        return str_replace($sentinel, '%', $format);
    }

    if (count($args) < $maxIndex) {
        $args = array_pad($args, (int) $maxIndex, '');
    }

    try {
        $out = @vsprintf($format, $args);
        if ($out === false) {
            $out = preg_replace('/%(\d+\$)?s/', '', $format);
        }
    } catch (ValueError $e) {
        $out = preg_replace('/%(\d+\$)?s/', '', $format);
    }

    return str_replace($sentinel, '%', $out);
}

/**
 * Refresh an icalendar feed
 * 
 * @param int $calendar_id
 * 
 */
add_action('init', function () {
    if (!isset($_GET['wpbs-refresh-ical'])) {
        return;
    }

    $start = microtime(true);

    echo "<pre>";

    echo 'Refreshing icalendar feeds.' . PHP_EOL . PHP_EOL;

    $calendars = wpbs_get_calendars(['status' => 'active']);

    $num_calendars = 0;
    $num_feeds = 0;

    foreach ($calendars as $calendar) {
        $calendar_id = $calendar->get('id');

        // Get iCal feeds
        $ical_feeds = wpbs_get_calendar_meta_ical_feeds($calendar_id);

        echo 'Refreshing feeds for calendar #' . $calendar_id . '.' . PHP_EOL;

        if (empty($ical_feeds)) {
            echo '- no feeds found.' . PHP_EOL . PHP_EOL;
            continue;
        }

        $num_calendars++;

        // Fetch new feeds
        foreach ($ical_feeds as $ical_feed) {

            if (empty($ical_feed['id']))
                continue;

            if (empty($ical_feed['url']))
                continue;

            $num_feeds++;

            echo '- refreshing <strong>' . $ical_feed['name'] . '</strong> (' . $ical_feed['url'] . ') for calendar #' . $calendar_id . '.' . PHP_EOL;

            $ical_contents = wp_remote_get($ical_feed['url'], array('timeout' => 30, 'user-agent' => 'Mozilla/5.0 ( Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0'));

            if (wp_remote_retrieve_response_code($ical_contents) != 200)
                continue;

            $ical_contents = wp_remote_retrieve_body($ical_contents);

            $ical_contents = trim($ical_contents);

            if (0 !== strpos($ical_contents, 'BEGIN:VCALENDAR') || false === strpos($ical_contents, 'END:VCALENDAR'))
                continue;

            $ical_contents = addslashes($ical_contents);

            $ical_feed['file_contents'] = $ical_contents;
            $ical_feed['last_updated']  = current_time('Y-m-d H:i:s');

            wpbs_update_calendar_meta($calendar_id, 'ical_feed_' . $ical_feed['id'], $ical_feed);
        }

        echo PHP_EOL;
    }

    $end = microtime(true);

    $time = $end - $start;

    echo "Refreshing icalendar feeds completed in " . round($time, 2) . " seconds. " . PHP_EOL . $num_feeds . " feeds were updated for " . $num_calendars . " calendars." . PHP_EOL;
    echo "</pre>";



    die();
});
