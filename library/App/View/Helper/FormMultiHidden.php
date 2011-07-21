<?php


/** Zend_View_Helper_FormRadio */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a set of checkbox button elements
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class App_View_Helper_FormMultiHidden extends Zend_View_Helper_FormElement
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'hidden';

    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Generates a set of checkbox button elements.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The checkbox value to mark as 'checked'.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the checkbox value, and the array value is the radio text.
     *
     * @param array|string $attribs Attributes added to each radio.
     *
     * @return string The radio buttons XHTML.
     */
    public function formMultiHidden($name, $value = null, $attribs = null,
        $options = null, $listsep = "\n")
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        if (isset($id)) {
            if (isset($attribs) && is_array($attribs)) {
                $attribs['id'] = $id;
            } else {
                $attribs = array('id' => $id);
            }
        }
        $value = (array) $value;
        $xhtml = '';
        foreach($value as $opt_value){
            $xhtml .= $this->_hidden($name, $opt_value, $attribs);
        }
        return $xhtml;
        //return $this->formRadio($name, $value, $attribs, $options, $listsep);
    }
    
    public function formRadio($name, $value = null, $attribs = null,
        $options = null, $listsep = "\n")
    {

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable

        $listsep = "\n";
        // the radio button values and labels
        $options = (array) $options;

        // build the element
        $xhtml = '';
        $list  = array();

        // should the name affect an array collection?
        $name = $this->view->escape($name);
        if ($this->_isArray && ('[]' != substr($name, -2))) {
            $name .= '[]';
        }

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }
 
        require_once 'Zend/Filter/Alnum.php';
        $filter = new Zend_Filter_Alnum();
        foreach($value as $opt_value){
            // Wrap the radios in labels
            $optId = $id . '-' . $filter->filter($opt_value);
            $radio =  '<input type="' . $this->_inputType . '"'
                    . ' name="' . $name . '"'
                    . ' id="' . $optId . '"'
                    . ' value="' . $this->view->escape($opt_value) . '"'
                    . $this->_htmlAttribs($attribs)
                    . $endTag;

            // add to the array of radio buttons
            $list[] = $radio;
        }

        // done!
        $xhtml .= implode($listsep, $list);

        return $xhtml;
    }
    
}
