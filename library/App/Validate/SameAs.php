<?php

/**
 * Checks if a field has the same value as another,
 * very useful in validating passwords
 *
 * @category App
 * @package App_Validate
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Validate_SameAs extends Zend_Validate_Abstract {

    /**
     * Validation failure message key for when the values are not
     * the same
     */
    const NOT_THE_SAME = 'not_the_same';

    /**
     * the external element that we check the value against 
     *
     * @var Zend_Form_Element
     */
    protected $_element;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_THE_SAME => 'The two values are not identical'
    );

    /**
     * Can receive a Zend_Form_Element parameter that will be used
     * into the validation process
     *
     * @param Zend_Form_Element $element 
     * @access public
     * @return void
     */
    public function __construct(Zend_Form_Element $element = NULL){

        if( NULL !== $element ){
            $this->setElement($element);
        }
    }

    /**
     * Setter for $this->_element
     *
     * @param Zend_Form_Element $element
     * @access public
     * @return void
     */
    public function setElement(Zend_Form_Element $element){

        $this->_element = $element;
    }

    /**
     * Getter for $this->_element
     *
     * @access public
     * @return Zend_Form_Element
     */
    public function getElement(){

        return $this->_element;
    }

    /**
     * Overrides isValid() from Zend_Validate_Interface
     *
     * @param string $value
     * @access public
     * @return bool
     */
    public function isValid($value){

        if( NULL === $this->_element ){
            require_once 'Zend/Exception.php';
            throw new Zend_Exception(
            'You must add a Zend_Form_Element to the SameAs validator prior to calling the isValid() method');
        }
        if( $value != $this->_element->getValue() ){
            $this->_error(self::NOT_THE_SAME);
            return FALSE;
        }
        return TRUE;
    }
}