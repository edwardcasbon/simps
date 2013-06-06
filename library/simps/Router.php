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
	 * var array
	 */
	public $request;
	
	/**
	 * The requested controller.
	 * 
	 * Defaults to "index" so that if no controller
	 * is recognised then the index controller will
	 * be chosen.
	 *
	 * @var string
	 */
	public $controller = "index";
	
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
	public function __construct($request) {
		$this->request = $request;
		$this->setController();
		$this->setAction();
		$this->setParams();
	}
	
	/**
	 * Determine and set the requested controller
	 * from the request.
	 *
	 * @return void
	 */
	protected function setController () {
		$exploded = explode("/", trim($this->request['REQUEST_URI'], "/"));
		if(!empty($exploded[0])) {
			$this->controller = str_replace(" ", "", lcfirst(ucwords(str_replace("-", " ", $exploded[0]))));
		}
	}
	
	/**
	 * Determine and set the requested action
	 * from the request.
	 *
	 * @return void
	 */
	protected function setAction () {
		$exploded = explode("/", trim($this->request['REQUEST_URI'], "/"));
		if(!empty($exploded[1])) {
			$this->action = str_replace(" ", "", lcfirst(ucwords(str_replace("-", " ", $exploded[1]))));
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
		$firstParams = explode("?", $_SERVER['REQUEST_URI'])[0];
		$firstParams = explode("/", trim($firstParams, "/"));
		unset($firstParams[0], $firstParams[1]);
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