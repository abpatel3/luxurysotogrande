<?php
/**
 * Plugin Name:       StackCache
 * Plugin URI:        http://cache.stackcp.com/
 * Description:       Wrapper to include the Stack Cache Plugin Library 
 * Author:            Stack CP
 */
if ( ! defined( "WPINC" ) ) die;
$file_suffix = "/wp-admin/includes/file.php";
$count = 0;
while ( true ) {
        $path = str_repeat("../", $count) . $file_suffix;
        $full_path = plugin_dir_path(__FILE__) . $path;
        if ( file_exists($full_path) ) {
                require_once($full_path);
                define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
                break;
        }
        if ( $count++ > 5 ) {
                print("Failed to find 'wp-admin/includes/file.php'");
                exit(1);
        }
}
require "/usr/share/php/wp-stack-cache.php";
$wpsc = new WPStackCache();

