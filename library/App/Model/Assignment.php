<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Assignment extends App_Model {

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
    protected $_name = 'assignments';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Assignment';
    
    
    protected $_dependentTables = array('AssignmentClass');
    
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'assignment_title';
    
	/**
     * Receives an array of data that needs to be saved
     * into the database. If the primary key is contained in
     * this array, it will do an update, otherwise, it will do
     * an insert
     *
     * It returns the primary key of the inserted / updated row
     *
     * @param array $data 
     * @access public
     * @return int
     */
    public function save(array $data){
        
        $preId = (isset($data['id']))?$data['id']:null;
        
        if(isset($data['fork']) && $data['fork']){
            //force model to create new assignment
            unset($data['id']);
            $id = parent::save($data);

            //copy all related problems
            $problemModel = new Problem();
            $problemModel->copyToAssignment($preId, $id, $data['class_id'],$data['class_id']);
        }else{
            $id = parent::save($data);
        }
        
        $assignmentClassModel = new AssignmentClass();
        $assignmentClassModel->save($data,$id,$preId);
        return $id;
    }
    
    public function deleteById($id,$classId){
        $assignmentClassModel = new AssignmentClass();
        $assignmentClassModel->deleteForAssignment($id,$classId);
        
        //delete problems
        $problemModel = new Problem();
        $problemModel->deleteByAssignmentClass($id,$classId);
        
        if( count($assignmentClassModel->findByAssignment($id)) == 0){
            //delete assignment
            parent::deleteById($id);
        }
        
    }
    
    public function findByClass($classId,$page = 1, $paginate = NULL){
        $select = $this->_getSelect(false);
        $select->joinLeft(
            array(
                'p' => 'problems_view'
            ), 'p.assignment_id = a.id AND p.class_id = c.class_id'
        );
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.*','COUNT(p.id) AS problem_count','CONCAT(ca.class_name," - ",co.course_name) AS class_name'
            )
        );
        $select->group(
        	array('p.assignment_id','a.id')
        );
        $select->where('c.class_id = ?',$classId);
        //$select->where('cp.class_id = ? OR cp.class_id IS NULL',$classId);
        return $this->_paginate($select, $page, $paginate);
    }
    
    public function retrieveAssignments($classId,$page = 1, $paginate = NULL){
        
        $today = App_Date::now();
        
        $select = $this->_getSelect();
        $select->joinLeft(
            array(
                'p' => 'problems_view'
            ), 'p.assignment_id = a.id AND p.class_id = c.class_id'
        );
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.*','COUNT(p.id) AS problem_count','CONCAT(ca.class_name," - ",co.course_name) AS class_name'
            )
        );
        $select->group(
        	array('p.assignment_id','a.id')
        );
        $select->where('c.class_id = ?',$classId);
        $select->where('c.assignment_start_at < ?',$today->toString('Y-M-d H:m:s'));

        return $this->_paginate($select, $page, $paginate);
    }
    
    public function findByIdClass($id,$classId){
        $select = $this->_getSelect(false);
        $select->where('a.id = ?',$id);
        $select->where('c.class_id = ?',$classId);
        return $this->_db->fetchRow($select);
    }
    
    public function findPairsForClass($classId){
        $select = $this->_getSelect();
        $select->where('c.class_id = ?',$classId);
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
                'a.id', 
                'a.assignment_title'
            )
        );
        
        return $this->_db->fetchPairs($select);
    }
    
    public function findRelatedClasses($id){
        $select = $this->_getSelect();
        $select->where('a.id = ?',$id);
        return $this->_db->fetchAll($select);
    }
    
    public function copy($data,$fromClassId){
        $classes = $data['class_id'];
        $id = $data['id'];
        $assignmentClassModel = new AssignmentClass();
        $row = $assignmentClassModel->findByAssignmentClass($id, $fromClassId);
        $row['class_id'] = $classes;
        $assignmentClassModel->save($row,$id);
    }
    
    public function archive($assignmentId,$classId){
        
        $zip = new ZipArchive();
        $problemModel = new Problem();
        $viewPartial = new Zend_View_Helper_Partial();
        
        $problems = $problemModel->findByAssignmentClass($assignmentId, $classId,1,false);
        $assignment = $this->findByIdClass($assignmentId, $classId);
        
        $zip->open('/tmp/assignment_'.$assignment['id'].'.zip',ZipArchive::CREATE);
        $allAttachments = array();
        foreach($problems as $problem){
            $allAttachments[$problem['id']] = $problemModel->archiveAttachments($zip, $problem['id'], $problem['problem_number']);
        }
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewPartial->setView($viewRenderer->view);
        $content = $viewPartial->partial('partials/print-problem.phtml',
            array(
                'problems'	    => $problems,
                'assignment'	=> $assignment,
                'attachments'	=> $allAttachments
            )
        );
        
        $zip->addFromString('assignment.html', $content);
        $zip->close();
        
        return '/tmp/assignment_'.$assignment['id'].'.zip';
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
        $select->joinLeft(
            array(
            	'c' => 'assignment_class'
            ),
            'a.id = c.assignment_id'
        );
        $select->joinLeft(
            array(
                'ca' => 'class'        
            ), 'c.class_id = ca.id'
        );
        $select->joinLeft(
            array(
                'co' => 'course'
            ), 'ca.course_id = co.id'
        );
        $select->order(array('a.assignment_title ASC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.*','CONCAT(ca.class_name," - ",co.course_name) AS class_name'
            )
        );
        
        return $select;
    }
}
