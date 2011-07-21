<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentCopyForm extends App_Admin_Form {
    
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
        
        $classModel = new ClassModel();
        $classIdOptions = $classModel->findPairs();
        
        $classes = new Zend_Form_Element_MultiCheckbox('class_id');
        $classes->setOptions(
            array(
                'label'	        => 'Classes',
                'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators'    => array(
                    new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
                'multiOptions' => $classIdOptions
            )
        );
        $this->addElement($classes);
        
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
            	'label' => 'Create assignment',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function populate($values){
        $return = parent::populate($values);
        $assignmentClassModel = new AssignmentClass();
        $id = $this->getElement('id')->getValue();
        
        $options = $assignmentClassModel->copyOptions($id); 
        $this->getElement('class_id')->setMultiOptions($options);
        return $return;
    }
    
    public function copyable(){
        return (count($this->getElement('class_id')->getMultiOptions()) == 0)?false:true;
    }
}