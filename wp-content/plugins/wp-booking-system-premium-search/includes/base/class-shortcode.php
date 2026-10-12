<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_S_Shortcodes
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {

        // Register the single search-widget
        add_shortcode('wpbs-search', array(__CLASS__, 'search_widget'));
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
     * The callback for the Search Widget shortcode
     *
     * @param array $atts
     *
     */
    public static function search_widget($atts)
    {

        if (!wpbs_check_if_scripts_and_styles_are_enqueued()) {
            return self::scripts_and_styles_not_enqueued_notification();
        }

        // Shortcode default attributes
        $default_atts = wpbs_s_get_search_widget_default_args();

        // Shortcode attributes
        $args = shortcode_atts($default_atts, $atts);

        $search_widget_outputter = new WPBS_S_Search_Widget_Outputter($args);

        $output = $search_widget_outputter->get_display();

        return apply_filters('wpbs_shortcode_search_widget_output', $output);
    }
}

// Init shortcodes
new WPBS_S_Shortcodes();
