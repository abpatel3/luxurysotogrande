<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the WP Bakery Modules
 *
 */
function wpbs_s_include_files_wp_bakery()
{

    // Get dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include single calendar component
    if (file_exists($dir_path . 'vc-search-widget.php')) {
        include $dir_path . 'vc-search-widget.php';
    }

}
add_action('wpbs_s_include_files', 'wpbs_s_include_files_wp_bakery');