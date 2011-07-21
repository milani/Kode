<?php
/**
 * Allows user to manage the user groups
 *
 * @category admin
 * @package admin_controllers
 * @copyright Copyright (c) 2011, Morteza Milani
 */

class GroupsController extends App_Admin_Controller
{
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
     * Allows the user to view all the user groups registered
     * in the application
     *
     * @access public
     * @return void
     */
    public function indexAction(){
        $this->title = '';
        
        $groupModel = new Group();
        $this->view->paginator = $groupModel->findAll($this->_getPage());
    }
    
    /**
     * Allows the user to add another user group in the
     * application
     *
     * @access public
     * @return void
     */
    public function addAction(){
        $this->title = 'Add a new user group.';
        
        $form = new GroupForm();
        $groupModel = new Group();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $groupModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('The group %s was successfully added.', $form->getValue('name')),
                    )
                );
                
                $this->_redirect('/groups/');
            }
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Edits an existing user group
     *
     * @access public
     * @return void
     */
    public function editAction(){
        $this->title = 'Edit user group';
        
        $form = new GroupForm();
        $groupModel = new Group();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $groupModel->save($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The group was successfully edited.',
                    )
                );
                
                $this->_redirect('/groups/');
            }
        }else{
            $id = $this->_getParam('id');
            
            if (!is_numeric($id)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The provided group_id is invalid.',
                    )
                );
                
                $this->_redirect('/groups/');
            }
            if(in_array($id,array(1,2,3,4))){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'You can not edit default user groups',
                    )
                );
                
                $this->_redirect('/groups/');
            }
            $row = $groupModel->findById($id);
            
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The requested group could not be found.',
                    )
                );
                
                $this->_redirect('/groups/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Allows the user to delete an existing user group. All the users attached to
     * this group *WILL NOT* be deleted, they will just lose all 
     * privileges granted by this group
     *
     * @access public
     * @return void
     */
    public function deleteAction(){
        $this->title = 'Delete user group';
        
        $form = new DeleteForm();
        $groupModel = new Group();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $groupModel->deleteById($form->getValue('id'));
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => 'The group was successfully deleted.',
                    )
                );
                
                $this->_redirect('/groups/');
            }
        }else{
            $id = $this->_getParam('id');
            $row = $groupModel->findById($id);
            
            $identity=Zend_Auth::getInstance()->getIdentity();
        	$userGroupModel = new AdminUserGroup();
            $userGroupId = $userGroupModel->findByUserId($identity->id);
            
            if ($id == $userGroupId[0]['group_id']) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'You can\'t delete the group you are a member of.',
                    )
                );
                
                $this->_redirect('/groups/');
            }
            if(in_array($id,array(1,2,3,4))){
                $this->_helper->FlashMessenger(
                    array(
                        'msg-error' => 'You can not delete default user groups',
                    )
                );
                
                $this->_redirect('/groups/');
            }
            if (empty($row)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('We cannot find group with id %s', $id),
                    )
                );
                $this->_redirect('/groups/');
            }
            
            $form->populate($row);
            $this->view->item = $row;
        }
        
        $this->view->form = $form;
    }
    
    /**
     * Allows the user to manage individual permissions for each
     * user group
     *
     * @access public
     * @return void
     */
    public function flippersAction(){
        $this->title = 'Manage permissions for this group.';
        
        $form = new GroupPermissionsForm();
        $fliperModel = new Flipper();
        $groupModel = new Group();
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $fliperModel->savePermissions($form->getValues());
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('Permissions for group %s were successfully updated.', $form->getValue('name')),
                    )
                );
                
                $this->_redirect('/groups/');
            }
        }else{
            $id = $this->_getParam('id');
            
            if (!is_numeric($id)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('We cannot find group with id %s', $id),
                    )
                );
                $this->_redirect('/groups/');
            }
            
            $group = $groupModel->findById($id);
            $flipper = $fliperModel->findByGroupId($id);
            $flipper += array('group_id' => $id);
            
            if (empty($group)) {
                $this->_helper->FlashMessenger(
                    array(
                        'msg-success' => sprintf('The permissions for the group %s were updated.', $form->getValue('name')),
                    )
                );
                
                $this->_redirect('/groups/');
            }
            
            $form->populate($flipper);
            $this->view->item = $group;
        }
        
        $this->view->form = $form;
        
    }
}