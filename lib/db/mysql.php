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
 * MySQL DB Adapter
 *
 * Implements all the required functions to provide MySQL support
 *
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/db_adapter.html
 */


/**
 * Connect to a database
 *
 * @param	string	host
 * @param	string	user
 * @param	string	password
 */
function db_connect($host, $user, $password) {
	if(!$host || !$user || !$password)
		return false;
	
	$connection = mysql_connect($host, $user, $password);
	
	if($error = db_error())
		die("Unable to connect to the specified database. [{$error}]");
	
	return $connection;
}

/**
 * Select a database
 *
 * @param	string	database
 */
function db_select_database($database) {
	mysql_select_db($database);
	
	if($error = db_error())
		die("An unexpected database error occurred. [{$error}]");
}

/**
 * Run a query/statement
 *
 * @param	string	statement
 * @return	resource
 */
function db_query($statement) {
	$result = mysql_query($statement);
	
	if($error = db_error())
		die("Poorly formed query. [{$error}]");
	
	return $result;
}

/**
 * Prepare a query/statement
 *
 * @param	string	statement
 */
function db_prepare_statement($statement) {
	if(preg_match("/^(UPDATE|DELETE)/i", $statement)) {
		if(!preg_match("/ WHERE /i", $statement))
			die("Destructive queries (UPDATE and DELETE) require a WHERE clause.");
	}
}

/**
 * Escape a value for safe use in a query
 *
 * @param	string	value
 * @return	string
 */
function db_escape_value($value) {
	return mysql_real_escape_string($value);
}

/**
 * Number of rows in a given result
 *
 * @param	resource	result set
 * @return	integer
 */
function db_num_rows($result) {
	return mysql_num_rows($result);
}

/**
 * Fetch the next object of a result set
 *
 * @param	resource	result set
 * @return	object
 */
function db_fetch_object($result) {
	$object = mysql_fetch_object($result, 'stdClass');
	
	if($error = db_error())
		die("Could not fetch object. [{$error}]");
	
	return $object;
}

/**
 * Free a result set
 *
 * @param	resource	result set
 */
function db_free_result($result) {
	mysql_free_result($result);
}

/**
 * Close an active connection
 *
 * @param	resource	connection
 */
function db_close($connection) {
	if($connection)
		mysql_close($connection);
}

/**
 * Determine if a database error has occurred
 *
 * @return	string
 */
function db_error() {
	return mysql_error();
}

?>