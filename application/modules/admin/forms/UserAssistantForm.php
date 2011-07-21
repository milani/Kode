<?php

/**
 * Form for adding users 
 *
 * It extends the UserForm and adds additional password fields
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Local Billing Lid.
 */
class UserAssistantForm extends App_Admin_Form {

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
        
        // init the parent
        parent::init();
        // set the form's method
        $this->setMethod('post');
        
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
