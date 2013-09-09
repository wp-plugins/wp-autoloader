<?php

/**
 * Register given path as location for classes autoload search
 *
 * @param string $path
 * @since 2.0.0
 */
function wp_autoload_register_path( $path ) {
	return \WPAutoloader\AutoLoad::RegisterPath( $path );
}

/**
 * Register given path as root location for classes /lib folder
 *
 * @param string $path        	
 * @since 2.0.1
 */
function wp_autoload_register_root( $path ) {
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
function wp_autoload_register_function( $autoload_function = null, $throw = null, $prepend = null ) {
	return \WPAutoloader\AutoLoad::RegisterFunction( $autoload_function, $throw, $prepend );
}

/**
 * Determines whether the method is called inside itself
 * @param string $f
 * @return boolean
 * @since 2.0.4
 */
function are_we_in( $f ) {
	$aray = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
	foreach ( $aray as $a ) {
		$aa [ ] = $a [ "function" ];
	}
	if ( in_array( $f, $aa ) ) {
		return true;
	}
	return false;
}
