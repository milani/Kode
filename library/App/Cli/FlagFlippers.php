<?php

/**
 * Creates automatic inserts for the Flag and Flipper system
 *
 * @category App
 * @package App_Cli
 * @subpackage App_Cli_FlagFlippers
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Cli_FlagFlippers {

    /**
     * Singleton instance
     * 
     * @static
     * @var App_Cli_FlagFlippers
     * @access protected
     */
    protected static $_instance;

    /**
     * Database adapter
     * 
     * @var Zend_Db_Adapter_Abstract
     * @access protected
     */
    protected $_db;

    /**
     * Inits the object with the default db adapter
     * 
     * @access protected
     * @return void
     */
    protected function __construct(){

        $this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * Returns a singleton instance
     * 
     * @static
     * @access public
     * @return void
     */
    public static function getInstance(){

        if( NULL === self::$_instance ){
            self::$_instance = new App_Cli_FlagFlippers();
        }
        return self::$_instance;
    }

    /**
     * Overrides the __clone() magic method in order
     * to prevent cloning of this object
     * 
     * @access public
     * @return void
     */
    public function __clone(){

        throw new Zend_Exception('Cloning singleton objects is forbidden');
    }

    /**
     * Generates an array of SQL insert statements that 
     * will save the current 
     * 
     * @param array $resources 
     * @access public
     * @return string
     */
    public function generateInserts(array $resources){

        $quotedName = $this->_db->quoteIdentifier('name');
        $quotedDescription = $this->_db->quoteIdentifier('description');
        $quotedFlagsTable = $this->_db->quoteIdentifier('flags');
        $insertResourceTemplate = sprintf(
        'INSERT IGNORE INTO %s (%s, %s) VALUES (?, ?);', $quotedFlagsTable, 
        $quotedName, $quotedDescription);
        $selectResourceTemplate = sprintf(
        'SET @flag_id := (SELECT id FROM %s WHERE %s = ?);', $quotedFlagsTable, 
        $quotedName);
        $insertPrivilegeTemplate = '(@flag_id, %s, %s)';
        $inserts = array();
        foreach( $resources as $resource ){
            // ready the insert resource query
            $insertResourceSql = $this->_db->quoteInto(
            $insertResourceTemplate, $resource['name'], NULL, 1);
            $insertResourceSql = $this->_db->quoteInto($insertResourceSql, 
            $resource['description'], NULL, 1);
            // ready the select resource query
            $selectResourceSql = $this->_db->quoteInto(
            $selectResourceTemplate, $resource['name']);
            // ready the insert privilege query
            $insertPrivilegeSql = sprintf(
            'INSERT IGNORE INTO %s (%s, %s, %s) VALUES ', 
            $this->_db->quoteIdentifier('privileges'), 
            $this->_db->quoteIdentifier('flag_id'), $quotedName, 
            $quotedDescription);
            $insertPrivilegeSqlParts = array();
            foreach( $resource['methods'] as $method ){
                $insertPrivilegeSqlParts[] = sprintf($insertPrivilegeTemplate, 
                $this->_db->quote($method['name']), 
                $this->_db->quote($method['description']));
            }
            $inserts[] = $insertResourceSql . PHP_EOL . $selectResourceSql .
             PHP_EOL . $insertPrivilegeSql . PHP_EOL . "\t" .
             implode(',' . PHP_EOL . "\t", $insertPrivilegeSqlParts) . ';' .
             PHP_EOL;
        }
        return $inserts;
    }
}