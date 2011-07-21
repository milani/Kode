<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class GroupForm extends App_Admin_Form {

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
        
        $uniqueGroupNameValidator = new Zend_Validate_Db_NoRecordExists(
            array(
                'table' => 'groups',
            	'field' => 'name'
            )
        );
        $groupModel = new Group();
        $parentIdOptions = $groupModel->findPairs();
        
        $name = new Zend_Form_Element_Text('name');
        $name->setOptions(
            array(
                'label' => 'Name',
                'required' => true,
                'filters' => array(
                	'StringTrim',
                	'StripTags'
                ), 
            	'validators' => array(
                	'NotEmpty', $uniqueGroupNameValidator
                )
            )
        );
        $this->addElement($name);
        
        $parentId = new Zend_Form_Element_Select('parent_id');
        $parentId->setOptions(
            array(
            	'label' => 'Parent group',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                ),
                'multiOptions' => $parentIdOptions
            )
        );
        $this->addElement($parentId);
        
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
            	'label' => 'Save user group',
            	'required' => true
            )
        );
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
            $element = $this->getElement('parent_id');
            $options = $element->getMultiOptions();
            unset($options[$data['id']]);
            $element->setMultiOptions($options);
        }
        parent::populate($data);
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
            $this->getElement('name')
                ->getValidator('Zend_Validate_Db_NoRecordExists')
                ->setExclude(
            array(
                'field' => 'id', 'value' => $data['id']
            ));
        }
        return parent::isValid($data);
    }
}