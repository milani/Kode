<?php

/**
 * Error Controller
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ErrorController extends App_Admin_Controller {

    /**
     * List of Zend_Exceptions for which we display
     * the 404 page
     * 
     * @var array
     * @access protected
     */
    protected $_dispatch404s = array(
        Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE, 
    Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER, 
    Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION
    );

    /**
     * Overrides init() defined in App_Admin_Controller
     * 
     * @access public
     * @return void
     */
    public function init(){

        parent::init();
        $this->_helper->layout()->setLayout('layout');
    }

    /**
     * Handles all errors in the application
     *
     * @access public
     * @return void
     */
    public function errorAction(){

        $errorInfo = $this->_getParam('error_handler');
        if( in_array($errorInfo->type, $this->_dispatch404s) ){
            $this->_dispatch404();
            return;
        }
        $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
        $this->title = 'Internal Server Error';
        $this->view->exception = $errorInfo->exception;
    }

    /**
     * Handles the Flag and Flipper errors
     *
     * @access public
     * @return void
     */
    public function flagflippersAction(){

        if( Zend_Registry::get('IS_DEVELOPMENT') ){
            $this->title = 'Flag and Flipper not found';
            $this->view->originalController = $this->_getParam(
            'originalController');
            $this->view->originalAction = $this->_getParam('originalAction');
        }else{
            $this->_dispatch404();
        }
    }

    /**
     * Displays the forbidden page
     *
     * @access public
     * @return void
     */
    public function forbiddenAction(){

        $this->title = 'Forbidden';
    }

    /**
     * Dispatches the 404 error page as it should be seen
     * by the end user. 
     * 
     * @access protected
     * @return void
     */
    protected function _dispatch404(){

        $this->title = 'Page not found';
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
        $this->render('error-404');
    }
}
