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
 * Model
 *
 * A basic model to be extended
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/model.html
 */
class Model {
	public static $root;
	public $db;
	
	/**
	 * Initialize model-specific objects (db)
	 *
	 * @access	public
	 */
	public function __construct() {
		$this->db = new DB();
	}
}

?>