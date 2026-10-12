<?php

namespace WPBS_Divi5_Modules;

if (! defined('ABSPATH')) {
    return;
}

if(!file_exists(ABSPATH . 'wp-content/themes/Divi/includes/builder-5/server/Framework/DependencyManagement/Interfaces/DependencyInterface.php')){
    return;
}

require_once ABSPATH . 'wp-content/themes/Divi/includes/builder-5/server/Framework/DependencyManagement/Interfaces/DependencyInterface.php';

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Simple Quick Module" module output in frontend.
 */
class WPBS_Divi5_Single_Calendar implements DependencyInterface
{
    /**
     * Register module.
     * `DependencyInterface` interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [WPBS_Divi5_Single_Calendar::class, 'register_module']);
    }

    /**
     * Register module.
     */
    public static function register_module()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/single-calendar';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [WPBS_Divi5_Single_Calendar::class, 'render_callback'],
            ]
        );
    }

    /**
     * Render module style.
     */
    public static function module_styles($args)
    {
        $attrs    = $args['attrs'] ?? [];
        $elements = $args['elements'];

        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    // Module.
                    $elements->style(
                        [
                            'attrName'   => 'module',
                            'styleProps' => [
                                'disabledOn' => [
                                    'disabledModuleVisibility' => $args['settings']['disabledModuleVisibility'] ?? null,
                                ],
                            ],
                        ]
                    ),
                    // Title.
                    $elements->style(
                        [
                            'attrName' => 'title',
                        ]
                    ),
                    // Content.
                    $elements->style(
                        [
                            'attrName' => 'content',
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Render module script data.
     */
    public static function module_script_data($args)
    {
        $elements = $args['elements'];

        // Element Script Data Options.
        $elements->script_data(
            [
                'attrName' => 'module',
            ]
        );
    }

    /**
     * Render module classnames.
     */
    public static function module_classnames($args)
    {
        $classnames_instance = $args['classnamesInstance'];
        $attrs               = $args['attrs'];

        // Module.
        // $classnames_instance->add(
        //     ElementClassnames::classnames(
        //         [
        //             'attrs' => $attrs['module']['decoration'] ?? [],
        //         ]
        //     )
        // );
    }

    /**
     * Render module HTML output.
     */
    public static function render_callback($attrs, $content, $block, $elements)
    {

        $get = function ($key, $default = '') use ($attrs) {
            return $attrs[$key]['innerContent']['desktop']['value'] ?? $default;
        };

        $id = $get('id', '');


        $show_title                = $get('show_title', 'yes');
        $show_legend               = $get('show_legend', 'yes');
        $legend_position           = $get('legend_position', 'side');
        $months                    = $get('months', '1');
        $start_year                = $get('start_year', '0');
        $start_month               = (string) $get('start_month', '0');
        $show_first_available_date = $get('show_first_available_date', 'no');
        $language                  = $get('language', 'auto');
        $start                     = $get('start', '1');
        $dropdown                  = $get('dropdown', 'yes');
        $history                   = $get('history', '1');
        $tooltip                   = $get('tooltip', '1');
        $highlighttoday            = $get('highlighttoday', 'no');
        $weeknumbers               = $get('weeknumbers', 'no');
        $show_prices               = $get('show_prices', 'no');

        $form_id                   = $get('form_id', '');
        $form_position             = $get('form_position', 'bottom');
        $auto_pending              = $get('auto_pending', 'yes');
        $selection_type            = $get('selection_type', 'multiple');
        $selection_style           = $get('selection_style', 'normal');
        $minimum_days              = $get('minimum_days', '0');
        $maximum_days              = $get('maximum_days', '0');
        $booking_start_day         = $get('booking_start_day', '0');
        $booking_end_day           = $get('booking_end_day', '0');
        $show_date_selection       = $get('show_date_selection', 'no');

        $parts = [
            'id="' . esc_attr($id) . '"',
            'title="' . esc_attr($show_title) . '"',
            'legend="' . esc_attr($show_legend) . '"',
            'legend_position="' . esc_attr($legend_position) . '"',
            'display="' . esc_attr($months) . '"',
            'year="' . esc_attr($start_year) . '"',
            'month="' . esc_attr($start_month) . '"',
            'show_first_available_date="' . esc_attr($show_first_available_date) . '"',
            'language="' . esc_attr($language) . '"',
            'start="' . esc_attr($start) . '"',
            'dropdown="' . esc_attr($dropdown) . '"',
            'jump="no"',
            'history="' . esc_attr($history) . '"',
            'tooltip="' . esc_attr($tooltip) . '"',
            'weeknumbers="' . esc_attr($weeknumbers) . '"',
            'highlighttoday="' . esc_attr($highlighttoday) . '"',
            'show_prices="' . esc_attr($show_prices) . '"',
            'form_id="' . esc_attr($form_id) . '"',
            'form_position="' . esc_attr($form_position) . '"',
            'auto_pending="' . esc_attr($auto_pending) . '"',
            'selection_type="' . esc_attr($selection_type) . '"',
            'selection_style="' . esc_attr($selection_style) . '"',
            'minimum_days="' . esc_attr($minimum_days) . '"',
            'maximum_days="' . esc_attr($maximum_days) . '"',
            'booking_start_day="' . esc_attr($booking_start_day) . '"',
            'booking_end_day="' . esc_attr($booking_end_day) . '"',
            'show_date_selection="' . esc_attr($show_date_selection) . '"'
        ];

        $shortcode = '[wpbs ' . implode(' ', $parts) . ']';

        $output = do_shortcode($shortcode);

        $module_elements = $elements->style_components(
            [
                'attrName' => 'module',
            ]
        );

        // var_dump($shortcode);

        $module_container_children = $module_elements . $output;

        return Module::render(
            [
                'orderIndex'          => $block->parsed_block['orderIndex'] ?? 0,
                'storeInstance'       => $block->parsed_block['storeInstance'] ?? null,
                'attrs'               => $attrs,
                'elements'            => $elements,
                'id'                  => $block->parsed_block['id'] ?? '',
                'moduleClassName'     => 'wpbs_divi5_single_calendar',
                'name'                => $block->block_type->name ?? '',
                'moduleCategory'      => $block->block_type->category ?? 'module',
                'classnamesFunction'  => [self::class, 'module_classnames'],
                'stylesComponent'     => [self::class, 'module_styles'],
                'scriptDataComponent' => [self::class, 'module_script_data'],
                'children'            => $module_container_children,
            ]
        );
    }
}

// Register module.
add_action(
    'divi_module_library_modules_dependency_tree',
    function ($dependency_tree) {
        $dependency_tree->add_dependency(new WPBS_Divi5_Single_Calendar());
    }
);
