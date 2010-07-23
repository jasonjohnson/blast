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
 * View
 *
 * A simple template output class
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/view.html
 */
class View {
	public static $root;
	private $view_file;
	private $view_assignments;
	
	/**
	 * Initialize view
	 *
	 * @access	public
	 * @param	string	view file
	 */
	public function __construct($view_file = '') {
		if($view_file)
			$this->view_file = $view_file;
	}
	
	/**
	 * Assign a value to a given key
	 *
	 * @access	public
	 * @param	string	key
	 * @param	mixed	value
	 * @return	object
	 */
	public function assign($key, $value = null) {
		$this->view_assignments[$key] = $value;
		return $this;
	}
	
	/**
	 * Display view
	 *
	 * @access	public
	 * @param	string	view file
	 * @param	array	data
	 */
	public function display($view_file = '', $data = array()) {
		if($view_file)
			$this->view_file = $view_file;
		
		foreach($data as $key => $value)
			$this->assign($key, $value);
		
		if(count($this->view_assignments) > 0) {
			foreach($this->view_assignments as $key => $value)
				$$key = $value;
		}
		
		$view_path = self::$root.'/'.$this->view_file.'.php';
		
		if(!file_exists($view_path))
			die("Could not find view file {$this->view_file}.");
		
		include $view_path;
	}
	
	/**
	 * Assign data and display a view file 
	 *
	 * @static
	 * @access	public
	 * @param	string	view file
	 * @param	array	data
	 */
	public static function load($view_file, $data = array()) {
		$view = new View();
		$view->display($view_file, $data);
	}
}

?>