<?php
namespace WPAutoloader\Abstracts;

/**
 * Abstract class for singleton object creation.
 * For WordPress plugins creation use Plugins class instead of this class
 *
 * @author Dave A. Holyfield
 * @version 1.0.0
 */
abstract class Singleton {

    protected function __construct () {
    }

    abstract protected function init ();

    final public static function getInstance () {
        static $aoInstance = array ();
        
        $calledClassName = get_called_class();
        
        if ( ! isset( $aoInstance[$calledClassName] ) ) {
            $aoInstance[$calledClassName] = new $calledClassName();
            $aoInstance[$calledClassName]->init();
        }
        
        return $aoInstance[$calledClassName];
    }

    final private function __clone () {
    }

}

