<?php
/**
 * Blast Framework
 *
 * A simple web application framework for PHP
 *
 * @package		BlastFramework
 * @author		Jason Johnson
 * @copyright	Copyright (c) 2009, Jason Johnson
 * @license		MIT License
 * @link		http://jasonjohnson.org/blast/
 * @since		Version 1.0
 */


/**
 * Application
 *
 * Sets up and routes requests to the proper controllers
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/application.html
 */
class Application {	
	static public $urls;
	static public $dirs;
	static public $query_string;
	static public $request_uri;
	static public $request_method;
	static public $script_name;
	
	/**
	 * Initialize URL pattern and search path arrays
	 *
	 * @static
	 * @access	public
	 */
	public static function initialize() {
		self::$urls = array();
		self::$dirs = array();
	}
	
	/**
	 * Matches the current request against specified patterns
	 *
	 * @static
	 * @access	public
	 */
	public static function match() {
		for($i = 0; $i < count(self::$urls); $i++) {
			$pattern = str_replace('/', "\/", self::$urls[$i]['pattern']);
			$pattern = "/^{$pattern}$/i";
			
			if(preg_match($pattern, self::$request_uri)) {
				return self::$urls[$i]['controller'];
			}
		}
		
		return false;
	}
	
	/**
	 * Cleans up requests for matching and execution
	 *
	 * @static
	 * @access	public
	 */
	public static function route() {	
		// copy needed server variables, leave the environment alone
		self::$query_string = $_SERVER['QUERY_STRING'];
		self::$request_uri = $_SERVER['REQUEST_URI'];
		self::$request_method = $_SERVER['REQUEST_METHOD'];
		self::$script_name = $_SERVER['SCRIPT_NAME'];
		
		// remove query string components
		self::$request_uri = str_replace(self::$query_string, '', self::$request_uri);
		self::$request_uri = rtrim(self::$request_uri, '?');
		
		// strip full script name off, if needed
		self::$request_uri = str_replace(self::$script_name, '', self::$request_uri);
		
		// finally, trim all /'s
		self::$request_uri = trim(self::$request_uri, '/');
		self::$request_method = strtolower(self::$request_method);
		
		if(!$match = self::match())
			die("Match for URL pattern not found.");
		
		// instantiate the matched controller, call the requested method
		$object = new $match;
		$method = self::$request_method;
		
		if(!method_exists($object, $method))
			die("Required method does not exist.");
		
		$object->$method();
	}
	
	/**
	 * Cleans up requests for matching and execution
	 *
	 * @static
	 * @access	public
	 * @param	string	regexp pattern to match
	 * @param	string	name of controller class
	 */
	public static function map($pattern, $controller) {
		self::$urls[] = array('pattern' => $pattern, 'controller' => $controller);
	}
	
	/**
	 * Attempts to locate and include the specified class
	 *
	 * @static
	 * @access	public
	 * @param	string	class name
	 */
	public static function load($class = null) {
		$exp = "/^$class\.php$/i";
		
		for($i = 0; $i < count(self::$dirs); $i++) {
			$dir = self::$dirs[$i];
			$dir = realpath($dir);
			
			if(!is_dir($dir))
				continue;
			
			if($handle = opendir($dir)) {
				while(false !== ($file = readdir($handle))) {
					if(preg_match($exp, $file)) {
						$path = $dir.'/'.$file;
						
						if(is_file($path)) {
							closedir($handle);
							
							include $path;
							return true;
						}
					}
				}
				
				closedir($handle);
			}
		}
		
		return false;
	}
	
	/**
	 * Sets up and runs application routing 
	 *
	 * @static
	 * @access	public
	 * @param	array	additional URLs to match
	 * @param	array	additional directories to search
	 */
	public static function run($urls = array(), $dirs = array()) {
		self::$urls = array_merge(self::$urls, $urls);
		self::$dirs = array_merge(self::$dirs, $dirs, array(Controller::$root, Model::$root));
		
		self::route();
	}
}

?>