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
 * DB
 *
 * Basic database connection and query abstraction
 *
 * @package		BlastFramework
 * @subpackage	Libraries
 * @author		Jason Johnson
 * @link		http://jasonjohnson.org/blast/guide/db.html
 */
class DB {
	public static $adapter_root;
	
	public static $adapter;
	public static $host;
	public static $user;
	public static $password;
	public static $database;
	
	// connection and result properties
	public $connection;
	public $result;
	
	// query building properties
	private $statement;
	private $data;
	private $table;
	private $columns;
	private $where = array();
	private $order_by = array();
	private $limit;
	private $offset;
	
	
	/**
	 * Load the driver specified in boostrap.php
	 *
	 * @static
	 * @access	public
	 */
	public static function load_adapter() {
		$valid_adapters = array('mysql','sqlite');
		
		if(!self::$adapter)
			die("No adapter specified.");
		
		if(!in_array(self::$adapter, $valid_adapters))
			die("Specified adapter invalid.");
		
		include self::$adapter_root.'/'.self::$adapter.'.php';
	}
	
	/**
	 * Setup the database connection
	 *
	 * @access	public
	 */
	public function __construct() {
		if(!self::$host && !self::$user && !self::$password && !self::$database)
			return false;
		
		$this->connection = db_connect(self::$host, self::$user, self::$password);
		db_select_database(self::$database);
	}
	
	/**
	 * Specify which columns to return
	 *
	 * @access	public
	 * @param	string	list of columns
	 * @return	object
	 */
	public function select($columns) {
		$this->columns = db_escape_value($columns);
		return $this;
	}
	
	/**
	 * Set the table to use for the next query
	 *
	 * @access	public
	 * @param	string	name of the table
	 * @return	object
	 */
	public function from($table) {
		$this->table = db_escape_value($table);
		return $this;
	}
	
	/**
	 * Set the value of a column
	 *
	 * @access	public
	 * @param	string	name of column
	 * @param	string	value
	 * @return	object
	 */
	public function set($column, $value) {
		if(!is_object($this->data))
			$this->data = new stdClass();
		
		$this->data->$column = $value;
	}
	
	/**
	 * Insert row
	 *
	 * @access	public
	 * @param	string	name of the table
	 * @param	array	optional data to use
	 * @return	object
	 */
	public function insert($table, $data = null) {
		$table = db_escape_value($table);
		
		if(!$data)
			$data = $this->data;
		
		$this->statement = " INSERT INTO `{$table}` ";
		$this->statement .= $this->insert_string($data);
		
		db_prepare_statement($this->statement);
		db_execute($this->statement);
	}
	
	/**
	 * Update row(s)
	 *
	 * @access	public
	 * @param	string	name of the table
	 * @param	array	optional data to use
	 * @param	array	optional where clause to use
	 * @return	object
	 */
	public function update($table, $data = null, $where = array()) {
		if(count($where) == 0)
			return;
		
		$table = db_escape_value($table);
		
		if(!$data)
			$data = $this->data;
		
		foreach($where as $column => $value)
			$this->where($column, $value);
		
		$this->statement = " UPDATE `{$table}` ";
		$this->statement .= $this->update_string($data);
		$this->statement .= $this->where_string();
		$this->statement .= $this->limit_string();
		
		db_prepare_statement($this->statement);
		db_execute($this->statement);
	}
	
	/**
	 * Delete row(s)
	 *
	 * @access	public
	 * @param	string	name of the table
	 * @param	array	optional where clause to use
	 * @return	object
	 */
	public function delete($table, $where = array()) {
		if(count($where) == 0)
			return;
		
		$table = db_escape_value($table);
		
		foreach($where as $column => $value)
			$this->where($column, $value);
		
		$this->statement = " DELETE FROM `{$table}` ";
		$this->statement .= $this->where_string();
		$this->statement .= $this->limit_string();
		
		db_prepare_statement($this->statement);
		db_execute($this->statement);
	}
	
	/**
	 * Set the "where" clause
	 *
	 * @access	public
	 * @param	string	column
	 * @param	string	value
	 * @return	object
	 */
	public function where($column, $value) {
		$this->where[] = array('column' => $column, 'value' => $value);
		return $this;
	}
	
	/**
	 * Set the "order by" clause and its direction
	 *
	 * @access	public
	 * @param	string	column
	 * @param	string	direction of sort
	 * @return	object
	 */
	public function order_by($column, $direction = 'DESC') {
		$this->order_by[] = array('column' => $column, 'direction' => $direction);
		return $this;
	}
	
	/**
	 * Set the "limit" clause
	 *
	 * @access	public
	 * @param	integer	limit
	 * @param	integer	offset
	 * @return	object
	 */
	public function limit($limit, $offset = 0) {
		$this->limit = db_escape_value($limit);
		$this->offset = db_escape_value($offset);
		return $this;
	}
	
	/**
	 * Get the result set
	 *
	 * @access	public
	 * @param	string	optional table to use
	 * @param	array	optional where clause to use
	 * @return	DB_Result
	 */
	public function get($table = '', $where = array()) {
		$this->statement = " SELECT ";
		
		if(!$table)
			$table = $this->table;
		
		$table = db_escape_value($table);
		
		if($this->columns)
			$this->statement .= " {$this->columns} FROM {$table} ";
		else
			$this->statement .= " * FROM `{$table}` ";
		
		$this->statement .= $this->where_string();
		$this->statement .= $this->order_by_string();
		$this->statement .= $this->limit_string();
		
		db_prepare_statement($this->statement);
		$result = db_execute($this->statement);
		
		return new DB_Result($result);
	}
	
	/**
	 * Generate an insert string
	 *
	 * @access	private
	 * @param	array	optional data to use
	 * @return	string
	 */
	private function insert_string($data = null) {
		$insert_string = "";
		
		if(!$data)
			return $insert_string;
		
		$columns = array();
		$values = array();
		
		foreach($data as $column => $value) {
			$columns[] = "`".db_escape_string($columns)."`";
			$values[] = "'".db_escape_string($value)."'";
		}
		
		$insert_string = " (".implode(',', $columns).") VALUES (".implode(',', $values).") ";
		
		return $insert_string;
	}
	
	/**
	 * Generate an update string
	 *
	 * @access	private
	 * @param	array	optional data to use
	 * @return	string
	 */
	private function update_string($data = null) {
		$update_string = "";
		
		if(!$data)
			return $update_string;
		
		$column_value_pairs = array();
		
		foreach($data as $column => $value) {
			$column = db_escape_value($column);
			$value = db_escape_value($value);
			
			$column_value_pairs[] = " `$column` = '{$value}' ";
		}
		
		$update_string = implode(',', $column_value_pairs);
		
		return $update_string;
	}
	
	/**
	 * Generate a "where" clause
	 *
	 * @access	private
	 * @return	string
	 */
	private function where_string() {
		$where_string = "";
		
		if(!$this->where)
			return $where_string;
		
		$where_string .= " WHERE ";
		$where_count = count($this->where);
		
		for($i = 0; $i < $where_count; $i++) {
			$where = $this->where[$i];
			
			$where['column'] = db_escape_value($where['column']);
			$where['value'] = db_escape_value($where['value']);
		
			$where_string .= " `{$where['column']}` = '{$where['value']}' ";
			
			if($i < ($where_count-1))
				$where_string .= " AND ";
		}
		
		return $where_string;
	}
	
	/**
	 * Generate an "order by" clause
	 *
	 * @access	private
	 * @return	string
	 */
	private function order_by_string() {
		$order_by_string = "";
		
		if(!$this->order_by)
			return $order_by_string;
		
		$order_by_string .= " ORDER BY ";
		$order_by_count = count($this->order_by);
		
		for($i = 0; $i < $order_by_count; $i++) {
			$order_by = $this->order_by[$i];
			
			$order_by['column'] = db_escape_value($order_by['column']);
			$order_by['direction'] = db_escape_value($order_by['direction']);
			
			$order_by_string .= " `{$order_by['column']}` {$order_by['direction']} ";
			
			if($i < ($order_by_count-1))
				$order_by_string .= " , ";
		}
		
		return $order_by_string;
	}
	
	/**
	 * Generate a "limit" clause
	 *
	 * @access	private
	 * @return	string
	 */
	private function limit_string() {
		$limit_string = "";
		
		if(!$this->limit)
			return $limit_string;
		
		if($this->offset)
			$limit_string .= " LIMIT {$this->limit}, {$this->offset} ";
		else
			$limit_string .= " LIMIT {$this->limit} ";
		
		return $limit_string;
	}
	
	/**
	 * Close the database connection
	 *
	 * @access	public
	 */
	public function __destruct() {
		db_close($this->connection);
	}
}

?>