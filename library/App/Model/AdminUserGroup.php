<?php

/**
 * Model that manages the association between groups
 * and users
 *
 *
 * @category admin
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AdminUserGroup extends App_Model {

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
    protected $_name = 'admin_users_groups';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_AdminUserGroup';

    /**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
        
    'User' => array(
        'columns' => 'user_id', 'refTableClass' => 'AdminUser', 
    'refColumns' => 'id'
    ), 
    'Group' => array(
        'columns' => 'group_id', 'refTableClass' => 'Group', 
    'refColumns' => 'id'
    )
    );

    /**
     * Returns all the groups an user is associated
     * with
     * 
     * @param int $userId 
     * @param bool $fullData
     * @access public
     * @return array
     */
    public function findByUserId($userId, $fullData = FALSE){

        $select = new Zend_Db_Select($this->_db);
        if( ! $fullData ){
            $select->from($this->_name);
        }else{
            $select->from(
            array(
                'ug' => $this->_name
            ));
            $select->join(array(
                'g' => 'groups'
            ), 'ug.group_id = g.id');
        }
        $select->where('user_id = ?', $userId);
        return $this->_db->fetchAll($select);
    }

    /**
     * Delete all associations with the given group
     * 
     * @param int $groupId 
     * @access public
     * @return void
     */
    public function deleteByGroupId($groupId){

        $this->delete($this->_db->quoteInto('group_id = ?', $groupId));
    }

    /**
     * Deletes all associations with the given user
     * 
     * @param int $userId 
     * @access public
     * @return void
     */
    public function deleteByUserId($userId){

        $this->delete($this->_db->quoteInto('user_id = ?', $userId));
    }

    /**
     * Saves the association
     * 
     * @param array $data 
     * @param int $userId 
     * @access public
     * @return void
     */
    public function saveForUser(array $data, $userId){

        $this->deleteByUserId($userId);
        if( ! empty($data) ){
            foreach( $data as $groupId ){
                $this->save(
                array(
                    'group_id' => $groupId, 'user_id' => $userId
                ));
            }
        }else{
            // defaults to member
            $this->save(
            array(
                'group_id' => 2, 'user_id' => $userId
            ));
        }
    }

    /**
     * Routes all the users associated with the "from" group
     * to a new group id (usually when the current group is being
     * deleted)
     * 
     * @param mixed $from 
     * @param mixed $to 
     * @access public
     * @return void
     */
    public function routeUsersToGroup($from, $to){

        $this->update(array(
            'group_id' => $to
        ), $this->_db->quoteInto('group_id = ?', $from));
    }
}
