<?php

/**
 * Assignment controller
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentController extends App_Admin_Controller {

    /**
     * Class Id
     * 
     * @var int
     */
    private $_classId;
    
    /**
     * Overrides Zend_Controller_Action::init()
     * sets classId for other actions to use.
     * Also adds it to view renderer for listingUtilities usage.
     *
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        $this->_classId = $this->_getParam('classid');
        $this->view->classId = $this->_classId; 
    }

    /**
     * Lists Assignments available to a class.
     *
     * @access public
     * @return void
     */
    public function indexAction(){
       $assignmentModel = new Assignment();
       
       $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T);
       
       if(is_numeric($classId)){
           $this->view->paginator = $assignmentModel->findByClass($classId,$this->_getPage());
       }else{
           $this->view->paginator = $assignmentModel->findAll($this->_getPage());
       }
    }
    
    /**
     * Adds a new assignment for a class.
     * 
     * @access public
     * @return void
     */
    public function addAction(){
        $this->title = 'New assignment';
        
        $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        
        $returnUrl = $this->_helper->url('index','assignment','admin',array('classid'=>$classId));
        
        $form = new AssignmentAddForm();
        $form->setClassId($classId);
        $form->setCancelLink($returnUrl);
        
        $assignmentModel = new Assignment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $assignmentModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('Assignment "%s" was successfully added.', $form->getValue('assignment_title')),
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }
        $this->view->form = $form;
    }
    
    /**
     * Is Called in class/index to add an assignment
     * to all classes
     * 
     * @access public
     * @return void
     */
    public function batchaddAction(){
        $this->title = 'New assignment';
        
        $returnUrl = $this->_helper->url('index','class','admin');
        
        $form = new AssignmentBatchForm();
        $form->setCancelLink($returnUrl);
        
        $assignmentModel = new Assignment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $assignmentId = $assignmentModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('Assignment "%s" was successfully added.', $form->getValue('assignment_title')),
                    )
                );
                $returnUrl = $this->_helper->url('batchadd','problem','admin',array('assignmentid' => $assignmentId));
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }
        $this->view->form = $form;
    }
    
    /**
     * Edit an existing assignment
     * 
     * @access public
     * @return void
     */
    public function editAction(){
        $this->title = 'Edit assignment';
        
        $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        
        $returnUrl = $this->_helper->url('index','assignment','admin',array('classid'=>$classId));
        
        $form = new AssignmentEditForm();
        $form->setCancelLink($returnUrl);
        $assignmentModel = new Assignment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $assignmentModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The assignment was successfully edited.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$returnUrl);
            
            $row = $assignmentModel->findByIdClass($id,$classId);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested assignment could not be found.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Delete an assignment with all its submissions,
     * problems and their attachments.
     * 
     * @access public
     * @return void
     */
    public function deleteAction(){
        $this->title = 'Delete assignment';
        
        $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        
        $returnUrl = $this->_helper->url('index','assignment','admin',array('classid'=>$classId));
        
        $form = new DeleteForm();
        $assignmentModel = new Assignment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $assignmentModel->deleteById($form->getValue('id'),$classId);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The assignment was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$returnUrl);
            
            $row = $assignmentModel->findByIdClass($id,$classId);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => sprintf('We cannot find assignment with id %s', $id),
                    )
                );
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Copy an assignment to other classes with all its problems
     * and their attachments.
     * 
     * @access public
     * @return void
     */
    public function copyAction(){
        $this->title = 'Copy assignment';
        
        $classId = $this->_requireParam('classid',App_Controller::NUMERIC_T,$this->_helper->url('index','class'));
        
        $returnUrl = $this->_helper->url('index','assignment','admin',array('classid'=>$classId));
        
        $form = new AssignmentCopyForm();
        $form->setCancelLink($returnUrl);
        $assignmentModel = new Assignment();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $assignmentModel->copy($form->getValues(),$classId);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The assignment was successfully edited.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }else{
            $id = $this->_requireParam('id',App_Controller::NUMERIC_T,$returnUrl);
            
            $row = $assignmentModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested assignment could not be found.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            
            $form->populate($row);
            
            if(!$form->copyable()){
                 $this->_helper->FlashMessenger(
                    array(
                        'msg-warn' => 'All classes include this assignment.',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
}
