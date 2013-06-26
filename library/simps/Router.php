<?php
/**
 * Simps MVC Framework (http://github.com/edwardcasbon/simps)
 *
 * @link 	http://github.com/edwardcasbon/simps
 * @author 	Edward Casbon <edward@edwardcasbon.co.uk>
 */

/**
 * Class that works out the application route from the URL/request.
 *
 * The request must be passed in on instantiation.
 */
class Simps_Router {
	
	/**
	 * The browser request.
	 *
	 * @var array
	 */
	public $request;
	
	/**
	 * The registered modules.
	 *
	 * @var array
	 */
	public $modules;
	
	/**
	 * The requested module.
	 *
	 * Defaults to "default" so that if no module
	 * is recognised then the default module will
	 * be chosen.
	 *
	 * @var string
	 */
	public $module = "default";
	
	/**
	 * The requested controller.
	 * 
	 * Defaults to "index" so that if no controller
	 * is recognised then the index controller will
	 * be chosen.
	 *
	 * @var string
	 */
	public $controller = "Index";
	
	/**
	 * The requested action.
	 * 
	 * Defaults to "index" so that if no action is
	 * recognised then the index action will be
	 * chosen.
	 *
	 * @var string
	 */
	public $action = "index";
	
	/**
	 * The requested parameters.
	 * 
	 * These can either be passed in the request
	 * URI in key/value pairs or via the $_REQUEST
	 * superglobal.
	 * 
	 * @var array
	 */	
	public $params = array();
	
	/**
	 * Is the module in the URL.
	 * 
	 * @var boolean
	 */
	public $moduleInUrl = false;
	
	/**
	 * Constructor.
	 *
	 * Store the request and determine the 
	 * requested controller, action and
	 * parameters.
	 *
	 * The constructor requires the request
	 * to be passed in on instantiation.
	 *
	 * @param array
	 */
	public function __construct($request, $registeredModules) {
		$this->request = $request;
		$this->modules = $registeredModules;
		$this->setModule();
		$this->setController();
		$this->setAction();
		$this->setParams();
	}
	
	/**
	 * Determine and set the requested module 
	 * from the request.
	 *
	 * @return void
	 */
	protected function setModule () {
		$exploded = explode("/", trim($this->request['REQUEST_URI'], "/"));
		if(in_array($exploded[0], $this->modules)) {
			$this->module = $exploded[0];
			$this->moduleInUrl = true;
		}
	}
	
	/**
	 * Determine and set the requested controller
	 * from the request.
	 *
	 * @return void
	 */
	protected function setController () {
		$exploded = explode("/", trim($this->request['REQUEST_URI'], "/"));
		$controller = ($this->moduleInUrl) ? $exploded[1] : $exploded[0];
		if(!empty($controller)) {
			$this->controller = str_replace(" ", "", ucwords(str_replace("-", " ", $controller)));
		}
		if($this->moduleInUrl && $this->module != "default") $this->controller = ucwords($this->module) . $this->controller;
	}
	
	/**
	 * Determine and set the requested action
	 * from the request.
	 *
	 * @return void
	 */
	protected function setAction () {
		$exploded = explode("/", trim($this->request['REQUEST_URI'], "/"));
		$action = ($this->moduleInUrl) ? $exploded[2] : $exploded[1];
		if(!empty($action)) {
			$action = ucwords(str_replace("-", " ", $action));
			$action[0] = strtolower($action[0]);
			$this->action = str_replace(" ", "", $action);
		}
	}
	
	/**
	 * Determine and set the requested parameters
	 * from the request.
	 * 
	 * @return void
	 */
	protected function setParams () {
		$params = array();
		$firstParams = explode("?", $_SERVER['REQUEST_URI']);
		$firstParams = $firstParams[0];
		$firstParams = explode("/", trim($firstParams, "/"));
		unset($firstParams[0], $firstParams[1]);
		if($this->moduleInUrl) unset($firstParams[2]);
		if(count($firstParams)>0) {
			$key = true;
			$keys = array();
			$values = array();
			foreach($firstParams as $param) {
				if($key) {
					$keys[] = $param;
				} else {
					$values[] = $param;
				}
				$key = !$key;
			}	
			for($i=0; $i<count($keys); $i++) {
				if(!isset($values[$i])) {
					$values[$i] = null;
				}			
				$params[$keys[$i]] = $values[$i];
			}
		}
		$params = array_merge($params, $_REQUEST);
		$this->params = $params;
	}
}
