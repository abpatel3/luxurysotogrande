<?php
if (!class_exists('VC_WPBS_Search_Widget') && class_exists('WPBakeryShortCode')) {

    class VC_WPBS_Search_Widget extends WPBakeryShortCode
    {

        //Initialize Component
        function __construct()
        {
            add_action('init', array($this, 'create_shortcode'), 999);
            add_shortcode('vc_wpbs_search_widget', array($this, 'render_shortcode'));
        }

        public function create_shortcode()
        {
            // Stop all if VC is not enabled
            if (!defined('WPB_VC_VERSION')) {
                return;
            }

            $settings = get_option('wpbs_settings');
            $languages = wpbs_get_languages();
            $languagesDropdown = array('Auto' => 'auto');

            if (!empty($settings['active_languages'])) {

                foreach ($settings['active_languages'] as $key => $code) {

                    if (empty($languages[$code])) {
                        continue;
                    }

                    $languagesDropdown[$languages[$code]] = $code;
                }
            }

            // Map blockquote with vc_map()
            vc_map(array(
                'name'          => __('Search Widget', 'wp-booking-system-search'),
                'base'          => 'vc_wpbs_search_widget',
                'description'   => __('', 'wp-booking-system-search'),
                'category'      => 'WP Booking System',
                'params' => array(

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Widget Title', "wp-booking-system-search"),
                        'param_name' => 'title',
                        'value' => array(
                            __('Yes', 'wp-booking-system-search') => 'yes',
                            __('No', 'wp-booking-system-search') => 'no',
                        ),
                    ),
                    
                    array(
                        'type' => 'dropdown',
                        'heading' => __('Language', "wp-booking-system-search"),
                        'param_name' => 'language',
                        'value' => $languagesDropdown
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Week Start Day', "wp-booking-system-search"),
                        'param_name' => 'start',
                        'value' => array(
                            __('Monday', 'wp-booking-system-search') => '1',
                            __('Tuesday', 'wp-booking-system-search') => '2',
                            __('Wednesday', 'wp-booking-system-search') => '3',
                            __('Thursday', 'wp-booking-system-search') => '4',
                            __('Friday', 'wp-booking-system-search') => '5',
                            __('Saturday', 'wp-booking-system-search') => '6',
                            __('Sunday', 'wp-booking-system-search') => '7',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Automatically Mark Selection', "wp-booking-system-search"),
                        'param_name' => 'automatically_mark_selection',
                        'value' => array(
                            __('Yes', 'wp-booking-system-search') => 'yes',
                            __('No', 'wp-booking-system-search') => 'no',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Selection Type', "wp-booking-system-search"),
                        'param_name' => 'selection_type',
                        'value' => array(
                            __('Date Range', 'wp-booking-system-search') => 'multiple',
                            __('Single Day', 'wp-booking-system-search') => 'single',
                        ),
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => __('Minimum Stay', "wp-booking-system-search"),
                        'param_name' => 'minimum_stay',
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show Featured Image', "wp-booking-system-search"),
                        'param_name' => 'show_featured_image',
                        'value' => array(
                            __('No', 'wp-booking-system-search') => 'no',
                            __('Yes', 'wp-booking-system-search') => 'yes',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show Starting Price', "wp-booking-system-search"),
                        'param_name' => 'show_starting_price',
                        'value' => array(
                            __('No', 'wp-booking-system-search') => 'no',
                            __('Yes', 'wp-booking-system-search') => 'yes',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show Results on load', "wp-booking-system-search"),
                        'param_name' => 'show_results_on_load',
                        'value' => array(
                            __('No', 'wp-booking-system-search') => 'no',
                            __('Yes', 'wp-booking-system-search') => 'yes',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Results Layout', "wp-booking-system-search"),
                        'param_name' => 'results_layout',
                        'value' => array(
                            __('List', 'wp-booking-system-search') => 'list',
                            __('Grid', 'wp-booking-system-search') => 'grid',
                        ),
                    ),

                    array(
                        'type' => 'textfield',
                        'heading' => __('Results per page', "wp-booking-system-search"),
                        'param_name' => 'results_per_page',
                    ),

                    //url
                    array(
                        'type' => 'textfield',
                        'heading' => __('Redirect', "wp-booking-system-search"),
                        'param_name' => 'redirect',
                        
                    ),

                ),
            ));
        }

        public function render_shortcode($atts, $content, $tag)
        {
            echo WPBS_S_Shortcodes::search_widget($atts);
        }
    }

    new VC_WPBS_Search_Widget();
}
