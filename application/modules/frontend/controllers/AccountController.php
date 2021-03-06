<?php

/**
 * Allows user to manage their profile data
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AccountController extends App_Frontend_Controller {

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
     * Allows users to see their dashboards
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->_redirect('/');
    }

    /**
     * Allows the users to update their profiles
     *
     * @access public
     * @return void
     */
    public function editAction(){

        $this->title = 'Edit your account';
        $form = new UserEditForm();
        $userModel = new FrontUser();
        if( $this->getRequest()->isPost() ){
            if( $form->isValid($this->getRequest()->getPost()) ){
                $values = $form->getValues();
                unset($values['username']);
                unset($values['class_id']);
                $userModel->updateProfile($values);
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Your profile was successfully updated.'
                    )
                );
                $this->_redirect('/account/edit/');
            }
        }else{
            $user = Zend_Auth::getInstance()->getIdentity();
            $row = $userModel->findById($user->id);
            $form->populate($row);
            $this->view->item = $row;
        }
        $this->view->form = $form;
    }

    /**
     * Allows users to change their passwords
     *
     * @access public
     * @return void
     */
    public function changePasswordAction(){

        $this->title = 'Change password';
        $user = Zend_Auth::getInstance()->getIdentity();
        $form = new ChangePasswordForm();
        $userModel = new FrontUser();
        if( $this->getRequest()->isPost() ){
            if( $form->isValid($this->getRequest()
                ->getPost()) ){
                $userModel->changePassword($form->getValue('password'));
                $this->_helper->FlashMessenger(
                array(
                    
                'msg-success' => 'Your password was successfully changed.'
                ));
                $this->_redirect('/account/');
            }
        }
        $this->view->form = $form;
    }

    public function registerAction(){
        $this->_helper->layout()->setLayout('login');
        
        $this->title = 'Register';
        
        $form = new UserAddForm();
        $userModel = new FrontUser();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $userModel->register($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'Your request sent. Your account will be activated after confirmation.',
                    )
                );
                
                $this->_redirect('/account/login');
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Allows users to log into the application
     *
     * @access public
     * @return void
     */
    public function loginAction(){

        $this->title = 'Login';
        // use the login layout
        $this->_helper->layout()->setLayout('login');
        $form = new LoginForm();
        if( $this->getRequest()->isPost() ){
            if( $form->isValid($this->getRequest()->getPost()) ){
                $userModel = new FrontUser();
                if( FrontUser::VALID == ($result = $userModel->login($form->getValue('username'),$form->getValue('password'))) ){
                    $session = new Zend_Session_Namespace('App.Frontend.Controller');
                    $request = unserialize($session->request);
                    if( ! empty($request) ){
                        $previousUri = $request->getRequestUri();
                        $this->_redirect($previousUri,array('prependBase'=>false));
                    }else{
                        $this->_redirect('/');
                    }
                }elseif(FrontUser::INACTIVE == $result){
                    $this->view->inactive = TRUE;
                }
            }
            $this->view->error = TRUE;
        }
        $this->view->form = $form;
    }

    /**
     * Allows users to log out of the application
     *
     * @access public
     * @return void
     */
    public function logoutAction(){

        // log the user out
        Zend_Auth::getInstance()->clearIdentity();
        // destroy the session
        Zend_Session::destroy();
        // go to the login page
        $this->_redirect('/account/login/');
    }

    public function resetpasswordAction(){
        $this->title = 'Reset Password';
        $this->_helper->layout()->setLayout('login');
        $form = new ResetPasswordForm();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                $username = $form->getValue('username');
                $userModel = new FrontUser();
                if($userModel->resetpassword($username)){
                    $this->_helper->FlashMessenger(
                        array(
                            'msg-success' => 'Your new password sent to your email.',
                        )
                    );
                    $this->_redirect('/account/login/');
                }else{
                    $this->_helper->FlashMessenger(
                        array(
                            'msg-error' => 'Username does not exists',
                        )
                    );
                }
            }
        }
        $this->view->form = $form;
    }
    
    public function deleteAction(){

    }
}
