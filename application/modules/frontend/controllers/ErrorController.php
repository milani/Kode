<?php

/**
 * Error Controller
 *
 * @package application_frontend_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ErrorController extends App_Frontend_Controller {

    /**
     * Overrides init() defined in App_Admin_Controller
     * 
     * @access public
     * @return void
     */
    public function init(){

        parent::init();
    }

    /**
     * Handles all errors in the application
     *
     * @access public
     * @return void
     */
    public function errorAction(){

        $content = null;
        $errors = $this->_getParam('error_handler');
        switch($errors->type){
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader(
                'HTTP/1.1 404 Not Found');
                // ... get some output to display...
                $content .= "<h1>404 Page not found!</h1>" .
                 PHP_EOL;
                $content .= "<p>The page you requested was not found.</p>";
                break;
            default:
                // application error; display error page, but don't change
                // status code 
                $content .= "<h1>Error!</h1>" .
                 PHP_EOL;
                $content .= "<p>An unexpected error occurred with your request. Please try again later.</p>";
                // Log the exception
                $exception = $errors->exception;
                $logger = Zend_Registry::get('Zend_Log');
                $logger->debug(
                $exception->getMessage() . PHP_EOL .
                 $exception->getTraceAsString());
                break;
        }
        // Clear previous content
        $this->getResponse()->clearBody();
        $this->view->content = $content;
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
     * Handles the Flag and Flipper errors
     *
     * @access public
     * @return void
     */
    public function flagflipperAction(){

        if( Zend_Registry::get('IS_DEVELOPMENT') ){
            $this->title = 'Flag and Flipper not found';
            $this->view->originalController = $this->_getParam(
            'originalController');
            $this->view->originalAction = $this->_getParam('originalAction');
        }else{
            $this->_dispatch404();
        }
    }
}
