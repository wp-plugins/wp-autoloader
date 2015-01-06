<?php
namespace WPExtend;

/**
 * Stylesheets collection
 * 
 * @author Dave A. Holyfield
 * @since 2.0.7
 */
final class StylesCollection {

    private $_styles_added;

    private $_styles_registered;

    private $_styles_enqued;

    private $_on_admin;

    private $_on_front;

    private $_autoinit;

    private $_initalized;

    /**
     * Object constructor
     * 
     * @param bool $autoinit
     *            Init method is hooked atuomatically and called after WordPress has finished loading but before any headers are sent.
     * @since 2.0.7
     */
    public function __construct ( $autoinit = true ) {
        $this->_styles_added = array ();
        $this->_styles_registered = array ();
        $this->_styles_enqued = array ();
        $this->_autoinit = (bool) $autoinit;
        $this->_initalized = false;
        if ( $this->_autoinit ) {
            $init_call = array ( 
                $this,
                'Init'
            );
            add_action( 'init', $init_call, 0 );
        }
    }

    /**
     * Proper hook to use when enqueuing items that are meant to appear on the front end.
     * since 2.0.7@
     */
    public function Init () {
        global $wp_scripts;
        if ( are_we_in( 'call_user_func_array' ) ) {
            if ( ! $this->_initalized ) {
                $register_call = array ( 
                    $this,
                    'Register'
                );
                $enque_call = array ( 
                    $this,
                    'Enque'
                );
                add_action( 'wp_loaded', $enque_call );
                // Enque styles
                add_action( 'wp_enqueue_scripts', $enque_call );
                add_action( 'login_enqueue_scripts', $enque_call );
                add_action( 'admin_enqueue_scripts', $enque_call );
                $this->Enque();
                $this->_initalized = true;
            }
        } else {
            _doing_it_wrong( __METHOD__, 'Direct call of method is disabled. Please use method "add_action" with tag "init" to call this method', '3.6.1' );
        }
    }

    /**
     *
     * @param multitype $handle_or_style            
     * @param string $src            
     * @param multitype $deps            
     * @param string $ver            
     * @param string $media            
     * @return multitype:
     * @since 2.0.7
     */
    public function Add ( $handle_or_style, $src = null, $deps = array(), $ver = false, $media = 'all', $flag = 1 ) {
        if ( (is_string( $handle_or_style ) && is_string( $src )) || $handle_or_style instanceof \WPExtend\StyleSheet ) {
            $style = null;
            if ( $handle_or_style instanceof \WPExtend\StyleSheet ) {
                $style = $handle_or_style;
            } else {
                $style = new \WPExtend\StyleSheet( $handle_or_style, $src, $deps, $ver, $media, $flag );
            }
            if ( $style->IsRegistered() && $style->IsEnqueued() ) {
                goto end_method_add_style;
            }
            $this->_styles_added[$style->Handle()] = $style;
        }
        end_method_add_style: // End of method
        return array_merge( $this->_styles_added, array () );
    }

    /**
     *
     * @param string $handle            
     * @since 2.0.7
     */
    public function Remove ( $handle ) {
        unset( $this->_styles_added[$key] );
    }

    /**
     *
     * @return multitype:
     * @since 2.0.7
     */
    public function Register () {
        if ( is_array( $this->_styles_added ) ) {
            foreach ( $this->_styles_added as $key => $style ) {
                if ( $style instanceof \WPExtend\StyleSheet ) {
                    $style->Register();
                    $this->_styles_registered[$key] = $style;
                }
                unset( $this->_styles_added[$key] );
            }
        }
        return array_merge( $this->_styles_registered, array () );
    }

    /**
     *
     * @return multitype:
     * @since 2.0.7
     */
    public function Enque () {
        $this->Register();
        if ( is_array( $this->_styles_registered ) ) {
            foreach ( $this->_styles_registered as $key => $style ) {
                if ( $style instanceof \WPExtend\StyleSheet ) {
                    $style->Enqueue();
                    $this->_styles_enqued[$key] = $style;
                }
                unset( $this->_styles_registered[$key] );
            }
        }
        return array_merge( $this->_styles_enqued, array () );
    }

    /**
     *
     * @since 2.0.7
     */
    public function Debug () {
        new \WP_Styles();
        new \WP_Scripts();
        wp_register_script();
        wp_enqueue_script();
        wp_deregister_script();
        wp_dequeue_script();
        do_action( '' );
    }

}
