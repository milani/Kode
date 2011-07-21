<?php

/**
 * Model that manages the users within the application
 *
 * @category App
 * @package App_Model
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class AdminUser extends App_Model {

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
    protected $_name = 'admin_users';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_AdminUser';
    
    protected $_displayColumn = 'lastname';
    /**
     * Logs an user in the application based on his
     * username and email
     * 
     * @param string $username
     * @param string $password 
     * @access public
     * @return void
     */
    public function login($username, $password){

        // adapter cfg
        $adapter = new Zend_Auth_Adapter_DbTable($this->_db);
        $adapter->setTableName($this->_name);
        $adapter->setIdentityColumn('username');
        $adapter->setCredentialColumn('password');
        // checking credentials
        $adapter->setIdentity($username);
        $adapter->setCredential(AdminUser::hashPassword($password));
        
        $result = $adapter->authenticate();
        if( $result->isValid() ){
            // get the user row
            $user = $adapter->getResultRowObject(NULL, 'password');
            if($user->active == 0){
                return FALSE;
            }
            // clear the existing data
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
            
            // check if the password has expired
            $auth->getStorage()->write($user);
            $this->update(
                array(
                	'last_login' => new Zend_Db_Expr('NOW()')
                ), $this->_db->quoteInto('id = ?', $user->id)
            );
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function toggleActive($userId){
        $user = Zend_Auth::getInstance()->getIdentity();
        if($user->id == $userId){
            return FALSE;
        }
        $row = $this->findById($userId);
        $row['active'] = 1 - $row['active'];
        $affectedRow = $this->update($row, $this->_db->quoteInto('id = ?', $userId));
        if($affectedRow == 0) return FALSE;
        
        return ($row['active'] == 0)?'deactivated':'activated'; 
    }
    /**
     * Changes the current user's password
     * 
     * @param string $password 
     * @access public
     * @return void
     */
    public function changePassword($password){

        if( ! Zend_Auth::getInstance()->hasIdentity() ){
            throw new Zend_Exception(
            'You must have one authenticated user in the application in order to be able to call this method');
        }
        $user = Zend_Auth::getInstance()->getIdentity();
        $password = AdminUser::hashPassword($password);
        $this->update(
            array(
            	'password' => $password, 
        		'last_password_update' => new Zend_Db_Expr('NOW()'), 
        		'password_valid' => 1
            ), $this->_db->quoteInto('id = ?', $user->id)
        );
    }

    /**
     * Updates the user's profile. 
     * 
     * @param array $data 
     * @access public
     * @return void
     */
    public function updateProfile(array $data){

        $user = Zend_Auth::getInstance()->getIdentity();
        $data['id'] = $user->id;
        $this->save($data);
    }

    /**
     * Overrides save() in App_Model
     * 
     * @param array $data 
     * @access public
     * @return int
     */
    public function save($data){

        $id = parent::save($data);
        if( isset($data['groups']) && is_array($data['groups']) &&
         ! empty($data['groups']) ){
            $groups = $data['groups'];
        }else{
            $groups = array();
        }
        $userGroupModel = new AdminUserGroup();
        $userGroupModel->saveForUser($groups, $id);
        return $id;
    }
    
    /**
     * Registers a user
     * 
     * @param array $data
     * @access public
     * @return int
     */
    public function register($data){
        $id = parent::save($data);
        $groups = array('2');
        $userGroupModel = new AdminUserGroup();
        $userGroupModel->saveForUser($groups,$id);
        return $id;
    }
    
    /**
     * Sends user's username to his email.
     * 
     * @param email
     * @access public
     * @return boolean
     */
    public function recoverUsername($email){
        $row = $this->findByEmail($email);
        if($row !== FALSE){
            $mailer = new App_Mail();
            $mailer->recoverUsername($row['email'],$row['username']);
            return true;
        }
        return false;
    }
    
    /**
     * Resets a user's password ( due to password lost )
     * 
     * @param username
     * @access public
     * @return boolean
     */
    public function resetpassword($username){
        $row = $this->findByUsername($username);
        if($row !== FALSE){
            $characters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F',
            'G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
            srand(time());
            $length = rand(6,12);
            $password = '';
            for($i = 0;$i < $length ; $i++){
                $password .= $characters[rand(0,61)];
            }
            $hashedPassword = AdminUser::hashPassword($password);
            $count = $this->update(
                array(
                	'password' => $hashedPassword, 
            		'last_password_update' => new Zend_Db_Expr('NOW()'), 
            		'password_valid' => 1
                ), $this->_db->quoteInto('id = ?', $row['id']));
                
            if($count > 0){

                $mailer = new App_Mail();
                $mailer->resetPassword($row['email'],$password);
            
                return true;
            }
            
        }
        return false;
    }
    /**
     * Overrides insert() in App_Model
     * 
     * @param array $data 
     * @access public
     * @return int
     */
    public function insert($data){

        $data['last_password_update'] = new Zend_Db_Expr('NOW()');
        $data['password'] = AdminUser::hashPassword($data['password']);
        $data['password_valid'] = 0;
        return parent::insert($data);
    }

    /**
     * Hashes a password using the salt in the app.ini
     *
     * @param string $password 
     * @static
     * @access public
     * @return string
     */
    public static function hashPassword($password){

        $config = Zend_Registry::get('config');
        return sha1($config->admin->security->passwordsalt . $password);
    }

    /**
     * Overrides getAll() in App_Model
     * 
     * @param int $page 
     * @access public
     * @return Zend_Paginator
     */
    public function findAll($page = 1){

        $paginator = parent::findAll($page);
        $items = array();
        $userGroupModel = new AdminUserGroup();
        foreach( $paginator as $item ){
            $item['groups'] = array();
            foreach( $userGroupModel->findByUserId($item['id'], TRUE) as $group ){
                $item['groups'][$group['id']] = $group['name'];
            }
            $items[] = $item;
        }
        return Zend_Paginator::factory($items);
    }
    
    public function findAllAssistants($page = 1){
        $paginator = parent::findAll($page);
        $items = array();
        $userGroupModel = new AdminUserGroup();
        foreach( $paginator as $item ){
            $item['groups'] = array();
            foreach( $userGroupModel->findByUserId($item['id'], TRUE) as $group ){
                $item['groups'][$group['id']] = $group['name'];
            }
            if($group['id'] == 2){
                $items[] = $item;
            }
        }
        return Zend_Paginator::factory($items);
        
    }
    /**
     * Overrides findById() in App_Model
     * 
     * @param int $userId 
     * @access public
     * @return array
     */
    public function findById($userId){

        $row = parent::findById($userId);
        if( ! empty($row) ){
            $row['groups'] = array();
            $userGroupModel = new AdminUserGroup();
            foreach( $userGroupModel->findByUserId($userId, TRUE) as $group ){
                $row['groups'][$group['id']] = $group['name'];
            }
        }
        return $row;
    }
    
    public function findByUsername($username,$force = false){
        $select = $this->_getSelect($force);
        $column = $this->_extractTableAlias($select) . '.' . 'username';
        $select->where($column . ' = ?', $username);
        $row = $this->_db->fetchRow($select);
        return $row;
    }
    
    public function findByEmail($email,$force = false){
        $select = $this->_getSelect($force);
        $column = $this->_extractTableAlias($select) . '.' . 'email';
        $select->where($column . ' = ?', $email);
        $row = $this->_db->fetchRow($select);
        return $row;
    }
    /**
     * Overrides delete() in App_Model.
     *
     * When an user is deleted, all associated objects are also
     * deleted
     * 
     * @param mixed $where 
     * @access public
     * @return int
     */
    public function delete($where){

        $this->_setupPrimaryKey();
        $primary = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];
        if( is_numeric($where) ){
            $where = $pkIdentity . ' = ' . $where;
        }
        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $select->where($where);
        $rows = $this->_db->fetchAll($select);
        $userGroupModel = new AdminUserGroup();
        foreach( $rows as $row ){
            $userGroupModel->deleteByUserId($row['id']);
        }
        return parent::delete($where);
    }
}
