=== WP Autoloader ===
Contributors: wp-apps
Tags: php, autoload
Donate link: http://myminiapp.com/wordpress-plugins/wp-autoloader/
Requires at least: 3.7
Tested up to: 4.1
Stable tag: stable
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Autoloader helps to make life of WP developers a lot easier. This plugin loads automatically PHP classes, no need to wrote include scripts.

== Description ==

= Cool Admin Colour Schemes =

If you don’t find a favourite colour scheme among the default WordPress colour schemes, there are more options available using a WP Autloader plugin - the plugin adds additional colour schemes. If you install it, you’ll see the new colours available under your profile’s personal options in the admin. With each major upgrade we add an extra colour scheme. All colour schemes are professionally designed and keep your eyes healthy.

= Easy access to PHP Information =

Because every system is setup differently, WP Autoloader provides an easy access to check configuration settings and available predefined variables on a given system. Login as administrator and go to Tools -> PHP Info to check PHP settings of your current installation.

= Automated loading of PHP classes =

[WordPress Autoloader](http://myminiapp.com/wordpress-plugins/wp-autoloader/) helps to make life of WordPress developers a lot easier. Most of developers of object-oriented applications are writing one PHP source file per class definition. One of the biggest annoyances is to write a long list of needed includes at the beginning of each script (one for each class). With WordPress Autoloader, this is no longer necessary.

This plugin loads automatically any PHP class. Put all classes into /lib or /framework folder inside your theme or plugin root directory and all classes from there are loaded automatically.

The great advantage of automatically loaded classes is also significant increase of performance, which means faster loading of web pages. Loading unneeded files wastes server resources and increases dramatically webpage load time.

= Performance Improvement =

But wait – that’s not all! WP Autoloader provides powerful performance improvement for plugins. We have tested a lot of free and commercial plugins, original against modified version which use WP Autoloader. Plugins based on WP Autoloader load faster and avoid waste  of resources.

You can use WP Autoloader to improve development of WordPress plugins, entire development process will be faster and easier. WP Autoloader comes with an easy to use interface for plugins, which can automatically hook your custom methods width appropriate WordPress filters and actions.  Almost 500 action hooks and more than 1000 filter hooks are supported by WP Autoloader.

== Installation ==

* Upload `wp-autoloader` folder to the `/wp-content/plugins` directory
* Activate the plugin through the 'Plugins' menu in WordPress
* Place your class files into `/lib` or `/frameworks` folder in your templates or plugins

= Multisite or must load installation: =
* Upload plugin into `/wp-content/mu-plugins` folder (mu-plugins/wp-autoloader)
* Create file `wp-autoloader.php` in mu-plugins folder
* Download [multisite conf file](http://myminiapp.com/support/wp-autoloader/wp-autoloader-must-use-installation/) or put this code into `wp-autoloader.php` file: <?php require WPMU_PLUGIN_DIR . '/wp-autoloader/index.php';

== Frequently asked questions ==

= How to use =

You have several options to get your classes loaded automatically. The best option is to use the /lib or /frameworks subfolder on you plugin or theme root path, just put all files inside this direcotry and all classess are loaded automatically. The second and easiest way is to define search path for your classes. For advanced needs, the second option is to create your own __autoload method and register it.

Please read [documentation](http://myminiapp.com/wordpress-plugins/wp-autoloader/) for detailed instructions.

== Screenshots ==

1. Information Panel (Menu -> Tools -> Autoloader)

== Changelog ==

= Coming soon =
* Admin Colour Scheme Manager
* Multisite Dashboard Widget: My Sites
* Cron Tasks Manager

= 2.0.9 =
* Admin Colour Scheme - Corporate Blue
* PHP Information Page
* Added support to hook actions and filters directly from AutoPlugin
* Minor documentation update
* Minor updates
* Added http_build_url if missing on current system

= 2.0.8 =
* Various enhancements
* Added support for /frameworks dircotries
* Code is maintained by [myminiapp.com](https://myminiapp.com) now
* WP Filter hooks compatibility updated to WordPress 4.1
* WP Action hooks compatibility updated to WordPress 4.1
* Multi site and must-load compatible
* Tested on WordPress 4.1

= 2.0.7 =
* Any PHP version safe load (Avoids failure if PHP version is less than 5.3)
* Enhanched AutoPlugin
* Almost 1000 WP filter hooks addedd for AutoPlugin 
* Almost 500 WordPress action hooks added for AutoPlugin

= 2.0.6 =
* Minor additions
* Minor fixes

= 2.0.5 =
* Minor additions
* Minor fixes

== Upgrade notice ==

= 2.0.5 =
* Backward compatible

