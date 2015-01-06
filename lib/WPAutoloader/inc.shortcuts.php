<?php

/**
 * Register given path as location for classes autoload search
 *
 * @param string $path
 * @since 2.0.0
 */
function wpautoload_register_path ( $path ) {
    return \WPAutoloader\AutoLoad::RegisterPath( $path );
}

/**
 * Register given path as root location for classes /lib folder
 *
 * @param string $path            
 * @since 2.0.1
 */
function wpautoload_register_root ( $path ) {
    return \WPAutoloader\AutoLoad::RegisterRootPath( $path );
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
function wpautoload_register_function ( $autoload_function = null, $throw = null, $prepend = null ) {
    return \WPAutoloader\AutoLoad::RegisterFunction( $autoload_function, $throw, $prepend );
}

/**
 * Determines whether the method is called inside itself
 * 
 * @param string $f            
 * @return boolean
 * @since 2.0.4
 */
function are_we_in ( $f ) {
    $array = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
    foreach ( $array as $a ) {
        $aa[] = $a["function"];
    }
    if ( in_array( $f, $aa ) ) {
        return true;
    }
    return false;
}
if ( ! function_exists( 'get_called_file_name' ) ) {

    function get_called_file_name ( $filename_only = false ) {
        $backtrace = debug_backtrace( defined( "DEBUG_BACKTRACE_IGNORE_ARGS" ) ? DEBUG_BACKTRACE_IGNORE_ARGS : FALSE );
        $top_frame = array_pop( $backtrace );
        $file = $top_frame['file'];
        if ( $filename_only ) {
            $path_parts = pathinfo( get_called_file_name() );
            $file = $path_parts['filename'];
        }
        return $file;
    }
}

if ( ! function_exists( 'is_login_page' ) ) {

    function is_login_page () {
        if ( get_called_file_name( true ) == 'wp-login.php' ) {
            return true;
        }
    }
}

if ( ! function_exists( 'is_signup_page' ) ) {

    function is_signup_page () {
        if ( get_called_file_name( true ) == 'wp-signup.php' ) {
            return true;
        }
    }
}

if ( ! function_exists( 'is_activate_page' ) ) {

    function is_activate_page () {
        if ( get_called_file_name( true ) == 'wp-activate.php' ) {
            return true;
        }
    }
}

