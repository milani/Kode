<?php

/**
 * Bootstraps the Admin module
 *
 * @category  admin
 * @package   admin_bootstrap
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Admin_Bootstrap extends App_Bootstrap_Abstract {

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
