<?php

/**
 * Decorator that is used to add condition support to forms
 *
 * @category App
 * @package App_Form
 * @subpackage Decorator
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Form_Decorator_Condition extends Zend_Form_Decorator_Abstract {

    /**
     * Default position
     * 
     * @var string
     * @access protected
     */
    protected $_placement = 'APPEND';

    /**
     * Overrides render() in Zend_Form_Decorator_Abstract
     * 
     * @param mixed $content 
     * @access public
     * @return void
     */
    public function render($content){

        $element = $this->getElement();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $options = $this->getOptions();
        $target = $options['condition']['target'];
        $values = $options['condition']['values'];
        $jsValues = Zend_Json_Encoder::encode($values, true);
        if( $target !== null ){
            $script = "
	    		$(document).ready(function(){
	    			var options=jQuery.parseJSON('" . $jsValues . "');
	    			var targetDefaultValue = $('#" . $target .
             "').val();
	    			if(typeof options[$('#" .
             $element->getId() . "').val()] != 'undefined'){
	    				$('#" . $target .
             "').empty();
		    			$.each(options[$('#" .
             $element->getId() .
             "').val()],function(index,value){
		    				var selected=(targetDefaultValue == index)?'selected=\"selected\"':'';
		    				$('#" .
             $target . "').append('<option label=\"'+value+'\" value=\"'+index+'\" ' + selected +'>'+value+'</option>');
		    			});
	    			}
	    			$('#" .
             $element->getId() . "').bind('change',function(){
	    				if(typeof options[this.value] != 'undefined'){
	    					jQuery('#" . $target .
             "').empty();
		    				jQuery.each(options[this.value],function(index,value){
		    					jQuery('#" .
             $target . "').append('<option label=\"'+value+'\" value=\"'+index+'\">'+value+'</option>');
		    				});
	    				}
	    			});
	    		});
	    		";
        }else{
            $script = "
    			$(document).ready(function(){
    				var options = $.parseJSON('" . $jsValues . "');
    				var defaultValue = $('#" .
             $element->getId() . "').val();
    				$.each(options,function(index,value){
    					var tmpArray=jQuery.makeArray(value);
    					for(i=0;i<tmpArray.length;i++){
    						if(tmpArray[i]=='')
    							continue;
    						jQuery('#'+tmpArray[i]+'-element').hide();
    						jQuery('#'+tmpArray[i]+'-label').hide();
    					}
    				});
	    			if(typeof options[defaultValue] != 'undefined'){
	    				var array2=jQuery.makeArray(options[defaultValue]);
	    				for(i=0;i<array2.length;i++){
	    					jQuery('#'+array2[i]+'-element').show();
	    					jQuery('#'+array2[i]+'-label').show();
	    				}
	    			}
	    				  
    				$('#" .
             $element->getId() . "').bind('change',function(){
    					
    					jQuery.each(options,function(index,value){
    						var tmpArray=jQuery.makeArray(value);
    						for(i=0;i<tmpArray.length;i++){
    							jQuery('#'+tmpArray[i]+'-element').hide();
    							jQuery('#'+tmpArray[i]+'-label').hide();
    						}
    					});
	    				if(typeof options[this.value] != 'undefined'){
	    					var array2=jQuery.makeArray(options[this.value]);
	    					for(i=0;i<array2.length;i++){
	    						jQuery('#'+array2[i]+'-element').show();
	    						jQuery('#'+array2[i]+'-label').show();
	    					}
	    				}
    				});
    			});
    			";
        }
        $scriptTag = sprintf('<script language="javascript">%1$s</script>', 
        $script);
        switch($placement){
            case self::APPEND:
                return $content . $separator . $scriptTag;
            case self::PREPEND:
                return $scriptTag . $separator . $content;
        }
    }
}