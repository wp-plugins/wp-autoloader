<?php
namespace WPAutoloader;

/**
 * Make sure that this file is loaded only once
 */
if ( !define( md5( dirname( __FILE__ ) ), dirname( __FILE__ ), true ) ) {
	return;
}

/**
 * Define shortcut for drectory separator
 */
@define( 'DS', DIRECTORY_SEPARATOR );

/**
 * Load Plugin class.
 */
if ( !class_exists( 'WPAutoloader\Abstracts\Plugin' ) ) {
	require_once 'Abstracts/Plugin.php';
}
/**
 * Load WP plugins file
 */
if ( !function_exists( 'is_plugin_inactive' ) ) {
	require_once ( ABSPATH . '/wp-admin/includes/plugin.php' );
}

/**
 * Autoload class for WordPress.
 *
 * WordPress Autoloader makes WordPress developers life really easy. This plugin
 * loads automatically any class. Many WordPress developers writing
 * object-oriented applications create one PHP source file per class definition.
 * One of the biggest annoyances is having to write a long list of needed
 * includes at the beginning of each script (one for each class). With WordPress
 * Autoloader, this is no longer necessary.
 *
 * @author Dave A. Holyfield
 * @version 2.0.6
 */
final class AutoLoad extends \WPAutoloader\Abstracts\Plugin {
	/**
	 * Registered paths
	 *
	 * @var array
	 */
	protected $_paths;
	/**
	 * Loaded classes
	 *
	 * @var array
	 */
	protected $_classes;
	const uri_homepage = 'https://bitbucket.org/holyfield/wp-autoloader/';

	/* (non-PHPdoc)
	 * @see \WPAutoloader\Abstracts\Plugin::init()
	 */
	protected function init() {
		$i = self::getInstance();
		$i->_paths = array();
		$i->_classes = array();
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		add_action( 'muplugins_loaded', 'WPAutoloader\AutoLoad::MakeMeFirst', 0 );
		add_action( 'plugins_loaded', 'WPAutoloader\AutoLoad::MakeMeFirst', 0 );
		add_action( 'activate_plugin', 'WPAutoloader\AutoLoad::ActivatePlugin', 0, 4 );
		add_action( 'validate_plugin', 'WPAutoloader\AutoLoad::ActivatePlugin', 0, 4 );
		add_action( 'activated_plugin', 'WPAutoloader\AutoLoad::MakeMeFirst', 0 );
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		$i->_plugin_links [ 'docs' ] = '<a href="' . get_admin_url() . '/tools.php?page=wp-autoloader">' . __( 'Usage Help' ) . '</a>';
		$i->_plugin_meta_row [ ] = '<a href="' . get_admin_url() . '/tools.php?page=wp-autoloader">Documentation</a>';
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		self::RegisterFunction( 'WPAutoloader\AutoLoad::LoadClass' );
		self::RegisterAllPluginLibs();
	}

	/**
	 *
	 * @param string $plugin        	
	 * @param string $redirect        	
	 * @param string $network_wide        	
	 * @param string $silent        	
	 * @since 2.0.2
	 */
	public static function ActivatePlugin( $plugin, $redirect = '', $network_wide = false, $silent = false ) {
		$plugin_path = realpath( WP_PLUGIN_DIR . DS . $plugin );
		if ( $plugin_path ) {
			self::RegisterRootPath( $plugin_path );
		}
	}

	/**
	 *
	 * @since 2.0.3
	 */
	public static function PrintFrontEndHeader() {
		//Do nothing
	}

	/**
	 *
	 * @since 2.0.3
	 */
	public static function PrintAdminHeader() {
		//Do nothing
	}

	/**
	 *
	 * @since 2.0.4
	 */
	public static function AddActionsAndFilters() {
		//Add Tools submenu
		add_action( 'admin_menu', 'WPAutoloader\AutoLoad::AddSubMenu' );
	}

	/**
	 * Shows an admin notice if plugin were set as firtst
	 * @since 2.0.5
	 */
	public static function MakeMeFirstNotice() {
		$msg [ ] = '<div class="updated"><p>';
		$msg [ ] = 'Plugins loading order has updated:';
		$msg [ ] = '<ul><li>Plugin <strong>WP Autoloader</strong> is the first plugin now!</li></ul>';
		$msg [ ] = '</p></div>';
		echo implode( PHP_EOL, $msg );
	}

	/**
	 * Make this plugin to load first
	 *
	 * @since 2.0.0
	 */
	public static function MakeMeFirst() {
		$i = self::getInstance();
		$active_plugins = get_option( 'active_plugins' );
		$my_key = array_search( $i->_plugin_basename, $active_plugins );
		# if it's 0 it's the first plugin already, no need to continue
		if ( $my_key ) {
			array_splice( $active_plugins, $my_key, 1 );
			array_unshift( $active_plugins, $i->_plugin_basename );
			update_option( 'active_plugins', $active_plugins );
			add_action( 'admin_notices', 'WPAutoloader\AutoLoad::MakeMeFirstNotice' );
		}
	}

	/**
	 * Add SubMenu for this tool
	 *
	 * @since 2.0.0
	 */
	public static function AddSubMenu() {
		add_submenu_page( 'tools.php', 'WordPress Autoloader', 'Autoloader', 'edit_posts', 'wp-autoloader', 'WPAutoloader\AutoLoad::ShowPage' );
	}

	/**
	 * Show Tool Page content
	 *
	 * @since 2.0.0
	 */
	public static function ShowPage() {
		$data = file_get_contents( self::GetDir( '/inc/html/tool-page.html' ) );
		$list = self::GetRegisteredPaths();
		foreach ( $list as $key => $value ) {
			$list [ $key ] = str_replace( ABSPATH, '↓ .' . DS, $value );
		}
		$html_img [ ] = '<a href="' . self::uri_homepage . '" target="_blank">';
		$html_img [ ] = '<img src="' . self::GetUri( '/inc/images/wordpress-autoloader-shield.jpg' ) . '" style="width:100%; height:auto;" />';
		$html_img [ ] = '</a>';
		$search = array(
				'<!-- Root Path -->',
				'<!-- Direcotories -->',
				'<!-- Header Image -->'
		);
		$replace = array(
				ABSPATH,
				implode( '<br>', $list ),
				implode( '', $html_img )
		);
		$data = str_replace( $search, $replace, $data );
		echo $data;
	}

	/**
	 * Register given function as __autoload() implementation
	 *
	 * @param $autoload_function callable        	
	 * @param $throw bool        	
	 * @param $prepend bool        	
	 * @return boolean
	 * @since 2.0.0
	 */
	public static function RegisterFunction( $autoload_function = null, $throw = null, $prepend = null ) {
		$i = self::getInstance();
		$splafunc = spl_autoload_functions();
		if ( is_array( $splafunc ) ) {
			if ( in_array( '__autoload', $splafunc ) ) {
				spl_autoload_register( '__autoload' );
			}
		}
		return spl_autoload_register( $autoload_function, $throw, $prepend );
	}

	/**
	 * Register given path as location for classes autoload search
	 *
	 * @param string $path        	
	 * @since 2.0.0
	 */
	public static function RegisterPath( $path ) {
		$classpath = realpath( $path );
		if ( $classpath ) {
			$i = self::getInstance();
			//Return true if path is already registered
			if ( self::IsPathRegistered( $classpath ) ) {
				return true;
			}
			//If path is file, create dirname
			$classpath = ( is_file( $classpath ) ) ? dirname( $classpath ) : $classpath;
			if ( is_dir( $classpath ) ) {
				$i->_paths [ md5( $classpath ) ] = $classpath;
				return true;
			}
		}
		//Return false
		return false;
	}

	/**
	 *
	 * @param string $root_path        	
	 * @since 2.0.1
	 */
	public static function RegisterRootPath( $root_path ) {
		$classpath = realpath( $root_path );
		if ( $classpath ) {
			$classpath = ( is_file( $classpath ) ) ? dirname( $classpath ) : $classpath;
			$classpath .= '/lib';
			$classpath = realpath( $classpath );
			if ( $classpath ) {
				self::RegisterPath( $classpath );
			}
		}
	}

	/**
	 * Return all registered paths
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public static function GetRegisteredPaths() {
		$i = self::getInstance();
		return array_merge( array(), $i->_paths );
	}

	/**
	 * Return all registered classes
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public static function GetRegisteredClasses() {
		$i = self::getInstance();
		return array_merge( array(), $i->_classes );
	}

	/**
	 * Determine whether specified path is already registered as autoload search
	 * path
	 *
	 * @param string $path        	
	 * @return boolean
	 * @since 2.0.0
	 */
	public static function IsPathRegistered( $path ) {
		$key = false;
		$classpath = realpath( $path );
		if ( $classpath ) {
			$i = self::getInstance();
			$key = array_search( $path, $i->_paths );
		}
		return ( $key === false ) ? false : true;
	}

	/**
	 * Determine whether specified class is already laoded
	 *
	 * @param string $class        	
	 * @return boolean
	 * @since 2.0.0
	 */
	public static function IsClassLoaded( $class ) {
		$i = self::getInstance();
		$key = array_search( $class, $i->_classes );
		return ( $key === false ) ? false : true;
	}

	/**
	 * Try to load the specified class
	 *
	 * @param string $class        	
	 * @return boolean
	 * @since 2.0.0
	 */
	public static function LoadClass( $class ) {
		//Parse class name into class file
		$class_file = DS . str_replace( '\\', DS, $class ) . '.php';
		//Detect if class is already loaded and stop execution
		if ( self::IsClassLoaded( $class ) ) {
			return true;
		}
		//Try to load class file
		$i = self::getInstance();
		foreach ( $i->_paths as $key => $path ) {
			$classpath = realpath( $path . DS . $class_file );
			if ( $classpath ) {
				$loaded = require_once ( $classpath );
				if ( $loaded ) {
					$i->_classes [ md5( $class ) ] = $class;
				}
				return $loaded;
			}
		}
		//Default return
		return false;
	}

	/**
	 * Registers autoload paths for all active plugins
	 *
	 * @since 2.0.0
	 */
	public static function RegisterAllPluginLibs() {

		//Load lib path for all MU plugins
		$mu_plugins = get_mu_plugins();
		foreach ( $mu_plugins as $file => $mu_plugin_data ) {
			self::RegisterRootPath( WPMU_PLUGIN_DIR . DS . $file );
		}

		$active_plugins = get_option( 'active_plugins' );
		$active_plugins = ( is_array( $active_plugins ) ) ? $active_plugins : array();
		$installed_plugins = array_keys( get_plugins() );
		$installed_plugins = ( is_array( $installed_plugins ) ) ? $installed_plugins : array();
		$load_list = array_merge( $active_plugins, $installed_plugins );
		foreach ( $load_list as $plugin ) {
			$plugin_path = realpath( WP_PLUGIN_DIR . DS . $plugin );
			if ( $plugin_path ) {
				self::RegisterRootPath( $plugin_path );
			}
		}

		//Load lib path for template
		$template_lib_path = realpath( get_template_directory() . DS . 'lib' );
		if ( $template_lib_path ) {
			self::RegisterPath( $template_lib_path );
		}
	}
}

//Register shortcuts for Autoloader class methods for easier usage
require_once 'inc.shortcuts.php';
