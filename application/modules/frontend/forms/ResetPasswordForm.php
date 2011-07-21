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
class ResetPasswordForm extends App_Frontend_Form {

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
        
        $username = new Zend_Form_Element_Text('username');
        $username->setOptions(
            array(
            	'label' => 'Student Number',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags',new App_Filter_Number()
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($username);
        
        $submitUsername = new Zend_Form_Element_Submit('submit');
        $submitUsername->setOptions(
        array(
            'label' => 'Reset Password', 'required' => true
        ));
        $this->addElement($submitUsername);
    }
}