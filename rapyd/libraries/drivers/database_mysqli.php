<?php



class rpd_database_mysqli_driver extends rpd_database_ar_library {


	public function connect()
	{
	$this->conn_id = new mysqli($this->hostname, $this->username, $this->password);
			return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = new mysqli($this->hostname, $this->username, $this->password);
		return $this->conn_id;
	}

	public function select_db()
	{
		return @mysqli_select_db($this->database, $this->conn_id);
	}

	public function db_set_charset($charset, $collation)
	{
		return @mysqli_query("SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'", $this->conn_id);
	}

	protected function execute($sql)
	{
		//rrr : reset result resources
		$this->result_id     = FALSE;
		$this->result_array = array();
		$this->result_object = array();

		return @mysqli_query($sql, $this->conn_id);
	}

	protected static function escape_str($str)
	{
		if (function_exists('mysqli_real_escape_string'))
		{
			return mysqli_real_escape_string($this->conn_id, $str);
		}
		elseif (function_exists('mysqli_escape_string'))
		{
			return mysqli_escape_string($this->conn_id, $str);
		}
		else
		{
			return addslashes($str);
		}
	}

	public function affected_rows()
	{
		return @mysqli_affected_rows($this->conn_id);
	}

	public function insert_id()
	{
		return @mysqli_insert_id($this->conn_id);
	}

	public function data_seek($n = 0)
	{
		return mysqli_data_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return mysqli_fetch_row($this->result_id);
	}

	public function fetch_assoc()
	{
		return mysqli_fetch_assoc($this->result_id);
	}

	public function fetch_object()
	{
		return mysqli_fetch_object($this->result_id);
	}

	public function fetch_field()
	{
		return mysqli_fetch_field($this->result_id);
	}

	public function num_rows()
	{
		return @mysqli_num_rows($this->result_id);
	}

	public function error_message()
	{
		return mysqli_error($this->conn_id);
	}

	public function error_number()
	{
		return mysqli_errno($this->conn_id);
	}

}
