<?php

/**
 * Form for adding new user groups in the application
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class SubmissionForm extends App_Frontend_Form {

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
        $fileSizeValidator = new Zend_Validate_File_Size(
            array(
            	'min'	=>    '5',
                'max'	=>    '500000'
            )
        );
        
        $renameFilter = new App_Filter_Rename(
            array(
                'overwrite' => false,
            )
        );
        
        $desc = new App_Form_Element_Editor('submission_desc');
        $desc->setOptions(
            array(
                'label'	=> 'Description',
                'required'	=> false,
                'filters'	=> array(
                    'StringTrim',
                ),
                'attribs'	=> array(
                    'height' => '100',
                    'width'	 => '100%'
                )
            )
        );
        $this->addElement($desc);
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setOptions(
            array(
        		'validators' => array( // either empty or numeric
                    new Zend_Validate_Regex('/^\d*$/')
                )
            )
        );
        $this->addElement($id);
        
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
                    $fileMimeValidator,$fileSizeValidator
                )
            )
        );
        $this->addElement($attachments);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setOptions(
            array(
            	'label' => 'Submit Answer',
            	'required' => true
            )
        );
        $this->addElement($submit);
    }
    public function isValid($data){
        
        if(is_numeric($data['id'])){
            $fileDuplicateValidator = new App_Validate_DuplicateFile(
                array(
                    'table'	        =>    'submission_attach',
                    'field'	        =>    'submission_file_name',
                    'groupingField'	=>    'submission_id',
                    'groupingValue'	=>    $data['id']
                )
            );
            $this->getElement('attachment')->addValidator($fileDuplicateValidator);
        }
        
        $isValid = parent::isValid($data);
        
        if($isValid == false){
            return $isValid;
        } else {
            if(empty($data['submission_desc']) && is_array($this->getElement('attachment')->getFileName()) && count($this->getElement('attachment')->getFileName()) == 0){
                    $id = $data['id'];
                    if(is_numeric($id)){
                        $attachModel = new SubmissionAttachment();
                        if(count($attachModel->findBySubmissionId($id)) > 0){
                            return true;
                        }
                        
                    }
                    $this->getElement('submission_desc')->addError('At least one file or a description should be provided.');
                return false;
            }
            return true;
        }
        
    }
}