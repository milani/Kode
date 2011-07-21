<?php

/**
 * Student Search form
 * 
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class SearchForm extends App_Admin_Form{

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $numberFilter = new App_Filter_Number();
        
        $username = new Zend_Form_Element_Text('username');
        $username->setOptions(
            array(
            	'label' => 'Student Number',
            	'required' => false,
                'filters' => array(
            		'StringTrim', 'StripTags',$numberFilter
                ),
        		'validators' => array(
            		new Zend_Validate_Regex('/^\d{8}$/')
                )
            )
        );
        $this->addElement($username);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setOptions(
            array(
            	'label' => 'Email',
            	'required' => false, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
            )
        );
        $this->addElement($email);
        
        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setOptions(
            array(
            	'label' => 'First name',
            	'required' => false, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
            )
        );
        $this->addElement($firstname);
        
        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setOptions(
            array(
            	'label' => 'Last name',
            	'required' => false, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
            )
        );
        $this->addElement($lastname);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Search',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function getFilledValues(){
        $values = parent::getValues();
        foreach($values as $key => $value){
            if(empty($value)) unset($values[$key]);
        }
        return $values;
    }
}