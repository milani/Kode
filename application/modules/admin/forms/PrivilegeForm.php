<?php

/**
 * Form for adding new privileges in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class PrivilegeForm extends App_Admin_Form {

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
        $flagModel = new Flag();
        $flagIdOptions = $flagModel->findPairs();
        $name = new Zend_Form_Element_Text('name');
        $name->setOptions(
        array(
            'label' => 'Name', 'required' => TRUE, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($name);
        $flagId = new Zend_Form_Element_Select('flag_id');
        $flagId->setOptions(
        array(
            'label' => 'Flag', 'required' => TRUE, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        ), 'multiOptions' => $flagIdOptions
        ));
        $this->addElement($flagId);
        $description = new Zend_Form_Element_Text('description');
        $description->setOptions(
        array(
            'label' => 'Description', 'required' => TRUE, 
        'filters' => array(
            'StringTrim', 'StripTags'
        ), 'validators' => array(
            'NotEmpty'
        )
        ));
        $this->addElement($description);
        $id = new Zend_Form_Element_Hidden('id');
        $id->setOptions(
        array(
            
        'validators' => array( // either empty or numeric
            new Zend_Validate_Regex('/^\d*$/')
        )
        ));
        $this->addElement($id);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
        array(
            'label' => 'Save privilege', 'required' => TRUE
        ));
        $this->addElement($submit);
    }

    /**
     * Overrides populate() in App_Form
     * 
     * @param mixed $data 
     * @access public
     * @return void
     */
    public function populate($data){

        if( isset($data['id']) && is_numeric($data['id']) ){
            $element = $this->getElement('flag_id');
            $options = $element->getMultiOptions();
            unset($options[$data['id']]);
            $element->setMultiOptions($options);
        }
        parent::populate($data);
    }
}