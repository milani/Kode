<?php

/**
 * Manages notifications
 *
 * @package frontend_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class NotificationUser extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = array('notification_id','user_id');

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'users_notifications';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_NotificationUser';

    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = null;
}
