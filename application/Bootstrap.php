<?php
/**
 * Generic bootstrap
 * 
 * This class bootstraps the common issues in all modules 
 * while each module's Bootstrap manages its own particular 
 * configuration
 *
 * @package   application
 * @copyright Copyright (c) 2011, Morteza Milani
 */
require_once 'App/Bootstrap/Abstract.php';

class Bootstrap extends App_Bootstrap_Abstract {

    /**
     * Resources to be bootstrapped first
     * 
     * @var    array    
     * @access protected
     */
    protected $_first = array(
        'Autoloader', 'Environment', 'Config', 'Log', 'Cache', 'Locale', 'Session'
    );

    /**
     * Resources to be bootstrapped last
     * 
     * @var    array    
     * @access protected
     */
    protected $_last = array(
        'Module', 'ModulePaths'
    );

    /**
     * Bootstraps the Autoloader
     * 
     * @access protected
     * @return void     
     */
    protected function _initAutoloader(){

        require_once 'Zend/Loader/Autoloader.php';
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('App_');
        $loader->registerNamespace('Zend_');
        $loader->setFallbackAutoloader(TRUE);
    }

    /**
     * Includes the file with the environment constant - APPLICATION_ENV
     * If the file cannot be read or the constant isn't defined, an exception 
     * will be throwed
     * 
     * @access protected
     * @return void     
     */
    protected function _initEnvironment(){

        $file = APPLICATION_PATH . '/config/environment.php';
        if( ! is_readable($file) ){
            throw new Zend_Exception('Cannot find the environment.php file!');
        }
        require_once ($file);
        if( ! defined('APPLICATION_ENV') ){
            throw new Zend_Exception(
            'The APPLICATION_ENV constant is not defined in ' . $file);
        }
        Zend_Registry::set('IS_PRODUCTION', 
        APPLICATION_ENV == APP_STATE_PRODUCTION);
        Zend_Registry::set('IS_DEVELOPMENT', 
        APPLICATION_ENV == APP_STATE_DEVELOPMENT);
        Zend_Registry::set('IS_STAGING', APPLICATION_ENV == APP_STATE_STAGING);
    }

    /**
     * Bootstraps the application's configuration by reading the content of the 
     * config/app.ini file, interpret it and saving the content in the Zend_Registry under 
     * the 'config' key
     * 
     * @access protected
     * @return void     
     */
    protected function _initConfig(){

        $configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config/app.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $configuration);
    }

    /**
     * Store the app version in the registry
     *
     * @access protected
     * @return void
     */
    protected function _initAppVersion(){

        $configuration = Zend_Registry::get('config');
        // Register the version of the app
        if( isset($configuration->release->version) ){
            define('APP_VERSION', $configuration->release->version);
        }else{
            define('APP_VERSION', 'unknown');
        }
        Zend_Registry::set('APP_VERSION', APP_VERSION);
    }

    /**
     * Bootstraps the current module 
     * This relies on the CURRENT_MODULE constant, if it's not defined 
     * an exception will the throwed
     * 
     * @access protected
     * @return void     
     */
    protected function _initModule(){

        if( ! defined('CURRENT_MODULE') ){
            throw new Zend_Exception(
            'The CURRENT_MODULE module constant is not defined! Please check the index.php file for this module.');
        }
        $filename = APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/Bootstrap.php';
        if( is_readable($filename) ){
            require_once $filename;
            $class = ucfirst(CURRENT_MODULE . '_Bootstrap');
            if( ! class_exists($class) ){
                throw new Zend_Exception(
                'Class ' . $class . ' could not be found in file ' . $filename);
            }
            $module = new $class();
        }
    }

    /**
     * Inits the current module's paths 
     * This relies on the CURRENT_MODULE constant, if it's not defined
     * an exception will be throwed
     * 
     * @access protected
     * @return void     
     */
    protected function _initModulePaths(){

        if( ! defined('CURRENT_MODULE') ){
            throw new Zend_Exception('The CURRENT_MODULE module constant is not defined! Please check the index.php file for this module.');
        }
        $paths = array(
            
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/controllers', 
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/forms', 
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/models', 
        ROOT_PATH . '/library/App/Model', get_include_path()
        );
        set_include_path(implode(PATH_SEPARATOR, $paths));
    }

    /**
     * Bootstraps the front controller
     * 
     * @access protected
     * @return void     
     */
    protected function _initFrontController(){

        $front = Zend_Controller_Front::getInstance();
        $front->addModuleDirectory(APPLICATION_PATH . '/modules');
        $front->setDefaultModule(CURRENT_MODULE);
    }

    /**
     * Initializes the database connection
     * 
     * @access protected
     * @return void     
     */
    protected function _initDb(){

        $config = Zend_Registry::get('config');
        $dbAdapter = Zend_Db::factory($config->resources->db);
        Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
        Zend_Registry::set('dbAdapter', $dbAdapter);
    }
    
    /**
     * Initializes session and starts it
     */
    protected function _initSession(){
        
        $config = Zend_Registry::get('config');
        $sessionSettings = $config->resources->session;
        Zend_Session::setOptions($sessionSettings->toArray());
        Zend_Session::start();
    }

    /**
     * Initializes the view helpers for the application
     * 
     * @access protected
     * @return void     
     */
    protected function _initViewHelpers(){

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
        'viewRenderer');
        if( NULL === $viewRenderer->view ){
            $viewRenderer->initView();
        }
        $viewRenderer->view->addHelperPath('App/View/Helper', 'App_View_Helper');
        $viewRenderer->view->addHelperPath('App/Frontend/View/Helper', 
        'App_Frontend_View_Helper');
    }

    /**
     * Initializes the action helpers for the application
     *
     * @return void
     */
    protected function _initActionHelpers(){

        // Add the possibility to redirect easily using current router
        Zend_Controller_Action_HelperBroker::addHelper(
        new App_Controller_Action_Helper_Redirector());
        // Add the possibility to log to Firebug. Example: $this->_helper->log('Message');
        Zend_Controller_Action_HelperBroker::addHelper(
        new App_Controller_Action_Helper_Logger());
        // Add the Flag and Flippers helper for the controllers. Example: $this->_helper->flagFlippers()
        Zend_Controller_Action_HelperBroker::addHelper(
        new App_Controller_Action_Helper_FlagFlippers());
    }

    /**
     * Setup the locale based on the browser
     *
     * @return void
     */
    protected function _initLocale(){

        $localeStr = Zend_Registry::get('config')->translation->default_locale;
        $locale = new Zend_Locale($localeStr);
        if( $locale instanceof Zend_Locale ){
            Zend_Registry::set('Zend_Locale', $locale);
        }
        $cacheHandler = Zend_Registry::get('Zend_Cache_Manager')->getCache('memcache');
        $locale->setCache($cacheHandler);
    }

    /**
     * Initialize the translation system
     *
     * @return void
     */
    protected function _initTranslator(){

        //Extract some info from the request
        $lang = Zend_Registry::get('Zend_Locale')->getLanguage();
        //Create a zend_log for missing translations
        $pathLog = ROOT_PATH . '/data/logs/' . CURRENT_MODULE .'/missing_translations/' . date('Ymd') . '_' . $lang . '.log';
        $writer = new Zend_Log_Writer_Stream($pathLog);
        $logger = new Zend_Log($writer);
        $translate = new Zend_Translate(
            array(
            	'adapter' => 'gettext', 
        		'content' => Zend_Registry::get('config')->translation->path, 
        		'locale' => $lang, 
        		'disableNotices' => Zend_Registry::get('config')->translation->disable_notices, 
        		'log' => $logger,
        		'logMessage' => "Missing translation: %message%", 
        		'logUntranslated' => Zend_Registry::get('config')->translation->log_missing_translations, 
        		'scan' => Zend_Translate::LOCALE_DIRECTORY
            )
        );
        $cacheHandler = Zend_Registry::get('Zend_Cache_Manager')->getCache('memcache');
        $translate->setCache($cacheHandler);
        
        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Registry::set('Translate_Logger', $logger);
    }

    /**
     * Inits the layouts (full configuration)
     * 
     * @access protected
     * @return void
     */
    protected function _initLayout(){

        Zend_Layout::startMvc(
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/views/layouts/');
        $view = Zend_Layout::getMvcInstance()->getView();
    }

    /**
     * Inits the view paths
     *
     * Additional paths are used in order to provide a better separation
     * 
     * @access protected
     * @return void     
     */
    protected function _initViewPaths(){

        $this->bootstrap('Layout');
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addScriptPath(
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/views/');
        $view->addScriptPath(
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/views/forms/');
        $view->addScriptPath(
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/views/paginators/');
        $view->addScriptPath(
        APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/views/scripts/');
    }

    /**
     * Initialize the ZFDebug Widget
     *
     * @return void
     */
    protected function _initZFDebug(){

        $this->bootstrap('Cache');
        $config = Zend_Registry::get('config');
        if( isset($config->zfdebug->enabled) && $config->zfdebug->enabled == TRUE ){
            $dbAdapter = Zend_Registry::get('dbAdapter');
            $options = array(
            	'plugins' => array(
                	'Variables', 'Html', 
            		'Database' => array(
            			'adapter' => array(
                			'standard' => $dbAdapter
                        )
                    ),
                    'File' => array(
                		'basePath' => ROOT_PATH
                    ),
                    'Memory',
                    'Time',
                    'Registry',
                    'Exception'
                )
            );
            if( $config->zfdebug->show_cache_panel ){
                $fileCache = Zend_Registry::get('Zend_Cache_Manager')->getCache('file');
                $memcacheCache = Zend_Registry::get('Zend_Cache_Manager')->getCache('memcache');
                $options['plugins']['Cache'] = array(
                	'backend' => array(
                        $fileCache->getBackend(), $memcacheCache->getBackend()
                    )
                );
            }
            $debug = new ZFDebug_Controller_Plugin_Debug($options);
            $frontController = Zend_Controller_Front::getInstance()->registerPlugin($debug);
        }
    }

    /**
     * Initialize and register the plugins
     * 
     * @access protected
     * @return void
     */
    protected function _initPlugins(){

        $frontController = Zend_Controller_Front::getInstance();
        // Application_Plugin_VersionHeader sends a X-SF header with the system version for debugging
        $frontController->registerPlugin(new App_Plugin_VersionHeader());
    }

    /**
     * Initialize the caching mechanism
     * 
     * @access protected
     * @return void
     */
    protected function _initCache(){

        $manager = new Zend_Cache_Manager();
        //Add the templates to the manager
        foreach( Zend_Registry::get('config')->cache->toArray() as $k => $v ){
            $v['frontend']['options']['logger'] = Zend_Registry::get('Zend_Log');
            $manager->setCacheTemplate($k, $v);
        }
        Zend_Registry::set('Zend_Cache_Manager', $manager);
    }

    /**
     * Initialize upload folder. Path should be relative and the base is APPLICATION_PATH
     * 
     * @access protected
     * @return void
     */
    protected function _initUpload(){
        define('UPLOAD_PATH','../data/uploads');
    }
    
    /**
     * Initialize the log system
     *
     * @access protected
     * @return void
     */
    protected function _initLog(){
        if( ! file_exists(realpath(ROOT_PATH . '/data/logs/' . CURRENT_MODULE . '/general.log')) ){
            $fh = fopen(ROOT_PATH . '/data/logs/' . CURRENT_MODULE . '/general.log', 'w');
            fclose($fh);
        }
        $path = realpath( ROOT_PATH . '/data/logs/' . CURRENT_MODULE . '/general.log');
        $writer = new Zend_Log_Writer_Stream($path);
        $logger = new Zend_Log($writer);
        if( ! Zend_Registry::get('IS_PRODUCTION') ){
            $firebug_writer = new Zend_Log_Writer_Firebug();
            $logger->addWriter($firebug_writer);
        }
        Zend_Registry::set('Zend_Log', $logger);
    }

    /**
     * Initialize the Flag and Flipper System
     *
     * @return void
     */
    protected function _initFlagFlippers(){

        $this->bootstrap('ModulePaths');
        App_FlagFlippers_Manager::load();
    }

    /**
     * Runs the application
     * 
     * @access public
     * @return void  
     */
    public function runApp(){

        $front = Zend_Controller_Front::getInstance();
        $front->dispatch();
    }
}
