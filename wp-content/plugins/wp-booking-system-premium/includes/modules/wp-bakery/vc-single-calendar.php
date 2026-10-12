<?php
if (!class_exists('VC_WPBS_Single_Calendar') && class_exists('WPBakeryShortCode')) {

    class VC_WPBS_Single_Calendar extends WPBakeryShortCode
    {

        //Initialize Component
        function __construct()
        {
            add_action('init', array($this, 'create_shortcode'), 999);
            add_shortcode('vc_wpbs_single_calendar', array($this, 'render_shortcode'));
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
                'name'          => __('Single Calendar', 'wp-booking-system'),
                'base'          => 'vc_wpbs_single_calendar',
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
                        'heading' => __('Months to Display', "wp-booking-system"),
                        'param_name' => 'display',
                        'value' => array_combine(range(1, 24), range(1, 24)),
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Start Year', "wp-booking-system"),
                        'param_name' => 'year',
                        'value' => [__('Current Year', 'wp-booking-system') => 0] + array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10)),
                    ),


                    array(
                        'type' => 'dropdown',
                        'heading' => __('Start Month', "wp-booking-system"),
                        'param_name' => 'month',
                        'value' => array(
                            __('Current Month', 'wp-booking-system') => 0,
                            __('January', 'wp-booking-system') => 1,
                            __('February', 'wp-booking-system') => 2,
                            __('March', 'wp-booking-system') => 3,
                            __('April', 'wp-booking-system') => 4,
                            __('May', 'wp-booking-system') => 5,
                            __('June', 'wp-booking-system') => 6,
                            __('July', 'wp-booking-system') => 7,
                            __('August', 'wp-booking-system') => 8,
                            __('September', 'wp-booking-system') => 9,
                            __('October', 'wp-booking-system') => 10,
                            __('November', 'wp-booking-system') => 11,
                            __('December', 'wp-booking-system') => 12
                        )
                    ),

                    array(
                        'type' => 'dropdown',
                        'heading' => __('Show First Available Month', "wp-booking-system"),
                        'param_name' => 'show_first_available_date',
                        'value' => array(
                            __('No', 'wp-booking-system') => 'no',
                            __('Yes', 'wp-booking-system') => 'yes',
                        ),
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
                        'heading' => __('Week Start Day', "wp-booking-system"),
                        'param_name' => 'start',
                        'value' => array(
                            __('Monday', 'wp-booking-system') => '1',
                            __('Tuesday', 'wp-booking-system') => '2',
                            __('Wednesday', 'wp-booking-system') => '3',
                            __('Thursday', 'wp-booking-system') => '4',
                            __('Friday', 'wp-booking-system') => '5',
                            __('Saturday', 'wp-booking-system') => '6',
                            __('Sunday', 'wp-booking-system') => '7',
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
                        'heading' => __('Show Week Numbers', "wp-booking-system"),
                        'param_name' => 'weeknumbers',
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
                            __('Split', 'wp-booking-system') => 'split',
                            __('Normal', 'wp-booking-system') => 'normal',
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
            return WPBS_Shortcodes::single_calendar($atts);
        }
    }

    new VC_WPBS_Single_Calendar();
}
