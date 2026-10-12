<?php
/**
 * Plugin Name: Ninja Forms Multilingual
 * Plugin URI: https://wpml.org/documentation/related-projects/creating-multilingual-forms-using-ninja-forms-and-wpml/?utm_source=plugin&utm_medium=gui&utm_campaign=ninjaforms
 * Description: Add multilingual support for Ninja Forms | <a href="https://wpml.org/documentation/related-projects/creating-multilingual-forms-using-ninja-forms-and-wpml/?utm_source=plugin&utm_medium=gui&utm_campaign=ninjaforms">Documentation</a>
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 0.4.0
 * Plugin Slug: wpml-ninja-forms
 *
 * @package WPML\NINJAFORMS
 */

define( 'WPML_NINJA_FORMS_VERSION', '0.4.0' );
define( 'WPML_NINJA_FORMS_FILE', __FILE__ );
define( 'WPML_NINJA_FORMS_PATH', dirname( WPML_NINJA_FORMS_FILE ) );
define( 'WPML_NINJA_FORMS_VENDOR_PATH', WPML_NINJA_FORMS_PATH . '/vendor' );

/**
 * 1. Initialize the code shared in `wpml/forms`.
 */
require_once WPML_NINJA_FORMS_VENDOR_PATH . '/wpml/forms/loader.php';
wpml_forms_initialize(
	WPML_NINJA_FORMS_VENDOR_PATH . '/wpml/forms',
	untrailingslashit( plugin_dir_url( WPML_NINJA_FORMS_FILE ) ) . '/vendor/wpml/forms'
);

function wpml_ninja_forms_activation_hook() {
	update_option( wpml_forms_bulk_registration_option_name( WPML_NINJA_FORMS_FILE ), true );
}

register_activation_hook( WPML_NINJA_FORMS_FILE, 'wpml_ninja_forms_activation_hook' );

/**
 * 2. Initialize WPML Ninja Forms' code.
 */
add_action( 'plugins_loaded', function() {

	$hasWPMLRequirements = function() {
		if ( ! class_exists( 'WPML_Core_Version_Check' ) ) {
			require_once WPML_NINJA_FORMS_VENDOR_PATH . '/wpml-shared/wpml-lib-dependencies/src/dependencies/class-wpml-core-version-check.php';
		}

		if ( WPML_Core_Version_Check::is_ok( WPML_NINJA_FORMS_PATH . '/wpml-dependencies.json' ) ) {
			return true;
		}

		require_once WPML_NINJA_FORMS_PATH . '/classes/Requirements.php';
		\WPML\Forms\NinjaForms\Requirements::addNotice();

		return false;
	};

	if ( $hasWPMLRequirements() ) {

		/** @see \WPML\Forms\NinjaForms\SharedAPI\Status::INIT_ACTION */
		add_action( 'nf_init', function() {
			require_once WPML_NINJA_FORMS_VENDOR_PATH . '/autoload.php';
			\WPML\Forms\NinjaForms\App::init();
		} );
	}
} );
