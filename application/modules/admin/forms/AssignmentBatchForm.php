<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentBatchForm extends AssignmentAddForm {

    protected $hasClass = false;

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();

        $classModel = new ClassModel();
        $classIdOptions = $classModel->findPairs();
        
        if(count($classIdOptions) > 0){
          $this->hasClass = true;
        }

        $classes = new Zend_Form_Element_MultiCheckbox('class_id');
        $classes->setOptions(
            array(
                'label'	        => 'Classes',
                'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators'    => array(
                    new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
                'multiOptions' => $classIdOptions
            )
        );
        $this->addElement($classes);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Create assignment',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function canCreate(){
      return $this->hasClass;
    }
}
