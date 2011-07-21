<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemForm extends App_Admin_Form {

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
        $classIdOptions = $classModel->findPairs();
        
        $assignmentModel = new Assignment();
        $assignmentIdOptions = $assignmentModel->findPairs();
        
        $fileMimeValidator=new Zend_Validate_File_MimeType(
            array(
            	'text/plain',
            	'text/xml',
            	'text/html',
            	'text/x-c',
            	'text/x-h',
            	'text/x-java-source',
            	'application/x-javascript',
            	'image',
            	'application/pdf',
            	'application/zip',
                'application/x-rar',
            	'application/msword',
            	'application/mspowerpoint',
            	'application/powerpoint',
            	'application/x-mspowerpoint',
            	'application/mspowerpoint',
            	'application/vnd.ms-powerpoint',
            	'application/vnd.oasis.opendocument.text',
            	'application/vnd.sun.xml.writer',
            	'application/vnd.oasis.opendocument.spreadsheet',
            	'application/vnd.oasis.opendocument.presentation'
            )
        );
        
        $renameFilter = new App_Filter_Rename(
            array(
                'overwrite' => false,
            )
        );


        $desc = new App_Form_Element_Editor('problem_desc');
        $desc->setOptions(
            array(
                'label'	=> 'Problem',
                'required'	=> true,
                'filters'	=> array(
                    'StringTrim',
                ),
                'validators'	=> array(
                    'NotEmpty'
                ),
                'attribs'	=> array(
                    'height' => '100',
                    'width'	 => '100%'
                )
            )
        );
        $this->addElement($desc);
        
        $attachments = new Zend_Form_Element_File('attachment');
        $attachments->setOptions(
            array(
                'label'	    => 'Attachments',
                'multiFile'	=> 2,
                'destination'	=> APPLICATION_PATH.DIRECTORY_SEPARATOR.UPLOAD_PATH,
                'filters'		=> array(
                    $renameFilter
                ),
                'validators'	=> array(
                    $fileMimeValidator
                )
            )
        );
        $this->addElement($attachments);
        
        $class = new Zend_Form_Element_Hidden('class_id');
        $class->setOptions(
             array(
        		'validators' => array(
                    'NotEmpty',
                    new Zend_Validate_InArray(array_keys($classIdOptions))
                ),
            )
        );
        $this->addElement($class);
        
        $assignment = new Zend_Form_Element_Hidden('assignment_id');
        $assignment->setOptions(
             array(
        		'validators' => array(
             		'NotEmpty',
                    new Zend_Validate_InArray(array_keys($assignmentIdOptions))
                ),
            )
        );
        $this->addElement($assignment);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Create problem',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }

    public function isValid($data){
        
        $fileDuplicateValidator = new App_Validate_DuplicateFile(
            array(
                'table'	        =>    'problem_attach_view',
                'field'	        =>    'file_name',
                'groupingField'	=>    'problem_id'
            )
        );
        
        if(!isset($data['id']) || !is_numeric($data['id'])){
            $fileDuplicateValidator->disableDbCheck();
        }else{
            $fileDuplicateValidator->setGroupingValue($data['id']);
        }
        
        $this->getElement('attachment')->addValidator($fileDuplicateValidator);
        
        $isValid = parent::isValid($data);
        
        return $isValid;

    }
    
    
    public function setClassId($classId){
        $this->getElement('class_id')->setValue($classId);
    }
    
    public function setAssignmentId($assignmentId){
        $this->getElement('assignment_id')->setValue($assignmentId);
    }
}