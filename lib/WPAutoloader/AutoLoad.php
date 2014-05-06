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
 * Load needed classes.
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
 * @version 2.0.7
 */
final class AutoLoad extends \WPAutoloader\Abstracts\Plugin {
	/**
	 * Registered paths
	 * @var array
	 */
	protected $_paths;
	/**
	 * Loaded classes
	 * @var array
	 */
	protected $_classes;

	const uri_homepage = 'http://wordpress.org/plugins/wp-autoloader/';

	/* (non-PHPdoc)
	 * @see \WPAutoloader\Abstracts\Plugin::init()
	 */
	protected function init() {
		$this->_paths = array();
		$this->_classes = array();
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		self::RegisterFunction( 'WPAutoloader\AutoLoad::LoadClass' );
		self::RegisterAllPluginLibs();
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		self::AddPluginActionItem( __( 'Help' ), get_admin_url() . '/tools.php?page=wp-autoloader' );
		self::AddPluginMetaRowItem( 'PHP ' . PHP_VERSION, 'http://www.php.net/releases/', '_blank' );
		self::AddPluginMetaRowItem( 'WP ' . get_bloginfo( 'version' ), 'https://wordpress.org/download/', '_blank' );
		self::AddPluginMetaRowItem( __( 'Documentation' ), get_admin_url() . '/tools.php?page=wp-autoloader' );
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	}

	/**
	 * Make this plugin to load as the very first plugin
	 *
	 * @since 2.0.0
	 */
	public static function MakeMeFirst() {
		static $done = false;
		# Avoid multiple calls
		if ( !$done ) {
			$i = self::getInstance();
			$active_plugins = get_option( 'active_plugins' );
			$my_key = array_search( $i->_plugin_basename, $active_plugins );
			# if it's 0 it's the first plugin already, no need to continue
			if ( $my_key ) {
				array_splice( $active_plugins, $my_key, 1 );
				array_unshift( $active_plugins, $i->_plugin_basename );
				update_option( 'active_plugins', $active_plugins );
				$msg = 'Plugins loading order has updated: <ul><li>Plugin <strong>WP Autoloader</strong> is the first plugin now!</li></ul>';
				self::AddAdminNotice( $msg );
			}
			$done = true;
		}
	}

	public static function OnActionActivatedPlugin() {
		self::MakeMeFirst();
	}

	public static function OnActionPluginsLoaded() {
		self::MakeMeFirst();
	}

	public static function OnActionMupluginsLoaded() {
		self::MakeMeFirst();
	}

	/**
	 *
	 * @param string $plugin
	 * @param string $redirect
	 * @param string $network_wide
	 * @param string $silent
	 * @since 2.0.2
	 */
	public static function OnActionActivatePlugin( $plugin, $redirect = '', $network_wide = false, $silent = false ) {
		$plugin_path = realpath( WP_PLUGIN_DIR . DS . $plugin );
		if ( $plugin_path ) {
			self::RegisterLibRootPath( $plugin_path );
		}
	}

	/**
	 *
	 * @since 2.0.3
	 */
	public static function OnActionWPHead() {
		self::PrintWaterMark();
	}

	/**
	 * Add SubMenu for this tool
	 *
	 * @since 2.0.0
	 */
	public static function OnActionAdminMenu() {
		add_submenu_page( 'tools.php', 'WordPress Autoloader', 'Autoloader', 'edit_posts', 'wp-autoloader', 'WPAutoloader\AutoLoad::OnActionShowInfoPage' );
	}

	/**
	 * Show Tool Page content
	 *
	 * @since 2.0.0
	 */
	public static function OnActionShowInfoPage() {
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
				'<!-- Header Image -->',
				'<!-- Actions List -->',
				'<!-- Filters List -->',
				'<!-- Actions Count -->',
				'<!-- Filters Count -->'
		);
		$replace = array(
				ABSPATH,
				implode( '<br>', $list ),
				implode( '', $html_img ),
				\WPExtend\BasicWPActions::GetHtmlTable(),
				\WPExtend\BasicWPFilters::GetHtmlTable(),
				\WPExtend\BasicWPActions::GetCount(),
				\WPExtend\BasicWPFilters::GetCount()
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
		$splafunc = spl_autoload_functions();
		if ( is_array( $splafunc ) ) {
			if ( in_array( '__autoload', $splafunc ) ) {
				spl_autoload_register( '__autoload' );
			}
		}
		return spl_autoload_register( $autoload_function, $throw, $prepend );
	}

	/**
	 * Register given directory path as location for classes autoload search
	 *
	 * @param string $path Directory path
	 * @since 2.0.0
	 */
	public static function RegisterPath( $path ) {
		$path = rtrim( $path, DS );
		if ( self::IsPathRegistered( $path ) ) {
			return true;
		}
		if ( is_dir( $path ) ) {
			$i = self::getInstance();
			$i->_paths [ md5( $path ) ] = $path;
			return true;
		}
		//Return false
		return false;
	}

	/**
	 *
	 * @param string $root_path Direcotry path
	 * @since 2.0.1
	 */
	public static function RegisterLibRootPath( $root_path ) {
		$root_path = rtrim( $root_path, DS ) . DS . 'lib';
		return self::RegisterPath( $root_path );
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
		$i = self::getInstance();
		return in_array( $path, $i->_paths );
		return false;
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
		return in_array( $class, $i->_classes );
	}

	/**
	 * Try to load the specified class
	 *
	 * @param string $class        	
	 * @return boolean
	 * @since 2.0.0
	 */
	public static function LoadClass( $class ) {
		$class_file = ( '\\' != DS ) ? DS . str_replace( '\\', DS, $class ) . '.php' : DS . $class . '.php';
		$i = self::getInstance();
		foreach ( $i->_paths as $path ) {
			$classpath = $path . $class_file;
			if ( is_file( $classpath ) ) {
				$loaded = @require_once ( $classpath );
				if ( $loaded ) {
					$i->_classes [ md5( $class ) ] = $class;
				}
				return $loaded;
			}
		}
		return false;
	}

	/**
	 * Registers autoload paths for all active plugins
	 *
	 * @since 2.0.0
	 */
	public static function RegisterAllPluginLibs() {

		# Load lib path for all MU plugins
		$mu_plugins = get_mu_plugins();
		foreach ( $mu_plugins as $file => $mu_plugin_data ) {
			self::RegisterLibRootPath( dirname( WPMU_PLUGIN_DIR . DS . $file ) );
		}

		# Load lib path for all plugins
		$load_list = array_unique( array_merge( get_option( 'active_plugins', array() ), array_keys( get_plugins() ) ) );
		foreach ( $load_list as $plugin ) {
			self::RegisterLibRootPath( dirname( WP_PLUGIN_DIR . DS . $plugin ) );
		}

		# Load lib path for template
		self::RegisterLibRootPath( get_template_directory() );

	}
}

//Register shortcuts for Autoloader class methods for easier usage
require_once 'inc.shortcuts.php';
