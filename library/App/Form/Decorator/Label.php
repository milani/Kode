<?php

/**
 * A more flexible label decorator
 *
 *
 * @category App
 * @package App_Form
 * @subpackage Decorator
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Form_Decorator_Label extends Zend_Form_Decorator_Label {

    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content){

        $element = $this->getElement();
        $view = $element->getView();
        if( NULL === $view ){
            return $content;
        }
        $label = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag = $this->getTag();
        $id = $this->getId();
        $class = $this->getClass();
        $options = $this->getOptions();
        unset($options['tagOptions']);
        if( empty($label) && empty($tag) ){
            return $content;
        }
        if( ! empty($label) ){
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), 
            trim($label), $options);
        }else{
            $label = '&nbsp;';
        }
        if($element instanceof Zend_Form_Element_Checkbox || $element instanceof Zend_Form_Element_Radio){
            $descDecorator = new Zend_Form_Decorator_Description();
            $descDecorator->setElement($element);
            $label = $label . $descDecorator->render('');
        }
        if( NULL !== $tag ){
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $options = array(
                'tag' => $tag, 
            'id' => $this->getElement()->getName() . '-label'
            );
            $tagOptions = $this->getOption('tagOptions');
            if( ! empty($tagOptions) ){
                $options += $tagOptions;
            }
            $decorator->setOptions($options);
            $label = $decorator->render($label);
            
            
            
        }
        switch($placement){
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}