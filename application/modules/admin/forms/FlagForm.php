<?php

/**
 * Form for adding new flags in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class FlagForm extends App_Admin_Form {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        // set the form's method
        $this->setMethod('post');
        $name = new Zend_Form_Element_Text('name');
        $name->setOptions(
        array(
            'label' => 'Module name', 'required' => TRUE, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($name);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
        array(
            'label' => 'Update', 'required' => TRUE
        ));
        $this->addElement($submit);
    }
}