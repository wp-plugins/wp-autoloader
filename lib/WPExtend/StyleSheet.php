<?php
namespace WPExtend;

/**
 * Represents Style Sheet files, loaded by WordPress
 *
 * @author Dave A. Holyfield
 * @since 2.0.3
 */
class StyleSheet {

    const flag_front = 1;

    const flag_admin = 2;

    const flag_login = 4;

    private $_handle;

    private $_src;

    private $_deps;

    private $_ver;

    private $_media;

    private $_flag;

    /**
     * Constructor
     *
     * @param string $handle            
     * @param string $src            
     * @param array $deps            
     * @param string $ver            
     * @param string $media            
     *
     */
    final public function __construct ( $handle, $src, $deps = array(), $ver = false, $media = 'all', $flag = 1 ) {
        $this->_handle = $handle;
        $this->_src = $src;
        $this->_deps = JScript::ParseDeps( $deps );
        $this->_ver = $ver;
        $this->_media = $media;
        $this->_flag = $flag;
    }

    /**
     * Register CSS style file.
     * 
     * @since 2.0.3
     */
    public function Register () {
        if ( $this->IsRegistered() ) {
            return true;
        }
        // Validate flag
        $register = false;
        // if we have admin screen
        if ( is_admin() && ($this->_flag & self::flag_admin) ) {
            $register = true;
        }
        // if we have login screen
        if ( is_login_page() && ($this->_flag & self::flag_login) ) {
            $register = true;
        }
        // if we have front end
        if ( ! is_admin() && ($this->_flag & self::flag_front) ) {
            $register = true;
        }
        if ( $register ) {
            wp_register_style( $this->_handle, $this->_src, $this->_deps, $this->_ver, $this->_media );
        }
        return $register;
    }

    /**
     * Enqueue a CSS style file.
     * 
     * @since 2.0.3
     */
    public function Enqueue () {
        if ( $this->IsEnqueued() ) {
            return true;
        }
        if ( $this->Register() ) {
            wp_enqueue_style( $this->_handle );
            return true;
        }
        return false;
    }

    /**
     *
     * @return boolean
     * @since 2.0.7
     */
    public function IsEnqueued () {
        return self::IsStyleEnqueued( $this->_handle );
    }

    /**
     *
     * @return boolean
     * @since 2.0.7
     */
    public function IsRegistered () {
        return self::IsStyleRegistered( $this->_handle );
    }

    /**
     *
     * @param string $handle            
     * @return boolean
     * @since 2.0.7
     */
    public static function IsStyleEnqueued ( $handle ) {
        global $wp_styles;
        if ( $wp_styles instanceof \WP_Styles ) {
            return in_array( $handle, $wp_styles->queue );
        }
        return false;
    }

    /**
     *
     * @param string $handle            
     * @return boolean
     * @since 2.0.7
     */
    public static function IsStyleRegistered ( $handle ) {
        global $wp_styles;
        if ( $wp_styles instanceof \WP_Styles && isset( $wp_styles->registered[$handle] ) ) {
            return true;
        }
        return false;
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

