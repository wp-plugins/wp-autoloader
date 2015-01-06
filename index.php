<?php
/*
 * Plugin Name: WP Autoloader
 * Plugin URI: http://myminiapp.com/wordpress-plugins/wp-autoloader/
 * Description: <strong>WordPress Autoloader </strong> helps to make life of WordPress developers a lot easier. Most of devlopers of object-oriented applications are writing one PHP source file per class definition. One of the biggest annoyances is to write a long list of needed includes at the beginning of each script (one for each class). With <strong>WordPress Autoloader</strong>, this is no longer necessary. This plugin loads automatically any class. Put all classes into /lib or /framework folder inside your theme or plugin root directory and all classes from there are loaded automatically. Please see documentation for usage instructions.
 * Version: 2.0.9
 * Author: MyMiniapp.com
 * Author URI: http://myminiapp.com/
 * License: GNU General Public License
 */
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Check that WordPress is loaded, if not, output a message and terminate the current script
if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
    die( md5_file( __FILE__ ) );
}
/**
 * Define PHP_INT_MIN - Missing on some systems
 */
@define( 'WPAUTOLOAD_DIR', dirname( __FILE__ ) );
if ( ! defined( 'PHP_INT_MIN' ) ) {
    if ( defined( 'PHP_INT_MAX' ) ) {
        define( 'PHP_INT_MIN', (int) (- 1 - PHP_INT_MAX) );
    }
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Check PHP version
if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
    require_once realpath( dirname( __FILE__ ) . '/lib/-functions/admin_fail_notices.php' );
    return add_action( 'admin_notices', 'plg_wpautoloader_admin_notice_php_version' );
}
// Load WP Autoloader
require_once realpath( dirname( __FILE__ ) . '/lib/WPAutoloader/AutoLoad.php' );
// Calls \WPAutoloader\AutoLoad::Hook(); to avoid fatal error on systems < PHP 5.3
call_user_func( '\WPAutoloader\AutoLoad::Hook' );
