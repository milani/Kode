<?php

/**
 * Form for editing users
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class UserForm extends App_Admin_Form {

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
        $uniqueUsernameValidator = new Zend_Validate_Db_NoRecordExists(
            array(
            	'table' => 'admin_users',
            	'field' => 'username'
            )
        );
        $uniqueEmailValidator = new Zend_Validate_Db_NoRecordExists(
            array(
            	'table' => 'admin_users',
            	'field' => 'email'
            )
        );
        
        $groupsInArrayValidator = new Zend_Validate_InArray(
            array_keys($groupsOptions)
        );
        $groupsInArrayValidator->setMessage('Please select at least one group. If you are not sure about which group is better, select "member".');
        
        $username = new Zend_Form_Element_Text('username');
        $username->setOptions(
            array(
            	'label' => 'Username',
            	'required' => true,
                'filters' => array(
            		'StringTrim', 'StripTags'
                ),
        		'validators' => array(
            		'NotEmpty', $uniqueUsernameValidator
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
        
        $phoneNumber = new Zend_Form_Element_Text('phone_number');
        $phoneNumber->setOptions(
            array(
                'label' => 'Phone number',
            	'required' => true, 
                'filters' => array(
                    'StringTrim', 'StripTags'
                ),
                'validators' => array(
                    'NotEmpty'
                )
            )
        );
        $this->addElement($phoneNumber);
        
        $groups = new Zend_Form_Element_MultiCheckbox('groups');
        $groups->setOptions(
            array(
            	'label' => 'Select the one or more user groups for this user', 
        		'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ), 
        		'validators' => array(
            		'NotEmpty', $groupsInArrayValidator
                ),
                'multiOptions' => $groupsOptions
            )
        );
        $this->addElement($groups);
        
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
                'label' => 'Add user',
            	'required' => true,
            	'order' => 100
            )
        );
        $this->addElement($submit);
        
        $this->addDisplayGroup(
            array(
            	'username', 'email', 'firstname', 'lastname', 'phone_number'
            ),
            'userdata'
        )->getDisplayGroup('userdata')
         ->setLegend('User details');

        $this->addDisplayGroup(array('groups'), 'usergroups')
             ->getDisplayGroup('usergroups')
             ->setLegend('Groups');
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