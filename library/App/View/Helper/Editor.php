<?php
/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';

/**
 * Helper to generate an "editor" element
 *
 * @category   App
 * @package    App_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_Editor extends Zend_View_Helper_FormElement {

    /**
     * Generates an 'editor' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function editor($name, $value = null, $attribs = null){

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $editor = new Ckeditor();
        // determine the XHTML value
        $urlHelper = new Zend_View_Helper_BaseUrl();
        $baseUrl = $urlHelper->baseUrl();
        $editor->basePath = $baseUrl.'/CKEditor/';
        $editor->config['height'] = $info['attribs']['height'];
        $editor->config['width'] = $info['attribs']['width'];
        $editor->config['plugins'] = 'basicstyles,bidi,blockquote,button,clipboard,colorbutton,colordialog,contextmenu,dialogadvtab,elementspath,enterkey,entities,filebrowser,find,font,format,horizontalrule,htmldataprocessor,indent,justify,keystrokes,link,list,liststyle,maximize,pastefromword,pastetext,popup,removeformat,resize,scayt,showborders,stylescombo,table,tabletools,specialchar,tab,toolbar,undo,wysiwygarea,wsc';
        $editor->returnOutput = true;
        $editor->textareaAttributes = $info['attribs'];
        //$config=array('toolbar'=>'Full');
        return $editor->editor($name, $info['value']);
    }
}
