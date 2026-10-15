<?php

/**
 * Functions and definitions
 */

//function enqueue_fancybox_assets() {
//	// Enqueue Fancybox CSS
//	wp_enqueue_style(
//		'fancybox-css',
//		get_template_directory_uri() . '/assets/css/fancybox.css',
//		array(),
//		null
//	);
//
//	// Enqueue Fancybox JS
//	wp_enqueue_script(
//		'fancybox-js',
//		get_template_directory_uri() . '/assets/js/fancybox.js',
//		array('jquery'), // depends on jQuery
//		null,
//		true // load in footer
//	);
//}
//add_action('wp_enqueue_scripts', 'enqueue_fancybox_assets');

function enqueue_magnific_popup_assets() {
	// Enqueue CSS file
	wp_enqueue_style(
		'magnific-popup-css', // Handle
		get_template_directory_uri() . '/assets/css/magnific-popup.css', // File path
		array(), // Dependencies
		'1.0.0' // Version
	);

	// Enqueue JavaScript file
	wp_enqueue_script(
		'magnific-popup-js', // Handle
		get_template_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', // File path
		array('jquery'), // Dependencies
		'1.0.0', // Version
		true // Load in footer
	);
}
add_action('wp_enqueue_scripts', 'enqueue_magnific_popup_assets');

function starter_scripts_and_styles() {
	if ( ! is_admin() ) {
		// Load Stylesheets
		wp_enqueue_style( 'select2', get_template_directory_uri() . '/assets/css/select2.css', null, '4.1.0' );
		wp_enqueue_style( 'slick', get_template_directory_uri() . '/assets/css/slick.css', null, '4.1.0' );
		wp_enqueue_style( 'custom', get_template_directory_uri() . '/assets/css/custom.css', null, '4.1.0' );

		// Load JavaScripts
		wp_enqueue_style( 'jquery-ui-datepicker', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', null, '1.12.1' );
		wp_enqueue_script( 'jquery' );
		// Plugins
		wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', array(), null, true); // FIXED

		wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/js/slick.min.js', null, '1.8.1', true );
		wp_enqueue_script( 'select2', get_template_directory_uri() . '/assets/js/select2.js', null, '4.1.0', true );

		// Custom JavaScript
		wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', [ 'jquery' ], filemtime( get_stylesheet_directory() . '/assets/js/main.js' ), true );

		// Additional PHP data that will be accessible in JS files
		$localize_args = [
			'url' => admin_url( 'admin-ajax.php' ),
		];
		wp_localize_script( 'global', 'ajax', $localize_args );
	}
}

add_action( 'wp_enqueue_scripts', 'starter_scripts_and_styles' );


if (!function_exists('acf_starter_setup')) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @return void
	 */
	function acf_starter_setup()
	{
		/**
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support('automatic-feed-links');

		/*
        * Let WordPress manage the document title.
        * This theme does not use a hard-coded <title> tag in the document head,
        * WordPress will provide it for us.
        */
		add_theme_support('title-tag');

		/**
		 * Add post-formats support.
		 */
		add_theme_support(
			'post-formats',
			[
				'link',
				'aside',
				'gallery',
				'image',
				'quote',
				'status',
				'video',
				'audio',
				'chat',
			]
		);

		/*
        * Enable support for Post Thumbnails on posts and pages.
        *
        * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
        */
		add_theme_support('post-thumbnails');

		/**
		 * Register a default primary and mobile menu
		 */
		register_nav_menus(
			array(
				'primary' 	      => __('Primary'),
				'mobile'  	      => __('Mobile'),
				'language'        => __('Language'),
				'bottom_footer'   => __('Bottom Footer', 'lsg'),
			)
		);
	}
}
add_action('after_setup_theme', 'acf_starter_setup');

/**
 * Build 3-level Menu Hierarchy Tree for Luxury Header & Mega Menus
 * Level 1: Main Menu (top bar)
 * Level 2: Column Header
 * Level 3: Column Submenu Links
 */
function lsg_get_primary_menu_tree() {
	$locations = get_nav_menu_locations();
	$menu_id = $locations['primary'] ?? 0;
	if (!$menu_id) {
		return [];
	}

	$menu_items = wp_get_nav_menu_items($menu_id);
	if (empty($menu_items)) {
		return [];
	}

	$tree = [];
	$l2_to_l1 = [];

	// Level 1 items (main menu)
	foreach ($menu_items as $item) {
		if ((int)$item->menu_item_parent === 0) {
			$tree[$item->ID] = [
				'item'    => $item,
				'columns' => []
			];
		}
	}

	// Level 2 items (columns)
	foreach ($menu_items as $item) {
		$p_id = (int)$item->menu_item_parent;
		if ($p_id !== 0 && isset($tree[$p_id])) {
			$tree[$p_id]['columns'][$item->ID] = [
				'item'      => $item,
				'sub_items' => []
			];
			$l2_to_l1[$item->ID] = $p_id;
		}
	}

	// Level 3 items (column submenus)
	foreach ($menu_items as $item) {
		$p_id = (int)$item->menu_item_parent;
		if ($p_id !== 0 && isset($l2_to_l1[$p_id])) {
			$l1_id = $l2_to_l1[$p_id];
			$tree[$l1_id]['columns'][$p_id]['sub_items'][] = $item;
		}
	}

	return $tree;
}

/**
 * Determine if a Top-Level menu item qualifies as a Mega Menu.
 * An item qualifies as a Mega Menu ONLY if at least one of its sub-items (columns)
 * has child items / subpages. If none of its sub-items have children, it should be a Simple Dropdown.
 */
function lsg_menu_item_has_mega($l1) {
	if (empty($l1['columns'])) {
		return false;
	}
	foreach ($l1['columns'] as $col) {
		$col_data = function_exists('lsg_get_megamenu_col_items') ? lsg_get_megamenu_col_items($col) : ['items' => $col['sub_items'] ?? []];
		if (!empty($col_data['items']) || !empty($col_data['all_link'])) {
			return true;
		}
	}
	return false;
}

/**
 * Retrieve sub-items for a mega menu column.
 * - If manual sub-items exist in Appearance -> Menus, use them directly.
 * - If no manual sub-items exist, automatically query published child pages of this item from WordPress!
 * - Also provides an automatic "All [Title]" link pointing to the parent page.
 */
function lsg_get_megamenu_col_items($col) {
	if (!empty($col['sub_items'])) {
		return [
			'items'    => $col['sub_items'],
			'all_link' => null,
			'is_auto'  => false,
		];
	}

	$col_item = isset($col['item']) ? $col['item'] : null;
	if (!$col_item) {
		return ['items' => [], 'all_link' => null, 'is_auto' => false];
	}

	$page_id = 0;
	if (!empty($col_item->object_id) && $col_item->object === 'page') {
		$page_id = (int) $col_item->object_id;
	}
	if (!$page_id && !empty($col_item->url)) {
		$page_id = url_to_postid($col_item->url);
		if (!$page_id) {
			$path = trim(parse_url($col_item->url, PHP_URL_PATH), '/');
			if ($path) {
				$p = get_page_by_path($path);
				if (!$p) {
					$parts = explode('/', $path);
					$slug = end($parts);
					$p = get_page_by_path($slug);
				}
				if ($p) {
					$page_id = $p->ID;
				}
			}
		}
	}
	if (!$page_id && !empty($col_item->title)) {
		$p = get_page_by_title($col_item->title, OBJECT, 'page');
		if ($p) {
			$page_id = $p->ID;
		}
	}

	if ($page_id > 0) {
		$children = get_pages([
			'child_of'    => $page_id,
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		]);

		if (!empty($children)) {
			$items = [];
			foreach ($children as $c) {
				$items[] = (object) [
					'ID'       => $c->ID,
					'title'    => $c->post_title,
					'url'      => get_permalink($c->ID),
					'target'   => '_self',
					'classes'  => [],
				];
			}

			$all_link = (object) [
				'title' => sprintf(__('All %s', 'luxurysotogrande'), get_the_title($page_id)),
				'url'   => get_permalink($page_id),
			];

			return [
				'items'    => $items,
				'all_link' => $all_link,
				'is_auto'  => true,
			];
		}
	}

	return ['items' => [], 'all_link' => null, 'is_auto' => false];
}

function enqueue_nouislider() {
	wp_enqueue_style('nouislider-css', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css', array(), '15.7.1');
	wp_enqueue_script('nouislider-js', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js', array('jquery'), '15.7.1', true);
}
add_action('wp_enqueue_scripts', 'enqueue_nouislider');


//function enqueue_select2_assets() {
//	// Enqueue the Select2 JavaScript file
//	wp_enqueue_script(
//		'select2',
//		get_template_directory_uri() . '/assets/js/select2.js',
//		array('jquery'),  // Ensure jQuery is loaded before Select2
//		'1.8.1',
//		true  // Load in footer
//	);
//
//	// Enqueue the Select2 CSS file
//	wp_enqueue_style(
//		'select2',
//		get_template_directory_uri() . '/assets/css/select2.css',
//		array(),  // No dependencies
//		'1.0',
//		'all'  // Apply to all media
//	);
//}
//add_action( 'wp_enqueue_scripts', 'enqueue_select2_assets' );

//function enqueue_slick_css() {
//	// Enqueue slick.css from your theme or plugin directory
//	wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/assets/css/slick.css', array(), '1.0', 'all' );
//}
//add_action( 'wp_enqueue_scripts', 'enqueue_slick_css' );


//function enqueue_custom_scripts() {
//	wp_enqueue_script( 'slick.min', get_template_directory_uri() . '/assets/js/slick.min.js', array(), '1.8.1', true );
//	wp_enqueue_script( 'jquery-3.4.1.min', get_template_directory_uri() . '/assets/js/jquery-3.4.1.min.js', array(), '1.8.1', true );
//	wp_enqueue_script( 'isotope', get_template_directory_uri() . '/assets/js/isotope.js', array(), '1.8.1', true );
//	wp_enqueue_script( 'jquery.matchHeight-min', get_template_directory_uri() . '/assets/js/jquery.matchHeight-min.js', array(), '1.8.1', true );
//}
//add_action( 'wp_enqueue_scripts', 'enqueue_custom_scripts' );





/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function acf_starter_scripts()
{
	wp_enqueue_style('gill-sans-nova', 'https://use.typekit.net/vml6itj.css');
	
	// 13-10-2026: Google Fonts (Mrs Saint Delafield, Playfair Display, Roboto)
	wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Mrs+Saint+Delafield&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap', array(), null);

	wp_dequeue_style('wp-block-library');

//	wp_enqueue_style('aos', 'https://unpkg.com/aos@next/dist/aos.css');

	wp_enqueue_style('glide-core-css', 'https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.2.0/css/glide.core.min.css');
//
	wp_enqueue_style('glide-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.2.0/css/glide.theme.min.css');


	wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.min.css');

	wp_enqueue_style('custom', get_template_directory_uri() . '/assets/css/custom.css');

	wp_enqueue_script('glide-js', 'https://cdn.jsdelivr.net/npm/@glidejs/glide', null, null, true);

	wp_enqueue_script('html2canvas', get_template_directory_uri() . '/assets/js/html2canvas.js', null, null, true);

	wp_enqueue_script('aos-js', 'https://unpkg.com/aos@next/dist/aos.js', null, null, true);

	wp_enqueue_script('slider-js', get_template_directory_uri() . '/assets/js/main.js', null, null, true);

	wp_enqueue_script('alpine', 'https://unpkg.com/alpinejs@3.11.1/dist/cdn.min.js', null, null, true);

	// Google Maps
	if (is_single() && get_post_type() === 'property') {
		wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_field('google_maps', 'options') . '&callback=initMap', null, null, true);
		wp_enqueue_script('acf-google-maps', get_template_directory_uri() . '/assets/js/acf-google-maps.js', null, null, true);
	}
}
add_action('wp_enqueue_scripts', 'acf_starter_scripts');









/**
 * Disabled the block editor
 *
 * @param bool $use_block_editor
 * @param string $post_type
 * @return void
 */
function acf_starter_disabled_block_editor($use_block_editor, $post_type)
{
	return false;
}
add_action('use_block_editor_for_post_type', 'acf_starter_disabled_block_editor', 10, 2);

/**
 * Disable the editor when acf-sections.php page template is used
 *
 * @return void
 */
/**
 * Disable the editor when acf-sections.php page template is used
 *
 * @return void
 */
function acf_starter_hide_editor()
{
	remove_post_type_support('page', 'editor');
}
add_action('admin_init', 'acf_starter_hide_editor');

/**
 * Create custom option pages
 */
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' 	=> 'Theme Options',
		'menu_title'	=> 'Theme Options',
		'menu_slug' 	=> 'theme-options',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	// Add sub options page for translations
	acf_add_options_sub_page(array(
		'page_title'    => 'Label Translations',
		'menu_title'    => 'Label Translations',
		'parent_slug'   => 'theme-options',
	));
}

/**
 * Displays custom scripts and styles in the site header
 *
 * @return void
 */
function acf_starter_custom_head_content()
{
	/**
	 * Custom header scripts
	 */
	the_field('header_scripts', 'option');

	/**
	 * Custom header styles
	 */
	the_field('header_styles', 'option');
}
add_action('wp_head', 'acf_starter_custom_head_content');

/**
 * Removed the wp generator meta tag
 */
remove_action('wp_head', 'wp_generator');

/**
 * Includes the required template for the flexible field layout
 *
 * @return void
 */
function get_section_templates() {
	if (have_rows('sections')) {
		while (have_rows('sections')) {
			the_row();

			$layout = get_row_layout();

			if ($layout === 'global_section') {
				/**
				 * Get the global section title
				 */
				$global_section = str_replace(' ', '-', get_the_title(get_sub_field('global_section')));
				$template = 'sections/global/' . strtolower($global_section) . '.php';
			} else {
				$template = 'sections/' . str_replace('_', '-', $layout) . '.php';
			}

			$template_path = locate_template($template);

			if ($template_path) {
				include $template_path;
			} else {
				error_log("Template not found: " . $template);
			}
		}
	} else {
		error_log("No sections found.");
	}
}


/**
 * Register widget area.
 */
function lsg_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('About', 'lsg'),
			'id'            => 'about',
			'description'   => esc_html__('Add widgets here.', 'lsg'),
			'before_widget' => '<section id="%1$s" class="widget %2$s footer-about-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="mb-2.5 font-mrs-saint font-normal text-[26px] leading-[36px] tracking-normal widget-title text-warm-beige">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__('Import Links', 'lsg'),
			'id'            => 'important-links',
			'description'   => esc_html__('Add widgets here.', 'lsg'),
			'before_widget' => '<section id="%1$s" class="widget %2$s footer-links-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="mb-3 font-roboto font-normal text-[12px] leading-[17px] tracking-[3px] uppercase text-[#EFEAE3]">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__('Areas', 'lsg'),
			'id'            => 'areas',
			'description'   => esc_html__('Add widgets here.', 'lsg'),
			'before_widget' => '<section id="%1$s" class="widget %2$s footer-links-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="mb-3 font-roboto font-normal text-[12px] leading-[17px] tracking-[3px] uppercase text-[#EFEAE3]">',
			'after_title'   => '</h2>',
		)
	);



	register_sidebar(
		array(
			'name'          => esc_html__('Contact', 'lsg'),
			'id'            => 'contact',
			'description'   => esc_html__('Add widgets here.', 'lsg'),
			'before_widget' => '<section id="%1$s" class="widget %2$s footer-links-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="mb-3 font-roboto font-normal text-[12px] leading-[17px] tracking-[3px] uppercase text-[#EFEAE3]">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'lsg_widgets_init');
add_filter('use_widgets_block_editor', '__return_false');

// Display language label for menus in admin to easily distinguish translations
add_filter('wp_get_nav_menus', function($menus) {
	if (is_admin() && !empty($menus)) {
		global $sitepress;
		if ($sitepress && method_exists($sitepress, 'get_language_for_element')) {
			foreach ($menus as $menu) {
				if (!empty($menu->term_taxonomy_id)) {
					$lang = $sitepress->get_language_for_element($menu->term_taxonomy_id, 'tax_nav_menu');
					if ($lang) {
						$label = ($lang === 'en') ? 'English' : (($lang === 'es') ? 'Español' : strtoupper($lang));
						if (strpos($menu->name, "($label)") === false) {
							$menu->name .= " ($label)";
						}
					}
				}
			}
		}
	}
	return $menus;
});

/**
 * Set the Google Maps API key for the ACF Google Maps Field.
 *
 * # Need to add a custom field to add this in the back-end.
 */
function my_acf_google_map_api($api)
{
	$api['key'] = get_field('google_maps', 'options');
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

/**
 * Populate the calendar select field with calendar options.
 */
function acf_load_calendar_field_choices($field)
{

	global $wpdb;

	// reset choices
	$field['choices'] = array();

	// get the calendars from the database
	$calendars = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpbs_calendars");

	// loop through array and add to field 'choices'
	if (is_array($calendars)) {
		foreach ($calendars as $calendar) {
			$field['choices'][$calendar->id] = $calendar->name;
		}
	}

	// return the field
	return $field;
}

add_filter('acf/load_field/name=calendar', 'acf_load_calendar_field_choices');

/**
 * Returns the English or Spanish text
 * depending on the current language.
 */
function label($label)
{
	return get_field($label, 'option');
}

/**
 * Returns an array of paramters used for setting the ajax load more shortcode and making the
 * filter form inputs sticky.
 */
function filter_params()
{
	$filter_params = [];
	$filter_params['pid'] = isset($_GET['pid']) && !empty($_GET['pid'])
		? $_GET['pid']
		: '';

	$filter_params['type'] = isset($_GET['type']) && !empty($_GET['type'])
		? get_term($_GET['type'], 'property_type')->slug
		: '';

	$filter_params['area'] = isset($_GET['area']) && !empty($_GET['area'])
		? get_term($_GET['area'], 'property_area')->slug
		: '';

	$filter_params['area_id'] = isset($_GET['area']) && !empty($_GET['area'])
		? $_GET['area']
		: '';

	$filter_params['subarea'] = isset($_GET['subarea']) && !empty($_GET['subarea'])
		? get_term($_GET['subarea'], 'property_area')->slug
		: '';

	$filter_params['subarea_id'] = isset($_GET['subarea']) && !empty($_GET['subarea'])
		? $_GET['subarea']
		: '';

	$filter_params['min_price'] = isset($_GET['min_price']) && !empty($_GET['min_price'])
		? $_GET['min_price']
		: '';

	$filter_params['max_price'] = isset($_GET['max_price']) && !empty($_GET['max_price'])
		? $_GET['max_price']
		: '';

	// Set property status depending on certain page
	$filter_params['status'] = match (true) {
		is_page(224) => 'long-term-rental',
		is_page(224) => 'long-term-rental-es',
		is_page(219) => 'for-sale',
		is_page(1068) => 'for-sale-es',
		is_page(221) => 'holiday-rental',
		is_page(1073) => 'holiday-rental-es',
		isset($_GET['status']) && !empty($_GET['status']) => get_term($_GET['status'], 'property_status')->slug,
		default => ''
	};

	return $filter_params;
}

/**
 * Redirect to the properties page with a query string that doesn't have empty values.
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
	$query_params = [];

	if (isset($_GET['pid']) && !empty($_GET['pid'])) {

		$pid = strip_tags($_GET['pid']);

		/**
		 * If $pid starts with LS or ls then add it to the query params
		 * otherwise prepend 'LS' to $pid.
		 */
		if (str_starts_with($pid, 'LS') || str_starts_with($pid, 'ls')) {
			$query_params['pid'] = $pid;
		} else {
			$query_params['pid'] = 'LS' . $pid;
		}
	}

	if (isset($_GET['status']) && !empty($_GET['status'])) {
		$query_params['status'] = strip_tags($_GET['status']);
	}

	if (isset($_GET['type']) && !empty($_GET['type'])) {
		$query_params['type'] = strip_tags($_GET['type']);
	}

	if (isset($_GET['area']) && !empty($_GET['area'])) {
		$query_params['area'] = strip_tags($_GET['area']);
	}

	if (isset($_GET['subarea']) && !empty($_GET['subarea'])) {
		$query_params['subarea'] = strip_tags($_GET['subarea']);
	}

	if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
		$query_params['min_price'] = strip_tags($_GET['min_price']);
	}

	if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
		$query_params['max_price'] = strip_tags($_GET['max_price']);
	}

	header('Location: ./properties?' . http_build_query($query_params));
	exit();
}

/**
 * Add custom admin columns for properties
 */

//  OLd code comment by Kevin Patel -- 4-2-2026
// function lsg_filter_posts_columns($columns)
// {
// 	$columns['download_pdf'] = __('Download PDF');

// 	return $columns;
// }
// add_filter('manage_property_posts_columns', 'lsg_filter_posts_columns');

// function lsg_property_column($column, $post_id)
// {
// 	if ($column === 'download_pdf') {
// 		$title = strtolower(get_the_title($post_id));
// 		$name = str_replace(' ', '-', $title);
// 		$unbranded = get_template_directory_uri() . '/../../pdfs/' . $name . '.pdf';
// 		$branded = get_template_directory_uri() . '/../../pdfs/' . $name . '-branded.pdf';
// 		echo '<a href="' . $unbranded . '" download>Unbranded</a> | <a href="' . $branded . '" download>Branded</a>';
// 	}
// }

// add_action('manage_property_posts_custom_column', 'lsg_property_column', 10, 2);


// function get_property_download_links($post_id) {
// 	$title = strtolower(get_the_title($post_id));
// 	$name = str_replace(' ', '-', $title);

// 	$branded = get_template_directory_uri() . '/../../pdfs/' . $name . '-branded.pdf';
// 	$unbranded = get_template_directory_uri() . '/../../pdfs/' . $name . '.pdf';

// 	return '<a class="pdf-print" href="' . esc_url($branded) . '" download>' . esc_html__('Print PDF', 'purple') . '</a>';
// }

	//  New code comment by Kevin Patel -- 4-2-2026
	function lsg_filter_posts_columns($columns){
		$columns['download_pdf'] = __('Download PDF');

		return $columns;
	}
	add_filter('manage_property_posts_columns', 'lsg_filter_posts_columns');

	add_action('manage_property_posts_custom_column', function ($column, $post_id) {
		if ($column !== 'download_pdf') {
			return;
		}
		if (!current_user_can('manage_options')) {
			return;
		}
		$lang = apply_filters('wpml_current_language', null);
		$url_un = admin_url(
			'admin-post.php?action=lsg_generate_pdf&post_id=' . $post_id . '&lang=' . $lang
		);
		$url_br = admin_url(
			'admin-post.php?action=lsg_generate_pdf&post_id=' . $post_id . '&lang=' . $lang . '&type=branded'
		);
		echo '<a href="' . esc_url($url_un) . '">Unbranded</a>';
		echo ' | ';
		echo '<a href="' . esc_url($url_br) . '">Branded</a>';
	}, 10, 2);
	
// =====06-02-2026	
// Commented by Abhi
// 	function get_property_download_links($post_id){
// 		$lang = apply_filters('wpml_current_language', null);
// 		$title = sanitize_title(get_the_title($post_id));

// 		$file = "{$title}-{$lang}-branded.pdf";
// 		$path = WP_CONTENT_DIR . '/pdfs/new/' . $file;
// 		$url  = content_url('/pdfs/new/' . $file);

// 		// Generate if missing
// 		if (!file_exists($path)) {
// 			lsg_create_pdf_on_save($post_id);
// 		}

// 		return '<a class="pdf-print" href="' . esc_url($url) . '" download>Print PDF</a>';
// 	}
// 	TODO: Re-enable once PDF generator function is safely loaded
function get_property_download_links($post_id){
    $lang  = apply_filters('wpml_current_language', null);
    $title = sanitize_title(get_the_title($post_id));

    $file = "{$title}-{$lang}-branded.pdf";
    $path = WP_CONTENT_DIR . '/pdfs/new/' . $file;
    $url  = content_url('/pdfs/new/' . $file);

    // Generate PDF only if function exists
    if (!file_exists($path) && function_exists('lsg_create_pdf_on_save')) {
        lsg_create_pdf_on_save($post_id);
    }

    // If PDF still doesn't exist, don’t show link
    if (!file_exists($path)) {
        return '';
    }

    return '<a class="pdf-print" href="' . esc_url($url) . '" download>Print PDF</a>';
}
// =====06-02-2026


	function lsg_unlock_post_for_pdf($post_id) {
		add_filter('post_password_required', '__return_false');
	}

	if (isset($_GET['action']) && $_GET['action'] === 'lsg_generate_pdf') {
		ini_set('memory_limit', '512M');
		set_time_limit(0);
	}	

	add_action('admin_post_lsg_generate_pdf', function () {

		error_reporting(E_ERROR | E_PARSE);
		ini_set('display_errors', '0');
		ob_start();

		if (!current_user_can('manage_options')) {
			wp_die('Permission denied');
		}

		$post_id = (int) ($_GET['post_id'] ?? 0);
		$lang    = $_GET['lang'] ?? 'en';
		$type    = $_GET['type'] ?? 'unbranded';

		if (!$post_id || get_post_type($post_id) !== 'property') {
			wp_die('Invalid property');
		}

		ini_set('memory_limit', '512M');
		set_time_limit(0);

		lsg_unlock_post_for_pdf($post_id);

		$branded = ($type === 'branded');

		// ✅ Generate ONLY what was requested
		$file = lsg_generate_one_pdf($post_id, $lang, $branded);
		$full = WP_CONTENT_DIR . '/pdfs/new/' . $file;

		if (!file_exists($full)) {
			wp_die('PDF generation failed');
		}

		ob_clean();
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="' . basename($file) . '"');
		header('Content-Length: ' . filesize($full));
		header('Cache-Control: private');
		header('Pragma: public');

		readfile($full);
		exit;
	});







/**
 * Custom Post Types
 */
function acf_starter_register_my_cpts()
{

	/**
	 * Post Type: Sliders.
	 */

	$labels = [
		"name" => __("Sliders", "custom-post-type-ui"),
		"singular_name" => __("Slider", "custom-post-type-ui"),
		"menu_name" => __("Sliders", "custom-post-type-ui"),
		"all_items" => __("All Sliders", "custom-post-type-ui"),
		"add_new" => __("Add new", "custom-post-type-ui"),
		"add_new_item" => __("Add new Slider", "custom-post-type-ui"),
		"edit_item" => __("Edit Slider", "custom-post-type-ui"),
		"new_item" => __("New Slider", "custom-post-type-ui"),
		"view_item" => __("View Slider", "custom-post-type-ui"),
		"view_items" => __("View Sliders", "custom-post-type-ui"),
		"search_items" => __("Search Sliders", "custom-post-type-ui"),
		"not_found" => __("No Sliders found", "custom-post-type-ui"),
		"not_found_in_trash" => __("No Sliders found in trash", "custom-post-type-ui"),
		"parent" => __("Parent Slider:", "custom-post-type-ui"),
		"featured_image" => __("Featured image for this Slider", "custom-post-type-ui"),
		"set_featured_image" => __("Set featured image for this Slider", "custom-post-type-ui"),
		"remove_featured_image" => __("Remove featured image for this Slider", "custom-post-type-ui"),
		"use_featured_image" => __("Use as featured image for this Slider", "custom-post-type-ui"),
		"archives" => __("Slider archives", "custom-post-type-ui"),
		"insert_into_item" => __("Insert into Slider", "custom-post-type-ui"),
		"uploaded_to_this_item" => __("Upload to this Slider", "custom-post-type-ui"),
		"filter_items_list" => __("Filter Sliders list", "custom-post-type-ui"),
		"items_list_navigation" => __("Sliders list navigation", "custom-post-type-ui"),
		"items_list" => __("Sliders list", "custom-post-type-ui"),
		"attributes" => __("Sliders attributes", "custom-post-type-ui"),
		"name_admin_bar" => __("Slider", "custom-post-type-ui"),
		"item_published" => __("Slider published", "custom-post-type-ui"),
		"item_published_privately" => __("Slider published privately.", "custom-post-type-ui"),
		"item_reverted_to_draft" => __("Slider reverted to draft.", "custom-post-type-ui"),
		"item_scheduled" => __("Slider scheduled", "custom-post-type-ui"),
		"item_updated" => __("Slider updated.", "custom-post-type-ui"),
		"parent_item_colon" => __("Parent Slider:", "custom-post-type-ui"),
	];

	$args = [
		"label" => __("Sliders", "custom-post-type-ui"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "slider", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-slides",
		"supports" => ["title"],
		"show_in_graphql" => false,
	];

	register_post_type("slider", $args);

	/**
	 * Post Type: Global Sections.
	 */

	$labels = [
		"name" => __("Global Sections", "custom-post-type-ui"),
		"singular_name" => __("Global Section", "custom-post-type-ui"),
		"menu_name" => __("Global Sections", "custom-post-type-ui"),
		"all_items" => __("All Global Sections", "custom-post-type-ui"),
		"add_new" => __("Add new", "custom-post-type-ui"),
		"add_new_item" => __("Add new Global Section", "custom-post-type-ui"),
		"edit_item" => __("Edit Global Section", "custom-post-type-ui"),
		"new_item" => __("New Global Section", "custom-post-type-ui"),
		"view_item" => __("View Global Section", "custom-post-type-ui"),
		"view_items" => __("View Global Sections", "custom-post-type-ui"),
		"search_items" => __("Search Global Sections", "custom-post-type-ui"),
		"not_found" => __("No Global Sections found", "custom-post-type-ui"),
		"not_found_in_trash" => __("No Global Sections found in trash", "custom-post-type-ui"),
		"parent" => __("Parent Global Section:", "custom-post-type-ui"),
		"featured_image" => __("Featured image for this Global Section", "custom-post-type-ui"),
		"set_featured_image" => __("Set featured image for this Global Section", "custom-post-type-ui"),
		"remove_featured_image" => __("Remove featured image for this Global Section", "custom-post-type-ui"),
		"use_featured_image" => __("Use as featured image for this Global Section", "custom-post-type-ui"),
		"archives" => __("Global Section archives", "custom-post-type-ui"),
		"insert_into_item" => __("Insert into Global Section", "custom-post-type-ui"),
		"uploaded_to_this_item" => __("Upload to this Global Section", "custom-post-type-ui"),
		"filter_items_list" => __("Filter Global Sections list", "custom-post-type-ui"),
		"items_list_navigation" => __("Global Sections list navigation", "custom-post-type-ui"),
		"items_list" => __("Global Sections list", "custom-post-type-ui"),
		"attributes" => __("Global Sections attributes", "custom-post-type-ui"),
		"name_admin_bar" => __("Global Section", "custom-post-type-ui"),
		"item_published" => __("Global Section published", "custom-post-type-ui"),
		"item_published_privately" => __("Global Section published privately.", "custom-post-type-ui"),
		"item_reverted_to_draft" => __("Global Section reverted to draft.", "custom-post-type-ui"),
		"item_scheduled" => __("Global Section scheduled", "custom-post-type-ui"),
		"item_updated" => __("Global Section updated.", "custom-post-type-ui"),
		"parent_item_colon" => __("Parent Global Section:", "custom-post-type-ui"),
	];

	$args = [
		"label" => __("Global Sections", "custom-post-type-ui"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => ["slug" => "global_section", "with_front" => true],
		"query_var" => true,
		"menu_icon" => "dashicons-layout",
		"supports" => ["title"],
		"show_in_graphql" => false,
	];

	register_post_type("global_section", $args);
}

add_action('init', 'acf_starter_register_my_cpts');

/**
 * Include PDF Generator
 */
require get_template_directory() . '/includes/pdf-generator.php';


function sanitize_number( $string ) {

	return preg_replace( '/[^+\d]+/', '', $string );

}

function modify_archive_title($title) {
	$title = str_replace('Property Status:', '', $title);
	return $title;
}

add_filter('get_the_archive_title', 'modify_archive_title');


function hide_child_taxonomies( $args, $field ) {
	if( 'property_area' === $field['_name'] ) {
		$args['parent'] = 0;
	}
	return $args;
}


add_filter('acf/fields/taxonomy/query', 'hide_child_taxonomies', 10, 3);


function bg( $img = '', $size = '', $echo = true ) {

	if ( empty( $img ) ) {
		return false;
	}

	if ( is_array( $img ) ) {
		$url = $size ? $img['sizes'][ $size ] : $img['url'];
	} else {
		$url = $img;
	}

	$string = 'style="background-image: url(' . $url . ')"';

	if ( $echo ) {
		echo $string;
	} else {
		return $string;
	}
}


function bootstrap_pagination( $query = '' ) {
	if ( empty( $query ) ) {
		global $wp_query;
		$query = $wp_query;
	}

	$big = 999999999;

	$links = paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'    => '?paged=%#%',
		'prev_next' => true,
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $query->max_num_pages,
		'type'      => 'list'
	) );

	$pagination = str_replace( 'page-numbers', 'pagination', $links );

	echo $pagination;
}


function bootstrap_paginationProperty( $query = '' ) {
	if ( empty( $query ) ) {
		global $wp_query;
		$query = $wp_query;
	}

	$big = 999999999;

	$links = paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) . '#main-posts-property' ) ),
		'format'    => '?paged=%#%',
		'prev_next' => true,
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $query->max_num_pages,
		'type'      => 'list'
	) );

	$pagination = str_replace( 'page-numbers', 'pagination', $links );

	echo $pagination;
}

add_filter( 'get_the_archive_title', 'my_theme_archive_title' );
/**
 * Remove archive labels.
 *
 * @param  string $title Current archive title to be displayed.
 * @return string        Modified archive title to be displayed.
 */
function my_theme_archive_title( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_home() ) {
		$title = single_post_title( '', false );
	}

	return $title;
}




// Add custom property_id field with LS prefix and incremental number
// Add custom property_id field with LS prefix and incremental number
function add_custom_property_id_on_create( $post_id, $post, $update ) {
	// Check if it's a new post and the correct post type
	if ( ! $update && 'property' === $post->post_type ) {
		// Get the user ID of the post author
		$user_id = $post->post_author;

		// Get the current property_id number for the user
		$current_number = get_user_meta( $user_id, 'last_property_id', true );

		// Increment the number
		$number = intval( substr( $current_number, 2 ) ) + 1;
		// Generate the new property_id with leading zeros
		$property_id = 'LS' . str_pad( $number, 4, '0', STR_PAD_LEFT );

		// Update the post meta with the new property_id
		update_post_meta( $post_id, 'property_id', $property_id );

		// Update the user meta with the last property ID
		update_user_meta( $user_id, 'last_property_id', $property_id );
	}
}
add_action( 'wp_insert_post', 'add_custom_property_id_on_create', 10, 3 );


function enqueue_custom_stylesheets() {
	// Get the theme directory URL
	$theme_directory = get_template_directory_uri();

	// Enqueue the stylesheet
	wp_enqueue_style(
		'custom-font-styles', // Handle name
		$theme_directory . '/assets/fonts/stylesheet.css', // Path to your CSS file
		array(), // Dependencies (leave empty if none)
		'1.0.0', // Version number (use file versioning for cache-busting)
		'all' // Media type (e.g., 'all', 'print', 'screen')
	);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_stylesheets');


add_action('pre_get_posts', function ($query) {
	// Only modify the main query for frontend searches (avoid admin area and other queries)
	if (!is_admin() && $query->is_main_query() && $query->is_search()) {

		// Bedrooms filter (same as your original code)
		if (!empty($_GET['bedrooms'])) {
			$query->set('meta_query', array(
				array(
					'key'     => 'bedrooms',
					'value'   => sanitize_text_field($_GET['bedrooms']),
					'compare' => '='
				)
			));
		}

		// Price range filter
		if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
			// Sanitize the input
			$min_price = sanitize_text_field($_GET['price_min']);
			$max_price = sanitize_text_field($_GET['price_max']);
		
			// Remove thousand separators (dots, commas, or spaces)
			$min_price = preg_replace('/[.,\s]/', '', $min_price);
			$max_price = preg_replace('/[.,\s]/', '', $max_price);
		
			// Convert to integer (optional but clean)
			$min_price = (int)$min_price;
			$max_price = (int)$max_price;
		
			// If there's already a meta_query, append the price filter
			$meta_query = $query->get('meta_query') ? $query->get('meta_query') : array();
		
			$meta_query[] = array(
				'key'     => 'property_price', // ACF field key for property price
				'value'   => array($min_price, $max_price),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
		
			// Set the updated meta_query
			$query->set('meta_query', $meta_query);
		}
		
	}
});




function filter_properties_by_sub_area( $query ) {
	if( !is_admin() && $query->is_main_query() && is_search() ) {
		if( !empty($_GET['sub-property_area']) ) {
			$query->set('tax_query', array(
				array(
					'taxonomy' => 'property_area', // Замените на нужную таксономию
					'field'    => 'slug',
					'terms'    => $_GET['sub-property_area'],
					'operator' => 'IN', // Можно использовать 'IN' или 'AND', в зависимости от логики
				),
			));
		}
	}
}
add_action( 'pre_get_posts', 'filter_properties_by_sub_area' );



function custom_acf_css() {

	echo '<style>
        .acf-relationship .list {
            height: 510px !important;
        }
    </style>';

}

add_action('admin_head', 'custom_acf_css');


//function filter_search_query($query) {
//	if ($query->is_search() && $query->is_main_query()) {
//		$properties_ids = get_field('properties_search_global', 'option'); // or use a specific post ID
//		if (!empty($properties_ids)) {
//			$query->set('post__in', $properties_ids);
//		}
//	}
//}
//add_action('pre_get_posts', 'filter_search_query');

function order_search_results_by_property_price( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		// 13-10-2026: For-sale properties sort by price High to Low, holiday rentals sort by date
		$property_status = isset($_GET['property_status']) ? sanitize_text_field($_GET['property_status']) : '';
		
		if ( in_array($property_status, ['for-sale', 'for-sale-es']) ) {
			$query->set( 'meta_key', 'property_price' ); // ACF field name
			$query->set( 'orderby', 'meta_value_num' ); // Order numerically High to Low
			$query->set( 'order', 'DESC' );
		} else {
			// Holiday rentals and other queries sort by date (Newest first)
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
		}
	}
}
add_action( 'pre_get_posts', 'order_search_results_by_property_price' );

// Start -- Comment BY Developer Kevin Patel -- 30-7-2025 -- OLD Code
// 13-10-2026: Commented out to prevent overwriting price sorting
/*
function order_search_results_by_publish_date( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'orderby', 'date' ); // Sort by post publish date
		$query->set( 'order', 'DESC' );   // Newest first
	}
}
add_action( 'pre_get_posts', 'order_search_results_by_publish_date' );
*/
// End -- Comment BY Developer Kevin Patel -- 30-7-2025 -- OLD Code

do_action( 'wpml_register_single_string', 'luxury', 'Price Reduction', 'Price Reduction' );

// Start -- New Code Added BY Developer Kevin Patel -- For Date Filter in Rantel property -- 29-7-2025 -- New Code
// function is_wpbs_range_available($calendar_id, $start_date, $end_date) {
//     global $wpdb;

//     $table_events = $wpdb->prefix . 'wpbs_events';
//     $table_legend = $wpdb->prefix . 'wpbs_legend_items';

//     $start = new DateTime($start_date);
//     $end   = new DateTime($end_date);
//     $interval = new DateInterval('P1D');
//     $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

//     foreach ($period as $date) {
//         $year  = (int)$date->format('Y');
//         $month = (int)$date->format('n'); // no leading 0
//         $day   = (int)$date->format('j'); // no leading 0

//         // Query to get legend_item_id from wpbs_events
//         $legend_item_id = $wpdb->get_var(
//             $wpdb->prepare(
//                 "SELECT legend_item_id FROM $table_events 
//                  WHERE calendar_id = %d AND date_year = %d AND date_month = %d AND date_day = %d",
//                 $calendar_id, $year, $month, $day
//             )
//         );

//         // If no entry exists, assume available
//         if (empty($legend_item_id)) {
//             continue;
//         }

//         // Now check if legend_item is bookable
//         $is_bookable = $wpdb->get_var(
//             $wpdb->prepare(
//                 "SELECT is_bookable FROM $table_legend WHERE id = %d",
//                 $legend_item_id
//             )
//         );

//         // If not bookable, this property is not available for full date range
//         if ($is_bookable !== '1') {
//             return false;
//         }
//     }

//     return true;
// }

// function filter_properties_by_booking_availability($posts, $query) {
//     if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
//         return $posts;
//     }


//     if (empty($_GET['wpbs-search-start-date']) || empty($_GET['wpbs-search-end-date'])) {
//         return $posts;
//     }

//     $start_date = sanitize_text_field($_GET['wpbs-search-start-date']);
//     $end_date   = sanitize_text_field($_GET['wpbs-search-end-date']);
//     $available_posts = [];

//     foreach ($posts as $post) {
//         $calendar_id = get_field('calendar', $post->ID); // ACF field containing calendar post ID

//         if (!$calendar_id || !is_numeric($calendar_id)) {
//             continue; // Skip if no calendar assigned
//         }

//         if (is_wpbs_range_available($calendar_id, $start_date, $end_date)) {
//             $available_posts[] = $post;
//         }
//     }
	
// 	// Update found_posts to reflect the filtered result count
//     $query->found_posts = count($available_posts);
//     $posts_per_page = $query->get('posts_per_page');
//     if ($posts_per_page > 0) {
//         $query->max_num_pages = ceil(count($available_posts) / $posts_per_page);
//     } else {
//         $query->max_num_pages = 1;
//     }

//     // Return only the posts for current page
//     $paged = max(1, (int) $query->get('paged'));
//     $offset = ($paged - 1) * $posts_per_page;
//     return array_slice($available_posts, $offset, $posts_per_page);

// }
// add_filter('the_posts', 'filter_properties_by_booking_availability', 10, 2);
add_action('pre_get_posts', function($q) {
    if (is_admin() || !$q->is_main_query() || !$q->is_search()) {
        return;
    }

    $start = isset($_GET['wpbs-search-start-date']) ? sanitize_text_field($_GET['wpbs-search-start-date']) : '';
    $end   = isset($_GET['wpbs-search-end-date'])   ? sanitize_text_field($_GET['wpbs-search-end-date'])   : '';
    if (!$start || !$end) {
        return;
    }

    global $wpdb;

    $startObj = new DateTime($start);
    $endObj   = new DateTime($end);
    $endObj->modify('+1 day');

    $dates = [];
    while ($startObj < $endObj) {
        $dates[] = [
            'y' => (int)$startObj->format('Y'),
            'm' => (int)$startObj->format('n'),
            'd' => (int)$startObj->format('j'),
        ];
        $startObj->modify('+1 day');
    }

    // Prepare conditions for all dates
    $dateConditions = [];
    foreach ($dates as $dt) {
        $dateConditions[] = $wpdb->prepare(
            "(e.date_year = %d AND e.date_month = %d AND e.date_day = %d)",
            $dt['y'], $dt['m'], $dt['d']
        );
    }
    $dateSql = implode(' OR ', $dateConditions);

    // Hook into SQL clauses
    add_filter('posts_clauses', function($clauses) use ($wpdb, $dateSql) {

        // Join to calendar ACF field (postmeta)
        $clauses['join'] .= " 
            INNER JOIN {$wpdb->postmeta} pm_cal
                ON pm_cal.post_id = {$wpdb->posts}.ID
                AND pm_cal.meta_key = 'calendar'";

        // Exclude posts where any date is not bookable
        $clauses['where'] .= "
            AND NOT EXISTS (
                SELECT 1
                FROM {$wpdb->prefix}wpbs_events e
                INNER JOIN {$wpdb->prefix}wpbs_legend_items l
                    ON e.legend_item_id = l.id
                WHERE e.calendar_id = pm_cal.meta_value
                  AND ($dateSql)
                  AND (l.is_bookable != 1)
            )";

        return $clauses;
    }, 20);

});
// End -- New Code Added BY Developer Kevin Patel -- 29-7-2025 -- New Code
 

//  New Code Added BY Developer Kevin Patel -- For Rental Properties Page Order Fields -- 5-8-2025 -- New Code
add_filter('acf/fields/relationship/query/key=field_67b6fcac8ae0e', function($args, $field, $post_id) {

    // Get current page/post object
    $post = get_post($post_id);
    if (!$post) return $args;

    $page_slug = $post->post_name;

    // Try to find matching property_area term by page slug
    $property_area_term = get_term_by('slug', $page_slug, 'property_area');

    // Build custom tax_query array
    $custom_tax_query = [
        [
            'taxonomy' => 'property_status',
            'field'    => 'term_id',
            'terms'    => [136],
        ]
    ];

    if ($property_area_term) {
        $custom_tax_query[] = [
            'taxonomy' => 'property_area',
            'field'    => 'term_id',
            'terms'    => [$property_area_term->term_id],
        ];
    }

    // Apply tax_query
    $args['tax_query'] = $custom_tax_query;

    // Order by publish date (post_date)
    $args['orderby'] = 'date';
    $args['order']   = 'DESC'; // Use 'ASC' for oldest first

    return $args;

}, 10, 3);


//  New Code Added BY Developer Kevin Patel -- For Sell Properties Page Order Fields -- 5-8-2025 -- New Code
// add_filter('acf/fields/relationship/query/key=field_675963be624de', function($args, $field, $post_id) {

//     // Get the current post object
//     $post = get_post($post_id);
//     if (!$post) return $args;

//     // Check if the page uses the specific template
//     $template = get_page_template_slug($post_id);
//     if ($template !== 'templates/template-sale_property.php') {
//         return $args; // Don't modify the query
//     }

//     $page_slug = $post->post_name;

//     // Find the matching term in 'property_area' taxonomy
//     $property_area_term = get_term_by('slug', $page_slug, 'property_area');

//     // Start building tax_query with property_status = 135
//     $custom_tax_query = [
//         [
//             'taxonomy' => 'property_status',
//             'field'    => 'term_id',
//             'terms'    => [135],
//         ]
//     ];

//     // If a matching property_area term exists, include it in the query
//     if ($property_area_term) {
//         $custom_tax_query[] = [
//             'taxonomy' => 'property_area',
//             'field'    => 'term_id',
//             'terms'    => [$property_area_term->term_id],
//         ];
//     }

//     // Apply the new tax_query
//     $args['tax_query'] = $custom_tax_query;
	
// 	// Order by publish date (post_date)
//     $args['orderby'] = 'date';
//     $args['order']   = 'DESC'; // Use 'ASC' for oldest first

//     return $args;

// }, 10, 3);

add_action('admin_enqueue_scripts', function($hook){
    if ( ! in_array($hook, array('post.php','post-new.php'), true) ) return;

    // Limit to the Property post type (change if your slug differs)
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if ( ! $screen || $screen->post_type !== 'property' ) return;

    $js = <<<JS
jQuery(function($){
  var box = $('#taxonomy-property_area');
  if(!box.length) return;

  function setCheckedByTermId(id, checked){
    // Toggle every instance of the checkbox (All + Most Used)
    box.find('input[type="checkbox"][value="'+id+'"]').prop('checked', checked).trigger('change.wp-sync'); 
  }

  function getParentLi(input){
    // Go from this input -> its <li> -> closest parent <li> that owns this child list
    var li = $(input).closest('li');
    var parentLi = li.parents('ul.children').first().closest('li');
    return parentLi.length ? parentLi : null;
  }

  function anyDescendantChecked(li){
    return $(li).find('ul.children input[type="checkbox"]:checked').length > 0;
  }

  // Handle changes; ignore our own synthetic change to prevent loops
  box.on('change', 'input[type="checkbox"]', function(e){
    if(e.namespace === 'wp-sync') return; // prevent recursion

    var checked = this.checked;
    var parentLi = getParentLi(this);

    // Cascade up through all ancestors (parent -> grandparent -> ...)
    while(parentLi){
      var parentInput = parentLi.find('> label input[type="checkbox"]');
      if(!parentInput.length) break;

      var parentId = parentInput.val();

      if(checked){
        setCheckedByTermId(parentId, true);
      } else {
        if(!anyDescendantChecked(parentLi)){
          setCheckedByTermId(parentId, false);
        }
      }
      parentLi = parentLi.parents('ul.children').first().closest('li');
    }
  });

  // On load: ensure parents are checked if any children are pre-checked
  box.find('ul.categorychecklist input[type="checkbox"]:checked').each(function(){
    $(this).trigger('change');
  });
});
JS;

    // Ensure jQuery is available in admin and inject inline
    wp_add_inline_script('jquery', $js);
});


// ==========================================================================================
// Old Code - Commented on 12-10-2026
// ==========================================================================================
/*
add_filter('acf/fields/relationship/query/key=field_675963be624de', function($args, $field, $post_id) {

    error_log('-------------------------------------------');
    error_log('🔄 ACF Relationship Query Triggered');
    error_log('Post ID: ' . $post_id);

    $post = get_post($post_id);
    if (!$post) {
        error_log('❌ No post found for this post_id.');
        return $args;
    }

    $page_slug = $post->post_name;
    error_log('📄 Page slug: ' . $page_slug);

    $property_area_term = get_term_by('slug', $page_slug, 'property_area');

    if ($property_area_term) {
        error_log('✅ Found property_area: ' . $property_area_term->name . ' (ID: ' . $property_area_term->term_id . ')');
    } else {
        error_log('⚠️ No property_area found matching slug: ' . $page_slug);
    }

    $custom_tax_query = [
        'relation' => 'AND',
        [
            'taxonomy' => 'property_status',
            'field'    => 'term_id',
            'terms'    => [135],
        ]
    ];

    if ($property_area_term) {
        $custom_tax_query[] = [
            'taxonomy' => 'property_area',
            'field'    => 'term_id',
            'terms'    => [$property_area_term->term_id],
        ];
    }

    $args['tax_query'] = array_merge($args['tax_query'] ?? [], $custom_tax_query);
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';

    error_log('🧠 Final tax_query: ' . print_r($args['tax_query'], true));

    return $args;

}, 10, 3);
*/

// ==========================================================================================
// New Code - Property Merchandising & Ordering System - Added on 12-10-2026
// ==========================================================================================
require_once get_template_directory() . '/includes/property-merchandising.php';


// For Bulk Pdf Generation
// /wp-admin/?lsg_bulk_pdf=1&post_id=42439
// add_action('admin_init', function () {

//     if (!current_user_can('manage_options')) {
//         return;
//     }

//     if (!isset($_GET['lsg_bulk_pdf'])) {
//         return;
//     }

//     set_time_limit(0);

//     // 🔥 If post_id is provided → generate ONLY that post
//     if (!empty($_GET['post_id'])) {

//         $post_id = (int) $_GET['post_id'];

//         if (get_post_type($post_id) !== 'property') {
//             wp_die('❌ Invalid property ID');
//         }

//         // Generate PDFs for this ONE post (EN + ES, branded + unbranded)
//         lsg_create_pdf_on_save($post_id);

//         wp_die('✅ 4 PDFs generated for property ID ' . $post_id);
//     }

//     // 🔁 Otherwise fallback to paginated bulk (optional)
//     $paged = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;

//     $query = new WP_Query([
//         'post_type'      => 'property',
//         'posts_per_page' => 1,
//         'paged'          => $paged,
//         'post_status'    => 'publish',
//         'fields'         => 'ids',
//     ]);

//     foreach ($query->posts as $id) {
//         lsg_create_pdf_on_save($id);
//     }

//     if ($query->max_num_pages > $paged) {
//         wp_redirect(admin_url('?lsg_bulk_pdf=1&paged=' . ($paged + 1)));
//         exit;
//     }

//     wp_die('✅ All PDFs regenerated successfully.');
// });


// For get Language Audit
// /wp-admin/?lsg_language_audit=1
// add_action('admin_init', function () {

//     if (!current_user_can('manage_options')) {
//         return;
//     }

//     if (!isset($_GET['lsg_language_audit'])) {
//         return;
//     }

//     $languages = apply_filters('wpml_active_languages', null);

//     echo '<h1>Property Language Audit</h1>';
//     echo '<table border="1" cellpadding="8" cellspacing="0">';
//     echo '<tr>
//         <th>#</th>
//         <th>Property ID</th>
//         <th>Title</th>
//         <th>Status</th>
//     </tr>';

//     $posts = get_posts([
//         'post_type'        => 'property',
//         'posts_per_page'   => -1,
//         'post_status'      => 'publish',
//         'suppress_filters' => false,
//     ]);

//     $i = 1;

//     foreach ($posts as $post) {

//         $translations = [];

//         foreach ($languages as $lang_code => $lang) {

//             $translated_id = apply_filters(
//                 'wpml_object_id',
//                 $post->ID,
//                 'property',
//                 false,
//                 $lang_code
//             );

//             if ($translated_id) {
//                 $translations[] = strtoupper($lang_code);
//             }
//         }

//         if (count($translations) === 2) {
//             $status = 'EN + ES';
//         } elseif (in_array('EN', $translations)) {
//             $status = 'EN only';
//         } elseif (in_array('ES', $translations)) {
//             $status = 'ES only';
//         } else {
//             $status = 'No language';
//         }

//         echo "<tr>
//             <td>{$i}</td>
//             <td>{$post->ID}</td>
//             <td>{$post->post_title}</td>
//             <td>{$status}</td>
//         </tr>";

//         $i++;
//     }

//     echo '</table>';
//     exit;
// });
// 21-05-2026
function custom_property_search_title_and_property_id($search, $wp_query)
{
    global $wpdb;

    // Frontend search only
    if (
        !is_admin() &&
        $wp_query->is_main_query() &&
        $wp_query->is_search()
    ) {

        $search_term = $wp_query->get('s');

        if (!empty($search_term)) {

            $like = '%' . $wpdb->esc_like($search_term) . '%';

            $search = $wpdb->prepare(
                "
                AND (
                    {$wpdb->posts}.post_title LIKE %s
                    OR EXISTS (
                        SELECT 1
                        FROM {$wpdb->postmeta}
                        WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                        AND {$wpdb->postmeta}.meta_key = 'property_id'
                        AND {$wpdb->postmeta}.meta_value LIKE %s
                    )
                )
                ",
                $like,
                $like
            );
        }
    }

    return $search;
}

add_filter('posts_search', 'custom_property_search_title_and_property_id', 10, 2);
/**
 * Sync WPML translation post status.
 * English -> Spanish
 */
add_action('transition_post_status', 'sync_wpml_translation_status', 10, 3);

function sync_wpml_translation_status($new_status, $old_status, $post)
{
    // Prevent unnecessary runs
    if ($new_status === $old_status) {
        return;
    }

    // Change 'property' to your actual post type if different
    if ($post->post_type !== 'property') {
        return;
    }

    // Ignore autosaves and revisions
    if (wp_is_post_revision($post->ID) || defined('DOING_AUTOSAVE')) {
        return;
    }

    // Only run for English posts
    $language = apply_filters('wpml_post_language_details', null, $post->ID);

    if (empty($language) || $language['language_code'] !== 'en') {
        return;
    }

    // Get Spanish translation
    $trid = apply_filters('wpml_element_trid', null, $post->ID, 'post_' . $post->post_type);

    if (!$trid) {
        return;
    }

    $translations = apply_filters('wpml_get_element_translations', null, $trid, 'post_' . $post->post_type);

    if (empty($translations['es']->element_id)) {
        return;
    }

    $spanish_post_id = $translations['es']->element_id;

    // Update Spanish status
    remove_action('transition_post_status', 'sync_wpml_translation_status', 10);

    wp_update_post([
        'ID'          => $spanish_post_id,
        'post_status' => $new_status,
    ]);

    add_action('transition_post_status', 'sync_wpml_translation_status', 10, 3);
}

// 
/**
 * Property Translation Report
 */
require_once get_template_directory() . '/inc/property-report.php';
require_once get_template_directory() . '/inc/property-report-export.php';