<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ClassModel extends App_Model {

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
    protected $_name = 'class';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Class';
	
	/**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
    	'Course' => array(
        	'columns' => 'course_id',
    		'refTableClass' => 'Course', 
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
    protected $_displayColumn = 'class_name';
    

    public function classList($page = 1, $userId, $paginate = NULL, $force = FALSE){

      if($userId == NULL){
        return $this->findAll();
      }

      $select = $this->_getSelect($force);
      $select->join(array(
           'ca' => 'class_assistant'
      ), 'ca.class_id = a.id');
      $select->where('admin_user_id = ?',$userId);

      return $this->_paginate($select, $page, $paginate);
    }

    public function findPairs($force = false){
        
        $this->_setupPrimaryKey();
        $select = $this->_getSelect($force);
        $select->reset(Zend_Db_Table::COLUMNS);
        $alias = $this->_extractTableAlias($select);
        $select->columns(
            array(
                'a.id', 
                'CONCAT(a.class_name," - ",c.course_name)'
            )
        );
        return $this->_db->fetchPairs($select);
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
            'a' => $this->_name
        ));
        $select->joinLeft(array(
            'c' => 'course'
        ), 'a.course_id = c.id');
        $select->order(array('a.class_name ASC','c.course_name ASC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.course_name'
            )
        );
        return $select;
    }
}
