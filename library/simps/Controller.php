<?php
/**
 * Simps MVC Framework (http://github.com/edwardcasbon/simps)
 *
 * @link 	http://github.com/edwardcasbon/simps
 * @author 	Edward Casbon <edward@edwardcasbon.co.uk>
 */

/**
 * Base controller that every application controller extends.
 *
 * Provides access to the view, route, configuration details
 * and database connection.
 */
class Simps_Controller {
	
	/**
	 * The view object.
	 * 
	 * @var Simps_View
	 */
	public $view;
	
	/**
	 * The requested route.
	 *
	 * @var array
	 */
	public $route;
	
	/**
	 * The application configuration details.
	 *
	 * @var array
	 */
	public $config;
	
	/**
	 * The database connection.
	 *
	 * @var PDO
	 */
	public $db;
	
	/**
	 * Constructor.
	 *
	 * Store the route and config details and
	 * set up the database connection and view
	 * object.
	 * 
	 * @param array $route
	 */
	public function __construct ($route) {
 		$this->route = $route;
		$this->config = $this->config();
		$this->db = $this->db();
		$this->view = new Simps_View($this->config['view']);
	}
	
	/**
	 * Get and parse the application configuration details.
	 * 
	 * @return array
	 */
	protected function config () {
		$allConfigs = parse_ini_file("config/app.config", true);
		$config = $allConfigs['default'];
		$serverName = $this->route->request['SERVER_NAME'];
		if(!empty($allConfigs[$serverName])) {
			if(function_exists('array_replace_recursive')) {
				$config = array_replace_recursive($config, $allConfigs[$serverName]);
			} else {
				$config = $this->array_replace_recursive($config, $allConfigs[$serverName]);
			}
		}
		$config = $this->parseFlatConfig($config);
		return $config;
	}
	
	/**
	 * Create a new database connection.
	 *
	 * @return PDO
	 */
	protected function db () {
		if(empty($this->config['db'])) return false;
		$config = $this->config['db'];
		$dsn 		= "mysql:dbname={$config['database']};host={$config['host']}";
		$user 		= $config['user'];
		$password	= $config['password'];
		try {
			$db = new PDO($dsn, $user, $password);
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			return $db;
		} catch (PDOException $exception) {
			exit('Database connection failed: ' . $exception->getMessage() . "\n");
		}
	}
	
	/**
	 * Destructor.
	 * 
	 * Close the database connection and render
	 * the view script.
	 *
	 * @return void
	 */
	public function __destruct () {
		unset($this->db);
		$regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
		$controller = explode(" ", preg_replace($regex, ' $1', $this->route->controller));
		if(strtolower($controller[0]) == $this->route->module) {
			unset($controller[0]);
		}
		$controller = strtolower(implode("-", $controller));
		
		$template = $this->route->module . "/views/scripts/" . $controller . "/" . $this->route->action . ".phtml";
		echo $this->view->render($this->route->module, $template);
	}
	
	/**
	 * Redirect to a new page.
	 *
	 * @var mixed $destination
	 */
	public static function redirect ($destination) {
		if(gettype($destination) == "array") {
			$params = "";
			if(!empty($destination['params'])) {
				$destParams = $destination['params'];
				unset($destination['params']);
				foreach($destParams as $key => $value) {
					$params .= $key . "/" . $value . "/";
				}
			}
			$destination = "/" . implode("/", $destination) . "/" . $params;
		}
		header("Location: " . $destination);
	}
	
	/**
	 * Recursive array replacement for PHP <5.3
	 *
	 * @var array $array
	 * @var array $array1
	 * @return array
	 */
	public function array_replace_recursive ($array, $array1) {
		$args = func_get_args();
		$array = $args[0];
		if (!is_array($array)) {
			return $array;
		}
		for ($i = 1; $i < count($args); $i++) {
			if (is_array($args[$i])) {
				$array = $this->recurseArray($array, $args[$i]);
			}
		}
		return $array;
	}
	
	/**
	 * Recurse an array
	 *
	 * @var array $array
	 * @var array $array1
	 * @return array
	 */
	public function recurseArray ($array, $array1) {
		foreach ($array1 as $key => $value) {
			if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
				$array[$key] = array();
			}
			if (is_array($value)) {
				$value = $this->recurseArray($array[$key], $value);
			}
			$array[$key] = $value;
		}
		return $array;
	}
	
	/**
	 * Parse a flat config array.
	 *
	 * Strip out keys with '.' and create multidimensional
	 * array.
	 *
	 * Needed for PHP 5.2 compatibility.
	 *
	 * @var array $config
	 * @return array
	 */
	public function parseFlatConfig ($config) {
		foreach($config as $key => $value) {
			if(strpos($key, ".")) {
				$label = explode(".", $key);
				$pConfig[$label[0]][$label[1]] = $value;
			} else {
				$pConfig[$key] = $value;
			}
		}
		return $pConfig;
	}
}
