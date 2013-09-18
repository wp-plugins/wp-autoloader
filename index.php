<?php
/*
* Plugin Name: WP Autoloader
* Plugin URI: https://bitbucket.org/holyfield/wp-autoloader/
* Description: <strong>WordPress Autoloader </strong> makes WordPress developers life really easy. This plugin loads automatically any class. Many WordPress developers writing object-oriented applications create one PHP source file per class definition. One of the biggest annoyances is having to write a long list of needed includes at the beginning of each script (one for each class). With <strong>WordPress Autoloader</strong>, this is no longer necessary. If you put all your classes into /lib folder on your theme or plugin root directory, then all these classes are loaded automatically. Additionally you can define class search path or your own method to automatically load your classes in case you are trying to use a class/interface which hasn't been defined yet. By using <strong>WordPress Autoloader</strong> the scripting engine is given a last chance to load the class before PHP fails with an error. Please see documentation for usage instructions. 
* Version: 2.0.6
* Author: Premium WordPress Apps
* Author URI: http://wp-apps.co.uk/
* License: GNU General Public License
*/
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Request AutoLoad class
require_once 'lib/WPAutoloader/AutoLoad.php';
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Hook WP Autoloader plugin
\WPAutoloader\AutoLoad::Hook ();
