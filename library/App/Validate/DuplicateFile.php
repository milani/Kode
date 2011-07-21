<?php

/**
 * Checks if another file with the same name uploaded already.
 * 
 * This situation will cause error in compressing attachments.
 *
 * @category App
 * @package App_Validate
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Validate_DuplicateFile extends Zend_Validate_Db_Abstract {
    /**
     * Error constants
     */
    const ERROR_DUPLICATE_FOUND    = 'duplicateFound';
    
    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_DUPLICATE_FOUND    => "Please do not upload files with same names ('%value%')",
    );
    
    /**
     * Internal file array
     * @var array
     */
    protected $_files = array();
    
    /**
     * Check duplicates by grouping records using this field.
     * @var string
     */
    protected $_groupingField;
    
    /**
     * Holds a boolean indicating whether database should be
     * removed from validation process or not.
     * 
     * @var boolean
     */
    protected $_dbCheck = true;
    
    /**
     * Value for groupingField
     * 
     * @var mixed
     */
    protected $_groupingValue;
    
    public function __construct($options){
        parent::__construct($options);
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['adapter'] = array_shift($options);
            }
            
            if(!empty($options)) {
                $temp['groupingField'] = array_shift($options);
            }
            
            if(!empty($options)) {
                $temp['groupingValue'] = array_shift($options);
            }
            
            if(!empty($options)) {
                $temp['dbCheck'] = array_shift($options);
            }
            $options = $temp;
        }
        if(isset($options['groupingField'])){
            $this->_groupingField = $options['groupingField'];
        }
        if(isset($options['groupingValue'])){
            $this->_groupingValue = $options['groupingValue'];
        }
        if(isset($options['dbCheck'])){
            $this->_dbCheck = $options['dbCheck'];
        }
    }
    
    /**
     * Set grouping field
     * @param string $field
     */
    public function setGroupingField($field){
        $this->_groupingField = $field;
    }
    
    /**
     * Get grouping field
     * @return string
     */
    public function getGroupingField(){
        return $this->_groupingField;
    }
    
	/**
     * Set grouping value
     * @param string $value
     */
    public function setGroupingValue($value){
        $this->_groupingValue = $value;
    }
    
    /**
     * Get grouping value
     * @return string
     */
    public function getGroupingValue(){
        return $this->_groupingValue;
    }
    
    /**
     * Adds a file for validation
     *
     * @param string|array $file
     */
    public function addFile($file)
    {
        $this->_files[] = $file;
        return $this;
    }
    
    public function disableDbCheck(){
        $this->_dbCheck = false;
    } 
    
    public function enableDbCheck(){
        $this->_dbCheck = true;
    }
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the files to be uploaded and groups of files within the database
     * have no conflict in their names.
     *
     * @param  string|array $value Filenames to check for conflict names
     * @param  array        $file  File data from Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if (($file !== null) && array_key_exists('tmp_name', $file)) {
            $value = $file['name'];
        }
        
        foreach($this->_files as $file){
            if($file == $value){
                $this->_throw($value);
                return false;
            }
        }

        $this->addFile($value);
        if($this->_dbCheck){
            $db = $this->getAdapter();
            $select = $this->getSelect();
            
            $select->reset(Zend_Db_Select::WHERE);
            if(isset($this->_groupingField) && isset($this->_groupingValue)){
                $select->where($db->quoteIdentifier($this->_groupingField, true).' = '. $db->quote($this->_groupingValue));
            }
            $select->where($db->quoteIdentifier($this->_field, true).' = '.$db->quote($value));
    
            $result = $select->getAdapter()->fetchRow($select, array(), Zend_Db::FETCH_ASSOC);
    
            if($result){
                $this->_throw($value);
                return false;
            }
        }
        return true;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($fileName = null)
    {
        if ($fileName !== null) {
            $this->_value = $fileName;
        }

        $this->_error(self::ERROR_DUPLICATE_FOUND);
        return false;
    }
}