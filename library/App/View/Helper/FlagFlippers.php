<?php

/**
 * Flag and Flippers view helper
 * 
 * This helper is provided to be able to check the permissions
 * over flag and flippers for the users in order to modify the views.
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_FlagFlippers extends Zend_View_Helper_Abstract {

    /**
     * Check the permissions of a role through flag and flippers
     *
     * @param string $role
     * @param string $resource
     * @return boolean
     */
    public function flagFlippers($role, $resource){

        return App_FlagFlippers_Manager::isAllowed($role, $resource);
    }
}