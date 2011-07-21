<?php

/**
 * Holds the frontend's navigation system
 *
 *
 * @category App
 * @package App_Frontend
 * @subpackage Navigation
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Frontend_Navigation implements App_Navigation_Interface{

	public function getPages() {
		$pages = array(
		    array(
		        'label'	        => 'Dashboard',
                'controller'	=> 'index',
                'action'	    => 'index'
		    ),
            array(
                'label'	        => 'Assignments',
                'controller'	=> 'assignment',
                'action'	    => 'index'
            ),
            array(
                'label'		    => 'Submissions',
                'controller'	=> 'problem',
                'action'		=> 'submissions',
            )
        );
        return $pages;
	}

}
