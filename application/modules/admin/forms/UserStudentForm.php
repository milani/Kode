<?php

/**
 * Form for editing users
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class UserStudentForm extends App_Frontend_Form {

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
        
        $groupModel = new Group();
        $groupsOptions = $groupModel->findPairs();
        
        $classModel = new ClassModel();
        $classOptions = $classModel->findPairs();
        
        $uniqueStudentNumberValidator = new Zend_Validate_Db_NoRecordExists(
            array(
            	'table' => 'users',
            	'field' => 'username'
            )
        );
        $uniqueEmailValidator = new Zend_Validate_Db_NoRecordExists(
            array(
            	'table' => 'users',
            	'field' => 'email'
            )
        );
        
        $classInArrayValidator = new Zend_Validate_InArray(
            array_keys($classOptions)
        );
        $classInArrayValidator->setMessage('Please select at least one group. If you are not sure about which group is better, select "member".');
        
        $groupsInArrayValidator = new Zend_Validate_InArray(
            array_keys($groupsOptions)
        );
        $groupsInArrayValidator->setMessage('Please select at least one group. If you are not sure about which group is better, select "member".');
        
        $numberFilter = new App_Filter_Number();
        
        $username = new Zend_Form_Element_Text('username');
        $username->setOptions(
            array(
            	'label' => 'Student Number',
            	'required' => true,
                'filters' => array(
            		'StringTrim', 'StripTags',$numberFilter
                ),
        		'validators' => array(
            		'NotEmpty',new Zend_Validate_Regex('/^\d{8}$/'), $uniqueStudentNumberValidator
                )
            )
        );
        $this->addElement($username);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setOptions(
            array(
            	'label' => 'Email',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ), 
        		'validators' => array(
            		'NotEmpty', $uniqueEmailValidator
                )
            )
        );
        $this->addElement($email);
        
        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setOptions(
            array(
            	'label' => 'First name',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($firstname);
        
        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setOptions(
            array(
            	'label' => 'Last name',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($lastname);
        
        $class = new Zend_Form_Element_Select('class_id');
        $class->setOptions(
            array(
                'label'	=> 'Class',
                'required'	=> true,
                'filters'	=> array(
                    'StringTrim','StripTags'
                ),
                'validators'	=> array(
                    'NotEmpty', $classInArrayValidator
                ),
                'multiOptions' => $classOptions
            )
        );
        $this->addElement($class);
        
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
            	'required' => true,
            	'order' => 100
            )
        );
        $this->addElement($submit);
    }

    /**
     * Overrides isValid() in App_Form
     * 
     * @param array $data 
     * @access public
     * @return bool
     */
    public function isValid($data){

        if( isset($data['id']) && is_numeric($data['id']) ){
            $this->getElement('username')
                ->getValidator('Zend_Validate_Db_NoRecordExists')
                ->setExclude(
            array(
                'field' => 'id', 'value' => $data['id']
            ));
            $this->getElement('email')
                ->getValidator('Zend_Validate_Db_NoRecordExists')
                ->setExclude(
            array(
                'field' => 'id', 'value' => $data['id']
            ));
        }
        return parent::isValid($data);
    }
}