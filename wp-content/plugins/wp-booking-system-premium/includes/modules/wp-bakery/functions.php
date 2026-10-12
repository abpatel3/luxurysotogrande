<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the WP Bakery Modules
 *
 */
function wpbs_include_files_wp_bakery()
{

    // Get dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include single calendar component
    if (file_exists($dir_path . 'vc-single-calendar.php')) {
        include $dir_path . 'vc-single-calendar.php';
    }
   
    // Include overview calendar component
        if (file_exists($dir_path . 'vc-overview-calendar.php')) {
        include $dir_path . 'vc-overview-calendar.php';
    }   

    // Include yearly calendar component
    if (file_exists($dir_path . 'vc-yearly-calendar.php')) {
        include $dir_path . 'vc-yearly-calendar.php';
    } 

}
add_action('wpbs_include_files', 'wpbs_include_files_wp_bakery');