<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemBatchForm extends ProblemForm {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();

        $classModel = new ClassModel();
        $classIdOptions = $classModel->findPairs();
        
        $classes = new App_Form_Element_MultiHidden('class_id');
        $classes->setOptions(
            array(
                'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators'    => array(
                    new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
                'decorators' => array('ViewHelper')
            )
        );
        $this->addElement($classes);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Create problem and go to next',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function setAssignmentId($assignmentId){
        $this->getElement('assignment_id')->setValue($assignmentId);
        $assignmentClassModel = new AssignmentClass();
        $rows = $assignmentClassModel->findByAssignment($assignmentId);
        $values = array();
        foreach($rows as $row){
            $values[] = $row['class_id'];
        }
        $this->getElement('class_id')->setValue($values);
    }
}