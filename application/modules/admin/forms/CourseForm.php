<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class CourseForm extends App_Admin_Form {

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
        
        $name = new Zend_Form_Element_Text('course_name');
        $name->setOptions(
            array(
                'label' => 'Course Name',
                'required' => true,
                'filters' => array(
                	'StringTrim',
                	'StripTags'
                ), 
            	'validators' => array(
                	'NotEmpty'
                )
            )
        );
        $this->addElement($name);
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setOptions(
            array(
        		'validators' => array( // either empty or numeric
                    new Zend_Validate_Regex('/^\d*$/')
                )
            )
        );
        $this->addElement($id);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Save',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
}
