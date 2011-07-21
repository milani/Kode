<?php

/**
 * Manages notifications
 *
 * @package frontend_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Notification extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'notifications';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Notification';

    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'notification';
    
    public function findByUser($userId, $page, $paginate = NULL){
        $select = $this->_getSelect();
        $select->where('un.user_id = ?',$userId);
        return $this->_paginate($select, $page, $paginate);
    }
    
    public function addBatchNotification($notification,$classId){
        $data = array(
            'notification' => $notification
        );
        $id = $this->save($data);
        if($id){
            $frontUserModel = new FrontUser();
            $unModel = new NotificationUser();
            
            $users = $frontUserModel->findByClass($classId,1,false);
            foreach($users as $user){
                $unModel->insert(
                    array(
                        'user_id'	        => $user['id'],
                        'notification_id'	=> $id
                    )
                );
                $mail = new App_Mail();
                $mail->notification($user['email'], $notification);
            }
        }
        
        return $id;
    }
    
    public function addNotification($notification,$userId){
        $data = array(
            'notification' => $notification
        );
        $id = $this->save($data);
        
        if(is_numeric($id)){
            $frontUserModel = new FrontUser();
            $unModel = new NotificationUser();
            
            $user = $frontUserModel->findById($userId);
            if($user){
                $unModel->save(
                    array(
                        'user_id'	        => $userId,
                        'notification_id'	=> $id
                    )
                );
                $mail = new App_Mail();
                $mail->notification($user['email'], $notification);
            }
        }
        
        return $id;
    }
    
    public function markAsRead($userId){
        $where = 'user_id = '.$this->_db->quote($userId).' AND notification_id = '.$this->_db->quote($notificationId);
        return $this->_db->update('users_notifications', array('unread'=>'0'), Where);
    }
    /**
     * Overrides delete() in App_Model.
     *
     * When a group is deleted, all its children are linked
     * to its parent
     * 
     * @param mixed $where 
     * @access public
     * @return int
     */
    public function delete($where){

        $this->_setupPrimaryKey();
        $primary = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];
        if( is_numeric($where) ){
            $where = $pkIdentity . ' = ' . $where;
        }
        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $select->where($where);
        $rows = $this->_db->fetchAll($select);
        $userGroupModel = new AdminUserGroup();
        foreach( $rows as $row ){
            $children = $this->findByParentId($row['id']);
            foreach( $children as $child ){
                $this->update(
                array(
                    'parent_id' => $row['parent_id']
                ), $this->_db->quoteInto('id = ?', $child['id']));
                $userGroupModel->routeUsersToGroup($row['id'], 
                $row['parent_id']);
            }
        }
        return parent::delete($where);
    }

    protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
            	'n'		=> $this->_name
            )
        );
        $select->join(
            array(
                'un'	=> 'users_notifications'
            ),
            'un.notification_id = n.id'
        );
        $select->order('created_at DESC');
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns('n.*,un.user_id,un.unread');
        return $select;
    }
}
