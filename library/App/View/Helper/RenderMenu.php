<?php

/**
 * Renders the main menu for the site. 
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_RenderMenu extends Zend_View_Helper_Abstract {

    /**
     * Template for the tabs at the top
     * 
     * @var string
     * @access protected
     */
    protected $_tabLinkTemplate = '<a href="javascript:return false;" title="%1$s">%1$s</a>';

    /**
     * Template for the links
     * 
     * @var string
     * @access protected
     */
    protected $_linkTemplate = '<a href="%1$s" title="%2$s">%2$s</a>';

    /**
     * Template for the currently selected link
     * 
     * @var string
     * @access protected
     */
    protected $_currentLinkTemplate = '<a class="current" href="%1$s" title="%2$s">%2$s</a>';
    
    /**
     * Router object and parameters
     * 
     * @var unknown_type
     */
    protected $_linkRouter;
    
    /**
     * Class name for menu styling
     * 
     * @var string
     */
    protected $_menuClass = 'sf-menu';
    
    /**
     * menu ul's ID attribute
     * 
     * @var string
     */
    protected $_menuId = 'nav';
    
    /**
     * parameters to pass to App_Navigation class
     * 
     * @var unknown_type
     */
    protected $_navigationParams  = null;
    
    /**
     * Set options using config array
     * 
     * @param array $config
     * @throws Zend_Exception
     */
    protected function _setOptions($config = array()){
        if(!is_array($config)){
            throw new Zend_Exception('Config should be an array, '.gettype($config).' given.');
        }
        
        if(!isset($config['router'])) $config['router'] = null;
        $this->_initRouterParams($config['router']);
        unset($config['router']);
        
        foreach($config as $key=>$value){
            $strParam = '_'.$key;
            $this->{$strParam} = $value;
        }
    }
    
    /**
     * Initialize $_linkRouter
     * 
     * @param array $params 
     * @access private
     */
    private function _initRouterParams($params){

        $currentRouteName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        if( is_string($params) ){
            $this->_linkRouter['name'] = $params;
            $this->_linkRouter['defaultParams'] = array();
        }elseif( is_array($params) ){
            if( ! isset($params['name']) ){
                $params['name'] = $currentRouteName;
            }
            if( ! isset($params['defaultParams']) ){
                $params['defaultParams'] = array();
            }
            $this->_linkRouter = $params;
        }else{
            $this->_linkRouter['name'] = $currentRouteName;
            $this->_linkRouter['defaultParams'] = array();
        }
    }

    /**
     * Assembles url according to router provided
     * 
     * @param array $params 
     * @access public
     * @return string
     */
    public function _assembleUrl(array $params){

        $params = array_merge($params, $this->_linkRouter['defaultParams']);
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $url = $router->assemble($params, $this->_linkRouter['name'], true);
        //@TODO check if there is any problem concatanating _baseUrl and $url
        // using apache alias, concatanation makes duplicates. but using virtualhosts with subdomains, no problem.
        //$url = $baseUrl . $url;
        return $url;
    }

    /**
     * Convenience method
     * call $this->renderMenu() in the view to access 
     * the helper
     *
     * @access public
     * @return string
     */
    public function renderMenu($config = array()){
        $this->_setOptions($config);
        
        $translator = Zend_Registry::get('Zend_Translate');
        $trans = $translator->getAdapter();
        
        $navigation = App_Navigation::getInstance()->getNavigation($this->_navigationParams);
        
        $menu = array();
        foreach( $navigation as $tab ){
            if( isset($tab['controller']) ){
                $urlParams = array(
                    'controller' => $tab['controller']
                );
                if( isset($tab['action']) ){
                    $urlParams['action'] = $tab['action'];
                }
                $url = $this->_assembleUrl($urlParams);
                if( isset($tab['active']) && $tab['active'] ){
                    $tabLink = sprintf($this->_currentLinkTemplate, $url, 
                    $trans->translate($tab['label']));
                }else{
                    $tabLink = sprintf($this->_linkTemplate, $url, 
                    $trans->translate($tab['label']));
                }
            }else{
                $tabLink = sprintf($this->_tabLinkTemplate, $trans->translate($tab['label']));
            }
            $links = array();
            if( isset($tab['pages']) ){
                foreach( $tab['pages'] as $page ){
                    $urlParams = array(
                        'controller' => $page['controller']
                    );
                    if( isset($page['action']) ){
                        $urlParams['action'] = $page['action'];
                    }
                    $url = $this->_assembleUrl($urlParams);
                    if( isset($page['active']) && $page['active'] ){
                        $links[] = sprintf($this->_currentLinkTemplate, $url, 
                        $trans->translate($page['label']));
                    }else{
                        $links[] = sprintf($this->_linkTemplate, $url, 
                        $trans->translate($page['label']));
                    }
                }
            }
            if( isset($tab['active']) && $tab['active'] ){
                $li = '<li class="current">' . PHP_EOL . $tabLink . PHP_EOL;
                $li .= ! empty($links) ? '<ul>' . PHP_EOL . '<li>' .
                 implode('</li>' . PHP_EOL . '<li>', $links) . '</li>' . PHP_EOL .
                 '</ul>' : '';
                $li .= '</li>';
            }else{
                $li = '<li>' . PHP_EOL . $tabLink . PHP_EOL;
                $li .= ! empty($links) ? '<ul>' . PHP_EOL . '<li>' .
                 implode('</li>' . PHP_EOL . '<li>', $links) . '</li>' . PHP_EOL .
                 '</ul>' : '';
                $li .= '</li>';
            }
            $menu[] = $li;
        }
        
        $xhtml = '<ul id="'.$this->_menuId.'" class="'.$this->_menuClass.'">' . PHP_EOL .
        implode(PHP_EOL, $menu) . PHP_EOL . '</ul>';
        return $xhtml;
    }
}
