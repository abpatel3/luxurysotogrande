<?php
if (! defined('ABSPATH')) {
    return;
}

// Setup constants.
define('WPBS_DIVI5_MODULE_PATH', plugin_dir_path(__FILE__));
define('WPBS_DIVI5_MODULE_URL', plugin_dir_url(__FILE__));

// Divi 5 Server Renderers
require_once WPBS_DIVI5_MODULE_PATH . 'server/wpbs-divi5-single-calendar.php';
require_once WPBS_DIVI5_MODULE_PATH . 'server/wpbs-divi5-overview-calendar.php';
require_once WPBS_DIVI5_MODULE_PATH . 'server/wpbs-divi5-yearly-calendar.php';

/**
 * Enqueue Divi 5 Visual Builder Assets
 */
function wpbs_divi5_module_assets()
{
    if (et_core_is_fb_enabled() && et_builder_d5_enabled()) {

        wp_enqueue_script('wpbs-divi5-dynamic-values', WPBS_DIVI5_MODULE_URL . 'visual-builder/build/wpbs-embed-options.js');
        wp_add_inline_script(
            'wpbs-divi5-dynamic-values',
            'window.wpbsDiviCalendarOptions = ' . wpbs_get_divi_calendar_options() . '; 
            window.wpbsDiviFormOptions = ' . wpbs_get_divi_form_options() . '; 
            window.wpbsDiviLangOptions = ' . wpbs_get_divi_language_options() . ';
            window.wpbsDiviMonthsDisplayOptions = ' . wpbs_get_divi_months_display_options() . ';
            window.wpbsDiviStartMonthOptions = ' . wpbs_get_divi_start_month_options() . ';
            window.wpbsDiviStartYearOptions = ' . wpbs_get_divi_start_year_options(),
            'before'
        );

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            [
                'name'    => 'wpbs-divi5-single-calendar-visual-builder',
                'version' => '1.0.0',
                'script'  => [
                    'src'                => WPBS_DIVI5_MODULE_URL . 'visual-builder/build/wpbs-divi5-single-calendar.js',
                    'deps'               => [
                        'react',
                        'jquery',
                        'divi-module-library',
                        'wp-hooks',
                        'divi-rest',
                    ],
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ],
            ]
        );

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            [
                'name'    => 'wpbs-divi5-overview-calendar-visual-builder',
                'version' => '1.0.0',
                'script'  => [
                    'src'                => WPBS_DIVI5_MODULE_URL . 'visual-builder/build/wpbs-divi5-overview-calendar.js',
                    'deps'               => [
                        'react',
                        'jquery',
                        'divi-module-library',
                        'wp-hooks',
                        'divi-rest',
                    ],
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ],
            ]
        );

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            [
                'name'    => 'wpbs-divi5-yearly-calendar-visual-builder',
                'version' => '1.0.0',
                'script'  => [
                    'src'                => WPBS_DIVI5_MODULE_URL . 'visual-builder/build/wpbs-divi5-yearly-calendar.js',
                    'deps'               => [
                        'react',
                        'jquery',
                        'divi-module-library',
                        'wp-hooks',
                        'divi-rest',
                    ],
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ],
            ]
        );
    }
}

add_action('divi_visual_builder_assets_before_enqueue_scripts', 'wpbs_divi5_module_assets');

function wpbs_get_divi_calendar_options()
{

    if (! function_exists('wpbs_get_calendars')) {
        return [];
    }

    $calendars = wpbs_get_calendars(array('status' => 'active'));

    if (empty($calendars)) {
        return [];
    }

    $values = [];

    foreach ($calendars as $calendar) {
        $values[$calendar->get('id')] = ['label' => $calendar->get('name') . ' (ID: ' . $calendar->get('id') . ')'];
    }

    return json_encode($values);
}

function wpbs_get_divi_form_options()
{

    if (! function_exists('wpbs_get_forms')) {
        return [];
    }

    $forms = wpbs_get_forms(array('status' => 'active'));

    if (empty($forms)) {
        return [];
    }

    $values = [0 => ['label' => __('No Form', 'wp-booking-system')]];

    foreach ($forms as $form) {
        $values[$form->get('id')] = ['label' => $form->get('name') . ' (ID: ' . $form->get('id') . ')'];
    }

    return json_encode($values);
}

function wpbs_get_divi_language_options()
{

    if (! function_exists('wpbs_get_languages')) {
        return [];
    }

    $langs = wpbs_get_languages();

    $settings = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    $values = ['auto' => ['label' => __('Auto (Let WP Choose)', 'wp-booking-system')]];

    foreach ($active_languages as $id) {
        $values[$id] = ['label' => $langs[$id]];
    }

    return json_encode($values);
}

function wpbs_get_divi_start_year_options()
{

    $values = ['0' => ['label' => __('Current Year', 'wp-booking-system')]];

    for ($i = date('Y'); $i <= date('Y') + 10; $i++) {
        $values[$i] = array('label' => (string) $i);
    }

    return json_encode($values);
}

function wpbs_get_divi_start_month_options()
{

    $values = ['0' => ['label' => __('Current Month', 'wp-booking-system')]];


    $months = [
        __('January', 'wp-booking-system'),
        __('February', 'wp-booking-system'),
        __('March', 'wp-booking-system'),
        __('April', 'wp-booking-system'),
        __('May', 'wp-booking-system'),
        __('June', 'wp-booking-system'),
        __('July', 'wp-booking-system'),
        __('August', 'wp-booking-system'),
        __('September', 'wp-booking-system'),
        __('October', 'wp-booking-system'),
        __('November', 'wp-booking-system'),
        __('December', 'wp-booking-system'),
    ];

    foreach ($months as $i => $item) {
        $values[($i + 1)] =  array('label' => $item);
    }

    return json_encode($values, JSON_FORCE_OBJECT);
}

function wpbs_get_divi_months_display_options()
{
    $values = array();

    for ($i = 1; $i <= 24; $i++) {
        $values[$i] = ['label' => $i];
    }

    return json_encode($values);
}
