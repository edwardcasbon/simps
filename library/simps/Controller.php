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
			$config = array_replace_recursive($config, $allConfigs[$serverName]);
		}
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
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
		$template = $this->route->controller . "/" . $this->route->action . ".phtml";
		echo $this->view->render($template);
	}
	
	/**
	 * Redirect to a new page.
	 *
	 * @var mixed $destination
	 */
	public static function redirect ($destination) {
		var_dump($destination);
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
	
}