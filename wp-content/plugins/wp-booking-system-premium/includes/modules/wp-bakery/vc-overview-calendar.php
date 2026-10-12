<?php
if (!class_exists('VC_WPBS_Overview_Calendar') && class_exists('WPBakeryShortCode')) {

    class VC_WPBS_Overview_Calendar extends WPBakeryShortCode
    {

        //Initialize Component
        function __construct()
        {
            add_action('init', array($this, 'create_shortcode'), 999);
            add_shortcode('vc_wpbs_overview_calendar', array($this, 'render_shortcode'));
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
                'name'          => __('Overview Calendar', 'wp-booking-system'),
                'base'          => 'vc_wpbs_overview_calendar',
                'description'   => __('', 'wp-booking-system'),
                'category'      => 'WP Booking System',
                'params' => array(

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
                        'heading' => __('Language', "wp-booking-system"),
                        'param_name' => 'language',
                        'value' => $languagesDropdown
                    ),

                ),
            ));
        }

        public function content($atts, $content = ''){
            $this->render_shortcode($atts, $content, '');
        }

        public function render_shortcode($atts, $content, $tag)
        {
            return WPBS_Shortcodes::calendar_overview($atts);
        }
    }

    new VC_WPBS_Overview_Calendar();
}
