<?php

/**
 * Default entry point in the application
 *
 * @package frontend_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class IndexController extends App_Frontend_Controller {

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
        $this->title = 'Dashboard';
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        if($identity === NULL || $identity->username === 'guests')
            $this->_redirect('/account/login');
            
        $userId = $identity->id;     
        $notificationModel = new Notification();
        $this->view->paginate = $notificationModel->retrieveNotifications($userId, $this->_getPage());
    }
    
    public function markasreadAction(){
        
        $id = $this->_requireParam('id', App_Controller::NUMERIC_T,'/');
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        $notificationModel = new Notification();
        $notificationModel->markAsRead($userId, $id);
        
        $this->_redirect('/');
        
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
}