<?php

/**
 * Model that manages the flags (controller names) for defining
 * the Flags in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Flag extends App_Model {

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
    protected $_name = 'flags';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Flag';

    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'name';

    /**
     * Paths that are hardcoded in the application and should not
     * be displayed to the users for editing. These resources manage
     * very critical areas of the app
     * 
     * @var array
     * @access protected
     */
    protected $_hardcodedResources = array(
        'error', 'index'
    );

    public function init(){

        $this->_db = Zend_Registry::get('dbAdapter');
    }

    /**
     * Finds a resource based on its name
     * 
     * @param string $name 
     * @access public
     * @return void
     */
    public function findByName($name){

        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $select->where('name = ?', $name);
        return $this->_db->fetchRow($select);
    }

    /**
     * Returns an array with all resources and their associated
     * privileges
     * 
     * @access public
     * @return array
     */
    public function getAllFlagsAndPrivileges(){

        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $rows = $this->_db->fetchAll($select);
        $privilegeModel = new Privilege();
        foreach( $rows as $key => $row ){
            if( in_array($row['name'], $this->_hardcodedResources) ){
                unset($rows[$key]);
            }else{
                $rows[$key]['privileges'] = $privilegeModel->findByFlagId(
                $row['id']);
            }
        }
        return $rows;
    }

    /**
     * Checks if a resource is registered. This is used only for
     * debugging purposes
     * 
     * @param string $resource 
     * @param string $privilege 
     * @access public
     * @return void
     */
    public function checkRegistered($resource, $privilege){

        $select = new Zend_Db_Select($this->_db);
        $select->from(array(
            'r' => $this->_name
        ));
        $select->join(array(
            'p' => 'privileges'
        ), 'r.id = p.flag_id');
        $select->where('r.name = ?', $resource);
        $select->where('p.name = ?', $privilege);
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(array(
            'COUNT(r.id)'
        ));
        $count = $this->_db->fetchOne($select);
        return $count != 0;
    }

    /**
     * Updates the flag and flippers using a module name
     *
     * @param string $module 
     * @return boolean
     */
    public function add($formData){

        $module = $formData['name'];
        $path = APPLICATION_PATH . '/modules/' . $module . '/controllers';
        if( ! is_readable($path) ){
            return false;
        }
        $files = array();
        if( is_file($path) ){
            $files[] = basename($path);
            $path = dirname($path);
        }else{
            if( ($dir = opendir($path)) !== false ){
                while( ($file = readdir($dir)) !== false ){
                    if( fnmatch('*.php', $file) &&
                     $file !== 'ErrorController.php' ){
                        $files[] = $file;
                    }
                }
                closedir($dir);
            }
        }
        $resources = array();
        foreach( $files as $file ){
            $filepath = $path . DIRECTORY_SEPARATOR . $file;
            require_once $filepath;
            $reflectionFile = new Zend_Reflection_File($filepath);
            foreach( $reflectionFile->getClasses() as $class ){
                $classInfo = array(
                    
                'description' => $class->getDocblock()->getShortDescription(), 
                'name' => strtolower($module) . '-' .
                 App_Inflector::convertControllerName($class->getName()), 
                'methods' => array()
                );
                foreach( $class->getMethods() as $method ){
                    if( substr($method->getName(), - 6) == 'Action' ){
                        $classInfo['methods'][] = array(
                            
                        'description' => $method->getDocblock()->getShortDescription(), 
                        'name' => App_Inflector::convertActionName(
                        $method->getName())
                        );
                    }
                }
                $resources[] = $classInfo;
            }
        }
        $flagFlippers = App_Cli_FlagFlippers::getInstance();
        $inserts = $flagFlippers->generateInserts($resources);
        if( empty($inserts) ){
            return true;
        }
        $db = Zend_Db::factory(Zend_Registry::get('config')->resources->db);
        try{
            foreach( $inserts as $insert )
                $db->query($insert);
        }catch(Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Change the activation of a flag in a given environment
     *
     * @param int $id 
     * @param string $env 
     * @return void
     */
    public function toogleFlag($id, $env){

        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $select->where('id = ?', $id);
        $row = $this->_db->fetchRow($select);
        switch($env){
            case APP_STATE_PRODUCTION:
                $row['active_on_prod'] = ! $row['active_on_prod'];
                break;
            default:
                $row['active_on_dev'] = ! $row['active_on_dev'];
                break;
        }
        $this->save($row);
    }
}
