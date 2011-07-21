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
class App_Form_Decorator_Loginlinks extends Zend_Form_Decorator_Abstract {

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
        $links = $options['links'];
        unset($options['links']);
        if( NULL !== ($translator = $element->getTranslator()) ){
            for($i = 0; $i < count($links); $i++){
                $links[$i]['title'] = $translator->translate($links[$i]['title']);
                $links[$i]['text'] = $translator->translate($links[$i]['text']);
            }
        }
        $renderedLinks = '';
        foreach($links as $link){
            $renderedLinks .= sprintf('<dd class="links"><a href="%1$s" title="%2$s">%3$s</a></dd>',$link['url'], $link['title'], $link['text']);
        }
        switch($placement){
            case self::APPEND:
                return $content . $separator . $renderedLinks;
            case self::PREPEND:
                return $renderedLinks . $separator . $content;
        }
    }
}