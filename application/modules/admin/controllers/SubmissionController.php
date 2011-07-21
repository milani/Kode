<?php

/**
 * Submission handling for a problem
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class SubmissionController extends App_Admin_Controller {

    
    private $_classId;
    
    private $_assignmentId;
    
    private $_problemId;
    
    private $_returnUrl;
    /**
     * Overrides Zend_Controller_Action::init()
     *
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $this->view->classId = $this->_classId = $this->_getParam('classid');

        $this->view->assignmentId = $this->_assignmentId = $this->_getParam('assignmentid');
        
        $this->view->problemId = $this->_problemId = $this->_getParam('problemid');
        
        $this->_returnUrl = $this->_helper->url(
        		'index',
        		'submission',
        		'admin',
                array(
                	'classid'	    => $this->_classId,
                    'assignmentid'	=> $this->_assignmentId,
                    'problemid'		=> $this->_problemId
                )
        );
        
    }

    /**
     * Lists submissions for a problem
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->title = 'Submissions';
        /*$this->_requireParam(
        										'assignmentid',
                                                 App_Controller::NUMERIC_T,
                                                 $this->_helper->url(
                                             						'index',
                                             					 	'assignment',
                                             					 	'admin',
                                                                     array(
                                                                 		'classid'    => $this->_classId
                                                                     )
                                                 )
                                            );
         */                                   
        $problemId = $this->_requireParam(
        										'problemid',
                                                 App_Controller::NUMERIC_T,
                                                 $this->_helper->url(
                                             						'index',
                                             					 	'problem',
                                             					 	'admin',
                                                                     array(
                                                                 		'classid'      => $this->_classId,
                                                                        'assignmentid' => $this->_assignmentId
                                                                     )
                                                 )
                                            );
        $classId = $this->_requireParam('classid',
                                        App_Controller::NUMERIC_T,
                                        $this->_helper->url('index','class')
                                        );
        
        $submissionModel = new Submission();
        $this->view->paginator = $submissionModel->findByProblemClass($problemId,$classId,$this->_getPage());
    }
    
    public function gradeAction(){
        $this->title = 'Grade submission';
        $returnUrl = $this->_returnUrl;
        
        $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnUrl);
        
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        $submissionModel = new Submission();
        $form = new GradeForm();
        $form->setCancelLink($returnUrl);
        
        $row = $submissionModel->findById($id);
        
        if (empty($row)) {
            $this->_helper->flashMessenger(
                array(
                    'msg-error'    => 'Submission not found.'
                )
            );
            $this->_redirect($returnUrl,array('prependBase'=>false));
        }
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                
                $values = $form->getValues();
                $values['admin_user_id'] = $userId;
                $values['submission_id'] = $values['id'];

                $submissionGradeModel = new SubmissionGrade();
                $submissionGradeModel->save($values);
                
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The grade is saved.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $form->populate($row);
        }
        
        $attachmentModel = new SubmissionAttachment();
        $attachments = $attachmentModel->findBySubmissionId($id);
        
        $this->view->backLink = $this->_returnUrl;
        $this->view->answerId = $id;
        $this->view->attachments = $attachments;
        $this->view->item = $row;
        $this->view->form = $form;
    }
    
    public function gradeallAction(){
        $this->title = 'Batch Grade';
        $form = new BatchGradeForm();
        $form->setCancelLink($this->_returnUrl);
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $fileInfo = $form->getElement('grade_file')->getFileInfo();
                $userId = Zend_Auth::getInstance()->getIdentity()->id;
                $submissionModel = new Submission();
                $flag = $submissionModel->batchGrade($fileInfo['grade_file']['tmp_name'],$userId);
                if(is_array($flag)){
                    if(count($flag) > 0){
                        $this->_helper->flashMessenger(
                            array(
                                'msg-warn'	=> 'Grades for IDs listed below did not saved. Either had grade before or had wrong data format.'
                            )
                        );
                        $this->_helper->flashMessenger(
                            array(
                                'msg-warn'	=> implode(',',$flag)
                            )
                        );
                    }else{
                        $this->_redirect($this->_returnUrl,array('prependBase'=>false));
                    }
                }else{
                    $this->_helper->flashMessenger(
                        array(
                            'msg-warn'	=> 'Uploaded file has a wrong format.'
                        )
                    );
                }
                
                
            }else{
                $this->_helper->flashMessenger(
                    array(
                        'msg-warn'	=> 'Form data is invalid. Please try again'
                    )
                );
            }
        }
        
        $this->view->form = $form;
    }

    public function downloadallAction(){
        $problemId = $this->_requireParam('problemid',App_Controller::NUMERIC_T,$this->_returnUrl);
        $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T,$this->_returnUrl);
        
        $submissionModel = new Submission();
        $file = $submissionModel->archiveByProblemId($problemId,$classId);
        if($file){
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');

            readfile($file);
        }else{
            $this->_helper->flashMessenger(
                array(
                    'msg-error'	=> 'No submission found.'
                )
            );
            $this->_redirect($this->_returnUrl,array('prependBase'=>false));
        }
        unlink($file);
        $this->view->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
        
    }
    
    public function downloadAction(){
        $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$this->_returnUrl);
        
        $submissionModel = new Submission();
        $file = $submissionModel->archive($id);
        if($file){
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');

            readfile($file);
        }else{
            $this->_helper->flashMessenger(
                array(
                    'msg-error'	=> 'Could not find desired submission.'
                )
            );
            $this->_redirect($this->_returnUrl,array('prependBase'=>false));
        }

        $this->view->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function downloadattachmentAction(){
        $id = $this->_requireParam('id',App_Controller::STRING_T,$this->_returnUrl);
        
        $attachmentModel = new SubmissionAttachment();
        $row = $attachmentModel->findByFileUnique($id);
        if(!empty($row['submission_file_mime'])){
            header('Content-Type: ' . $row['submission_file_mime']);
        }
        header('Content-Disposition: attachment; filename="' . $row['submission_file_name'] . '"');

        readfile(APPLICATION_PATH.DIRECTORY_SEPARATOR.$row['submission_file_path']);

        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function deleteattachmentAction(){
        $this->title = 'Delete attachment';
        
        $classId = $this->_classId;
        $problemId = $this->_problemId;
        $assignmentId = $this->_assignmentId;
        $submissionId = $this->_getParam('submissionid');
        
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        $returnUrl = $this->_helper->url('grade','submission','admin',array('id'=>$submissionId,'classid'=>$classId,'assignmentid'=>$assignmentId,'problemid'=>$problemId));
        
        $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$returnUrl);
        
        $form = new DeleteForm();
        $form->setCancelLink($returnUrl);
        $attachmentModel = new SubmissionAttachment();
        
        $row = $attachmentModel->findByAttachmentUser($id,$userId);
        if (empty($row)) {
            $this->_helper->FlashMessenger(
                array(
                    'msg-error' => 'Either Attachment Identifier is wrong or you do not have permission to delete it.',
                )
            );
            $this->_redirect($returnUrl,array('prependBase'=>false));
        }

        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $attachmentModel->deleteById($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The attachment was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
}
