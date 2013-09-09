# WP Autoloader
* Contributors: wp-apps
* Donate link: 
* Tags: php, autoload
* Requires at least: 3.0
* Tested up to: 3.6
* Stable tag: stable
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Autoloader makes WP developers life really easy. This plugin loads automatically PHP classes. No need to wrote include scripts anymore.

## Description

[WordPress Autoloader](https://bitbucket.org/holyfield/wp-autoloader) makes WordPress developers life really easy. This plugin loads automatically PHP classes. Many developers writing object-oriented applications create one PHP source file per class definition. One of the biggest annoyances is having to write a long list of needed includes at the beginning of each script (one for each class). With WordPress Autoloader, this is no longer necessary.

If you put all your classes into **/lib** folder on your theme or plugin root directory, then all these classes are loaded automatically. Additionally you can define class search path or your own method to automatically load your classes in case you are trying to use a class/interface which hasn't been defined yet. By using WordPress Autoloader the scripting engine is given a last chance to load the class before PHP fails with an error.

The great advantage of automatically loaded classes is also perfomance increase, wich means faster loading. Loading unneeded files wastes server resources and increases webpage load time.

## Installation

1. Upload wp-audotloader to the /wp-content/plugins/ directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place your class files into /lib folder in your templates and plugins

## Frequently asked questions

# How to use?
Please read [documentation](https://bitbucket.org/holyfield/wp-autoloader/wiki) for detailed instructions.

## Screenshots

1. /assets/screenshot.jpg

## Changelog



## Upgrade notice



## How to use

You have several options to get your classes loaded automatically. The best option is to use the /lib subfolder on you plugin or theme root path, just put all files inside this direcotry and all classess are loaded automatically. The second and easiest way is to define search path for your classes. For advanced needs, the second option is to create your own __autoload method and register it.

Please read [documentation](https://bitbucket.org/holyfield/wp-autoloader/wiki) for detailed instructions.