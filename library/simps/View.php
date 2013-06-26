<?php
/**
 * Simps MVC Framework (http://github.com/edwardcasbon/simps)
 *
 * @link 	http://github.com/edwardcasbon/simps
 * @author 	Edward Casbon <edward@edwardcasbon.co.uk>
 */

/**
 * The view class.
 */
class Simps_View {
	
	/**
	 * The view config.
	 *
	 * @var array
	 */
	protected $config;
	
	/**
	 * The view layout
	 *
	 * @var string
	 */
	public $layout = "";
	
	/**
	 * Display the layout.
	 */
	protected $includeLayout = true;
	
	/**
	 * Constructor.
	 *
	 * Store the config details.
	 */
	public function __construct ($config) {
		$this->config = $config;
	}
	
	/**
	 * Render a view script/template.
	 *
	 * Pass in a template (in the form '/directory/script-name.phtml') 
	 * and receive the parsed content.
	 *
	 * @param string $template The name of the template to render
	 * @return string The parsed content
	 */
	public function render ($layoutModule, $template) {
		ob_start();
		include($template);
		$output = ob_get_clean();
		$this->content = $output;
		if($this->includeLayout) {
			$layout = (!empty($this->layout)) ? $this->layout : $this->config['layout'];
			ob_start();
			if(!(include($layoutModule . "/views/layouts/{$layout}.phtml"))) {
				include("default/views/layouts/{$layout}.phtml");
			}
			$output = ob_get_clean();
		}
		return $output;
	}
	
	/**
	 * Disable the template layer.
	 *
	 * @return void
	 */
	public function disableLayout () {
		$this->includeLayout = false;
	}
}