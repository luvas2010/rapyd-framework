<?php


require_once("fSQL.php");

class rpd_database_fsql_driver extends rpd_database_ar_library {


	public function connect()
	{
		$fsql = new fSQLEnvironment;
		$this->conn_id = $fsql;
		$this->conn_id->define_db("rapyd", $this->database);
		$this->conn_id->select_db("rapyd");
		return $this->conn_id;
	}

	public function pconnect()
	{
		return $this->connect();
	}

	public function select_db()
	{
		$this->conn_id->select_db("rapyd");
		return TRUE;
	}

	public function db_set_charset($charset, $collation)
	{
		return TRUE;
	}

	protected function execute($sql)
	{
		//rrr : reset result resources
		$this->result_id	 = FALSE;
		$this->result_array = array();
		$this->result_object = array();

		$sql = str_replace(array("\r\n", "\n", "\r"), ' ', $sql);
		$resurce = $this->conn_id->query($sql);
		//if (!$resurce and $this->db_debug)
		//{
			echo '<pre>'.$sql.'</pre>';
		//}
		return $resurce;
	}

	protected static function escape_str($str)
	{

		return addslashes($str);

	}

	public function affected_rows()
	{
		return $this->conn_id->affected_rows();
	}

	public function insert_id()
	{
		return @$this->conn_id->insert_id();
	}

	public function data_seek($n = 0)
	{
		return $this->conn_id->data_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return $this->conn_id->fetch_row($this->result_id);
	}

	public function fetch_assoc()
	{
		return $this->conn_id->fetch_assoc($this->result_id);
	}

	public function fetch_object()
	{
		return $this->conn_id->fetch_object($this->result_id);
	}

	public function fetch_field($id)
	{
		//var_dump($this->conn_id);
		//die('me');
		//var_dump($this->conn_id->fetch_field($this->conn_id));
		//return $this->conn_id->fetch_field($this->conn_id, $id);
	}

	public function field_data($table)
	{
		$retval = array();
		$this->conn_id->query("SELECT * FROM ".self::escape_table($this->dbprefix.$table)." LIMIT 1");
		$fields = $this->conn_id->currentDB->loadedTables[$table]->columns;
		foreach ($fields as $field_name => $field_properties)
		{
			$field = new stdClass();
			$field->name = $field_name;
			$field->primary_key = ($field_properties['key'] == 'p') ? TRUE : FALSE;
			$field->type = ($field_properties['type'] == 's') ? 'varchar' : 'int';
			$retval[] = $field;
		}
		return $retval;
	}

	public function num_rows()
	{
		return $this->conn_id->num_rows($this->result_id);
	}

	public function error_message()
	{
		return $this->conn_id->error();
	}

	public function error_number()
	{
		return '';
	}

	public function count_all($table)
	{
		$this->query("SELECT COUNT(*) AS numrows FROM ".$this->dbprefix.$table);
		if ($this->num_rows() == 0)
			return '0';

		$row = $this->result_object();
		return $row[0]->numrows;
	}

}
