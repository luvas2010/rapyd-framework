<?php


class rpd_database_library {

	public $database;
	public $hostname;
	public $username;
	public $password;

	public $dbprefix	= '';
	public $port		= '';

	public $conn_id		 = FALSE;
	public $result_id	 = FALSE;
	public $db_debug	 = FALSE;

	public $result_array = array();
	public $result_object = array();

  public $last_query;

	public function __construct($conn_id=FALSE)
	{
		$this->conn_id = $conn_id;
	}

	// --------------------------------------------------------------------

	public function escape($str)
	{
		switch (gettype($str))
		{
			case 'string'		:	$str = "'".$this->escape_str($str)."'";
				break;
			case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
				break;
			default					:	$str = ($str === NULL) ? 'NULL' : $str;
				break;
		}
		return $str;
	}

	// --------------------------------------------------------------------

	public function query($sql)
	{

		if (FALSE === ($this->result_id = $this->execute($sql)))
		{
			if ($this->db_debug)
			{
				return $this->show_error(	array($this->error_number($this->conn_id), $this->error_message($this->conn_id), $sql));
			}
		  return FALSE;
		}
    $this->last_query = $sql;
		return $this->result_id;
	}

	// --------------------------------------------------------------------

	public function result_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_assoc())
		{
			$this->result_array[] = $row;
		}

		return $this->result_array;
	}

	// --------------------------------------------------------------------

	public function row_array()
	{
		return $this->fetch_assoc();
	}

	// --------------------------------------------------------------------

	public function row_object()
	{
		return $this->fetch_object();
	}
	// --------------------------------------------------------------------

	public function options_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_row())
    {
      switch(count($row))
      {
        case 2:
          $data[$row[0]] = $row[1];
        break;
        case 3:
          $data[$row[0]][$row[1]] = $row[2];
        break;
        default: return array();
      }
    }
    $this->result_array = $data;
    return $this->result_array;
  }

	// --------------------------------------------------------------------

	public function result_object()
	{
		if (count($this->result_object) > 0)
		{
			return $this->result_object;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_object())
		{
			$this->result_object[] = $row;
		}

		return $this->result_object;
	}

	// --------------------------------------------------------------------

	public function count_all($table)
	{
		$this->query("SELECT COUNT(*) AS numrows FROM `".$this->dbprefix.$table."`");
		if ($this->num_rows() == 0)
			return '0';

    $row = $this->result_object();
		return $row[0]->numrows;
	}

	// --------------------------------------------------------------------

	public function list_tables()
	{
		$retval = array();
		$this->query("SHOW TABLES FROM `".$this->database."`");
		if ($this->num_rows() > 0)
		{
			foreach($this->result_array() as $row)
			{
				if (isset($row['TABLE_NAME']))
				{
					$retval[] = $row['TABLE_NAME'];
				}
				else
				{
					$retval[] = array_shift($row);
				}
			}
		}
		return $retval;
	}

	// --------------------------------------------------------------------

 	public function list_fields($table = '')
	{
		$retval = array();
		$this->query("SHOW COLUMNS FROM ".self::escape_table($table));
		foreach($this->result_array() as $row)
		{
			if (isset($row['COLUMN_NAME']))
			{
				$retval[] = $row['COLUMN_NAME'];
			}
			else
			{
				$retval[] = current($row);
			}
		}
		return $retval;
	}

	// --------------------------------------------------------------------

	public function field_data($table)
	{
		$retval = array();
		$this->query("SELECT * FROM ".self::escape_table($this->dbprefix.$table)." LIMIT 1");
		while ($field = $this->fetch_field())
		{
			$retval[] = $field;
		}
		return $retval;
	}

	// --------------------------------------------------------------------

	protected static function escape_table($table)
	{
		if (stristr($table, '.'))
		{
			$table = preg_replace("/\./", "`.`", $table);
		}
		return $table;
	}

	// --------------------------------------------------------------------

	protected function show_error($message)
	{
		echo '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
	}

}

