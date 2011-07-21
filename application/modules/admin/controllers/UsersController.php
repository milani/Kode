<?php

/**
 * Allows user to manage their profile data
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class UsersController extends App_Admin_Controller {
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
     * Allows admins to see all other admin users that are registered in
     * the application
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->title = 'Manage Users';
        
        $userModel = new AdminUser();
        
        $this->view->paginator = $userModel->findAll($this->_getPage());
    }
    
    public function assistantsAction(){
        $this->title = 'Manage Assistants';
        
        $userModel = new AdminUser();
        $this->view->paginator = $userModel->findAllAssistants($this->_getPage());
    }
    
    public function studentsAction(){
        $this->title = 'Manage Students';
        $classId = $this->_getParam('classid');
        
        $userModel = new FrontUser();
        if($classId){
            $this->view->paginator = $userModel->findByClass($classId,$this->_getPage());
            $this->view->classId = $classId;
        }else{
            $this->view->paginator = $userModel->findAll($this->_getPage());
        }
    }
    
    public function addassistantAction(){
        $this->title = 'Add a new assistant';
        
        $form = new UserAssistantAddForm();
        $form->setCancelLink($this->_helper->url('assistants','users'));
        $userModel = new AdminUser();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $values = $form->getValues();
                $values['active'] = 1;
                $userModel->register($values);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The user was successfully added',
                    )
                );
                
                $this->_redirect('/users/assistants');
            }
        }
        
        $this->view->form = $form;
    }
    
    public function addstudentAction(){
        $this->title = 'Add a new student';
        
        $classId = $this->_getParam('classid');
        
        $returnUrl = $this->_helper->url('students','users','admin',array('classid'=>$classId));
        
        $form = new UserStudentAddForm();
        $form->setCancelLink($returnUrl);
        $userModel = new FrontUser();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $values = $form->getValues();
                $values['active'] = 1;
                $userModel->register($values);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The user was successfully added',
                    )
                );
                
                $this->_redirect($returnUrl,array('prependBase'=>false));
            }
        }
        
        $this->view->form = $form;
    }
    
    public function toggleactiveAction(){
        $this->title = '';
        
        $id = $this->_getParam('id');
        
        $userModel = new AdminUser();
        $activeState = $userModel->toggleActive($id);
        
        if($activeState !== FALSE){
            $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'User '.$activeState,
                    )
            );
        }else{
            $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'Could not toggle active state of this user.',
                    )
            );
        }
        $this->_redirect('/users/assistants');
        /*$session = new Zend_Session_Namespace('App.Admin.Controller');
        $request = unserialize($session->request);
        
        if( ! empty($request) ){
            $previousUri = $request->getRequestUri();
            $this->_redirect($previousUri,array('prependBase'=>false));
        }else{
            $this->_redirect('/users/');
        }*/
    }
    public function toggleactivestudentAction(){
        $this->title = '';
        
        $id = $this->_getParam('id');
        $classId = $this->_getParam('classid');
        
        $returnAddress = $this->_helper->url('students','users','admin',array('classid' => $classId));
        
        $userModel = new FrontUser();
        $activeState = $userModel->toggleActive($id);
        
        if($activeState !== FALSE){
            $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'User '.$activeState,
                    )
            );
        }else{
            $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'Could not toggle active state of this user.',
                    )
            );
        }
        $this->_redirect($returnAddress,array('prependBase' => false));
        /*
        $session = new Zend_Session_Namespace('App.Admin.Controller');
        $request = unserialize($session->request);
        
        if( ! empty($request) ){
            $previousUri = $request->getRequestUri();
            $this->_redirect($previousUri,array('prependBase'=>false));
        }else{
            $this->_redirect('/users/');
        }*/
    }
    
    /**
     * Allows users to add new users in the application
     * (should be reserved for administrators)
     *
     * @access public
     * @return void
     */
    public function addAction(){
        $this->title = 'Add a new user';
        
        $form = new UserAddForm();
        $userModel = new AdminUser();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The user was successfully added',
                    )
                );
                
                $this->_redirect('/users/');
            }
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Allows users to edit another users' data
     * (should be reserved for administrator)
     *
     * @access public
     * @return void
     */
    public function editassistantAction(){
        $this->title = 'Edit account';
        
        $form = new UserAssistantForm();
        $userModel = new AdminUser();
        
        //$session = new Zend_Session_Namespace('App.Admin.Controller');
        //$request = unserialize($session->request);
        
        
        /*if( ! empty($request) ){
            $form->setCancelLink($request->getRequestUri());
            $previousUri = $request->getRequestUri();
            $returnAddress = $previousUri;
        }else{
            $returnAddress = $this->_helper->url('index','users');
        }*/
        $returnAddress = $this->_helper->url('assistants','users');
        
        $form->setCancelLink($returnAddress);
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The user was successfully updated',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
             	
            }
        }else{
            $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnAddress);
            
            $row = $userModel->findById($id);
            
            if(empty($row)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested user could not be found',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            
            $data = $row;
            $data['groups'] = array_keys($data['groups']);
            $form->populate($data);
            
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
/**
     * Allows users to edit another users' data
     * (should be reserved for administrator)
     *
     * @access public
     * @return void
     */
    public function editstudentAction(){
        $this->title = 'Edit student account';
        
        $form = new UserStudentForm();
        $userModel = new FrontUser();
        
        /*$session = new Zend_Session_Namespace('App.Admin.Controller');
        $request = unserialize($session->request);
        
        
        if( ! empty($request) ){
            $form->setCancelLink($request->getRequestUri());
            $previousUri = $request->getRequestUri();
            $returnAddress = $previousUri;
        }else{
            $returnAddress = $this->_helper->url('index','users');
        }
        */
        $classId = $this->_getParam('classid');
        
        $returnAddress = $this->_helper->url('students','users','admin',array('classid'=>$classId));
        
        $form->setCancelLink($returnAddress);
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The user was successfully updated',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
             	
            }
        }else{
            $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnAddress);
            
            $row = $userModel->findById($id);
            
            if(empty($row)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested user could not be found',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            
            $form->populate($row);
            
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    public function searchAction(){
        $this->title = 'Search Students';
        
        $userModel = new FrontUser();
        $form = new SearchForm();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $values = $form->getFilledValues();
                unset($values['csrfhash']);
                $this->view->paginator = $userModel->search($values,$this->_getPage());
            }
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Allows admins to see students' profiles
     *
     * @access public
     * @return void
     */
    public function viewAction(){
        $this->title = 'Student account details';
        
        $classId = $this->_getParam('classid');
        
        $returnAddress = $this->_helper->url('students','users','admin',array('classid'=>$classId));
        
        $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnAddress);
        
        $userModel = new FrontUser();
        $row = $userModel->findById($id);
        if(empty($row)){
            $this->_helper->FlashMessenger(
                array(
                    'msg-error' => 'The user could not be found',
                )
            );
            
            $this->_redirect($returnAddress,array('prependBase'=>false));
        }
        
        //$this->view->backlink = Zend_Controller_Front::getInstance()->getBaseUrl() . '/users/assistants';
        $this->view->backlink = $returnAddress;
        $this->view->item = $row;
    }
    
    /**
     * Allows admins to delete other admin users
     * (should be reserved for administrator)
     *
     * @access public
     * @return void
     */
    public function deleteAction(){
        $this->title = 'Delete assistant account';
        
        $returnAddress = $this->_helper->url('assistants','users');
        
        $form = new DeleteForm();
        $form->setCancelLink($returnAddress);
        
        $userModel = new AdminUser();
        
        /*$session = new Zend_Session_Namespace('App.Admin.Controller');
        $request = unserialize($session->request);
        
        if( ! empty($request) ){
            $form->setCancelLink($request->getRequestUri());
            $previousUri = $request->getRequestUri();
            $returnAddress = $previousUri;
        }else{
            $returnAddress = $this->_helper->url('index','users');
        }
        */
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->deleteById($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The item was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
        }else{
            $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnAddress);
            
            if($id == 1){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'It is forbidden to mess with the admin account in this release.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            $identity=Zend_Auth::getInstance()->getIdentity();
            
        	if($id == $identity->id){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'It is forbidden for owner to delete his account.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            
            $row = $userModel->findById($id);
            
            if(empty($row)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested item cannot be found.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            
            $this->view->item = $row;
            $form->populate($row);
        }
        
        $this->view->form = $form;
    }
    
	/**
     * Allows users to logically delete students
     * (should be reserved for administrators)
     *
     * @access public
     * @return void
     */
    public function deletestudentAction(){
        $this->title = 'Delete student account';
        
        $classId = $this->_getParam('classid');
        $returnAddress = $this->_helper->url('students','users','admin',array('classid' => $classId));
        
        $form = new DeleteForm();
        $form->setCancelLink($returnAddress);
        
        $userModel = new FrontUser();
        
        /*$session = new Zend_Session_Namespace('App.Admin.Controller');
        $request = unserialize($session->request);
        
        
        if( ! empty($request) ){
            $form->setCancelLink($request->getRequestUri());
            $previousUri = $request->getRequestUri();
            $returnAddress = $previousUri;
        }else{
            $returnAddress = $this->_helper->url('index','users');
        }
        */
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->deleteById($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The item was successfully deleted.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
        }else{
            $id = $this->_requireParam('id', App_Controller::NUMERIC_T,$returnAddress);
            
            $row = $userModel->findById($id);
            
            if(empty($row)){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'The requested item cannot be found.',
                    )
                );
                
                $this->_redirect($returnAddress,array('prependBase'=>false));
            }
            
            $this->view->item = $row;
            $form->populate($row);
        }
        
        $this->view->form = $form;
    }
}
