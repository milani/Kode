<?php

/**
 * Form for editing users
 *
 *
 * @category admin
 * @package admin_forms
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class UserEditForm extends UserForm {

    /**
     * Overrides init() in Zend_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        // init the parent
        parent::init();
        $this->getElement('username')->setOptions(array('required'=>false))->setAttrib('disabled', 'disabled');
        $this->getElement('class_id')->setOptions(array('required'=>false))->setAttrib('disabled', 'disabled');
    }

}
