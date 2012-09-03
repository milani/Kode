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

        $this->getElement('assignment_start_at')->setValue(date('Y-m-d H:i',strtotime("+1 day")));
        $this->getElement('assignment_due_date')->setValue(date('Y-m-d H:i',strtotime("+7 days")));
        $this->getElement('assignment_end_at')->setValue(date('Y-m-d H:i',strtotime("+7 days")));
    }
    
    public function isValid($data){
        $isValid = parent::isValid($data);
        
        if( !$isValid ) return $isValid;

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
