<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ClassController extends App_Admin_Controller {


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
       $classModel = new ClassModel();
       $adminUserModel = new AdminUser();

       $userId = Zend_Auth::getInstance()->getIdentity()->id;

       $this->view->paginator = $classModel->classList($this->_getPage(),$userId);
    }
    
    public function assistantsAction(){
        $this->title = 'Assistants';
        $classId = $this->_getParam('id');
        
        $form = new ClassAssistantForm();
        
        $classAssistantModel = new ClassAssistant();
        
        if( $this->getRequest()->isPost() ){
            if($form->isValid($this->getRequest()->getPost())){
                $classAssistantModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Assistants assigned to this class'
                    )
                );
                
                $this->_redirect('/class/'); 
            }
        }else{

            $form->setClassId($classId);
            
        }
        
        $this->view->form = $form;
    }
    
    public function addAction(){
        $this->title = 'New class';
        
        $form = new ClassForm();
        $classModel = new ClassModel();
        $classAssistantModel = new ClassAssistant();

        if( !$form->canCreate() ){
          $this->_helper->FlashMessenger(
              array(
                  'msg-warn' => sprintf('Please define a course first.'),
              )
          );

          $this->_redirect('/class/');
        }
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $id = $classModel->save($form->getValues());
                $classAssistantModel->addProfessorAccess($id);
                
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('Class "%s" was successfully added.', $form->getValue('class_name')),
                    )
                );
                
                $this->_redirect('/class/');
            }
        }
        
        $this->view->form = $form;
    }
    
    public function editAction(){
        $this->title = 'Edit class';
        
        $form = new ClassForm();
        $classModel = new ClassModel();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $classModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The class was successfully edited.',
                    )
                );
                
                $this->_redirect('/class/');
            }
        }else{
            $id = $this->_getParam('id');
            
            if (!is_numeric($id)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The provided class_id is invalid.',
                    )
                );
                
                $this->_redirect('/class/');
            }
            
            $row = $classModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested class could not be found.',
                    )
                );
                
                $this->_redirect('/class/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    public function deleteAction(){
        $this->title = 'Delete class';
        
        $form = new DeleteForm();
        $classModel = new ClassModel();
        $classAssistantModel = new ClassAssistant();

        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $classModel->deleteById($form->getValue('id'));
                $classAssistantModel->unassignAssistants($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The class was successfully deleted.',
                    )
                );
                
                $this->_redirect('/class/');
            }
        }else{
            $id = $this->_getParam('id');
            $row = $classModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('We cannot find class with id %s', $id),
                    )
                );
                $this->_redirect('/class/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
}
