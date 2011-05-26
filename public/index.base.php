<?php
/**
 * Default index.php file 
 * All other {module}/index.php files should include this one
 *
 * @category public
 * @package public
 * @copyright Weapi
 */

// define the application path constant
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

$paths = array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require APPLICATION_PATH . '/Bootstrap.php';
$boostrap = new Bootstrap();
$boostrap->runApp();
