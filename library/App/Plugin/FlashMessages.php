<?php

/**
 * Handle the flash messages and pass it to the views
 *
 * @package App_Plugin
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Plugin_FlashMessages extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(
    Zend_Controller_Request_Abstract $request){

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
        'ViewRenderer');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper(
        'FlashMessenger');
        $view = $viewRenderer->view;
        if( $flash->hasMessages() ){
            $view->flashMessages = $flash->getMessages();
        }else{
            $view->flashMessages = NULL;
        }
    }
}