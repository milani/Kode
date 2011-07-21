<?php

/**
 * Form for grading a bunch of submissions
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class BatchGradeForm extends App_Admin_Form {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $this->setMethod('post');
        
        $fileMimeValidator=new Zend_Validate_File_MimeType(
            array(
            	'text/plain',
            )
        );
        
        $gradeFile = new Zend_Form_Element_File('grade_file');
        $gradeFile->setOptions(
            array(
                'label'	        => 'Grades File',
                //'destination'	=> APPLICATION_PATH.DIRECTORY_SEPARATOR.UPLOAD_PATH,
                'filters'		=> array(
                    new App_Filter_Rename(
                         array(
                            'overwrite' => false,
                        )
                    )
                ),
                'validators'	=> array(
                    $fileMimeValidator,new Zend_Validate_File_Extension('csv')
                )
            )
        );
        $this->addElement($gradeFile);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Process',
            	'required' => true
            )
        );
        $this->addElement($submit);
        
    }
    
}