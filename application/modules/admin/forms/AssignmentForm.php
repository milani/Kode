<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentForm extends App_Admin_Form {

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
        
        $title = new Zend_Form_Element_Text('assignment_title');
        $title->setOptions(
            array(
                'label' => 'Title',
                'required' => true,
                'filters' => array(
                	'StringTrim',
                	'StripTags'
                ), 
            	'validators' => array(
                	'NotEmpty',
                )
            )
        );
        $this->addElement($title);
        
        $startDate = new App_Form_Element_Date('assignment_start_at');
        $startDate->setOptions(
            array(
                'label' => 'Start date',
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
        $this->addElement($startDate);
        
        $dueDate = new App_Form_Element_Date('assignment_due_date');
        $dueDate->setOptions(
            array(
                'label'	=> 'Due date',
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
        $this->addElement($dueDate);

        $endDate = new App_Form_Element_Date('assignment_end_at');
        $endDate->setOptions(
            array(
                'label' => 'End date',
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
        $this->addElement($endDate);
        
        $class = new Zend_Form_Element_Hidden('class_id');
        $class->setOptions(
             array(
        		'validators' => array( // not empty and numeric
                    new Zend_Validate_Regex('/^\d+$/'),
                    new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
            )
        );
        $this->addElement($class);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Create assignment',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function setClassId($classId){
        $this->getElement('class_id')->setValue($classId);
    }
    
    public function isValid($data){
        $isValid = parent::isValid($data);
        
        $startDate = new App_Date($this->getValue('assignment_start_at'));
        $dueDate = new App_Date($this->getValue('assignment_due_date'));
        $endDate = new App_Date($this->getValue('assignment_end_at'));
        
        if(($endDate->isLater($dueDate) || $endDate->equals($dueDate)) && $dueDate->isLater($startDate)){
            $isValid = $isValid & true;
        }else{
            if($endDate->isEarlier($dueDate)){
                $this->getElement('assignment_end_at')->addError('End date is earlier than due date');
            }
            if($dueDate->isEarlier($startDate) || $dueDate->equals($startDate)){
                $this->getElement('assignment_due_date')->addError('Due date is earlier than or equal to the start date');
            }
            $isValid = $isValid & false;
        }
        return $isValid;
    }
}