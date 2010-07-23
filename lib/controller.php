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
 * Controller
 *
 * A basic controller to be extended
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/controller.html
 */
class Controller {
	public static $root;
	public $input;
	public $session;
	public $validation;
	
	/**
	 * Initialize controller-specific objects (input, session, validation, view)
	 *
	 * @access	public
	 */
	public function __construct() {
		$this->input = new Input();
		$this->session = new Session();
		$this->validation = new Validation();
		$this->view = new View();
	}
}

?>