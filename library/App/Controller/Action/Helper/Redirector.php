<?php

/**
 * Add simple redirection functionality using current route
 *
 * @category App
 * @package App_Controller
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Redirector {

    /**
     * Build a URL based on a route
     * current route is used if non provided 
     *
     * @param  array   $urlOptions
     * @param  string  $name Route name
     * @param  boolean $reset
     * @param  boolean $encode
     * @return void
     */
    public function setGotoRoute(array $urlOptions = array(), $name = null, $reset = false, 
    $encode = true){

        $router = $this->getFrontController()->getRouter();
        if( $name === null ){
            $name = $router->getCurrentRouteName();
        }
        $url = $router->assemble($urlOptions, $name, $reset, $encode);
        $this->_redirect($url);
    }
}