<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ClassForm extends App_Admin_Form {

    protected $hasCourse = false;
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
        
        $courseModel = new Course();
        $courseIdOptions = $courseModel->findPairs();
        
        if(count($courseIdOptions) > 0) $this->hasCourse = true;

        $numberFilter = new App_Filter_Number();

        $name = new Zend_Form_Element_Text('class_name');
        $name->setOptions(
            array(
                'label' => 'Name',
                'required' => true,
                'filters' => array(
                	'StringTrim',
                	'StripTags'
                ), 
            	'validators' => array(
                	'NotEmpty',
                )
            )
        );
        $this->addElement($name);
        
        $courseId = new Zend_Form_Element_Select('course_id');
        $courseId->setOptions(
            array(
            	'label' => 'Course Name',
            	'required' => true, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                ),
                'multiOptions' => $courseIdOptions
            )
        );
        $this->addElement($courseId);
        
        
        $term = new Zend_Form_Element_Select('class_term');
        $term->setOptions(
            array(
                'label' => 'Semester',
                'required' => true,
                'filters' => array(
                	'StringTrim',
                	'StripTags'
                ), 
            	'validators' => array(
                	'NotEmpty'
                ),
                'multiOptions' => array('1'=>'اول',
                                        '2'=>'دوم')
            )
        );
        $this->addElement($term);
        
        $year = new Zend_Form_Element_Text('class_year');
        $year->setOptions(
            array(
                'label' => 'Year',
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags',
                    $numberFilter
                ),
                'validators' => array(
                    'NotEmpty','Int'
                ),
                'attribs' => array(
                    'maxlength' => '2',
                    'style'      => 'width:40px;'
                )
            )
        );
        $this->addElement($year);
        
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
            	'label' => 'Save Class',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    
    public function canCreate(){
      return $this->hasCourse;
    }
}
