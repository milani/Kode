<?php

/**
 * This helper is used to check if a user have the right to do/view certain things
 * 
 * This helper is accessible from controllers via $this->_helper->flagFlippers()
 *
 * @category App
 * @package App_Controller
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Controller_Action_Helper_FlagFlippers extends Zend_Controller_Action_Helper_Abstract {

    /**
     * This method is called automatically when using the name of the helper directly
     *
     * @param string $role 
     * @param string $resource
     * @return boolean
     */
    public function direct($role, $resource){

        return App_FlagFlippers_Manager::isAllowed($role, $resource);
    }
}