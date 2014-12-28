<?php
/*
 * Plugin Name: WP Autoloader
 * Plugin URI: http://myminiapp.com/wordpress-plugins/wp-autoloader/
 * Description: <strong>WordPress Autoloader </strong> helps to make life of WordPress developers a lot easier. Most of devlopers of object-oriented applications are writing one PHP source file per class definition. One of the biggest annoyances is to write a long list of needed includes at the beginning of each script (one for each class). With <strong>WordPress Autoloader</strong>, this is no longer necessary. This plugin loads automatically any class. Put all classes into /lib or /framework folder inside your theme or plugin root directory and all classes from there are loaded automatically. Please see documentation for usage instructions.
 * Version: 2.0.8
 * Author: MyMiniapp.com
 * Author URI: http://myminiapp.com/
 * License: GNU General Public License
 */
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Check that WordPress is loaded, if not, output a message and terminate the current script
if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
    die( md5_file( __FILE__ ) );
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Check PHP version
if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
    require_once realpath( dirname( __FILE__ ) . '/lib/-functions/admin_fail_notices.php' );
    return add_action( 'admin_notices', 'plg_wpautoloader_admin_notice_php_version' );
}
require_once realpath( dirname( __FILE__ ) . '/lib/WPAutoloader/AutoLoad.php' );
// \WPAutoloader\AutoLoad::Hook();
call_user_func( '\WPAutoloader\AutoLoad::Hook' );
