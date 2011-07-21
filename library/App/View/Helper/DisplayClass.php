<?php

/**
 * View helper used in error pages. It displays links to ZF API pages for
 * known ZF classes
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_DisplayClass extends Zend_View_Helper_Abstract {

    /**
     * Path to Zend Framework's online API
     * 
     * @var string
     * @access protected
     */
    protected $_zendApiUrl = 'http://framework.zend.com/apidoc/core/';

    /**
     * Convenience method
     * call $this->displayClass() in the view to access 
     * the helper
     *
     * @param mixed $class 
     * @access public
     * @return string
     */
    public function displayClass($class){

        if( is_object($class) ){
            $class = get_class($class);
        }
        if( ! is_string($class) ){
            return $class;
        }
        if( substr($class, 0, 5) == 'Zend_' ){
            $url = $this->_getClassUrl($class);
            $xhtml = sprintf(
            '<a href="%1$s" title="View API docs for %2$s">%2$s</a>', $url, 
            $class);
        }else{
            $xhtml = $class;
        }
        return $xhtml;
    }

    /**
     * Returns the URL for the given class' API page 
     * 
     * @param mixed $class 
     * @access protected
     * @return string
     */
    protected function _getClassUrl($class){

        $reflection = new Zend_Reflection_Class($class);
        $docblock = $reflection->getDocblock();
        $url = $this->_zendApiUrl;
        if( $docblock->hasTag('package') ){
            $url .= $docblock->getTag('package')->getDescription();
        }
        if( $docblock->hasTag('subpackage') ){
            $url .= '/' . $docblock->getTag('subpackage')->getDescription();
        }
        $url .= '/' . $class . '.html';
        return $url;
    }
}