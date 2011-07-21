<?php

/**
 * Allows users to update their profiles 
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProfileForm extends App_Admin_Form {

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
            'label' => 'Username', 'required' => true, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        ), 'readonly' => 'readonly'
        ));
        $this->addElement($username);
        $email = new Zend_Form_Element_Text('email');
        $email->setOptions(
        array(
            'label' => 'Email address', 'required' => true, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 
        'validators' => array(
            'NotEmpty', 'EmailAddress'
        )
        ));
        $this->addElement($email);
        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setOptions(
        array(
            'label' => 'First name', 'required' => true, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($firstname);
        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setOptions(
        array(
            'label' => 'Last name', 'required' => true, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($lastname);
        $phoneNumber = new Zend_Form_Element_Text('phone_number');
        $phoneNumber->setOptions(
        array(
            'label' => 'Phone number', 'required' => true, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($phoneNumber);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
        array(
            'label' => 'Save changes', 'required' => true
        ));
        $this->addElement($submit);
    }
}