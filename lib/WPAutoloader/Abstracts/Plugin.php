<?php
namespace WPAutoloader\Abstracts;

if ( ! class_exists( 'WPExtend\BasicWPFilters' ) ) {
    require_once realpath( dirname( __FILE__ ) . '/../../WPExtend/BasicWPFilters.php' );
}

/**
 * Base class for WordPress plugins creation.
 * Use this class as root object for plugin creation. Simplifies plugins creation a lot.
 * 
 * @author Dave A. Holyfield
 * @version 2.0.8
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

    /**
     * Stylesheets collection
     * 
     * @var \WPExtend\StylesCollection
     */
    protected $_plugin_styles = null;

    /**
     * Javascript collections
     * 
     * @var \WPExtend\JSCollection
     */
    protected $_plugin_scripts = null;

    /**
     * Deprecated variable
     * 
     * @var array
     * @deprecated
     *
     */
    protected $_admin_scripts;

    /**
     * Deprecated variable
     * 
     * @var array
     * @deprecated
     *
     */
    protected $_admin_styles;

    /**
     * Deprecated variable
     * 
     * @var array
     * @deprecated
     *
     */
    protected $_front_scripts;

    /**
     * Deprecated variable
     * 
     * @var array
     * @deprecated
     *
     */
    protected $_front_styles;

    /**
     * Contains links to add into plugins page on WordPress Plugins admin page
     * 
     * @var array
     */
    protected $_plugin_links = array ();

    protected $_plugin_meta_row = array ();

    protected $_plugin_info = array ();

    /**
     * User defined priorties for dynamically hooked actions
     * 
     * @var array
     */
    protected $_actions_priority = null;

    /**
     * User defined priorties for dynamically hooked actions
     * 
     * @var array
     */
    protected $_filters_priority = null;

    /**
     * Disable construction
     * 
     * @since 2.0.3
     */
    protected function __construct () {
    }

    /**
     * Init Plugin
     */
    abstract protected function init ();

    /**
     * Retrieves name of file where plugin hook is called
     * 
     * @return string boolean
     * @since 2.0.3
     */
    final protected static function GetHookFile () {
        // Seek trough debug trace and get plugin path
        $class = get_called_class();
        foreach ( debug_backtrace() as $item ) {
            if ( isset( $item['class'] ) && isset( $item['function'] ) && isset( $item['file'] ) ) {
                if ( $item['class'] == __CLASS__ && $item['function'] == 'Hook' ) {
                    if ( strpos( $item['file'], WP_PLUGIN_DIR ) !== false || strpos( $item['file'], WPMU_PLUGIN_DIR ) !== false ) {
                        return $item['file'];
                    }
                }
            }
            if ( isset( $item['function'] ) && isset( $item['args'][0] ) ) {
                if ( $item['function'] == 'call_user_func' && $item['args'][0] == '\\' . $class . '::Hook' ) {
                    if ( strpos( $item['file'], WP_PLUGIN_DIR ) !== false || strpos( $item['file'], WPMU_PLUGIN_DIR ) !== false ) {
                        return $item['file'];
                    }
                }
            }
        }
        return false;
    }

    /**
     * Returns an instance og plugin
     * 
     * @return \WPAutoloader\Abstracts\Plugin
     * @since 2.0.3
     */
    final protected static function getInstance () {
        static $i = array ();
        
        // Get name of called class
        $c = get_called_class();
        
        // Return class object if it exists in classes array or create new instance otherwise
        if ( array_key_exists( $c, $i ) ) {
            return $i[$c];
        } else {
            
            // Get plugin file
            $plugin_initializer = $c::GetHookFile();
            if ( ! $plugin_initializer ) {
                return trigger_error( __( sprintf( 'Plugin is not initialized correctly. Please initalize plugin trough "%s::Hook" first.', $c ), 'wp-autoloader' ), E_USER_ERROR );
            }
            
            // Create a new instance of class
            $i[$c] = new $c();
            
            // Define basic variables
            $i[$c]->_plugin_path = $plugin_initializer;
            $i[$c]->_plugin_dir = dirname( $plugin_initializer );
            $i[$c]->_plugin_uri = plugin_dir_url( $plugin_initializer );
            $i[$c]->_plugin_basename = plugin_basename( $plugin_initializer );
            
            // Define plugin info
            $i[$c]->_plugin_info = get_plugin_data( $i[$c]->_plugin_path, false, false );
            
            // Add filters for plugin
            add_filter( 'plugin_action_links', $c . '::_OnFilterPluginActionLinks', PHP_INT_MAX, 2 );
            add_filter( 'plugin_row_meta', $c . '::_OnFilterPluginRowMeta', PHP_INT_MAX, 2 );
            
            // Add actions for plugin
            add_action( 'admin_notices', $c . '::_OnAdminErrors', 1 - PHP_INT_MAX );
            add_action( 'admin_notices', $c . '::_OnAdminNotices', 1 - PHP_INT_MAX );
            
            // Hook all action methods
            \WPExtend\BasicWPActions::HookAllClassActionMethods( $c, $i[$c]->_actions_priority );
            \WPExtend\BasicWPFilters::HookAllClassFilterMethods( $c, $i[$c]->_filters_priority );
            
            // Call user defined initialization routine
            $i[$c]->init();
            
            // Return new instance
            return $i[$c];
        }
        
        // End fith failure
        return trigger_error( __( sprintf( 'Something went wrong couldnt return object for class %s', $c ), 'wp-autoloader' ), E_USER_ERROR );
    }

    /**
     * Initializes and setups plugin.
     * Should be called from WordPress plugin definition file.
     * 
     * @since 2.0.3
     */
    final public static function Hook () {
        $class = get_called_class();
        $i = $class::getInstance();
    }

    /**
     * Deprecated
     */
    public static function AddAdminStyleSheet ( $handle, $src = null, $deps = array(), $ver = false, $media = 'all' ) {
        $class = get_called_class();
        $class::AddStyleSheet( $handle, $src, $deps, $ver, $media, 3 );
        $class::AddAdminNotice( 'WP Autoloader: Version of <a href="' . $class::GetPluginURI() . '" target="_blank">' . $class::GetName() . '</a> is outdated. Please upgrade the plugin.', true );
    }

    /**
     * Deprecated
     */
    public static function AddAdminJScript ( $handle = null, $src = null, $deps = array(), $ver = false, $in_footer = false ) {
        $class = get_called_class();
        $class::AddJScript( $handle, $src, $deps, $ver, $in_footer, 3 );
        $class::AddAdminNotice( 'WP Autoloader: Version of <a href="' . $class::GetPluginURI() . '" target="_blank">' . $class::GetName() . '</a> is outdated. Please upgrade the plugin.', true );
    }

    /**
     * Add WordPress Admin Notice
     * 
     * @param string $message            
     * @since 2.0.7
     */
    public static function AddAdminNotice ( $message, $error = false ) {
        $opt_name = $error == true ? '_wpa_admin_errors' : '_wpa_admin_notes';
        $class = get_called_class();
        $options = get_option( $opt_name, array () );
        if ( ! isset( $options[$class] ) ) {
            $options[$class] = array ();
        }
        $options[$class][md5( $message )] = $message;
        update_option( $opt_name, $options );
    }

    /**
     *
     * @param string $label            
     * @param string $url            
     * @param string $target            
     */
    final public static function AddPluginActionItem ( $label, $url = null, $target = '_self' ) {
        $label = trim( $label );
        $url = trim( $url );
        $target = trim( $target );
        if ( strlen( $label ) > 0 ) {
            if ( strlen( $url ) > 0 ) {
                $url = esc_url_raw( $url );
                $target = sanitize_html_class( $target );
                $target = strlen( $target ) > 0 ? ' target="' . $target . '"' : '';
                $label = '<a href="' . $url . '" ' . $target . '>' . $label . '</a>';
            }
            $class = get_called_class();
            $i = $class::getInstance();
            $i->_plugin_links[md5( $label )] = $label;
        }
    }

    /**
     *
     * @param string $label            
     * @param string $url            
     * @param string $target            
     */
    final public static function AddPluginMetaRowItem ( $label, $url = null, $target = '_self' ) {
        $label = trim( $label );
        $url = trim( $url );
        $target = trim( $target );
        if ( strlen( $label ) > 0 ) {
            if ( strlen( $url ) > 0 ) {
                $url = esc_url_raw( $url );
                $target = sanitize_html_class( $target );
                $target = strlen( $target ) > 0 ? ' target="' . $target . '"' : '';
                $label = '<a href="' . $url . '" ' . $target . '>' . $label . '</a>';
            }
            $class = get_called_class();
            $i = $class::getInstance();
            $i->_plugin_meta_row[] = $label;
        }
    }

    /**
     *
     * @param unknown $handle_or_script            
     * @param string $src            
     * @param unknown $deps            
     * @param string $ver            
     * @param string $in_footer            
     * @param number $flag            
     */
    final public static function AddJScript ( $handle_or_script, $src = null, $deps = array(), $ver = false, $in_footer = false, $flag = 1 ) {
        $class = get_called_class();
        $i = $class::getInstance();
        if ( is_null( $i->_plugin_scripts ) ) {
            $i->_plugin_scripts = new \WPExtend\JSCollection();
        }
        $i->_plugin_scripts->Add( $handle_or_script, $src, $deps, $ver, $in_footer, $flag );
    }

    /**
     *
     * @param string|\WPExtend\StyleSheet $handle_or_style            
     * @param string $src            
     * @param multitype $deps            
     * @param string $ver            
     * @param string $media            
     * @param number $flag            
     */
    final public static function AddStyleSheet ( $handle_or_style, $src = null, $deps = array(), $ver = false, $media = 'all', $flag = 1 ) {
        $class = get_called_class();
        $i = $class::getInstance();
        if ( is_null( $i->_plugin_styles ) ) {
            $i->_plugin_styles = new \WPExtend\StylesCollection();
        }
        $i->_plugin_styles->Add( $handle_or_style, $src, $deps, $ver, $media, $flag );
    }

    /**
     *
     * @since 2.0.3
     */
    final public static function EnqueueScripts () {
        $class = get_called_class();
        $class::AddAdminNotice( 'WP Autoloader: Version of <a href="' . $class::GetPluginURI() . '" target="_blank">' . $class::GetName() . '</a> is outdated. Please upgrade the plugin.', true );
    }

    /**
     *
     * @since 2.0.3
     */
    final public static function EnqueueAdminScripts () {
        $class = get_called_class();
        $class::AddAdminNotice( 'WP Autoloader: Version of <a href="' . $class::GetPluginURI() . '" target="_blank">' . $class::GetName() . '</a> is outdated. Please upgrade the plugin.', true );
    }

    /**
     * Disable cloning
     * 
     * @since 2.0.3
     */
    final private function __clone () {
    }

    /**
     * Get plugin root folder.
     * If relative file is defined, filename is merged with directory name.
     * 
     * @param string $relative_file            
     * @return string
     * @since 2.0.3
     */
    final public static function GetDir ( $relative_file = null ) {
        $class = get_called_class();
        $i = $class::getInstance();
        return ($relative_file == null) ? $i->_plugin_dir : $i->_plugin_dir . DS . ltrim( $relative_file, '/\\' );
    }

    /**
     * Gets plugin path realtive to the WP plugins direcotry.
     * If relative file is defined, filename is merged with directory name.
     * 
     * @param string $relative_file            
     * @return string
     * @since 2.0.3
     */
    final public static function GetBase ( $relative_file = null ) {
        $class = get_called_class();
        $i = $class::getInstance();
        return ($relative_file == null) ? $i->_plugin_basename : dirname( $i->_plugin_basename ) . DS . ltrim( $relative_file, '/\\' );
    }

    /**
     * Gets an URI of WP theme drictory.
     * If relative file is defined, filename is merged with uri.
     * 
     * @param string $relative_file            
     * @return string
     * @since 2.0.3
     */
    final public static function GetThemeUri ( $relative_file = null ) {
        return ($relative_file == null) ? get_template_directory_uri() : get_template_directory_uri() . '/' . str_replace( '\\', '/', ltrim( $relative_file, '/\\' ) );
    }

    /**
     * Gets WP theme folder path.
     * If relative file is defined, filename is merged with directory name.
     * 
     * @param string $relative_file            
     * @return Ambigous <string, mixed>
     * @since 2.0.3
     */
    final public static function GetThemeDir ( $relative_file = null ) {
        return ($relative_file == null) ? get_template_directory() : get_template_directory() . DS . ltrim( $relative_file, '/\\' );
    }

    /**
     * Gets an URI of plugin direcotry.
     * If relative file is defined, filename is merged with URI.
     * 
     * @param string $relative_file            
     * @return string
     * @since 2.0.3
     */
    final public static function GetUri ( $relative_file = null ) {
        $class = get_called_class();
        $i = $class::getInstance();
        return ($relative_file == null) ? $i->_plugin_uri : rtrim( $i->_plugin_uri, '/\\' ) . '/' . str_replace( '\\', '/', ltrim( $relative_file, '/\\' ) );
    }

    public static function GetPluginInfo ( $info_key ) {
        $class = get_called_class();
        $i = $class::getInstance();
        if ( array_key_exists( $info_key, $i->_plugin_info ) ) {
            return $i->_plugin_info[$info_key];
        }
        return '';
    }

    /**
     * Get name of plugin
     * 
     * @return string
     * @since 2.0.7
     */
    final public static function GetName () {
        return self::GetPluginInfo( 'Name' );
    }

    /**
     * Get Website of plugin
     * 
     * @since 2.0.7
     */
    final public static function GetPluginURI () {
        return self::GetPluginInfo( 'PluginURI' );
    }

    /**
     * Get verson of current plugin
     * 
     * @since 2.0.7
     */
    final public static function GetVersion () {
        return self::GetPluginInfo( 'Version' );
    }

    /**
     * Get WP action methods
     */
    final public static function GetActionMethods () {
        return \WPExtend\BasicWPActions::GetClassActionMethods( get_called_class() );
    }

    /**
     *
     * @return string
     * @since 2.0.7
     */
    public static function PrintWaterMark () {
        $class = get_called_class();
        echo '<!-- Powered by ' . $class::GetName() . ' (version: ' . $class::GetVersion() . ') (' . plugins_url() . '/' . $class::GetBase() . ') -->' . PHP_EOL;
    }

    /**
     * Print all WordPress Admin Notices
     * 
     * @param string $errors            
     * @since 2.0.7
     */
    public static function _OnAdminNotices ( $errors = false ) {
        $opt_name = $errors == true ? '_wpa_admin_errors' : '_wpa_admin_notes';
        $class = get_called_class();
        $options = get_option( $opt_name, array () );
        $messages = array ();
        if ( isset( $options[$class] ) && is_array( $options[$class] ) ) {
            foreach ( $options[$class] as $id => $message ) {
                $messages[] = '<p>' . $message . '</p>';
                unset( $options[$class][$id] );
            }
        }
        if ( count( $messages ) ) {
            $css_class = $errors == true ? 'error' : 'updated';
            $msg[] = '<div class="' . $css_class . '">';
            $msg[] = implode( PHP_EOL, $messages );
            $msg[] = '</div>';
            echo implode( PHP_EOL, $msg );
        }
        update_option( $opt_name, $options );
    }

    /**
     * Print all WordPress Admin Notices
     * 
     * @since 2.0.7
     */
    public static function _OnAdminErrors () {
        $class = get_called_class();
        $class::_OnAdminNotices( true );
    }

    /**
     *
     * @param array $items            
     * @param string $file            
     * @return multitype:
     * @since 2.0.3
     */
    final public static function _OnFilterPluginActionLinks ( $items, $file ) {
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
    final public static function _OnFilterPluginRowMeta ( $items, $file ) {
        $class = get_called_class();
        $i = $class::getInstance();
        if ( $file == $i->_plugin_basename && count( $i->_plugin_meta_row ) > 0 ) {
            $items = array_merge( $i->_plugin_meta_row, $items );
        }
        return $items;
    }

    /**
     * Hook all WP action hook methods for specified class
     * 
     * @param string $class_name            
     * @param array $priorites            
     * @since 2.0.9
     * @version 2.0.9
     */
    final public static function HookActions ( $class_name, $priorites = null ) {
        \WPExtend\BasicWPActions::HookAllClassActionMethods( $class_name, $priorites );
    }

    /**
     * Hook all WP filter hook methods for specified class
     * 
     * @param string $class_name            
     * @param array $priorites            
     * @since 2.0.9
     * @version 2.0.9
     */
    final public static function HookFilters ( $class_name, $priorites = null ) {
        \WPExtend\BasicWPFilters::HookAllClassFilterMethods( $class_name, $priorites );
    }

}

/**
 * Defines must have methods for WP Autoload based plugins
 * 
 * @author Dave A. Holyfield
 * @version 2.0.7
 * @since 2.0.6
 */
interface iPlugin {

    static function Hook ();

    static function AddAdminNotice ( $message, $error = false );

    static function AddPluginActionItem ( $label, $url = null, $target = '_self' );

    static function AddPluginMetaRowItem ( $label, $url = null, $target = '_self' );

    static function AddJScript ( $handle_or_script, $src = null, $deps = array(), $ver = false, $in_footer = false, $flag = 1 );

    static function AddStyleSheet ( $handle_or_style, $src = null, $deps = array(), $ver = false, $media = 'all', $flag = 1 );

    static function GetDir ( $relative_file = null );

    static function GetBase ( $relative_file = null );

    static function GetThemeUri ( $relative_file = null );

    static function GetThemeDir ( $relative_file = null );

    static function GetUri ( $relative_file = null );

    static function GetPluginInfo ( $info_key );

    static function GetName ();

    static function GetPluginURI ();

    static function GetVersion ();

    static function GetActionMethods ();

    static function PrintWaterMark ();

    static function _OnAdminNotices ();

    static function _OnAdminErrors ();

    static function _OnFilterPluginActionLinks ( $items, $file );

    static function _OnFilterPluginRowMeta ( $items, $file );

    static function HookActions ( $class_name );

    static function HookFilters ( $class_name );

}

