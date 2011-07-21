<?php
/**
 * Allows user to manage the user groups
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */

class SystemController extends App_Admin_Controller
{
    /**
     * Overrides Zend_Controller_Action::init()
     *
     * @access public
     * @return void
     */
    public function init(){
        // init the parent
        parent::init();
    }
    
    /**
     * Allows the user to view all the user groups registered
     * in the application
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->_redirect('/system/configure');
    }
    
    public function configureAction(){
        
    }
}