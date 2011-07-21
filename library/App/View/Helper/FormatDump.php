<?php

/**
 * Formats an error dump
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_FormatDump extends Zend_View_Helper_Abstract {

    /**
     * Convenience method
     * call $this->formatDump() in the view to access 
     * the helper
     *
     * @access public
     * @return string
     */
    public function formatDump($data){

        $output = '<div>';
        if( is_array($data) ){
            $output .= $this->_displayArrayDump($data);
        }else{
            $output .= $this->_displayObjectDump($data);
        }
        return $output;
    }

    /**
     * Displays an array dump
     * 
     * @param array $array 
     * @access protected
     * @return string
     */
    protected function _displayArrayDump($array){

        if( empty($array) ){
            return 'empty array';
        }
        $output = 'array (<a href="">view details</a>)';
        $output .= '<div class="toggle pre">' .
         Zend_Debug::dump($array, NULL, FALSE) . '</div>';
        return $output;
    }

    /**
     * Displays an object dump
     * 
     * @param mixed $object 
     * @access protected
     * @return string
     */
    protected function _displayObjectDump($object){

        $output = $this->view->displayClass($object) .
         ' (<a href="">view details</a>)';
        $output .= '<div class="toggle pre">' .
         Zend_Debug::dump($object, NULL, FALSE) . '</div>';
        return $output;
    }
}