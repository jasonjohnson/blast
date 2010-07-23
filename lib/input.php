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
 * Input
 *
 * Handles get, post and request input
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/input.html
 */
class Input {
	public static $request;
	public static $get;
	public static $post;
	public $validation;
	
	/**
	 * Initialization, copies global vars
	 *
	 * @access	public
	 */
	public function __construct() {
		self::$request = $_REQUEST;
		self::$get = $_GET;
		self::$post = $_POST;
		
		$this->validation = new Validation();
	}
	
	/**
	 * Validate input
	 *
	 * @access	public
	 * @param	string	input key
	 * @param	string	method used to validate
	 * @param	string	optional parameter to use in validation
	 */
	public function validate($key, $method = 'required', $parameter = '') {		
		$value = null;
		$value = $this->request($key);
		if(!$value) $value = $this->post($key);
		if(!$value) $value = $this->get($key);
		
		if($value)
			return $this->validation->test($value, $method, $parameter);
		
		return false;
	}
	
	/**
	 * Find the value of a $_REQUEST key
	 *
	 * @access	public
	 * @param	string	input key
	 * @return	mixed
	 */
	public function request($key) {
		$value = isset(self::$request[$key])?self::$request[$key]:null;
		$value = self::clean($value);
		
		if($value)
			return $value;
		
		return null;
	}
	
	/**
	 * Find the value of a $_GET key
	 *
	 * @access	public
	 * @param	string	input key
	 * @return	mixed
	 */
	public function get($key) {
		$value = isset(self::$get[$key])?self::$get[$key]:null;
		$value = self::clean($value);
		
		if($value)
			return $value;
		
		return null;
	}
	
	/**
	 * Find the value of a $_POST key
	 *
	 * @access	public
	 * @param	string	input key
	 * @return	mixed
	 */
	public function post($key) {
		$value = isset(self::$post[$key])?self::$post[$key]:null;
		$value = self::clean($value);
		
		if($value)
			return $value;
		
		return null;
	}
	
	/**
	 * Clean input
	 *
	 * @static
	 * @access	public
	 * @param	string	input
	 * @param	string
	 */
	public static function clean($input) {
		$input = trim($input);
		return $input;
	}
}

?>