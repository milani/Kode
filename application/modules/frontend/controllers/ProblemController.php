<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemController extends App_Frontend_Controller {

    /**
     * Overrides Zend_Controller_Action::init()
     *
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
    }

    /**
     * Controller's entry point
     *
     * @access public
     * @return void
     */
    public function indexAction(){
       $this->title = 'Problems';
       
       $userId = Zend_Auth::getInstance()->getIdentity()->id;
       $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
       $assignmentId = $this->_requireParam('assignmentid',App_Controller::NUMERIC_T,$this->_helper->url('index','assignment'));
       
       $problemModel = new Problem();
       $this->view->assignmentId = $assignmentId;
       $this->view->paginator = $problemModel->findByAssignmentClassStudent($assignmentId,$classId,$userId,$this->_getPage());
    }
    
    public function viewAction(){
        $this->title = 'View Problem';
        
        $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$this->_helper->url('index','assignment'));
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T);
        $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
        
        $problemModel = new Problem();
        $attachmentModel = new ProblemAttachment();
                
        $row = $problemModel->findByIdClass($id,$classId);
        
        if (empty($row)) {
            $this->_helper->FlashMessenger(
                array(
					'msg-error' => sprintf('We cannot find problem with id %s', $id),
                )
            );
            $this->_redirect('/problem/');
        }
        $attachments = $attachmentModel->findByProblemId($id);
        $this->view->problemId = $id;
        $this->view->item = $row;
        $this->view->attachments = $attachments;
        $this->view->backlink = $this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId));
    }
    
    public function downloadAction(){
        
        $assignmentId = $this->_requireParam('assignmentid',App_Controller::NUMERIC_T);
        $returnUrl = $this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId));
        
        $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnUrl);
        
        $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
        
        $problemModel = new Problem();
        $archive = $problemModel->archive($id,$classId);
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.basename($archive).'"');
        readfile($archive);
        
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function downloadattachmentAction(){
        $id = $this->_getParam('id');
        $attachmentModel = new Attachment();
        $row = $attachmentModel->findByFileUnique($id);
        
        if(!empty($row['file_mime'])){
            header('Content-Type: ' . $row['file_mime']);
        }
        header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');

        readfile(APPLICATION_PATH.DIRECTORY_SEPARATOR.$row['file_path']);
        
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function submissionsAction(){
        
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        $problemModel = new Problem();
        $this->view->paginator = $problemModel->findByAnswered($userId,$this->_getPage());
    }
}
