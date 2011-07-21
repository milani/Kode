<?php

/**
 * Default delete form, it's used to prevent CSRF attacks
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class DeleteForm extends App_Admin_Form {

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
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setOptions(
            array(
            	'required' => true, 
        		'validators' => array( // either empty or numeric
                    new Zend_Validate_Regex('/^\d*$/')
                )
            )
        );
        $this->addElement($id);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
        array(
            'label' => 'Yes, delete it', 'required' => true
        ));
        $this->addElement($submit);
    }
}


