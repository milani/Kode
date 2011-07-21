<?php

/**
 * Holds the admin's navigation system
 *
 *
 * @category App
 * @package App_Admin
 * @subpackage Navigation
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Admin_Navigation implements App_Navigation_Interface{

    /**
     * Returns an array of pages
     * 
     * @access protected
     * @return void
     */
    public function getPages(){

        $pages = array(
            array(
                'label' => 'Configure',
                'controller'	=> 'system',
                'action'	=> 'configure', 
        		
            ),
            array(
            	'label' => 'User Management', 
        		'pages' => array(
                    array(
            			'label' => 'User Groups',
                    	'controller' => 'groups', 
        				'action' => 'index'
                    ),
                    array(
            			'label' => 'Assistants',
                    	'controller' => 'users', 
        				'action' => 'assistants'
                    ),
                    array(
            			'label' => 'Students',
                    	'controller' => 'users', 
        				'action' => 'students'
                    ),
                    array(
            			'label' => 'Search',
                    	'controller' => 'users', 
        				'action' => 'search'
                    ),
                )
            ),
            array(
                'label'	=> 'Class Management',
                'pages'	=> array(
                    array(
                        'label' => 'Courses',
                    	'controller' => 'course', 
        				'action' => 'index'
                    ),
                    array(
                        'label'	=> 'Classes',
                        'controller' => 'class',
                        'action'	=> 'index',
                    )
                )
            )
        );
        return $pages;
    }
}
