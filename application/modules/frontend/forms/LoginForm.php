<?php

/**
 * User login form
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class LoginForm extends App_Frontend_Form {

    /**
     * This form does not have a cancel link
     * 
     * @var mixed
     * @access protected
     */
    protected $_cancelLink = false;

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
            		'StringTrim',
            		'StripTags',
                    new App_Filter_Number()
                ),
                'validators' => array(
            		'NotEmpty'
                )
            )
        );
        $this->addElement($username);
        
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
        
        $link = new App_Form_Decorator_Loginlinks();
        $link->setOption('links',
            array(
                array('url'=>$this->_assembleUrl(array('controller'=>'account','action'=>'resetpassword')),'title'=>'Click to reset your password','text'=>'I forgot my password'),
                array('url'=>$this->_assembleUrl(array('controller'=>'account','action'=>'register')),'title'=>'Click to register an account','text'=>'Register')
            )
        );
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
        array(
            'label' => 'Log in →', 'required' => true
        ));
        $this->addElement($submit);
        $submit->addDecorator($link);

    }
}
