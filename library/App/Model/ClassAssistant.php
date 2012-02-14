<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ClassAssistant extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'class_id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'class_assistant';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_ClassAssistant';
	
	/**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
    	'Class' => array(
        	'columns' => 'class_id',
    		'refTableClass' => 'Class', 
    		'refColumns' => 'id'
        ),
        'Admin' => array(
            'columns' => 'admin_user_id',
            'refTableClass' => 'AdminUser',
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
    protected $_displayColumn = 'class_id';

    public function save($data){
        $row = array(
            'class_id' => $data['class_id']
        );
        
        $this->unassignAssistants($data['class_id']);
        
        foreach($data['admin_user_id'] as $userId){
            $row['admin_user_id'] = $userId;
            $this->insert($row);
        }
        
        $this->addProfessorAccess($data['class_id']);
    }
    
    public function addProfessorAccess($classId){
        $adminUserModel = new AdminUserGroup();
        $professors = $adminUserModel->findByGroupId('1');
        foreach($professors as $professor){
          $this->insert(array('class_id' => $classId,'admin_user_id' => $professor['user_id']));
        }
    }
    
    public function unassignAssistants($classId){
        $this->delete(array('class_id'=>$classId));
    }

    public function checkAccess($classId, $userId){
        $select = $this->_getSelect(true);
        $select->where('class_id = ?',$classId);
        $select->where('admin_user_id = ?',$userId);
        $hasRecord = $this->_db->fetchRow($select);
        return ($hasRecord == NULL)?false:true;
    }

    public function findByClass($classId){
        $select = $this->_getSelect(true);
        $select->where('class_id = ?',$classId);
        return $this->_db->fetchAll($select);
    }

    protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(array(
            'a' => $this->_name
        ));
        $select->joinLeft(array(
            'c' => 'class'
        ), 'a.class_id = c.id');
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.*'
            )
        );
        return $select;
    }
}
