<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');



class rpd_database_ar_library extends rpd_database_library {

	public $ar_select		= array();
	public $ar_distinct	= FALSE;
	public $ar_from		  = array();
	public $ar_join		  = array();
	public $ar_where		= array();
	public $ar_like		  = array();
	public $ar_groupby	= array();
	public $ar_having		= array();
	public $ar_limit		= FALSE;
	public $ar_offset		= FALSE;
	public $ar_order		= FALSE;
	public $ar_orderby	= array();
	public $ar_set			= array();
	public $last_vars		= array();

	public function select($select = '*')
	{
		if (is_string($select))
		{
			$select = explode(',', $select);
		}

		foreach ($select as $val)
		{
			$val = trim($val);

			if ($val != '')
				$this->ar_select[] = $val;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function distinct($val = TRUE)
	{
		$this->ar_distinct = (is_bool($val)) ? $val : TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	public function from($from)
	{
		foreach ((array)$from as $val)
		{
			$this->ar_from[] = $this->dbprefix.$val;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function join($table, $cond, $type = '')
	{
		if ($type != '')
		{
			$type = strtoupper(trim($type));

			if ( ! in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
			{
				$type = '';
			}
			else
			{
				$type .= ' ';
			}
		}

		if ($this->dbprefix)
		{
			$cond = preg_replace('|([\w\.]+)([\W\s]+)(.+)|', $this->dbprefix . "$1$2" . $this->dbprefix . "$3", $cond);
		}

		// If a DB prefix is used we might need to add it to the column names
		if ($this->dbprefix)
		{
			// First we remove any existing prefixes in the condition to avoid duplicates
			$cond = preg_replace('|('.$this->dbprefix.')([\w\.]+)([\W\s]+)|', "$2$3", $cond);

			// Next we add the prefixes to the condition
			$cond = preg_replace('|([\w\.]+)([\W\s]+)(.+)|', $this->dbprefix . "$1$2" . $this->dbprefix . "$3", $cond);
		}

		$this->ar_join[] = $type.'JOIN '.$this->dbprefix.$table.' ON '.$cond;
		return $this;
	}

	// --------------------------------------------------------------------

	public function where($key, $value = NULL)
	{
		return $this->_where($key, $value, 'AND ');
	}

	// --------------------------------------------------------------------

	public function orwhere($key, $value = NULL)
	{
		return $this->_where($key, $value, 'OR ');
	}

	// --------------------------------------------------------------------

	protected function _where($key, $value = NULL, $type = 'AND ')
	{
		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->ar_where) == 0) ? '' : $type;

			if ( ! is_null($v))
			{
				if ( ! $this->_has_operator($k))
				{
					$k .= ' =';
				}

				$v = ' '.$this->escape($v);
			}

			$this->ar_where[] = $prefix.$k.$v;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function like($field, $match = '')
	{
		return $this->_like($field, $match, 'AND ');
	}

	// --------------------------------------------------------------------

	public function orlike($field, $match = '')
	{
		return $this->_like($field, $match, 'OR ');
	}

	// --------------------------------------------------------------------

	protected function _like($field, $match = '', $type = 'AND ')
	{
		if ( ! is_array($field))
		{
			$field = array($field => $match);
		}

		foreach ($field as $k => $v)
		{
			$prefix = (count($this->ar_like) == 0) ? '' : $type;

			$v = $this->escape_str($v);

			$this->ar_like[] = $prefix." $k LIKE '%{$v}%'";
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function groupby($by)
	{
		if (is_string($by))
		{
			$by = explode(',', $by);
		}

		foreach ($by as $val)
		{
			$val = trim($val);

			if ($val != '')
				$this->ar_groupby[] = $val;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function having($key, $value = '')
	{
		return $this->_having($key, $value, 'AND ');
	}

	// --------------------------------------------------------------------

	public function orhaving($key, $value = '')
	{
		return $this->_having($key, $value, 'OR ');
	}

	// --------------------------------------------------------------------

	protected function _having($key, $value = '', $type = 'AND ')
	{
		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->ar_having) == 0) ? '' : $type;

			if ($v != '')
			{
				$v = ' '.$this->escape($v);
			}

			$this->ar_having[] = $prefix.$k.$v;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function orderby($orderby, $direction = '')
	{
		if (trim($direction) != '')
		{
			$direction = (in_array(strtoupper(trim($direction)), array('ASC', 'DESC', 'RAND()'), TRUE)) ? ' '.$direction : ' ASC';
		}

		$this->ar_orderby[] = $orderby.$direction;
		return $this;
	}

	// --------------------------------------------------------------------

	public function limit($value, $offset = '')
	{
		$this->ar_limit = $value;

		if ($offset != '')
			$this->ar_offset = $offset;

		return $this;
	}

	// --------------------------------------------------------------------

	public function offset($value)
	{
		$this->ar_offset = $value;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set($key, $value = '')
	{
		$key = $this->_object_to_array($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$this->ar_set[$k] = $this->escape($v);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get($table = '', $limit = null, $offset = null)
	{
		if ($table != '')
		{
			$this->from($table);
		}

		if ( ! is_null($limit))
		{
			$this->limit($limit, $offset);
		}

		$sql = $this->_compile_select();

		$result = $this->query($sql);
		$this->_reset_select();
		return $result;
	}

	// --------------------------------------------------------------------

	public function getwhere($table = '', $where = null, $limit = null, $offset = null)
	{
		if ($table != '')
		{
			$this->from($table);
		}

		if ( ! is_null($where))
		{
			$this->where($where);
		}

		if ( ! is_null($limit))
		{
			$this->limit($limit, $offset);
		}

		$sql = $this->_compile_select();

		$result = $this->query($sql);
		$this->_reset_select();
		return $result;
	}

	// --------------------------------------------------------------------

	public function insert($table = '', $set = NULL)
	{
		if ( ! is_null($set))
		{
			$this->set($set);
		}

		if (count($this->ar_set) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_must_use_set');
			}
			return FALSE;
		}

		if ($table == '')
		{
			if ( ! isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}

			$table = $this->ar_from[0];
		}

		$sql = $this->_insert($this->dbprefix.$table, array_keys($this->ar_set), array_values($this->ar_set));

		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	public function update($table = '', $set = NULL, $where = null)
	{
		if ( ! is_null($set))
		{
			$this->set($set);
		}

		if (count($this->ar_set) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_must_use_set');
			}
			return FALSE;
		}

		if ($table == '')
		{
			if ( ! isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}
			$table = $this->ar_from[0];
		}

		if ($where != null)
		{
			$this->where($where);
		}

		$sql = $this->_update($this->dbprefix.$table, $this->ar_set, $this->ar_where);

		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	public function delete($table = '', $where = '')
	{
		if ($table == '')
		{
			if ( ! isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}

			$table = $this->ar_from[0];
		}

		if ($where != '')
		{
			$this->where($where);
		}

		if (count($this->ar_where) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_del_must_use_where');
			}
			return FALSE;
		}

		$sql = $this->_delete($this->dbprefix.$table, $this->ar_where);

		$this->_reset_write();
		return $this->query($sql);
	}


	// --------------------------------------------------------------------

	protected function _insert($table, $keys, $values)
	{
		return "INSERT INTO ".self::escape_table($table)." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}

	// --------------------------------------------------------------------

	protected function _update($table, $values, $where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}

		return "UPDATE ".self::escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".implode(" ", $where);
	}

	// --------------------------------------------------------------------

	protected function _delete($table, $where)
	{
		return "DELETE FROM ".self::escape_table($table)." WHERE ".implode(" ", $where);
	}

	// --------------------------------------------------------------------

	protected function _limit($sql, $limit, $offset)
	{
		if ($offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ", ";
		}

		return $sql."LIMIT ".$offset.$limit;
	}

	// --------------------------------------------------------------------

	protected function _has_operator($str)
	{
		$str = trim($str);
		if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	public function _compile_select()
	{
    $this->_save_vars();

		$sql = ( ! $this->ar_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

		$sql .= (count($this->ar_select) == 0) ? '*' : implode(', ', $this->ar_select);

		if (count($this->ar_from) > 0)
		{
			$sql .= "\nFROM ";
			$sql .= implode(', ', $this->ar_from);
		}

		if (count($this->ar_join) > 0)
		{
			$sql .= "\n";
			$sql .= implode("\n", $this->ar_join);
		}

		if (count($this->ar_where) > 0 OR count($this->ar_like) > 0)
		{
			$sql .= "\nWHERE ";
		}

		$sql .= implode("\n", $this->ar_where);

		if (count($this->ar_like) > 0)
		{
			if (count($this->ar_where) > 0)
			{
				$sql .= " AND ";
			}

			$sql .= implode("\n", $this->ar_like);
		}

		if (count($this->ar_groupby) > 0)
		{
			$sql .= "\nGROUP BY ";
			$sql .= implode(', ', $this->ar_groupby);
		}

		if (count($this->ar_having) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $this->ar_having);
		}

		if (count($this->ar_orderby) > 0)
		{
			$sql .= "\nORDER BY ";
			$sql .= implode(', ', $this->ar_orderby);

			if ($this->ar_order !== FALSE)
			{
				$sql .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
			}
		}

		if (is_numeric($this->ar_limit))
		{
			$sql .= "\n";
			$sql = $this->_limit($sql, $this->ar_limit, $this->ar_offset);
		}

		return $sql;
	}

	// ------------------------------------------------------------------

	protected function _object_to_array($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			if ( ! is_object($val) AND ! is_array($val))
			{
				$array[$key] = $val;
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	protected function _reset_select()
	{
		$this->ar_select	= array();
		$this->ar_distinct	= FALSE;
		$this->ar_from		= array();
		$this->ar_join		= array();
		$this->ar_where		= array();
		$this->ar_like		= array();
		$this->ar_groupby	= array();
		$this->ar_having	= array();
		$this->ar_limit		= FALSE;
		$this->ar_offset	= FALSE;
		$this->ar_order		= FALSE;
		$this->ar_orderby	= array();
	}

	// --------------------------------------------------------------------

	public function refill_query()
	{
    foreach($this->last_vars as $clause => $value)
    {
      if (substr($clause,0,3) == 'ar_')
        $this->$clause = $value;
    }
	}

	// --------------------------------------------------------------------

	protected function _save_vars()
	{
    $this->last_vars = get_object_vars ($this);
  }

	// --------------------------------------------------------------------

	protected function _reset_write()
	{
		$this->ar_set		 = array();
		$this->ar_from	 = array();
		$this->ar_where	 = array();
	}

}
