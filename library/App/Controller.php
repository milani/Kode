<?php

/**
 * Default application wide controller parent class
 *
 * @category App
 * @package App_Controller
 * @copyright Copyright (c) 2011, Morteza Milani
 */
abstract class App_Controller extends Zend_Controller_Action {
    
    /**
     * Defines integer for Numeric types
     * 
     * @const int
     */
    const NUMERIC_T = 1;
    
    /**
     * Defines integer for String type
     * 
     * @const int
     */
    const STRING_T = 2;

    /**
     * Defines integer for Null type
     * 
     * @const int
     */
    const NULL_T = 3;
    
    /**
     * Returns variable type as integer
     * 
     * @param mixed $var
     * @return int
     */
    protected function getType($var)
    {
        if (is_null($var)) return 3;
        if (is_numeric($var)) return 1;
        if (is_string($var)) return 2;
    }
    
    /**
     * Checks if a parameter with a certain type is passed to the controller
     * if not, then sets a message and redirects user to the given url. 
	 *
     * @param string $paramName
     * @param int $type
     * @param string $returnUrl
     */
    protected function _requireParam($paramName,$type,$returnUrl = NULL){
        
        if($this->gettype($this->_getParam($paramName)) !== $type){
            $controller = Zend_Registry::get('controllerName');
            $action = Zend_Registry::get('actionName');
            if($returnUrl == null){
                $this->_helper->FlashMessenger(
                    array(
        				'msg-warn' => 'require_'.strtolower($paramName).'_'.$controller.'_'.$action
                    )
                );
            }else{
                $this->_helper->FlashMessenger(
                    array(
        				'msg-error' => 'REQUIRE_'.strtoupper($paramName).'_'.$controller.'_'.$action
                    )
                );
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            return $this->_getParam($paramName);
        }
    }
    
    /**
     * Converts a human readable size to number of bytes as integer
     * 
     * @param string $str
     * @return int
     */
    protected static function str2byte($str)
    {
        if(is_numeric($str))
            return (int) $str;
    
        if(!preg_match('/^([0-9]+) ?([KMGTPEZY])?B?$/i', trim($str), $match))
            return 0;
    
        if(!empty($match[2]))
            return $match[1] * pow(1024, 1 + (int) stripos('KMGTPEZY', $match[2]));
    
        return (int) $match[1];
    
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init(){
        if($this->getRequest()->isPost()){
            $postSize = (int) $_SERVER['CONTENT_LENGTH'];
            $iniSize = $this->str2byte(ini_get('post_max_size'));
            if($postSize > $iniSize){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-warn' => sprintf('Post size exceeded php defined size. You may see inappropriate errors.'),
                    )
                );
            }
        }
        parent::init();
    }
}
