<?php

/**
 * Helper that will be used to display dates throughout 
 * the application
 *
 * 
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_FormatDate extends Zend_View_Helper_Abstract {

    /**
     * Convenience method
     * call $this->formatDate() in the view to access 
     * the helper
     *
     * @access public
     * @return string
     */
    public function formatDate($date){
        $date = new App_Date($date);
        return $date->jdate('d M Y، ساعت H:i');
    }
}