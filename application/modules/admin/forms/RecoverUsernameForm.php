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
class RecoverUsernameForm extends App_Admin_Form {

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
        
        $email = new Zend_Form_Element_Text('email');
        $email->setOptions(
            array(
            	'label' => 'Email',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty',
                    'EmailAddress'
                )
            )
        );
        $this->addElement($email);

        $submitUsername = new Zend_Form_Element_Submit('submit');
        $submitUsername->setOptions(
        array(
            'label' => 'Send', 'required' => true
        ));
        $this->addElement($submitUsername);
    }
}