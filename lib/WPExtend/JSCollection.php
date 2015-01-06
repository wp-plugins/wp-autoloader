<?php
namespace WPExtend;

/**
 * Javascripts collection
 *
 * @author Dave A. Holyfield
 * @since 2.0.7
 */
final class JSCollection {

    private $_scripts_added;

    private $_scripts_registered;

    private $_scripts_enqued;

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
        $this->_scripts_added = array ();
        $this->_scripts_registered = array ();
        $this->_scripts_enqued = array ();
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
                // Enque scripts
                add_action( 'wp_enqueue_scripts', $enque_call );
                add_action( 'login_enqueue_scripts', $enque_call );
                add_action( 'admin_enqueue_scripts', $enque_call );
                $this->Enque();
                $this->_initalized = true;
            }
        } else {
            _doing_it_wrong( __METHOD__, 'Direct call of method is disabled. Please use method "add_action" with tag "init" to call this method', '2.0.7' );
        }
    }

    /**
     *
     * @param multitype $handle_or_script            
     * @param string $src            
     * @param multitype $deps            
     * @param string $ver            
     * @param string $media            
     * @return multitype:
     * @since 2.0.7
     */
    public function Add ( $handle_or_script, $src = null, $deps = array(), $ver = false, $in_footer = false, $flag = 1 ) {
        if ( (is_string( $handle_or_script ) && is_string( $src )) || $handle_or_script instanceof \WPExtend\JScript ) {
            $script = null;
            if ( $handle_or_script instanceof \WPExtend\JScript ) {
                $script = $handle_or_script;
            } else {
                $script = new \WPExtend\JScript( $handle_or_script, $src, $deps, $ver, $in_footer, $flag );
            }
            if ( $script->IsRegistered() && $script->IsEnqueued() ) {
                goto end_method_add_script;
            }
            $this->_scripts_added[$script->Handle()] = $script;
        }
        end_method_add_script: // End of method
        return array_merge( $this->_scripts_added, array () );
    }

    /**
     *
     * @param string $handle            
     * @since 2.0.7
     */
    public function Remove ( $handle ) {
        unset( $this->_scripts_added[$key] );
    }

    /**
     *
     * @return multitype:
     * @since 2.0.7
     */
    public function Register () {
        if ( is_array( $this->_scripts_added ) ) {
            foreach ( $this->_scripts_added as $key => $script ) {
                if ( $script instanceof \WPExtend\JScript ) {
                    $script->Register();
                    $this->_scripts_registered[$key] = $script;
                }
                unset( $this->_scripts_added[$key] );
            }
        }
        return array_merge( $this->_scripts_registered, array () );
    }

    /**
     *
     * @return multitype:
     * @since 2.0.7
     */
    public function Enque () {
        $this->Register();
        if ( is_array( $this->_scripts_registered ) ) {
            foreach ( $this->_scripts_registered as $key => $script ) {
                if ( $script instanceof \WPExtend\JScript ) {
                    $script->Enqueue();
                    $this->_scripts_enqued[$key] = $script;
                }
                unset( $this->_scripts_registered[$key] );
            }
        }
        return array_merge( $this->_scripts_enqued, array () );
    }

}

