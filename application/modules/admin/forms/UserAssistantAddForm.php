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
class UserAssistantAddForm extends UserAssistantForm {

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
        
        $password = new Zend_Form_Element_Password('password');
        $password->setOptions(
            array(
            	'label' => 'Password',
            	'required' => true, 
        		'filters' => array(
            			'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($password);
        
        $retypePassword = new Zend_Form_Element_Password('retypePassword');
        $retypePassword->setOptions(
            array(
            	'label' => 'Retype password',
            	'required' => true, 
        		'filters' => array(
                    'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($retypePassword);
         
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
                'label' => 'Save user',
            	'required' => true,
            	'order' => 100
            )
        );
        $this->addElement($submit);
    }
}
