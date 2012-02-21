<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AnswerController extends App_Frontend_Controller {

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

        $this->title = 'Answer problem';
        
        $assignmentId = $this->_requireParam('assignmentid',App_Controller::NUMERIC_T,'/assignment');
        $returnUrl = $this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId));
        
        $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnUrl);
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
        
        $submissionModel = new Submission();
        
        if(!$submissionModel->canAnswer($id, $classId,$assignmentId)){
            $this->_helper->FlashMessenger(
                array(
    				'msg-error' => 'You can not answer this problem. Maybe end date is passed.',
                )
            );

            $this->_redirect($returnUrl,array('prependBase'=>false));
        }
        
        $form = new SubmissionForm();
        $form->setCancelLink($returnUrl);
                
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $values['user_id'] = $userId;
                $values['problem_id'] = $id;
                $submissionModel->save($values);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The answer was successfully edited.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }

        $row = $submissionModel->findByProblemId($id,$userId);
        if (!empty($row)) {
            $form->populate($row);
            $attachmentModel = new SubmissionAttachment();
            $attachments = $attachmentModel->findBySubmissionId($row['id']);
            $this->view->answerId = $row['id'];
            $this->view->attachments = $attachments;
        }
        
        $this->view->item = $row;
        $this->view->assignmentId = $assignmentId;
        $this->view->problemId = $id;
        $this->view->form = $form;
    }
    
    public function deleteAction(){
        $this->title = 'Delete answer';
        
        $assignmentId = $this->_requireParam('assignmentid', App_Controller::NUMERIC_T, '/assignment');
        $returnUrl = $this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId));
        
        $submissionModel = new Submission();
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $id = $values['id'];
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
            $classId = Zend_Auth::getInstance()->getIdentity()->class_id;
            if(!$submissionModel->canDelete($id,$classId,$userId)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'You can not delete answer after End Date expiration.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            
            if(is_numeric($id) && $submissionModel->deleteById($id)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Your answer was successfully deleted.',
                    )
                );
                
            }else{
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'There was a problem. Please try again',
                    )
                );
            }
        }
        $this->_redirect($returnUrl,array('prependBase'=>false));
    }
    
    public function downloadAction(){
        $assignmentId = $this->_getParam('assignmentid');
        $returnUrl = $this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId));
        $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$returnUrl);
        
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
            $this->_redirect($returnUrl,array('prependBase'=>false));
        }

        $this->view->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);

    }
    
    public function downloadattachmentAction(){
        $assignmentId = $this->_getParam('assignmentid');
        $id = $this->_requireParam('id',App_Controller::STRING_T,$this->_helper->url('index','problem','frontend',array('assignmentid'=>$assignmentId)));
        
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
        
        $problemId = $this->_getParam('problemid');
        $assignmentId = $this->_getParam('assignmentid');
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        $returnUrl = $this->_helper->url('index','answer','frontend',array('assignmentid'=>$assignmentId,'id'=>$problemId));
        
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
