<?php
if (!class_exists('VC_WPBS_Yearly_Calendar') && class_exists('WPBakeryShortCode')) {

    class VC_WPBS_Yearly_Calendar extends WPBakeryShortCode
    {

        //Initialize Component
        function __construct()
        {
            add_action('init', array($this, 'create_shortcode'), 999);
            add_shortcode('vc_wpbs_yearly_calendar', array($this, 'render_shortcode'));
        }

        public function create_shortcode()
        {
            // Stop all if VC is not enabled
            if (!defined('WPB_VC_VERSION')) {
                return;
            }

            $calendars = wpbs_get_calendars(array('status' => 'active'));
            $calendarDropdown = array('0' => '-');
            foreach ($calendars as $calendar) {
                $calendarDropdown[$calendar->get('name') . ' (ID:' . $calendar->get('id') . ')'] = $calendar->get('id');
            }

            $forms = wpbs_get_forms(array('status' => 'active'));
            $formDropdown = array('0' => '-');
            foreach ($forms as $form) {
                $formDropdown[$form->get('name') . ' (ID:' . $form->get('id') . ')'] = $form->get('id');
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
                'name'          => __('Yearly Calendar', 'wp-booking-system'),
                'base'          => 'vc_wpbs_yearly_calendar',
                'description'   => __('', 'wp-booking-system'),
                'category'      => 'WP Booking System',
                'params' => array(

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Calendar', "wp-booking-system"),
                        'param_name' => 'id',
                        'value' => $calendarDropdown
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Form', "wp-booking-system"),
                        'param_name' => 'form_id',
                        'value' => $formDropdown
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Display Calendar Title', "wp-booking-system"),
                        'param_name' => 'title',
                        'value' => array(
                            __('Yes', 'wp-booking-system') => 'yes',
                            __('No', 'wp-booking-system') => 'no',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Display Legend', "wp-booking-system"),
                        'param_name' => 'legend',
                        'value' => array(
                            __('Yes', 'wp-booking-system') => 'yes',
                            __('No', 'wp-booking-system') => 'no',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Legend Position', "wp-booking-system"),
                        'param_name' => 'legend_position',
                        'value' => array(
                            __('Side', 'wp-booking-system') => 'side',
                            __('Top', 'wp-booking-system') => 'top',
                            __('Bottom', 'wp-booking-system') => 'bottom'
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Start Year', "wp-booking-system"),
                        'param_name' => 'year',
                        'value' => [__('Current Year', 'wp-booking-system') => 0] + array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10)),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Display Dropdown', "wp-booking-system"),
                        'param_name' => 'dropdown',
                        'value' => array(
                            __('Yes', 'wp-booking-system') => 'yes',
                            __('No', 'wp-booking-system') => 'no',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show History', "wp-booking-system"),
                        'param_name' => 'history',
                        'value' => array(
                            __('Display booking history', 'wp-booking-system') => '1',
                            __('Replace booking history with the default legend item', 'wp-booking-system') => '2',
                            __('Use the Booking History Color from the Settings', 'wp-booking-system') => '3',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Display Tooltips', "wp-booking-system"),
                        'param_name' => 'tooltip',
                        'value' => array(
                            __('No', 'wp-booking-system') => '1',
                            __('Yes', 'wp-booking-system') => '2',
                            __('Yes, with red indicator', 'wp-booking-system') => '3',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Highlight Today', "wp-booking-system"),
                        'param_name' => 'highlighttoday',
                        'value' => array(
                            __('No', 'wp-booking-system') => 'no',
                            __('Yes', 'wp-booking-system') => 'yes',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show Prices', "wp-booking-system"),
                        'param_name' => 'show_prices',
                        'value' => array(
                            __('No', 'wp-booking-system') => 'no',
                            __('Yes', 'wp-booking-system') => 'yes',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Language', "wp-booking-system"),
                        'param_name' => 'language',
                        'value' => $languagesDropdown
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Form Position', "wp-booking-system"),
                        'param_name' => 'form_position',
                        'value' => array(
                            __('Bottom', 'wp-booking-system') => 'bottom',
                            __('Side', 'wp-booking-system') => 'side',
                        )
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Automatically Block Dates', "wp-booking-system"),
                        'param_name' => 'auto_pending',
                        'value' => array(
                            __('Yes', 'wp-booking-system') => 'yes',
                            __('No', 'wp-booking-system') => 'no',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Selection Type', "wp-booking-system"),
                        'param_name' => 'selection_type',
                        'value' => array(
                            __('Date Range', 'wp-booking-system') => 'multiple',
                            __('Single Day', 'wp-booking-system') => 'single',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Selection Style', "wp-booking-system"),
                        'param_name' => 'selection_style',
                        'value' => array(
                            __('Normal', 'wp-booking-system') => 'normal',
                            __('Split', 'wp-booking-system') => 'split',
                        ),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show Date Selection', "wp-booking-system"),
                        'param_name' => 'show_date_selection',
                        'value' => array(
                            __('No', 'wp-booking-system') => 'no',
                            __('Yes', 'wp-booking-system') => 'yes',
                        ),
                    ),

                ),
            ));
        }

        public function content($atts, $content = ''){
            $this->render_shortcode($atts, $content, '');
        }

        public function render_shortcode($atts, $content, $tag)
        {
            return WPBS_Shortcodes::yearly_calendar($atts);
        }
    }

    new VC_WPBS_Yearly_Calendar();
}
