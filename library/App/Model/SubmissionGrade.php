<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class SubmissionGrade extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'submission_id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'submission_grade';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_SubmissionGrade';
    
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'grade';
    
    public function save($data){
        $this->_setupPrimaryKey();
        $primary = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];
        if( isset($data[$pkIdentity]) && $data[$pkIdentity] ){
            // we have a non-null value for the primary key, check if we can update
            $select = new Zend_Db_Select($this->_db);
            $select->from($this->_name);
            $select->where($pkIdentity . '= ?', $data[$pkIdentity]);
            $select->reset(Zend_Db_Table::COLUMNS);
            $select->columns(
            array(
                'COUNT(' . $pkIdentity . ')'
            ));
            if( $this->_db->fetchOne($select) == 1 ){
                // we have valid pk, update it
                $id = $data[$pkIdentity];
                $this->update($data, 
                $this->_db->quoteInto($pkIdentity . '= ?', $data[$pkIdentity]));
                return $id;
            }else{
                // we don't have a valid pk, insert it
                //$data[$pkIdentity] = NULL;
                return $this->insert($data);
            }
        }else{
            return false;
        }
    }
    
    public function autoGrade($data){
        $this->_setupPrimaryKey();
        $primary = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];
        if( isset($data[$pkIdentity]) && $data[$pkIdentity] ){
            // we have a non-null value for the primary key, check if we can update
            $select = new Zend_Db_Select($this->_db);
            $select->from($this->_name);
            $select->where($pkIdentity . '= ?', $data[$pkIdentity]);
            $select->reset(Zend_Db_Table::COLUMNS);
            $select->columns(
            array(
                'COUNT(' . $pkIdentity . ')'
            ));
            if( $this->_db->fetchOne($select) == 1 ){
                //we have a grade before. ignore new data
                return false;
            }else{
                // we don't have a valid pk, insert it
                //$data[$pkIdentity] = NULL;
                return $this->insert($data);
            }
        }else{
            return false;
        }
    }
    
    protected function _select(){
        $select = new Zend_Db_Select($this->_db);
        return $select;
    }
}
