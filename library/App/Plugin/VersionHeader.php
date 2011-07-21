<?php

/**
 * Set a special header to send the app version
 *
 * @package App_Plugin
 * @copyright Copyright (c) 2011, Morteza Milani
 */
/**
 * Pushes release version information through a special X-Version header
 */
class App_Plugin_VersionHeader extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(
    Zend_Controller_Request_Abstract $request){

        $version = APP_VERSION;
        if( ! headers_sent() ){
            header('X-Version: ' . $version);
        }
    }
}
