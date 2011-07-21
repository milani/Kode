<?php
require_once 'Zend/Form/Element/Multi.php';

class App_Form_Element_MultiHidden extends Zend_Form_Element_Multi {

    public $helper = "formMultiHidden";

    protected $_isArray = true;

}