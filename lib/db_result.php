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
 * DB Result
 *
 * Basic database result set abstraction
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/db_result.html
 */
class DB_Result {
	private $resource;
	private $results;
	private $result_index;
	private $num_rows;
	
	/**
	 * Prepare the result set
	 *
	 * @access	public
	 * @param	resource	the query result resource
	 * @return	object
	 */
	public function __construct($result_resource = null) {
		if(!$resource)
			die("Not a valid result resource.");
		
		$this->result_resource = $result_resource;
		$this->results = array();
		$this->result_index = -1;
		$this->result_count = db_num_rows($this->result_resource);
		
		return $this;
	}
	
	/**
	 * Returns the next object in the result set
	 *
	 * @access	public
	 * @return	object
	 */
	public function result() {
		$result = db_fetch_object($this->result_resource);
		
		if(!$result)
			return false;
		
		$this->results[] = $result;
		$this->result_index++;
		
		return $this->results[$this->result_index];
	}
	
	/**
	 * Free the result set
	 *
	 * @access	public
	 */
	public function __destruct() {
		db_free_result($this->result_resource);
	}
}

?>