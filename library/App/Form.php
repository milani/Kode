<?php

/**
 * Default parent form for all the forms in the application
 *
 * @category App
 * @package App_Form
 * @copyright Copyright (c) 2011, Morteza Milani
 */
abstract class App_Form extends Zend_Form {

    /**
     * Add a new element
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * @param  string|Zend_Form_Element $element
     * @param  string $name
     * @param  array|Zend_Config $options
     * @return Zend_Form
     */
    public function addElement($element, $name = null, $options = null, $sidebar = false){

        parent::addElement($element, $name, $options);
        if( $element instanceof Zend_Form_Element ){
            if( null === $name ){
                $name = $element->getName();
            }
        }
        /*
        if($sidebar == true){
        	$groupName = 'sidebar';
        }else{
        	$groupName = 'main';
        }
    	if($this->getDisplayGroup($groupName) instanceof Zend_Form_DisplayGroup){
        	$this->getDisplayGroup($groupName)->addElement($this->_elements[$name]);
		}else{
			$this->addDisplayGroup(array($name),$groupName);
			$this->getDisplayGroup($groupName)->removeDecorator('DtDdWrapper');
		}
		*/
        return $this;
    }
}
