<?php
/**
 * Environment configuration
 * Copy this file to APPLICATION_PATH/config/environment.php to define 
 * the working environment.
 *
 * Posibile values are:
 * - development
 * - staging
 * - production
 * 
 * Use the predefined constants as they improve code readability (no magic strings)
 * and help avoiding typos.
 *
 * Information on the current environment will be also placed in the registry. Use the 
 * registry to check the application's state. 
 *
 * Example:
 *
 * if (APPLICATION_ENV == 'production') {
 * // bad
 * }
 *
 * if (APPLICATION_ENV == APP_STATE_PRODUCTION) {
 * // better
 * }
 *
 * if (Zend_Registry::get('IS_PRODUCTION')) {
 * // best
 * }
 *
 * Available Zend_Registry keys are IS_PRODUCTION, IS_STAGING, IS_DEVELOPMENT
 *
 * @package application_config
 * @copyright Copyright (c) 2011, Morteza Milani
 */
define('APP_STATE_PRODUCTION', 'production');
define('APP_STATE_STAGING', 'staging');
define('APP_STATE_DEVELOPMENT', 'development');
define('APPLICATION_ENV', APP_STATE_DEVELOPMENT);
