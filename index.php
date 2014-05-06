<?php
/*
 * Plugin Name: WP Autoloader
 * Plugin URI: http://wordpress.org/plugins/wp-autoloader/
 * Description: <strong>WordPress Autoloader </strong> makes WordPress developers life really easy. This plugin loads automatically any class. Many WordPress developers writing object-oriented applications create one PHP source file per class definition. One of the biggest annoyances is having to write a long list of needed includes at the beginning of each script (one for each class). With <strong>WordPress Autoloader</strong>, this is no longer necessary. If you put all your classes into /lib folder on your theme or plugin root directory, then all these classes are loaded automatically. Additionally you can define class search path or your own method to automatically load your classes in case you are trying to use a class/interface which hasn't been defined yet. By using <strong>WordPress Autoloader</strong> the scripting engine is given a last chance to load the class before PHP fails with an error. Please see documentation for usage instructions. 
 * Version: 2.0.7
 * Author: Premium WordPress Apps
 * Author URI: http://wp-apps.co.uk/
 * License: GNU General Public License
 */
# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
# Check that WordPress is loaded, if not, output a message and terminate the current script
if ( !defined( 'WP_PLUGIN_DIR' ) ) {
	die( md5_file( __FILE__ ) );
}
# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
# Check PHP version
if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
	require_once 'lib/-functions/admin_fail_notices.php';
	return add_action( 'admin_notices', 'plg_wpautoloader_admin_notice_php_version' );
}
require_once 'lib/WPAutoloader/AutoLoad.php';
# \WPAutoloader\AutoLoad::Hook();
call_user_func( '\WPAutoloader\AutoLoad::Hook' );
