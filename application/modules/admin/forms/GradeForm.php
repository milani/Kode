<?php

/**
 * Form for grading a submission
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class GradeForm extends App_Admin_Form {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $this->setMethod('post');
        
        $grade = new Zend_Form_Element_Text('grade');
        $grade->setOptions(
            array(
                'label'	    => 'Grade',
                'required'	=> true,
                'filters'	=> array(
                    new App_Filter_Number()
                ),
        		'validators'=> array( // either empty or numeric
                    new Zend_Validate_Regex('/^\d{1,3}(\.\d{1,2})?$/')
                ),
            )
        );
        $grade->setAttrib('maxlength', '6');
        $grade->setAttrib('style', 'width:60px;');
        $this->addElement($grade);
        
        $desc = new App_Form_Element_Editor('grade_desc');
        $desc->setOptions(
            array(
                'label'	    => 'Description',
                'required'	=> true,
                'filters'	=> array(
                    'StringTrim',
                ),
                'validators'	=> array(
                    'NotEmpty'
                ),
                'attribs'	=> array(
                    'height' => '100',
                    'width'	 => '100%'
                )
            )        
        );
        $this->addElement($desc);
        
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