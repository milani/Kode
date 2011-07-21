<?php

/**
 * User login form
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class LoginForm extends App_Admin_Form {

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
            	'label' => 'Username',
            	'required' => true,
                'filters' => array(
            		'StringTrim',
            		'StripTags'
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
                array('url'=>$this->_assembleUrl(array('controller'=>'account','action'=>'register')),'title'=>'Register As Teacher Assistant','text'=>'Register As Teacher Assistant'),
                array('url'=>$this->_assembleUrl(array('controller'=>'account','action'=>'recoverusername')),'title'=>'To Recover your account, click here','text'=>'I forgot my username'),
                array('url'=>$this->_assembleUrl(array('controller'=>'account','action'=>'resetpassword')),'title'=>'Click to reset your password','text'=>'I forgot my password')
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
