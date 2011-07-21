<?php

/**
 * Decorator that displays a "cancel" link of each of the
 * forms
 *
 * @category App
 * @package App_Form
 * @subpackage Decorator
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Form_Decorator_Backlink extends Zend_Form_Decorator_Abstract {

    /**
     * Default position
     * 
     * @var string
     * @access protected
     */
    protected $_placement = 'APPEND';

    /**
     * Overrides render() in Zend_Form_Decorator_Abstract
     * 
     * @param mixed $content 
     * @access public
     * @return void
     */
    public function render($content){

        $element = $this->getElement();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $options = $this->getOptions();
        $url = $options['url'];
        unset($options['url']);
        if( NULL !== ($translator = $element->getTranslator()) ){
            $linkText = $translator->translate('Cancel');
            $spanText = $translator->translate('or');
        }else{
            $linkText = 'Cancel';
            $spanText = 'or';
        }
        switch($placement){
            case self::APPEND:
                $link = sprintf('<span class="or">%3$s</span> <a class="cancel" href="%1$s" title="%2$s">%2$s</a>',$url, $linkText, $spanText);
                return $content . $separator . $link;
            case self::PREPEND:
                $link = sprintf('<a class="cancel" href="%1$s" title="%2$s">%2$s</a> <span class="or">%3$s</span>',$url, $linkText, $spanText);
                return $link . $separator . $content;
        }
    }
}