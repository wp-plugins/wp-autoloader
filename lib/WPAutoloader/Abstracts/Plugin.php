<?php
namespace WPAutoloader\Abstracts;

/**
 * Base class for WordPress plugins creation.
 *
 * Use this class as root object for plugin creation. Simplifies plugins
 * creation a lot.
 *
 * @author Dave A. Holyfield
 * @version 2.0.6
 * @since 2.0.3
 */
abstract class Plugin implements iPlugin {
	/**
	 * Full path of current plugin
	 *
	 * @var string
	 * @since 2.0.3
	 */
	protected $_plugin_path;
	/**
	 * WordPress plugin basename
	 *
	 * @var string
	 * @since 2.0.3
	 */
	protected $_plugin_basename;
	/**
	 * Full path to the plugin root directory
	 *
	 * @var string
	 * @since 2.0.3
	 */
	protected $_plugin_dir;
	/**
	 * Full URI of plugin root directory
	 *
	 * @var string
	 * @since 2.0.3
	 */
	protected $_plugin_uri;
	//
	protected $_admin_scripts;
	protected $_admin_styles;
	protected $_front_scripts;
	protected $_front_styles;
	protected $_plugin_links;
	protected $_plugin_meta_row;

	/**
	 * Disable construction
	 *
	 * @since 2.0.3
	 */
	protected function __construct() {
	}

	/**
	 * Init Plugin
	 */
	abstract protected function init();

	/**
	 * Returns an instance og plugin
	 *
	 * @return \WPAutoloader\Abstracts\Plugin
	 * @since 2.0.3
	 */
	final protected static function getInstance() {
		static $i = array();
		$c = get_called_class();
		if ( !isset( $i [ $c ] ) ) {
			$plugin_initializer = $c::GetHookFile();
			if ( !$plugin_initializer ) {
				return trigger_error( __( sprintf( 'Plugin is not initialized correctly. Please initalize plugin trough "%s::Hook" first.', $c ), 'wp-autoloader' ), E_USER_ERROR );
			}
			// Create a new instance
			$i [ $c ] = new $c();
			// Define basic variables
			$i [ $c ]->_plugin_path = $plugin_initializer;
			$i [ $c ]->_plugin_dir = dirname( $i [ $c ]->_plugin_path );
			$i [ $c ]->_plugin_uri = plugin_dir_url( $i [ $c ]->_plugin_path );
			$i [ $c ]->_plugin_basename = plugin_basename( $i [ $c ]->_plugin_path );
			// Create empty arrays
			$i [ $c ]->_admin_scripts = array();
			$i [ $c ]->_admin_styles = array();
			$i [ $c ]->_front_scripts = array();
			$i [ $c ]->_front_styles = array();
			$i [ $c ]->_plugin_links = array();
			$i [ $c ]->_plugin_meta_row = array();
			// Add basic actions
			add_action( 'plugins_loaded', $c . '::AddBasicActionsAndFilters' );
			// Add extra actions
			add_action( 'plugins_loaded', $c . '::AddActionsAndFilters' );
			// Call initalization method
			$i [ $c ]->init();
		}
		//Return an instance
		return $i [ $c ];
	}

	final public static function AddAdminJScript( $handle, $src = null, $deps = array(), $ver = false, $in_footer = false ) {
		$class = get_called_class();
		$i = $class::getInstance();
		$i->_admin_scripts [ ] = new \WPExtend\JScript( $handle, $src, $deps, $ver, $in_footer);
	}

	final public static function AddAdminStyleSheet( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
		$class = get_called_class();
		$i = $class::getInstance();
		$i->_admin_styles [ ] = new \WPExtend\StyleSheet( $handle, $src, $deps, $ver, $media);
	}

	final public static function AddJScript( $handle, $src = null, $deps = array(), $ver = false, $in_footer = false ) {
		$class = get_called_class();
		$i = $class::getInstance();
		$i->_front_scripts [ ] = new \WPExtend\JScript( $handle, $src, $deps, $ver, $in_footer);
	}

	final public static function AddStyleSheet( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
		$class = get_called_class();
		$i = $class::getInstance();
		$i->_front_styles [ ] = new \WPExtend\StyleSheet( $handle, $src, $deps, $ver, $media);
	}

	/**
	 *
	 * @since 2.0.3
	 */
	final public static function AddBasicActionsAndFilters() {
		$class = get_called_class();
		$i = $class::getInstance();
		add_action( 'init', $class . '::WPInit' );
		add_action( 'wp_enqueue_scripts', $class . '::EnqueueScripts' );
		add_action( 'admin_enqueue_scripts', $class . '::EnqueueAdminScripts' );
		add_action( 'wp_head', $class . '::PrintFrontEndHeader' );
		add_action( 'admin_head', $class . '::PrintAdminHeader' );
		add_filter( 'plugin_action_links', $class . '::FilterActionLinks', 10, 2 );
		add_filter( 'plugin_row_meta', $class . '::FilterPluginMetaRow', 10, 2 );
	}

	/**
	 * Called by WordPress Action Hook "init"
	 * @since 2.0.3
	 */
	final public static function WPInit() {
		$class = get_called_class();
		$i = $class::getInstance();
		foreach ( $i->_admin_scripts as $script ) {
			if ( $script instanceof \WPExtend\JScript ) {
				$script->Register();
			}
		}
		foreach ( $i->_admin_styles as $style ) {
			if ( $style instanceof \WPExtend\StyleSheet ) {
				$style->Register();
			}
		}
		foreach ( $i->_front_scripts as $script ) {
			if ( $script instanceof \WPExtend\JScript ) {
				$script->Register();
			}
		}
		foreach ( $i->_front_styles as $style ) {
			if ( $style instanceof \WPExtend\StyleSheet ) {
				$style->Register();
			}
		}
	}

	/**
	 *
	 * @since 2.0.3
	 */
	final public static function EnqueueScripts() {
		$class = get_called_class();
		$i = $class::getInstance();
		foreach ( $i->_front_scripts as $sckey => $script ) {
			if ( $script instanceof \WPExtend\JScript ) {
				$script->Enqueue();
			}
			unset( $i->_front_scripts [ $sckey ] );
		}
		foreach ( $i->_front_styles as $stkey => $style ) {
			if ( $style instanceof \WPExtend\StyleSheet ) {
				$style->Enqueue();
			}
			unset( $i->_front_styles [ $stkey ] );
		}
	}

	/**
	 *
	 * @since 2.0.3
	 */
	final public static function EnqueueAdminScripts() {
		$class = get_called_class();
		$i = $class::getInstance();
		foreach ( $i->_admin_scripts as $sckey => $script ) {
			if ( $script instanceof \WPExtend\JScript ) {
				$script->Enqueue();
			}
			unset( $i->_admin_scripts [ $sckey ] );
		}
		foreach ( $i->_admin_styles as $stkey => $style ) {
			if ( $style instanceof \WPExtend\StyleSheet ) {
				$style->Enqueue();
			}
			unset( $i->_admin_styles [ $stkey ] );
		}
	}

	/**
	 *
	 * @param array $items        	
	 * @param string $file        	
	 * @return multitype:
	 * @since 2.0.3
	 */
	final public static function FilterActionLinks( $items, $file ) {
		$class = get_called_class();
		$i = $class::getInstance();
		if ( $file == $i->_plugin_basename && count( $i->_plugin_links ) > 0 ) {
			$items = array_merge( $i->_plugin_links, $items );
		}
		return $items;
	}

	/**
	 *
	 * @param array $items        	
	 * @param string $file        	
	 * @return multitype:
	 * @since 2.0.3
	 */
	final public static function FilterPluginMetaRow( $items, $file ) {
		$class = get_called_class();
		$i = $class::getInstance();
		if ( $file == $i->_plugin_basename && count( $i->_plugin_meta_row ) > 0 ) {
			$items = array_merge( $i->_plugin_meta_row, $items );
		}
		return $items;
	}

	/**
	 * Disable cloning
	 *
	 * @since 2.0.3
	 */
	final private function __clone() {
	}

	/**
	 * Retrieves name of file where plugin hook is called
	 *
	 * @return string boolean
	 * @since 2.0.3
	 */
	final protected static function GetHookFile() {
		foreach ( debug_backtrace() as $item ) {
			if ( isset( $item [ 'class' ] ) && isset( $item [ 'function' ] ) ) {
				if ( $item [ 'class' ] == __CLASS__ && $item [ 'function' ] == 'Hook' ) {
					return $item [ 'file' ];
				}
			}
		}
		return false;
	}

	/**
	 * Initializes and setups plugin.
	 *
	 * Should be called from WordPress plugin definition file.
	 *
	 * @since 2.0.3
	 */
	final public static function Hook() {
		$class = get_called_class();
		$class::getInstance();
	}

	/**
	 * Get plugin root folder.
	 * If relative file is defined, filename is merged with directory name.
	 *
	 * @param string $relative_file        	
	 * @return string
	 * @since 2.0.3
	 */
	final public static function GetDir( $relative_file = null ) {
		$class = get_called_class();
		$i = $class::getInstance();
		return ( $relative_file == null ) ? $i->_plugin_dir : $i->_plugin_dir . DS . ltrim( $relative_file, '/\\' );
	}

	/**
	 * Gets plugin path realtive to the WP plugins direcotry.
	 * If relative file is defined, filename is merged with directory name.
	 *
	 * @param string $relative_file        	
	 * @return string
	 * @since 2.0.3
	 */
	final public static function GetBase( $relative_file = null ) {
		$class = get_called_class();
		$i = $class::getInstance();
		return ( $relative_file == null ) ? $i->_plugin_basename : dirname( $i->_plugin_basename ) . DS . ltrim( $relative_file, '/\\' );
	}

	/**
	 * Gets an URI of WP theme drictory.
	 * If relative file is defined, filename is merged with uri.
	 *
	 * @param string $relative_file        	
	 * @return string
	 * @since 2.0.3
	 */
	final public static function GetThemeUri( $relative_file = null ) {
		return ( $relative_file == null ) ? get_template_directory_uri() : get_template_directory_uri() . '/' . str_replace( '\\', '/', ltrim( $relative_file, '/\\' ) );
	}

	/**
	 * Gets WP theme folder path.
	 * If relative file is defined, filename is merged with directory name.
	 *
	 * @param string $relative_file        	
	 * @return Ambigous <string, mixed>
	 * @since 2.0.3
	 */
	final public static function GetThemeDir( $relative_file = null ) {
		return ( $relative_file == null ) ? get_template_directory() : get_template_directory() . DS . ltrim( $relative_file, '/\\' );
	}

	/**
	 * Gets an URI of plugin direcotry.
	 * If relative file is defined, filename is merged with URI.
	 *
	 * @param string $relative_file        	
	 * @return string
	 * @since 2.0.3
	 */
	final public static function GetUri( $relative_file = null ) {
		$class = get_called_class();
		$i = $class::getInstance();
		return ( $relative_file == null ) ? $i->_plugin_uri : rtrim( $i->_plugin_uri, '/\\' ) . '/' . str_replace( '\\', '/', ltrim( $relative_file, '/\\' ) );
	}
}

/**
 * @author holyfield
 * @since 2.0.6
 */
interface iPlugin {

	/**
	 * Adds actions to WordPress
	 *
	 * @since 2.0.6
	 */
	static function AddActionsAndFilters();

	/**
	 * Prints spcified content on WP webpage fornt end header
	 *
	 * @since 2.0.6
	 */
	static function PrintFrontEndHeader();

	/**
	 * Prints spcified content on WP Admin webpage header
	 *
	 * @since 2.0.6
	 */
	static function PrintAdminHeader();
}
