<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemClass extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = array('problem_id','class_id');

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'problem_class';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_ProblemClass';
    
    
     protected $_referenceMap = array(
    	'Problem' => array(
        	'columns' => 'problem_id',
     		'refTableClass' => 'Problem',
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
    protected $_displayColumn = 'problem_number';
    
	public function saveForProblem($data,$problemId,$preProblemId = NULL){
        $class_ids = $data['class_id'];
        //delete all previous problem-class relations
        //create new relations
        $data['problem_id'] = $problemId;
        foreach($class_ids as $classId){
            if($preProblemId) $this->delete(array('problem_id'=>$preProblemId,'class_id'=>$classId));
                
            $data['class_id'] = $classId;
            $this->insert($data);
        }
	}
    
	public function deleteForProblem($id,$classId){
	    $this->delete(array('problem_id'=>$id,'class_id'=>$classId));
	}
	
	public function findByProblem($id){
	    $select = $this->_getSelect(true);
	    $select->where('problem_id = ?',$id);
	    return $this->_db->fetchAll($select);
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
