<?php

/**
 * Abstract Bootstrap file to avoid problems with Zend_Application_Bootstrap_Bootstrap
 *
 * Each module will have an individual bootstrap file called ModuleName_Bootstrap (ex: 
 * Frontend_Module for the frontend module) and the whole application will have a generic 
 * boostrap file. 
 *
 * Each resource should be bootstrapped in a protected method called _init{Resource}. If a 
 * resource is dependent on another, dependences can be forced using $this->boostrap('Resource')
 *
 * Example: 
 *
 * class Bootstrap extends App_Bootstrap_Abstract
 * {
 * protected function _initDb()
 * {
 * // init the db connection
 * }
 *
 * protected function _initDbProfiler()
 * {
 * // this one depends on the Db resource so...
 * $this->bootstrap('Db');
 * }
 * }
 *
 * Some resources might be forced to load first or last using the $this->_first and $this->_last
 * variables
 *
 * @category App
 * @package App_Bootstrap
 * @copyright Copyright (c) 2011, Morteza Milani
 */
abstract class App_Bootstrap_Abstract {

    /**
     * Holds an array with the already bootstrapped
     * resources
     * 
     * @var array
     * @access private
     */
    private $_bootstrapped = array();

    /**
     * Array of resources to be bootstrapped first
     * 
     * @var array
     * @access protected
     */
    protected $_first = array();

    /**
     * Array of resources to be bootstrapped last
     * 
     * @var array
     * @access protected
     */
    protected $_last = array();

    /**
     * Constructs a new boostrap object
     * 
     * @param bool $boostrapEverything 
     * @access public
     * @return void
     */
    public function __construct($boostrapEverything = TRUE){

        if( $boostrapEverything ){
            $this->boostrapEverything();
        }
    }

    /**
     * Bootstraps all the _init* style methods
     * 
     * @access public
     * @return void
     */
    public function boostrapEverything(){

        // bootstrap the first batch
        foreach( $this->_first as $resource ){
            $this->bootstrap($resource);
        }
        // bootstrap the main resources
        $methods = get_class_methods($this);
        foreach( $methods as $method ){
            if( strpos($method, '_init') === 0 ){
                $resource = substr($method, 5);
                if( ! in_array($resource, $this->_first) &&
                 ! in_array($resource, $this->_last) ){
                    $this->bootstrap($resource);
                }
            }
        }
        // bootstrap the last resources
        foreach( $this->_last as $resource ){
            $this->bootstrap($resource);
        }
    }

    /**
     * Bootstraps the specified resource(s)
     * 
     * @param mixed $resources 
     * @access public
     * @return void
     */
    public function bootstrap($resources){

        if( ! is_array($resources) ){
            $resources = array(
                $resources
            );
        }
        foreach( $resources as $resource ){
            if( ! in_array($resource, $this->_bootstrapped) ){
                $method = '_init' . $resource;
                if( method_exists($this, $method) ){
                    call_user_func(
                    array(
                        $this, $method
                    ));
                    $this->_bootstrapped[] = $resource;
                }else{
                    throw new Zend_Exception(
                    'Method ' . $method .
                     ' could not be found in order to boostrap ' . $resource);
                }
            }
        }
    }
}