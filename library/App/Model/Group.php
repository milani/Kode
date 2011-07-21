<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Group extends App_Model {

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
    protected $_name = 'groups';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Group';

    /**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
        
    'Parent' => array(
        'columns' => 'parent_id', 'refTableClass' => 'Group', 
    'refColumns' => 'id'
    )
    );

    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'name';

    /**
     * Fetches all groups, always checks that the parents are
     * before the children, so that the data can be fed into the 
     * Flag and Flippers
     * 
     * @access public
     * @return array
     */
    public function fetchAllThreaded(){

        $select = $this->_select();
        return $this->_db->fetchAll($select);
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
                    ), $this->_db->quoteInto('id = ?', $child['id'])
                );
                $userGroupModel->routeUsersToGroup($row['id'],$row['parent_id']);
            }
        }
        return parent::delete($where);
    }
    
    public function deleteById($id){
        $defaults = array(1,2,3,4);
        if(in_array($id,$defaults)){
            return false;
        }else{
            return $this->delete($id);
        }
    }
    /**
     * Returns all the children for the specified group
     * 
     * @param int $groupId 
     * @access public
     * @return void
     */
    public function findByParentId($groupId){

        $select = $this->_getQuery();
        $select->where('g.parent_id = ?', $groupId);
        return $this->_db->fetchAll($select);
    }

    /**
     * Overrides App_Model::getPairs()
     * 
     * @access public
     * @return array
     */
    public function findPairs(){

        $pairs = parent::findPairs();
        //@TODO : why it changed the second key and value?
        //$pairs = array('2' => 'member') + $pairs;
        //@TODO : check to see if this setting makes any problem. 
        unset($pairs['1']);
        return $pairs;
    }

    /**
     * Overrides App_Model::getQuery()
     * 
     * @access protected
     * @return void
     */
    protected function _getQuery(){

        $select = $this->_select();
        $select->where('g.id NOT IN (1, 2, 3, 4)');
        return $select;
    }

    /**
     * Overrides App_Model::getQuery()
     * 
     * @access protected
     * @return void
     */
    protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(array(
            'g' => $this->_name
        ));
        $select->joinLeft(array(
            't' => $this->_name
        ), 'g.parent_id = t.id');
        $select->order('g.parent_id ASC');
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
        array(
            'g.*', 't.name AS parent_name'
        ));
        return $select;
    }
}
