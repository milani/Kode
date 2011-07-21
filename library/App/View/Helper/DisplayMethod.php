<?php

/**
 * View helper used in error pages. It displays links to ZF API pages for
 * known ZF methods
 *
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_DisplayMethod extends App_View_Helper_DisplayClass {

    /**
     * Convenience method
     * call $this->displayMethod() in the view to access 
     * the helper
     *
     * @param string $method 
     * @param mixed $class 
     * @access public
     * @return string
     */
    public function displayMethod($method, $class = NULL){

        if( NULL == $class ){
            return $method;
        }
        if( is_object($class) ){
            $class = get_class($class);
        }
        if( ! is_string($class) ){
            return $method;
        }
        if( substr($class, 0, 5) == 'Zend_' ){
            $url = $this->_getClassUrl($class);
            $xhtml = sprintf(
            '<a href="%1$s#%3$s" title="View API docs for method %3$s() of class %2$s">%3$s</a>', 
            $url, $class, $method);
        }else{
            $xhtml = $method;
        }
        return $xhtml;
    }
}