<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class IndexController extends App_Admin_Controller {

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
     * Controller's entry point
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity(); 
        if($identity === NULL || $identity->username === 'guests')
            $this->_redirect('/account/login');
        else
            $this->_redirect('/class');
    }
}
