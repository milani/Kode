<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentController extends App_Frontend_Controller {

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
       $identity = Zend_Auth::getInstance()->getIdentity();
       $classId = $identity->class_id;
       $assignmentModel = new Assignment();
       $this->view->paginator = $assignmentModel->findByClass($classId,$this->_getPage());
    }

    public function printAction(){
        $id = $this->_getParam('id');
        $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
        
        $problemModel = new Problem();
        $assigmentModel = new Assignment();
        $attachmentModel = new ProblemAttachment();
        
        $assignment = $assigmentModel->findByIdClass($id, $classId);
        $problems = $problemModel->findByAssignmentClass($id, $classId);
        $attachments = $attachmentModel->findByAssignmentClass($id,$classId);
        
        $this->view->attachments = $attachments;
        $this->view->assignment = $assignment;
        $this->view->problems = $problems;
        
        $this->view->layout()->disableLayout();
	    //$this->_helper->viewRenderer->setNoRender(true);
        
    }
    
    public function downloadAction(){
        $id = $this->_getParam('id');
        $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
        
        $assignmentModel = new Assignment();
        $archive = $assignmentModel->archive($id,$classId);
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.basename($archive).'"');
        readfile($archive);
        
        $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }
}
