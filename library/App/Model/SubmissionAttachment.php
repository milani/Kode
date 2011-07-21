<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class SubmissionAttachment extends App_Model {

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
    protected $_name = 'submission_attach';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_SubmissionAttachment';
    
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'submission_file_name';
    
    public function findBySubmissionId($submissionId){
        $select = $this->_getSelect();
        $select->where('submission_id = ?',$submissionId);
        return $this->_db->fetchAll($select);
    }
    
    public function findByFileUnique($uniqueName){
        $select = $this->_getSelect();
        $select->where('submission_file_unique = ?',$uniqueName);
        return $this->_db->fetchRow($select);
    }
    
    // userId added for security reasons.
    public function findByAttachmentUser($id,$userId){
        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array('sa' => $this->_name)
        );
        $select->join(
            array(
                's'	=> 'submissions'
            ),
            'sa.submission_id = s.id',
            ''
        );
        $select->where('s.user_id = ?',$userId);
        $select->where('sa.id = ?',$id);
        return $this->_db->fetchRow($select);
    }
   
}
