<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentEditForm extends AssignmentForm {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $fork = new Zend_Form_Element_Checkbox('fork');
        $fork->setOptions(
            array(
                'label'		=> 'Changes should only effect this class',
                'required' => TRUE, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ), 
        		'validators' => array(
            		'NotEmpty'
                ),
                'value'	=> '1',
                'order'	=> 6
            )
        );
        //$fork->setOrder(6);
        $this->addElement($fork);
        
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
            	'label' => 'Save',
            	'required' => true
            )
        );
        $this->addElement($submit);
        
    }
    
    public function populate($values){

        $result = parent::populate($values);
        
        $forkElem = $this->getElement('fork');
        $id = $this->getElement('id')->getValue();
        
        $assignmentModel = new Assignment();
        $rows = $assignmentModel->findRelatedClasses($id);
        
        if(count($rows) > 1){
            $desc = array();
            foreach($rows as $row){
                $desc[] = $row['class_name'];
            }

            $desc = 'این سری تمرین در کلاس های "'.implode('" و "',$desc).'"استفاده شده است.';
            $forkElem->setDescription($desc);
            $forkElem->setValue(0);
        }else{
            $forkElem->setAttrib('disabled', 'disabled');
            $forkElem->setDescription('This Assignment is only available in this class.');
        }
        
        return $result;
    }
}