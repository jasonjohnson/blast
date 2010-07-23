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
 * Session
 *
 * Basic session handling with cookies
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/session.html
 */
class Session {
	public static $cookie;
	public static $cookie_name;
	public static $cookie_value;
	public static $cookie_expire;
	
	/**
	 * Initialize session
	 *
	 * @access	public
	 */
	public function __construct() {
		self::$cookie =& $_COOKIE;
		self::$cookie_name = 'antiphp';
		self::$cookie_value = array();
		self::$cookie_expire = time()+(60*60*24*30);
		
		$this->read_cookie();
	}
	
	/**
	 * Get a stored session object by key
	 *
	 * @access	public
	 * @param	string	key
	 * @return	mixed
	 */
	public function get($key) {
		if($value = self::$cookie_value[$key])
			return $value;
		
		return null;
	}
	
	/**
	 * Store a session object
	 *
	 * @access	public
	 * @param	string	key
	 * @param	mixed	value
	 * @return	mixed
	 */
	public function store($key, $value) {
		self::$cookie_value[$key] = $value;
		$this->write_cookie();
	}
	
	/**
	 * Destroy the current session
	 *
	 * @access	public
	 */
	public function destroy() {
		self::$cookie_value = array();
		$this->destroy_cookie();
	}
	
	/**
	 * Read cookie data
	 *
	 * @access	private
	 */
	private function read_cookie() {
		if(isset(self::$cookie[self::$cookie_name]))
			self::$cookie_value = self::$cookie[self::$cookie_name];
	}
	
	/**
	 * Write cookie data
	 *
	 * @access	private
	 */
	private function write_cookie() {
		setcookie(self::$cookie_name, self::$cookie_value, self::$cookie_expire);
	}
	
	/**
	 * Destroy cookie data
	 *
	 * @access	private
	 */
	private function destroy_cookie() {
		setcookie(self::$cookie_name, null, time()-1);
	}
}

?>