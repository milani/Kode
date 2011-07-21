<?php

/**
 * Default entry point in the application
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class CourseController extends App_Admin_Controller {

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
       $courseModel = new Course();
       $this->view->paginator = $courseModel->findAll($this->_getPage());
    }
    
    public function addAction(){
        $this->title = 'New course';
        
        $form = new CourseForm();
        $courseModel = new Course();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $courseModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('Course "%s" was successfully added.', $form->getValue('course_name')),
                    )
                );
                
                $this->_redirect('/course/');
            }
        }
        
        $this->view->form = $form;
    }
    
    public function editAction(){
        $this->title = 'Edit course';
        
        $form = new CourseForm();
        $courseModel = new Course();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $courseModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The course was successfully edited.',
                    )
                );
                
                $this->_redirect('/course/');
            }
        }else{
            $id = $this->_getParam('id');
            
            if (!is_numeric($id)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The provided course_id is invalid.',
                    )
                );
                
                $this->_redirect('/course/');
            }
            
            $row = $courseModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested course could not be found.',
                    )
                );
                
                $this->_redirect('/course/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    public function deleteAction(){
        $this->title = 'Delete course';
        
        $form = new DeleteForm();
        $courseModel = new Course();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $courseModel->deleteById($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The course was successfully deleted.',
                    )
                );
                
                $this->_redirect('/course/');
            }
        }else{
            $id = $this->_getParam('id');
            $row = $courseModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('We cannot find course with id %s', $id),
                    )
                );
                $this->_redirect('/course/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
}
