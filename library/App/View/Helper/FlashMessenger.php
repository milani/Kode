<?php

/**
 * Flash messenger
 * 
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract {

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger = NULL;

    /**
     * Display Flash Messages.
     *
     * @param string $key Message level for string messages
     * @param string $template Format string for message output
     * @return string Flash messages formatted for output
     */
    public function flashMessenger($key = 'text', 
    $template = '<div class="msg %s"><p>%s</p></div>'){

        $flashMessenger = $this->_getFlashMessenger();
        //get messages from previous requests
        $messages = $flashMessenger->getMessages();
        //add any messages from this request
        if( $flashMessenger->hasCurrentMessages() ){
            $messages = array_merge($messages, 
            $flashMessenger->getCurrentMessages());
            //we don't need to display them twice.
            $flashMessenger->clearCurrentMessages();
        }
        //initialise return string
        $translator = Zend_Registry::get('Zend_Translate');

        $output = '';
        //process messages
        foreach( $messages as $message ){
            if( is_array($message) ){
                list ($key, $message) = each($message);
            }
            $output .= '<div id="messages"><div class="top ' . $key . '"></div>';
            $output .= sprintf($template, $key, $translator->translate($message));
            $output .= '<div class="bottom ' . $key . '"></div></div>';
        }
        return $output;
    }

    /**
     * Lazily fetches FlashMessenger Instance.
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function _getFlashMessenger(){

        if( NULL === $this->_flashMessenger ){
            $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'FlashMessenger');
        }
        return $this->_flashMessenger;
    }
}
