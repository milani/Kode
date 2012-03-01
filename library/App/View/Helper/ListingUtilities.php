<?php

/**
 * Helper used in order to automate creating admin views
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_View_Helper_ListingUtilities extends Zend_View_Helper_Abstract {

    /**
     * Human readable names for the main table's 
     * columns
     * 
     * @var array
     * @access protected
     */
    protected $_columnNames = array();

    /**
     * Database indexes for the main table's columns.
     * They must be in the same order as the columnNames
     * 
     * @var array
     * @access protected
     */
    protected $_columnIndexes = array();

    /**
     * Database datat types for the main table's columns.
     * They must be in the same order as the columnNames
     * 
     * @var array
     * @access protected
     */
    protected $_columnDataTypes = array();

    /**
     * Page title
     * 
     * @var string
     * @access protected
     */
    protected $_pageTitle = '';

    /**
     * Holds an array of default placeholders for 
     * null values
     * 
     * @see displayItem()
     * @var array
     * @access protected
     */
    protected $_nullPlaceholders = array();

    /**
     * Default placeholder if none is provided in the
     * nullPlaceholders array for the current column
     *
     *
     * @see displayItem()
     * @var string
     * @access protected
     */
    protected $_defaultNullPlaceholder = '-';

    /**
     * Add more buttons on the toolbar just like "Add" button.
     * It acts like additionalActions, but the render place is different
     *
     * @see renderToolbarActions()
     * @var array
     * @access protected
     */
    protected $_additionalToolbarActions = array();

    /**
     * Label for the "Create new item" button
     * 
     * @var string
     * @access protected
     */
    protected $_addMessage = '';

    /**
     * Control if we show the add button
     * 
     * @var boolean
     * @access protected
     */
    protected $_showAddButton = TRUE;

    /**
     * Confirmation label to be displayed on the
     * page
     * 
     * @var string
     * @access protected
     */
    protected $_areYouSureMessage = '';

    /**
     * Message that will be displayed when there are
     * no items in the main table
     * 
     * @var string
     * @access protected
     */
    protected $_emptyMessage = '';

    /**
     * Holds the current controller's name
     * 
     * @var mixed
     * @access protected
     */
    protected $_controllerName;

    /**
     * Holds the base url for generating 
     * links
     * 
     * @var mixed
     * @access protected
     */
    protected $_baseUrl;

    /**
     * Default actions
     * 
     * @var array
     * @access protected
     */
    protected $_actions = array(
        
        'edit' => array(
            'action' => 'edit',
            'title' => 'Edit',
            'link_class' => 'ico', 
        	'img_alt' => 'edit',
        	'img_url' => '/images/led-ico/pencil.png', 
        	'parameter' => TRUE
        ), 
        'delete' => array(
            'action' => 'delete',
            'title' => 'Delete',
            'link_class' => 'ico', 
        	'img_alt' => 'delete',
        	'img_url' => '/images/led-ico/cross.png', 
        	'parameter' => TRUE
        )
    );

    /**
     * Indicates the order in which the actions
     * have to be displayed in the table
     * 
     * @var array
     * @access protected
     */
    protected $_linkOrder = array();

    /**
     * Indicates the router to use for assembling links,and default values for this router.
     * 
     * @var array
     * @access protected
     */
    protected $_linkRouter = array();

    /**
     * Holds the name of the view link column.
     * The item on this column will automatically be linked to 
     * the view action
     * 
     * @var mixed
     * @access protected
     */
    protected $_viewLinkColumn = '';

    /**
     * Hook that allows inserting a partial view to be
     * executed before the main content
     * 
     * @var string
     * @access protected
     */
    protected $_beforeContentHook = '';

    /**
     * Array of parameters to be pushed to the beforeContentHook
     * 
     * @var array
     * @access protected
     */
    protected $_beforeContentHookParams = array();

    /**
     * Hook that allows inserting a partial view to be
     * executed after the main content
     * 
     * @var string
     * @access protected
     */
    protected $_afterContentHook = '';

    /**
     * Array of parameters to be pushed to the afterContentHook
     * 
     * @var array
     * @access protected
     */
    protected $_afterContentHookParams = array();

    /**
     * Flag to indicate whether translation should be done or not
     * 
     * @var bool
     * @access protected
     */
    protected $_enableTranslation;

    /**
     * Inits the helper with data from the config array
     * 
     * @param array $config 
     * @access protected
     * @return string
     */
    protected function _init(array $config){

        $this->_enableTranslation = true;
        $this->_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_controllerName = Zend_Registry::get('controllerName');
        if( isset($config['additionalActions']) && ! empty($config['additionalActions']) ){
            $this->_actions += $config['additionalActions'];
            unset($config['additionalActions']);
        }
        foreach( $config as $key => $value ){
            if( isset($this->{'_' . $key}) ){
                $this->{'_' . $key} = $value;
            }
        }
        $tmp = $this->_linkRouter;
        if( is_string($tmp) ){
            $this->_linkRouter['name'] = $tmp;
            $this->_linkRouter['defaultParams'] = array();
        }elseif( is_array($tmp) ){
            if( isset($tmp['name']) )
                $this->_linkRouter['name'] = $tmp['name'];
            else
                $this->_linkRouter['name'] = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
            if( ! isset($tmp['defaultParams']) || ! is_array($tmp['defaultParams']) )
                $this->_linkRouter['defaultParams'] = array();
            else
                $this->_linkRouter['defaultParams'] = $tmp['defaultParams'];
        }else{
            $this->_linkRouter['name'] = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
            $this->_linkRouter['defaultParams'] = array();
        }
    }

    /**
     * Getter for the columnNames
     * 
     * @access public
     * @return array
     */
    public function getColumnNames(){
        $columns = array();
        $translator = Zend_Registry::get('Zend_Translate');
        foreach($this->_columnNames as $column){
            $columns[] = ($this->_enableTranslation) ? $translator->translate($column) : $column;
        }
        return $columns;
    }

    /**
     * Getter for the columnIndexes
     * 
     * @access public
     * @return array
     */
    public function getColumnIndexes(){

        return $this->_columnIndexes;
    }

    /**
     * Function to render additional actions
     * in the title bar next to "Add" button
     *
     * @access public
     * @return formatted actions in the form of <a></a> elements
     */
    public function renderToolbarActions(){

        $result = '';
        foreach( $this->_additionalToolbarActions as $action ){
            if( $this->can($action['action'], $action['controller']) ){
                $urlParams = array(
                    'action' => $action['action'], 
                	'controller' => $action['controller']
                );
                
                if(isset($action['parameter']) && $action['parameter']) {
                    $urlParams[$action['parameter_name']] = $action['parameter_value'];
                }
                
                $translator = Zend_Registry::get('Zend_Translate');
                $action['title'] = ($this->_enableTranslation)? $translator->translate($action['title']) : $action['title'];
                $result .= '<a class="button altbutton" href="' . $this->_assembleUrl($urlParams) . '">' . $action['title'] . '</a>';
            }
        }
        return $result;
    }

    /**
     * Getter for the showAddButton
     *
     * @access public
     * @return boolean
     */
    public function canShowAddButton(){

        return $this->_showAddButton;
    }

    /**
     * Returns true if the current user can add 
     * a new item
     * 
     * @access public
     * @return string
     */
    public function canAdd(){

        return $this->can('add');
    }

    /**
     * Queries the Flag and Flippers and returns true if the current
     * user is allowed to access the requested page.
     * 
     * @param string $action 
     * @param string $controller 
     * @access public
     * @return string
     */
    public function can($action, $controller = NULL){
        $user = Zend_Auth::getInstance()->getIdentity();
        if( NULL === $controller ){
            $controller = $this->_controllerName;
        }
        return App_FlagFlippers_Manager::isAllowed($user->username, $controller, 
        $action);
    }

    /**
     * Displays the add url
     * 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function addUrl($echo = TRUE){

        $params = array(
            'controller' => $this->_controllerName,
            'action' => 'add'
        );
        $url = $this->_assembleUrl($params);
        return $this->_return($url, $echo);
    }

    /**
     * Displays the indicated action link
     * 
     * @param string $link 
     * @param array $item
     * @param bool $echo
     * @access public
     * @return string
     */
    public function link($link, array $item = array(), $echo = TRUE){

        if( ! isset($this->_actions[$link]) ){
            throw new Zend_Exception('Action ' . $link . ' is not registered');
        }

        $action = $this->_actions[$link];
        
        if( ! isset($action['controller'])) $action['controller'] = $this->_controllerName;
        
        $urlParams = array(
            'controller' => $action['controller'], 
        	'action' => $action['action']
        );
        if( isset($action['parameter']) ){
            
            $paramIndex = isset($action['parameter_index'])?$action['parameter_index']:'id';
            
            if( ! isset($item[$paramIndex]) ){
                throw new Zend_Exception('This action ("'.$link.'") requires a parameter. Please pass the correct array to this method.');
            }
            if( ! isset($action['parameter_name'])) $action['parameter_name'] = 'id';
            $urlParams[$action['parameter_name']] = $item[$paramIndex];
        }
        if( isset($action['other_parameters']) && is_array($action['other_parameters'])){
            foreach($action['other_parameters'] as $parameterName=>$parameterIndex){
                if(!isset($item[$parameterIndex])){
                    throw new Zend_Exception('Action "'.$link.'" requires parameter index "'.$parameterIndex.'"');
                }
                $urlParams[$parameterName] = $item[$parameterIndex];
            }
        }
        $url = $this->_assembleUrl($urlParams);
        $translator = Zend_Registry::get('Zend_Translate');
        $action['title'] = ($this->_enableTranslation)? $translator->translate($action['title']) : $action['title'];        
        if( ! isset($action['link_class']) )
            $action['link_class'] = "";
        if( ! isset($action['icon']) || (isset($action['icon']) && $action['icon']) ){
            $action['link_class'] = 'ico';
            $link = '<li>' . PHP_EOL . '<a class="' . $action['link_class'] . '"href="' . $url . '" title="' . $action['title'] . '">' . PHP_EOL .
             '<img src="' . $this->_baseUrl . $action['img_url'] . '" alt="' . $action['img_alt'] . '" />' . PHP_EOL . '</a>' . PHP_EOL . '</li>' .
             PHP_EOL;
        }else{
            $link = '<li>' . PHP_EOL . '<a class="' . $action['link_class'] . '"href="' . $url . '" title="' . $action['title'] . '">' . PHP_EOL .
             $action['title'] . PHP_EOL . '</a>' . PHP_EOL . '</li>' . PHP_EOL;
        }
        return $this->_return($link, $echo);
    }

    /**
     * Displays the action links
     * 
     * @param array $item 
     * @access public
     * @return string
     */
    public function links($item, $echo = TRUE){

        if( empty($this->_linkOrder) ){
            $order = array_keys($this->_actions);
        }else{
            $order = $this->_linkOrder;
        }
        $links = array();
        foreach( $order as $link ){
            $action = $this->_actions[$link];
            if(!isset($action['controller'])) $action['controller'] = NULL;
            if( $this->can($action['action'],$action['controller'])){
                $links[] = $this->link($link, $item, FALSE);
            }
        }
        if( ! empty($links) ){
            $result = '<ul class="actions">' . PHP_EOL . implode(PHP_EOL, $links) . '</ul>';
        }else{
            $result = '';
        }
        
        return $this->_return($result, $echo);
    }

    /**
     * Displays the "Create new {item name}" message
     * 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function addMessage($echo = TRUE){

        if( empty($this->_addMessage) ){
            $controllerName = $this->_controllerName;
            if( $controllerName{strlen($controllerName) - 1} == 's' ){
                $controllerName = substr($controllerName, 0, - 1);
            }
            $this->_addMessage = 'Create new ' .
             str_replace('-', ' ', $controllerName);
        }
        return $this->_return($this->_addMessage, $echo);
    }

    /**
     * Displays the "Are you sure you want to delete this {item}?"
     * message
     * 
     * @param mixed $echo 
     * @access public
     * @return void
     */
    public function areYouSureMessage($echo = TRUE){

        if( empty($this->_areYouSureMessage) ){
            $controllerName = $this->_controllerName;
            if( $controllerName{strlen($controllerName) - 1} == 's' ){
                $controllerName = substr($controllerName, 0, - 1);
            }
            $this->_areYouSureMessage = 'Are you sure you want to delete this ' .
             str_replace('-', ' ', $controllerName) . '?';
        }
        return $this->_return($this->_areYouSureMessage, $echo);
    }

    /**
     * Returns the page title. It can be configured through the config
     * array and it defaults to the controller's name
     * 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function pageTitle($echo = TRUE){
        if( empty($this->_pageTitle) ){
            $action = Zend_Registry::get('actionName');
            if( $action == 'index' || $action == 'list' ){
                $this->_pageTitle = 'Manage ' .
                 ucwords(str_replace('-', ' ', $this->_controllerName));
            }else{
                $this->_pageTitle = ucwords(str_replace('-', ' ', $action));
                ;
            }
        }
        $this->_return($this->_pageTitle, $echo);
    }

    /**
     * Displays an item. If the item is not "displayable" (null value, etc.)
     * it will display a placeholder instead. 
     *
     * Custom placeholders can be defined via the nullPlaceholders array for each
     * column and a generic placeholder via the defaultNullPlaceholder variable. 
     *
     * Ex:
     *
     * $config['nullPlaceholders'] = array('name' => 'John Doe',
     * 'email' => 'unknown'
     * $config['defaultNullPlaceholder'] = '-';
     *
     * In this case, if a row doesn't have non-null values on the "name" or "email" columns,
     * the helper will display the strings "John Doe", respectively 'unknown', whereas for 
     * all other columns with null values it will display a dash (-)
     *
     * @param string $item 
     * @param string $index 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function displayItem($item, $index = NULL, $echo = TRUE){

        $viewLink = FALSE;
        if( NULL !== $index ){
            if( isset($item[$index]) && ! empty($item[$index]) ){
                if( $index === $this->_viewLinkColumn ){
                    $viewLink = TRUE;
                    $itemId = $item['id'];
                }
                $item = $item[$index];
            }else{
                if( isset($this->_nullPlaceholders[$index]) ){
                    $result = $this->_nullPlaceholders[$index];
                }else{
                    $result = $this->_defaultNullPlaceholder;
                }
                return $this->_return($result, $echo);
            }
        }else{
            if( empty($item) ){
                return $this->_return($this->_defaultNullPlaceholder, $echo);
            }
        }
        if( is_array($item) ){
            if( count($item) == 1 ){
                $result = array_pop($item);
            }else{
                $result = '<ul><li>' . implode('</li><li>', $item) . '</li></ul>';
            }
        }else{
//            $time = @strtotime($item);
            
            if( strlen($item) > 20 && App_Date::isDate($item,'Y-m-d H:i:s')){
                $result = $this->view->formatDate($item);
            }else 
                if( isset($this->_columnDataTypes[$index]) && $this->_columnDataTypes[$index] == 'boolean' ){
                    if( (bool) $item ){
                        $result = '<img src="'.$this->_baseUrl.'/images/led-ico/accept.png" />';
                    }else{
                        //$result = $item;
                        $result = '<img src="'.$this->_baseUrl.'/images/led-ico/cross.png" />';
                    }
                }else{
                    $result = $item;
                }
        }
        if( $viewLink && $this->can('view') ){
            $urlParams = array(
                'controller' => $this->_controllerName,
            	'action' => 'view', 
            	'id' => $itemId
            );
            $result = sprintf('<a href="%1$s" title="%2$s">%3$s</a>', $this->_assembleUrl($urlParams), sprintf('View details for %s', $result), $result);
        }
        return $this->_return($result, $echo);
    }

    /**
     * Returns the column count for the current listing. It includes the first
     * and the last columns.
     * 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function columnCount($echo = TRUE){

        return $this->_return(count($this->_columnNames) + 1, $echo);
    }

    /**
     * If in a listing we don't have any items yet, this method will be
     * called and it will display a 
     * 
     * @param bool $echo 
     * @access public
     * @return string
     */
    public function emptyMessage($echo = TRUE){

        if( ! empty($this->_emptyMessage) ){
            $message = $this->_emptyMessage;
        }else{
            $message = 'No items registered';
        }
        return $this->_return($message, $echo);
    }

    /**
     * Returns a columnName => columnValue for the specified 
     * item
     * 
     * @param array $item
     * @access public
     * @return array
     */
    public function getColumnNamesAndValues(array $item){

        $array = array();
        reset($this->_columnNames);
        foreach( $this->_columnIndexes as $columnIndex ){
            list (, $columnName) = each($this->_columnNames);
            $array[$columnName] = $item[$columnIndex];
        }
        return $array;
    }
    
    /**
     * Renders a template before the main content
     * 
     * @param bool $echo
     * @access public
     * @return string 
     */
    public function beforeContentHook($echo = TRUE){

        if( empty($this->_beforeContentHook) ){
            return;
        }
        $content = $this->view->partial($this->_beforeContentHook, 
        array(
            'params' => $this->_beforeContentHookParams
        ));
        return $this->_return($content, $echo);
    }

    /**
     * Renders a template after the main content
     * 
     * @param bool $echo
     * @access public
     * @return string
     */
    public function afterContentHook($echo = TRUE){

        if( empty($this->_afterContentHook) ){
            return;
        }
        $content = $this->view->partial($this->_afterContentHook, 
        array(
            'params' => $this->_afterContentHookParams
        ));
        
        return $this->_return($content, $echo);
    }

    /**
     * Implements the "fluent" interface. In the view, it it's called 
     * directly, it will return the current object, so all the other methods
     * can be called without the need of explicitly instantiating the helper
     *
     * Ex:
     * $this->listingUtilities($config)->can('add');
     * or (preferred):
     * $helper = $this->listingUtilities($config);
     * $helper->can('add')
     *
     * @param array $config
     * @access public
     * @return App_View_Helper_ListingUtilities
     */
    public function listingUtilities(array $config){

        $this->_init($config);
        return $this;
    }
    
    /**
     * Mechanism that enables the programmer to force the helper
     * to return the results rather than printing them
     * 
     * @param string $string 
     * @param bool $echo 
     * @access protected
     * @return string
     */
    protected function _return($string, $echo){

        $string = ($this->_enableTranslation && strlen($string) < 100) ? Zend_Registry::get('Zend_Translate')->translate($string) : $string;
        
        if( $echo ){
            echo $string;
        }else{
            return $string;
        }
    }

    /**
     * Assembles url according to router provided
     * 
     * @param array $params 
     * @access protected
     * @return string
     */
    protected function _assembleUrl(array $params){

        $params = array_merge($params, $this->_linkRouter['defaultParams']);
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $url = $router->assemble($params, $this->_linkRouter['name'], true);
        //@TODO check if there is any problem concatanating _baseUrl and $url
        //$url = $this->_baseUrl . $url;
        return $url;
    }
}
