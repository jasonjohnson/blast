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
 * Bootstrap
 *
 * Loads the required files to get a basic web app started
 *
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/bootstrap.html
 */
error_reporting(E_ERROR ^ E_WARNING | E_NOTICE);

set_error_handler('__error_handler');
set_magic_quotes_runtime(false);

// Determine the base path of the application
$root = realpath('.');

$lib_root = $root.'/lib';
$app_root = $root.'/app';

// Include all base classes explicitly, autoload the rest
include $lib_root.'/application.php';
include $lib_root.'/db.php';
include $lib_root.'/db_result.php';
include $lib_root.'/input.php';
include $lib_root.'/session.php';
include $lib_root.'/validation.php';
include $lib_root.'/model.php';
include $lib_root.'/controller.php';
include $lib_root.'/view.php';

// Initialize the application
Application::initialize();

Model::$root = $app_root.'/models';
Controller::$root = $app_root.'/controllers';
View::$root = $app_root.'/views';

// Database connectivity
DB::$adapter = "mysql";
DB::$host = "";
DB::$user = "";
DB::$password = "";
DB::$database = "";

DB::$adapter_root = $lib_root.'/db';

// Load the database adapter, will die() if not found
DB::load_adapter();

function __autoload($class) {
	if(!Application::load($class))
		die("Could not locate class: {$class}");
}

function __error_handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array()) {
	print("<p>PHP Error: <strong>{$errstr}</strong> in <strong>{$errfile}</strong> at line <strong>{$errline}</strong>.</p>");
}

?>