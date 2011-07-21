<?php
/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';

/**
 * Helper to generate a "Date" element
 *
 * @category   App
 * @package    App_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_Date extends Zend_View_Helper_FormElement {

    /**
     * Generates a "date" element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    private function _appendDependencies($baseUrl){
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if( NULL === $viewRenderer->view ){
            $viewRenderer->initView();
        }
        
        $viewRenderer->view->headLink()->appendStylesheet($baseUrl . '/css/calendar-system.css');

        $viewRenderer->view->headScript()->appendFile($baseUrl . '/js/jalali.js');
        $viewRenderer->view->headScript()->appendFile($baseUrl. '/js/calendar.js');
        $viewRenderer->view->headScript()->appendFile($baseUrl. '/js/calendar-setup.js');
        $viewRenderer->view->headScript()->appendFile($baseUrl . '/js/calendar-fa.js');
        
    }
    public function date($name, $value = null, $attribs = null){

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        $urlHelper = new Zend_View_Helper_BaseUrl();
        $baseUrl = $urlHelper->baseUrl();
        
        $this->_appendDependencies($baseUrl);
        
        if($value){
            $script = "<script type='text/javascript'>
					$(document).ready(function(){
    					var ".$name." = $('#".$name."').val().split(' ');
    	                var ".$name."_t =  ".$name."[1];
    					var ".$name."_d = new Date(".$name."[0]);
    					".$name."_d.setUTCHours(".$name."_t.split(':')[0]);
    					".$name."_d.setUTCMinutes(".$name."_t.split(':')[1]);
                    	Calendar.setup({
                    		showsTime		:	true,
                    		timeFormat		:	'24',
                    		daFormat       	:	'%d %b %Y ساعت %H:%M',
                    		ifFormat       	:	'%Y-%m-%d %H:%M',
                    		dateType		:	'jalali',
                    		ifDateType		:	'gregorian',
                    		inputField     	:	'".$name."',
                    		displayArea 	:	'".$name."_show',
                    		button			:	'".$name."_btn',
                    		autoFillAtStart	:	true,
                    		date			:	".$name."_d
                    	});
                	});
                </script>";
        }else{
            $script = "<script type='text/javascript'>
					$(document).ready(function(){
                    	Calendar.setup({
                    		showsTime		:	true,
                    		timeFormat		:	'24',
                    		daFormat       	:	'%d %b %Y ساعت %H:%M',
                    		ifFormat       	:	'%Y-%m-%d %H:%M',
                    		dateType		:	'jalali',
                    		ifDateType		:	'gregorian',
                    		inputField     	:	'".$name."',
                    		displayArea 	:	'".$name."_show',
                    		button			:	'".$name."_btn',
                    		autoFillAtStart	:	false
                    	});
                	});
                </script>";
        }
        return  '<input id="'.$name.'" name="'.$name.'" type="hidden" value="'.$value.'" />'.
        		'<div id="'.$name.'_show" class="dateBox"></div>'.
        		'<div class="dateBtn"><img src="'.$baseUrl.'/images/led-ico/calendar_2.png" id="'.$name.'_btn"/></div>'.$script; 
    }
}