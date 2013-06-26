<?php
/**
 * Simps MVC Framework (http://github.com/edwardcasbon/simps)
 *
 * @link 	http://github.com/edwardcasbon/simps
 * @author 	Edward Casbon <edward@edwardcasbon.co.uk>
 */

// Set the modules here.
$modules = array('default', 'admin');
$GLOBALS['modules'] = $modules;

// Set the include path to include Simps library and app controllers and models.
$paths =	dirname(dirname(__FILE__)) . "/app" . PATH_SEPARATOR . 	
			dirname(dirname(__FILE__)) . "/library" . PATH_SEPARATOR . 
			dirname(dirname(__FILE__)) . "/app/modules" . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $paths);

// Get route details from URL and farm off to controller.
$route = new Simps_Router($_SERVER, $modules);
$controller = $route->controller . "Controller";
$action = $route->action . "Action";
$params = $route->params;
if(!class_exists($controller)) { throw new Simps_Exception('"' . $controller . '" Controller not found', 404, array("controller" => $controller)); }
$controller = new $controller($route);
if(!method_exists($controller, $action)) { throw new Simps_Exception("Action not found", 404, array("action" => $action)); }
$controller->$action();

// Autoload function for loading in application classes.
function __autoload($className) {
	if(substr($className, 0, 6) == "Simps_") {
		$simpsClass = substr($className, 6);
		include_once('simps/' . $simpsClass . ".php");
	} else {
		$regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
		$className = explode(" ", preg_replace($regex, ' $1', $className));
		$modules = $GLOBALS['modules'];
		if(in_array(strtolower($className[0]), $modules)) {
			$module = strtolower($className[0]);
			unset($className[0]);
		} else {
			$module = "default";
		}
		$classNameStr = implode("", $className);
		$classNameReversed = array_reverse($className);
		if ($classNameReversed[0] == "Controller") {
			include_once($module . "/controllers/" . $classNameStr . ".php");
		} else {
			include_once($module . "/models/" . $classNameStr . ".php");
		}
	}
}