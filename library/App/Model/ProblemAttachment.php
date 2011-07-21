<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class ProblemAttachment extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'problem_id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'problem_attach';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_ProblemAttachment';
    
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = null;
    
    public function save($data){
        $attachment = new Attachment();
        $id = $attachment->save($data);
        $row = array(
            'file_id'	    => $id,
            'problem_id'	=> $data['problem_id']
        );
        return parent::insert($row);
    }
    
    public function copy($fromId,$toId){
        $select = $this->_getSelect(true);
        $select->where('problem_id = ?',$fromId);
        $rows = $this->_db->fetchAll($select);
        foreach($rows as $row){
            $row['problem_id'] = $toId;
            $this->save($row);
        }
    }
    
    public function deleteByProblemId($id){
        parent::deleteById($id);
    }
    
    public function deleteByAttachmentId($id){
        $this->delete($this->_db->quoteInto('file_id = ?', $id));
    }
    
    public function deleteByAttachmentProblem($attachmentId,$problemId){
        return $this->delete($this->_db->quoteInto('file_id = ?',$attachmentId).' AND '.$this->_db->quoteInto('problem_id = ?', $problemId));
    }
    
    public function findByProblemId($problemId){
        $select = $this->_getSelect();
        $select->where('pa.problem_id = ?',$problemId);
        return $this->_db->fetchAll($select);
    }
    
    public function findByAttachmentId($id){
        $select = $this->_getSelect();
        $select->where('pa.file_id = ?',$id);
        return $this->_db->fetchAll($select);
    }
    
    public function findByAssignmentClass($assignmentId,$classId){
        $select = $this->_getSelect();
        $select->join(
        	array(
        		'pv'    => 'problems_view'
        	),
        	'pv.id = pa.problem_id',
        	''
        );
        $select->where('pv.class_id = ?',$classId);
        $select->where('pv.assignment_id = ?',$assignmentId);
        $select->order('pv.id ASC');
        $results = $this->_db->fetchAll($select);
        
        $attachments = array();
        foreach($results as $result){
            $attachments[$result['problem_id']][] = $result; 
        } 
        
        return $attachments;
    }
    
    protected function _select(){
        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
            	'pa'    => $this->_name
            )
        );
        $select->join(
        	array(
        		'a'    => 'attachments'
        	),
        	'a.file_id = pa.file_id'
        );
        return $select;
    }
}
