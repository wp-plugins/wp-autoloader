<?php

namespace WPExtend;


/**
 * Represents Style Sheet files, loaded by WordPress
 *
 * @author Dave A. Holyfield
 * @since 2.0.3
 */
class StyleSheet {
	private $_handle;
	private $_src;
	private $_deps;
	private $_ver;
	private $_media;


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
	final public function __construct($handle, $src, $deps = array(), $ver = false, $media = 'all') {
		$this->_handle = $handle;
		$this->_src = $src;
		$this->_deps = JScript::ParseDeps ( $deps );
		$this->_ver = $ver;
		$this->_media = $media;
	}


	/**
	 * Register CSS style file.
	 */
	public function Register() {
		wp_register_style ( $this->_handle, $this->_src, $this->_deps, $this->_ver, $this->_media );
	}


	/**
	 * Enqueue a CSS style file.
	 */
	public function Enqueue() {
		wp_enqueue_style ( $this->_handle );
	}
}

