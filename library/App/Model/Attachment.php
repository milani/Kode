<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Attachment extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'file_id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'attachments';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Attachments';
    
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'file_name';
    
    public function findByFileUnique($uniqueName){
        $select = $this->_getSelect();
        $select->where('file_unique = ?',$uniqueName);
        return $this->_db->fetchRow($select);
    }
    
    /**
     * Delete unused files in attachment table.
     *
     */
    public function deleteUnusedFiles(){
        
    }
}
