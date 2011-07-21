<?php

/**
 * Parent form for all the admin forms
 *
 * @category App
 * @package App_Admin
 * @copyright Copyright (c) 2011, Morteza Milani
 */
abstract class App_Frontend_Form extends App_Form {

    /**
     * URL for the cancelLink
     * 
     * @var mixed
     * @access protected
     */
    protected $_cancelLink;

    /**
     * Conditions to add
     * @var array
     * @access protected
     */
    protected $_conditions = array();

    /**
     * Overrides init() in App_Form
     * 
     * @access public
     * @return void
     */
    public function init(){

        parent::init();
        $config = Zend_Registry::get('config');
        // add an anti-CSRF token to all forms
        $csrfHash = new Zend_Form_Element_Hash('csrfhash');
        $csrfHash->setOptions(
            array(
            	'required' => TRUE, 
        		'filters' => array(
            		'StringTrim', 'StripTags'
                ),
                'validators' => array(
            		'NotEmpty'
                ),
                'salt' => $config->admin->security->csrfsalt . get_class($this)
            )
        );
        $this->addElement($csrfHash);
    }

    /**
     * Overrides render() in App_Form
     * 
     * @param Zend_View_Interface $view 
     * @access public
     * @return string
     */
    public function render(Zend_View_Interface $view = NULL){

        foreach( $this->getElements() as $element ){
            $this->_replaceLabel($element);
            if( $element instanceof Zend_Form_Element_Hidden ||
             $element instanceof Zend_Form_Element_Hash ){
                $this->_addHiddenClass($element);
            }else{
                if( $element instanceof Zend_Form_Element_Checkbox ){
                    $this->_appendLabel($element);
                }else{
                    if( $element instanceof Zend_Form_Element_MultiCheckbox ){
                        $element->getDecorator('Label')->setOption('tagOptions', 
                        array(
                            'class' => 'checkboxGroup'
                        ));
                        $element->getDecorator('HtmlTag')->setOption('class', 
                        'checkboxGroup');
                    }
                }
            }
        }
        $this->_cancelLink();
        if( NULL === $this->getAttrib('id') ){
            $controllerName = Zend_Registry::get('controllerName');
            $actionName = Zend_Registry::get('actionName');
            $this->setAttrib('id', $controllerName . '-' . $actionName);
        }
        return parent::render($view);
    }

    /**
     * Add the hidden class
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _addHiddenClass($element){

        $label = $element->getLabel();
        if( empty($label) ){
            $element->setLabel('&nbsp;');
        }
        $element->getDecorator('HtmlTag')->setOption('class', 'hidden');
        $element->getDecorator('Label')->setOption('tagOptions', 
        array(
            'class' => 'hidden'
        ));
    }

    /**
     * Forces the element's label to be appended to it rather
     * than prepend it
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _appendLabel($element){

        $element->getDecorator('HtmlTag')->setOption('class', 'radiocheck');
        $element->getDecorator('Label')
                ->setOption('placement', Zend_Form_Decorator_Abstract::APPEND)
                ->setOption('tagOptions', 
                            array(
                                'class' => 'radiocheck'
                            )
                );
        $element->removeDecorator('Description');
    }

    /**
     * Replaces the default label decorator with a more
     * versatile one
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _replaceLabel($element){

        $decorators = $element->getDecorators();
        if( isset($decorators['Zend_Form_Decorator_Label']) ){
            $newDecorators = array();
            foreach( $decorators as $key => $decorator ){
                if( $key === 'Zend_Form_Decorator_Label' ){
                    $label = new App_Form_Decorator_Label();
                    $label->setOptions($decorator->getOptions());
                    $newDecorators['App_Form_Decorator_Label'] = $label;
                }else{
                    $newDecorators[$key] = $decorator;
                }
            }
            $element->clearDecorators();
            $element->setDecorators($newDecorators);
        }
    }

    /**
     * Assembles url according to router provided
     * 
     * @param array $params 
     * @access protected
     * @return string
     */
    protected function _assembleUrl(array $params){

        $router = Zend_Controller_Front::getInstance()->getRouter();
        $currentRouter = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        $url = $router->assemble($params, $currentRouter, true);
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl(); 
        //@TODO check if there is any problem concatanating _baseUrl and $url
        //$url =  $baseUrl . $url;
        return $url;
    }

    /**
     * Adds a cancel link to the form
     * 
     * @access protected
     * @return void
     */
    protected function _cancelLink(){

        if( $this->_cancelLink !== FALSE ){
            if( $this->_cancelLink === NULL ){
                $urlParams = array(
                    'controller' => Zend_Registry::get('controllerName')
                );
                $this->_cancelLink = $this->_assembleUrl($urlParams);
            }
            $cancelLink = $this->_cancelLink;
            $cancelLinkDecorator = new App_Form_Decorator_Backlink();
            $cancelLinkDecorator->setOption('url', $cancelLink);
            $element = $this->getElement('submit');
            $decorators = $element->getDecorators();
            $element->clearDecorators();
            foreach( $decorators as $decorator ){
                $element->addDecorator($decorator);
                if( $decorator instanceof Zend_Form_Decorator_ViewHelper ){
                    $element->addDecorator($cancelLinkDecorator);
                }
            }
        }
    }

    /**
     * Setter for $this->_cancelLink
     *
     * @param string $cancelLink
     * @access public
     * @return void
     */
    public function setCancelLink($cancelLink){

        $this->_cancelLink = $cancelLink;
    }

    /**
     * Getter for $this->_cancelLink
     *
     * @access public
     * @return string
     */
    public function getCancelLink(){

        if( NULL === $this->_cancelLink ){
            $urlParams = array(
                'controller' => Zend_Registry::get('controllerName')
            );
            $this->_cancelLink = $this->_assembleUrl($urlParams);
        }
        return $this->_cancelLink;
    }

    /**
     * Adds conditional elements to the form.
     *
     * @access public
     * @return string
     */
    public function addCondition($element, $target, $values){

        if( is_string($element) ){
            $element = $this->getElement($element);
        }
        //$this->_conditions[$element->getName()]=array('target'=>$target,'values'=>$values);
        array_push($this->_conditions, $element->getName());
        $scriptDecorator = new App_Form_Decorator_Condition();
        $scriptDecorator->setOptions(
        array(
            'placement' => Zend_Form_Decorator_Abstract::APPEND, 
        'condition' => array(
            'target' => $target, 'values' => $values
        )
        ));
        $element->addDecorator($scriptDecorator);
    }

    /**
     * Validate the form
     * @todo needs more attention
     * @param  array $data
     * @return boolean
     */
    /*public function isValid($data){

        $this->populate($data);
        foreach( $this->_conditions as $conditionedElement ){
            $condition = $this->getElement($conditionedElement)
                ->getDecorator('App_Form_Decorator_Condition')
                ->getOption('condition');
            if( $condition['target'] === null ){
                $value = $this->getElement($conditionedElement)->getValue();
                foreach( $condition['values'] as $key => $val ){
                    if( $value !== $key ){
                        if( is_string($val) )
                            $val = array(
                                $val
                            );
                        foreach( $val as $target ){
                            if( in_array($target, $condition['values'][$value]) ){
                                continue;
                            }
                            $this->getElement($target)
                                ->setRequired(false)
                                ->removeValidator('NotEmpty');
                            unset($data[$target]);
                        }
                    }
                }
            }
        }
        return parent::isValid($data);
    }*/
}
