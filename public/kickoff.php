<?php
/**
 * Simps MVC Framework (http://github.com/edwardcasbon/simps)
 *
 * @link 	http://github.com/edwardcasbon/simps
 * @author 	Edward Casbon <edward@edwardcasbon.co.uk>
 */

// Set the include path to include Simps library and app controllers and models.
$paths =	dirname(__DIR__) . "/app"			 	. PATH_SEPARATOR . 	
			dirname(__DIR__) . "/app/controllers" 	. PATH_SEPARATOR .
			dirname(__DIR__) . "/app/models" 		. PATH_SEPARATOR .
			dirname(__DIR__) . "/app/views" 		. PATH_SEPARATOR .
			dirname(__DIR__) . "/library" 			. PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $paths);

// Get route details from URL and farm off to controller.
$route = new Simps_Router($_SERVER);
$controller = $route->controller . "Controller";
$action = $route->action . "Action";
$params = $route->params;
if(!class_exists($controller)) { throw new Simps_Exception("Controller not found", 404, array("controller" => $controller)); }
$controller = new $controller($route);
if(!method_exists($controller, $action)) { throw new Simps_Exception("Action not found", 404, array("action" => $action)); }
$controller->$action();

// Autoload function for loading in application classes.
function __autoload($className) {
	if(substr($className, 0, 6) == "Simps_") {
		$simpsClass = substr($className, 6);
		include_once('simps/' . $simpsClass . ".php");
	} else {
		include_once($className . ".php");
	}
}