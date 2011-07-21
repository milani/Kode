<?php

/**
 * Checks if a password exists in the database.
 * 
 * Extremely useful for cases when an user tries to change
 * his password and the current password is also required
 * 
 *
 * @category App
 * @package App_Validate
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Validate_PasswordExists extends Zend_Validate_Db_RecordExists {

    /**
     * Treatment for the password field.
     *
     * Can be one of:
     * i. a callable that will be executed by PHP
     * ii. a SQL expression that will be executed by the RDBMS
     * iii. a Zend_Db_Expr
     *
     * If it's an SQL function, use ? as a placeholder for the value
     * Ex: 'MD5(?)'
     * 
     * @var mixed
     * @access protected
     */
    protected $_treatment = '?';

    /**
     * Name of the table's primary or unique key. It will be used to
     * identify the user
     * 
     * @var string
     * @access protected
     */
    protected $_userPkField = 'id';

    /**
     * Value for the primary key. 
     * This value on the $_userPkField column should identify the 
     * user
     * 
     * @var mixed
     * @access protected
     */
    protected $_userPkValue;

    /**
     * Holds the error messages
     * 
     * @var array
     * @access protected
     */
    protected $_messageTemplates = array(
        
    self::ERROR_NO_RECORD_FOUND => 'The password you provided is not valid.'
    );

    /**
     * Constructs the validator
     * 
     * @param Zend_Config|array $config 
     * @access public
     * @return void
     */
    public function __construct($config){

        parent::__construct($config);
        if( $config instanceof Zend_Config ){
            $config = $config->toArray();
        }
        if( isset($config['treatment']) ){
            $this->setTreatment($config['treatment']);
        }
        if( isset($config['userPkField']) ){
            $this->setUserPkField($config['userPkField']);
        }
        if( isset($config['userPkValue']) ){
            $this->setUserPkValue($config['userPkValue']);
        }
    }

    /**
     * Setter for $this->_treatment
     *
     * @param string $treatment
     * @access public
     * @return void
     */
    public function setTreatment($treatment){

        if( ! ($treatment instanceof Zend_Db_Expr) ){
            if( strpos($treatment, '?') === FALSE ){
                if( ! is_callable($treatment) ){
                    require_once 'Zend/Validate/Exception.php';
                    throw new Zend_Validate_Exception(
                    'The provided password treatment is not valid');
                }
            }
        }
        $this->_treatment = $treatment;
    }

    /**
     * Getter for $this->_treatment
     *
     * @access public
     * @return string
     */
    public function getTreatment(){

        return $this->_treatment;
    }

    /**
     * Setter for $this->_userPkField
     *
     * @param string $userPkField
     * @access public
     * @return void
     */
    public function setUserPkField($userPkField){

        $this->_userPkField = $userPkField;
    }

    /**
     * Getter for $this->_userPkField
     *
     * @access public
     * @return string
     */
    public function getUserPkField(){

        return $this->_userPkField;
    }

    /**
     * Setter for $this->_userPkValue
     *
     * @param mixed $userPkValue
     * @access public
     * @return void
     */
    public function setUserPkValue($userPkValue){

        $this->_userPkValue = $userPkValue;
    }

    /**
     * Getter for $this->_userPkValue
     *
     * @access public
     * @return mixed
     */
    public function getUserPkValue(){

        return $this->_userPkValue;
    }

    /**
     * Overrides _query() from Zend_Validate_Db_Abstract
     * 
     * @param mixed $value 
     * @access protected
     * @return void
     */
    protected function _query($value){

        if( $this->_adapter === NULL ){
            $this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            if( NULL === $this->_adapter ){
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception('No database adapter present');
            }
        }
        $select = new Zend_Db_Select($this->_adapter);
        $select->from($this->_table, array(
            $this->_field
        ), $this->_schema);
        if( NULL == $this->_userPkValue ){
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception(
            'You must specify the value for the primary / unique key');
        }
        $select->where(
        $this->_adapter->quoteIdentifier($this->_userPkField) . ' = ?', 
        $this->_userPkValue);
        if( strpos($this->_treatment, '?') !== FALSE ||
         $this->_treatment instanceof Zend_Db_Expr ){
            $where = $this->_adapter->quoteIdentifier($this->_field) . ' = ' .
             $this->_treatment;
            $select->where($where, $value);
        }else{
            $value = call_user_func($this->_treatment, $value);
            $select->where(
            $this->_adapter->quoteIdentifier($this->_field) . ' = ?', $value);
        }
        $select->limit(1);
        $result = $this->_adapter->fetchRow($select, array(), Zend_Db::FETCH_ASSOC);
        return $result;
    }
}