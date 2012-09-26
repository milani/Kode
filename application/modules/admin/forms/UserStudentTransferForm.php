<?php

/**
 * Form for transferring users from a class to another.
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class UserStudentTransferForm extends App_Frontend_Form {

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

        $classModel = new ClassModel();
        $classOptions = $classModel->findPairs();
        $classInArrayValidator = new Zend_Validate_InArray(
            array_keys($classOptions)
        );
        $classInArrayValidator->setMessage('Please select at least one group. If you are not sure about which group is better, select "member".');

        $classFrom = new Zend_Form_Element_Select('class_from_id');
        $classFrom->setOptions(
            array(
                'label'	=> 'From Class',
                'required'	=> true,
                'filters'	=> array(
                    'StringTrim','StripTags'
                ),
                'validators'	=> array(
                    'NotEmpty', $classInArrayValidator
                ),
                'multiOptions' => $classOptions
            )
        );
        $this->addElement($classFrom);

        $classTo = new Zend_Form_Element_Select('class_to_id');
        $classTo->setOptions(
            array(
                'label'	=> 'To Class',
                'required'	=> true,
                'filters'	=> array(
                    'StringTrim','StripTags'
                ),
                'validators'	=> array(
                    'NotEmpty', $classInArrayValidator
                ),
                'multiOptions' => $classOptions
            )
        );
        $this->addElement($classTo);

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

}
