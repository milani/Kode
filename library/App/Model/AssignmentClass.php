<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AssignmentClass extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = array('assignment_id','class_id');

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'assignment_class';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_AssignmentClass';
    
    
     protected $_referenceMap = array(
    	'Assignment' => array(
        	'columns' => 'assignment_id',
     		'refTableClass' => 'Assignment',
    		'refColumns' => 'id'
        ),
        'Class' => array(
        	'columns' => 'class_id',
     		'refTableClass' => 'Class',
    		'refColumns' => 'id'
        ),
    );
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'assignment_id';
    
    public function save($data,$assignmentId,$preAssignmentId = NULL){
        if(!isset($data['class_id'])){
            throw new Zend_Exception('You should pass class_id to create relation');
        }

        $classIds = (is_array($data['class_id']))?$data['class_id']:array($data['class_id']);
        
        $data['assignment_id'] = $assignmentId;
        
        foreach($classIds as $classId){
            if($preAssignmentId){
                $this->delete(array('assignment_id'=>$preAssignmentId,'class_id'=>$classId));
            }
            $data['class_id'] = $classId;
            $this->insert($data);
            
            $notificationModel = new Notification();
            
            $notification = 'New Assignment Created.';
            $notificationModel->addBatchNotification($notification,$classId);
        }
    }
    
	public function deleteForAssignment($id,$classId){
	    $this->delete(array('assignment_id'=>$id,'class_id'=>$classId));
	}
	
	public function findByAssignment($id){
	    $select = $this->_getSelect();
	    $select->where('assignment_id = ?',$id);
	    return $this->_db->fetchAll($select);
	}
	
	public function findByAssignmentClass($assignmentId,$classId){
	    $select = $this->_getSelect();
	    $select->where('assignment_id = ?',$assignmentId);
	    $select->where('class_id = ?',$classId);
	    return $this->_db->fetchRow($select);
	}
	
	public function copyOptions($id){
	    $classModel = new ClassModel();
	    $pairs = $classModel->findPairs();
	    
        $classAssignments = $this->findByAssignment($id);
        foreach($classAssignments as $class){
            unset($pairs[$class['class_id']]);
        }
        return $pairs;
	}
    /*protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
            	'ac' => $this->_name
            )
        );
        $select->joinLeft(
            array(
            	'c' => 'assignment_class'
            ),
            'a.id = c.assignment_id'
        );
        $select->order(array('a.assignment_title ASC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	'a.*', 'c.*'
            )
        );
        return $select;
    }*/
}
