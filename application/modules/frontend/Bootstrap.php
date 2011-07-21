<?php

/**
 * Frontend bootstrap
 *
 * @package Frontend
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Frontend_Bootstrap extends App_Bootstrap_Abstract {

    /**
     * Inits the session for the frontend
     * 
     * @access protected
     * @return void
     */
    protected function _initSession(){

        Zend_Session::start();
    }
	
	/**
     * Inits the Zend Paginator component
     *
     * @access protected
     * @return void
     */
    protected function _initPaginator(){
        Zend_Paginator::setDefaultScrollingStyle(
        Zend_Registry::get('config')->paginator->scrolling_style);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('default.phtml');
    }
}
