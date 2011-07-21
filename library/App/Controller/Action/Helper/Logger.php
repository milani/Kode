<?php

/**
 * Take the main logger and put it as a Action Helper
 *
 * @category App
 * @package App_Controller
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Controller_Action_Helper_Logger extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Logger to use
     *
     * @var Object
     */
    private $logger;

    /**
     * Called automatically after creating the object
     *
     * @return void
     */
    public function init(){

        //Retrieve the main logger from the registry
        $this->logger = Zend_Registry::get('Zend_Log');
    }

    /**
     * This method is called automatically when using the name of the helper directly
     *
     * @param string $message 
     * @param string $debugLevel 
     * @return void
     */
    public function direct($message, $debugLevel = Zend_Log::INFO){

        $this->logger->log($message, $debugLevel);
    }
}