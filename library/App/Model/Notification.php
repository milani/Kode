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
    
    /**
     * Alias of findByUser
     * 
     * @see Notification::findByUser()
     */
    public function retrieveNotifications($userId, $page, $paginate = NULL ){
        $rows = $this->findByUser($userId, $page,$paginate);
        //$this->markAsReadUnreads($userId);
        return $rows;
    }
    
    /**
     * Finds notifications for a user
     * 
     * @param int $userId
     * @param int $page
     * @param boolean $paginate (Optional)
     * @return Zend_Paginator|array
     */
    public function findByUser($userId, $page, $paginate = NULL){
        $select = $this->_getSelect();
        $select->where('un.user_id = ?',$userId);
        return $this->_paginate($select, $page, $paginate);
    }
    
    /**
     * Adds notification to all users in a class
     * and informs them via email
     *
     * @param string $notification
     * @param int $classId
     * @return int new notification id
     */
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
    /**
     * Adds a notification for user
     * and informs him about it via Email
     * 
     * @param string $notification
     * @param int $userId
     * @return int new notification id
     */
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
    
    /**
     * Marks a notification as read.
     * 
     * @param int $userId
     * @param int $notificationId
     * @return int number of affected rows
     */
    public function markAsRead($userId,$notificationId){
        $where = 'user_id = '.$this->_db->quote($userId).' AND notification_id = '.$this->_db->quote($notificationId);
        return $this->_db->update('users_notifications', array('unread'=>'0'), $where);
    }
    
    /**
     * Overwrites App_Model::_select() to add join tables
     * 
     * @see App_Model::_select()
     */
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
        $select->order(array('un.unread DESC','n.created_at DESC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(array('n.*','un.user_id','un.unread'));
        return $select;
    }
}
