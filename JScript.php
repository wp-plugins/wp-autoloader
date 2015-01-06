<?php
namespace WPExtend;

/**
 * Represents JavaScript files, loaded by WordPress
 *
 * @author Dave A. Holyfield
 * @since 2.0.3
 */
class JScript {

    const flag_front = 1;

    const flag_admin = 2;

    const flag_login = 4;

    private $_handle;

    private $_src;

    private $_deps;

    private $_ver;

    private $_footer;

    private $_where;

    private $_flag;

    /**
     * Constructor
     *
     * @param string $handle            
     * @param string $src            
     * @param array $deps            
     * @param string $ver            
     * @param string $in_footer            
     * @since 2.0.3
     */
    final public function __construct ( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $flag = 1 ) {
        $this->_handle = $handle;
        $this->_src = $src;
        $this->_deps = self::ParseDeps( $deps );
        $this->_ver = $ver;
        $this->_footer = $in_footer;
        $this->_flag = $flag;
    }

    /**
     * Parses given comma separated string into an array
     *
     * @param string $string            
     * @return array string
     * @since 2.0.3
     */
    final public static function ParseDeps ( $string ) {
        if ( is_array( $string ) ) {
            return $string;
        }
        $deps = array ();
        if ( is_string( $string ) ) {
            $depsp = explode( ',', $string );
            foreach ( $depsp as $dep ) {
                $dep = trim( $dep );
                if ( strlen( $dep ) > 0 ) {
                    $deps[] = $dep;
                }
            }
        }
        return $deps;
    }

    /**
     * Register new Javascript file.
     *
     * @since 2.0.3
     */
    final public function Register () {
        wp_register_script( $this->_handle, $this->_src, $this->_deps, $this->_ver, $this->_footer );
    }

    /**
     * Enqueues script.
     *
     * @since 2.0.3
     */
    final public function Enqueue () {
        wp_enqueue_script( $this->_handle );
    }

    /**
     *
     * @return boolean
     * @since 2.0.7
     */
    public function IsEnqueued () {
        return self::IsScriptEnqueued( $this->_handle );
    }

    /**
     *
     * @return boolean
     * @since 2.0.7
     */
    public function IsRegistered () {
        return self::IsScriptRegistered( $this->_handle );
    }

    /**
     *
     * @param string $handle            
     * @return boolean
     * @since 2.0.7
     */
    public static function IsScriptEnqueued ( $handle ) {
        global $wp_scripts;
        if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
            return false;
        }
        return (bool) $wp_scripts->query( $handle, 'enqueued' );
    }

    /**
     *
     * @param string $handle            
     * @return boolean
     * @since 2.0.7
     */
    public static function IsScriptRegistered ( $handle ) {
        global $wp_scripts;
        if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
            return false;
        }
        return (bool) $wp_scripts->query( $handle, 'registered' );
    }

    /**
     *
     * @return string
     * @since 2.0.7
     */
    public function Handle () {
        return $this->_handle;
    }

    /**
     *
     * @return number
     * @since 2.0.7
     */
    public function GetFlag () {
        return $this->_flag;
    }

}

