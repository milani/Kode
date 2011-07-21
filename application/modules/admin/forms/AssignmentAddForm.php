<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentAddForm extends AssignmentForm {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
    }
    
    public function isValid($data){
        $isValid = parent::isValid($data);
        
        $dueDate = new App_Date($this->getValue('assignment_due_date'));
        $today = new App_Date();
        if($dueDate->isLater($today)){
            $isValid = $isValid & true;
        }else{
            
            $this->getElement('assignment_due_date')->addError('Due date is earlier than now');
            $isValid = $isValid & false;
        }
        return $isValid;
    }
}