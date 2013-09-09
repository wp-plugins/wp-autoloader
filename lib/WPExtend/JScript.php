<?php

namespace WPExtend;


/**
 * Represents JavaScript files, loaded by WordPress
 *
 * @author Dave A. Holyfield
 * @since 2.0.3
 */
class JScript {
	private $_handle;
	private $_src;
	private $_deps;
	private $_ver;
	private $_footer;
	private $_where;


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
	final public function __construct($handle, $src = null, $deps = array(), $ver = false, $in_footer = false) {
		$this->_handle = $handle;
		$this->_src = $src;
		$this->_deps = self::ParseDeps ( $deps );
		$this->_ver = $ver;
		$this->_footer = $in_footer;
	}


	/**
	 * Parses given comman separated string into an array
	 *
	 * @param string $string        	
	 * @return array string
	 * @since 2.0.3
	 */
	final public static function ParseDeps($string) {
		if (is_array ( $string )) {
			return $string;
		}
		$deps = array ();
		if (is_string ( $string )) {
			$depsp = explode ( ',', $string );
			foreach ( $depsp as $dep ) {
				$dep = trim ( $dep );
				if (strlen ( $dep ) > 0) {
					$deps [] = $dep;
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
	final public function Register() {
		if (! is_null ( $this->_src )) {
			wp_register_script ( $this->_handle, $this->_src, $this->_deps, $this->_ver, $this->_footer );
		}
	}


	/**
	 * Enqueues script.
	 *
	 * @since 2.0.3
	 */
	final public function Enqueue() {
		wp_enqueue_script ( $this->_handle );
	}
}

