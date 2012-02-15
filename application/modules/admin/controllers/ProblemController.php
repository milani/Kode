<?php

/**
 * Problem handling controller
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemController extends App_Admin_Controller {

    /**
     * Assignment Id used by problem actions
     * 
     * @var int
     */
    private $_assignmentId;
    
    /**
     * Class ID used by actions
     *
     * @var int
     */
    private $_classId;
    /**
     * Overrides Zend_Controller_Action::init()
     *
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        
        $this->_assignmentId = $this->view->assignmentId = $this->_getParam('assignmentid');
        $this->_classId = $this->view->classId = $this->_getParam('classid');
        
    }

    /**
     * Lists problems for an assignment in a class
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->title = 'Problems';
        
	    $problemModel = new Problem();

        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$this->_helper->url('index','assignment','admin',array('classid'=>$classId)));

        $this->view->paginator = $problemModel->findByAssignmentClass($assignmentId,$classId,$this->_getPage());
    }
    
    /**
     * Add a new problem to an assignment for a class
     * 
     * @access public
     * @return void
     */
    public function addAction(){
        $this->title = 'New problem';
        
        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$this->_helper->url('index','assignment','admin',array('classid'=>$classId)));
        
        $returnUrl = $this->_helper->url('index','problem','admin',array('assignmentid'=>$assignmentId,'classid'=>$classId));
        
        $form = new ProblemForm();
        $form->setCancelLink($returnUrl);
        
        $problemModel = new Problem();
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $problemModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Problem was successfully added.'
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }
        
        $form->setClassId($classId);
        $form->setAssignmentId($assignmentId);
        
        $this->view->form = $form;
    }
    
    public function batchaddAction(){
        $this->title = 'New problem';
        
        $returnUrl = $this->_helper->url('index','class','admin');
        
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$returnUrl);

        $form = new ProblemBatchForm();
        $form->setAssignmentId($assignmentId);
        $form->setCancelLink($returnUrl);
        
        $problemModel = new Problem();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $problemModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Problem was successfully added.'
                    )
                );
                $returnUrl = $this->_helper->url('batchadd','problem','admin',array('assignmentid'=>$assignmentId));
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }
        
        $this->view->form = $form;
    }
    
    public function editAction(){
        $this->title = 'Edit problem';
        
        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$this->_helper->url('index','assignment','admin',array('classid'=>$classId)));

        $returnUrl = $this->_helper->url('index','problem','admin',array('assignmentid'=>$assignmentId,'classid'=>$classId));
        
        $form = new ProblemEditForm();
        $form->setClassId($classId);
        $form->setAssignmentId($assignmentId);
        $form->setCancelLink($returnUrl);
        
        $problemModel = new Problem();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $problemModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The problem was successfully edited.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            $id = $form->getValue('id');
            $attachmentModel = new ProblemAttachment();
            $attachments = $attachmentModel->findByProblemId($id);
            
        }else{
            $id = $this->_getParam('id');
            
            if (!is_numeric($id)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The provided problem_id is invalid.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            
            $row = $problemModel->findByIdClass($id,$classId);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested problem could not be found.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            
            $attachmentModel = new ProblemAttachment();
            $attachments = $attachmentModel->findByProblemId($id);
            
            $form->populate($row);
            $this->view->item = $row;
            
        }
        $this->view->problemId = $id;
        $this->view->attachments = $attachments;
        $this->view->form = $form;
    }
    
    public function deleteAction(){
        $this->title = 'Delete problem';
        
        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$this->_helper->url('index','assignment','admin',array('classid'=>$classId)));
        
        $returnUrl = $this->_helper->url('index','problem','admin',array('assignmentid'=>$assignmentId,'classid'=>$classId));
        
        $form = new DeleteForm();
        $form->setCancelLink($returnUrl);
        $problemModel = new Problem();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $problemModel->deleteById($form->getValue('id'),$classId);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The problem was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $id = $this->_getParam('id');
            $row = $problemModel->findByIdClass($id,$classId);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => sprintf('We cannot find problem with id %s', $id),
                    )
                );
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    public function downloadattachmentAction(){
        $fileUnique = $this->_getParam('file');
        $attachmentModel = new Attachment();
        $row = $attachmentModel->findByFileUnique($fileUnique);
        if(!empty($row['file_mime'])){
            header('Content-Type: ' . $row['file_mime']);
        }
        header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');
        readfile(APPLICATION_PATH.DIRECTORY_SEPARATOR.$row['file_path']);
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function downloadAction(){
        $id = $this->_getParam('id');
        
        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        
        $problemModel = new Problem();
        $archive = $problemModel->archive($id,$classId);
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.basename($archive).'"');
        readfile($archive);
        
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function deleteattachmentAction(){
        $this->title = 'Delete attachment';
        
        $classId = $this->_requireParam('classid', App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T,$this->_helper->url('index','assignment','admin',array('classid'=>$classId)));
        $problemId = $this->_requireParam('problemid', App_Controller::NUMERIC_T,$this->_helper->url('index','problem','admin',array('classid'=>$classId,'assignmentid'=>$assignmentId)));
        
        $returnUrl = $this->_helper->url('edit','problem','admin',array('assignmentid'=>$assignmentId,'classid'=>$classId,'id'=>$problemId));
        
        
        
        $form = new DeleteForm();
        $form->setCancelLink($returnUrl);
        $attachmentModel = new ProblemAttachment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $attachmentModel->deleteByAttachmentProblem($form->getValue('id'),$problemId);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The attachment was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $id = $this->_getParam('id');
            $row = $attachmentModel->findByAttachmentId($id);
            $row = $row[0];
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => sprintf('We cannot find attachment with id %s', $id),
                    )
                );
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            $row['id'] = $row['file_id'];
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
        
    }
}
