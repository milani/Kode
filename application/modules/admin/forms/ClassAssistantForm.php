<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ClassAssistantForm extends App_Admin_Form {

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
        
        $adminUserModel = new AdminUser();
        $adminIdOptions = $adminUserModel->findPairs();
        
        $users = new Zend_Form_Element_MultiCheckbox('admin_user_id');
        $users->setOptions(
            array(
                'label'	=> 'Assistants',
                'validators' => array(
                    new Zend_Validate_InArray(array_keys($adminIdOptions))
                ),
                'multiOptions' => $adminIdOptions
            )
        );
        $this->addElement($users);
        
        $classId = new Zend_Form_Element_Hidden('class_id');
        $classId->setOptions(
            array(
                'validators' => array(
            		'NotEmpty',new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
            )
        );
        $this->addElement($classId);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Assign',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function setClassId($classId){
        
        $classAssistantModel = new ClassAssistant();
        $classAssistants = $classAssistantModel->findByClass($classId);
        
        $values = array();
        foreach($classAssistants as $row){
            $values[] = $row['admin_user_id'];
        }
        $this->getElement('admin_user_id')->setValue($values);
        $this->getElement('class_id')->setValue($classId);
    }
}