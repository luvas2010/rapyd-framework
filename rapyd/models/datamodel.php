<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class datamodel_model {

  public $table  = null;
  public $loaded = false;

  public $pk = array();

  public $fields = array();
  public $field_meta = array();
  public $data = array();
  public $data_rel = array();
  public $new_data = array();

  public $preprocess_functions = array();
  public $postprocess_functions = array();
  public $postprocess_result = null;
	public $preprocess_result = null;

  protected $one_to_one = array();
  protected $one_to_many = array();
  protected $many_to_many = array();

  // --------------------------------------------------------------------

	public function __construct($table)
	{
    rpd::connect();
    $this->db = rpd::$db;

    $this->table  = $table;
    $this->fields = $this->db->field_data($table);

    // to support tables with one or more PK
    foreach ($this->fields as $field)
    {
      $this->field_names[] = $field->name;
      $this->field_meta[$field->name] = $field;
      if ($field->primary_key)
      {
        $this->pk[$field->name] = "";
      }
    }

    if (count($this->pk)==0)
    {
      //table must have a PK
      $this->show_error("no PK for ".$table);
      die();
    }
  }

	// --------------------------------------------------------------------

	protected function show_error($error_arr)
	{
		echo '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
	}

	// --------------------------------------------------------------------

  public function pre_process($action,$function,$arr_values=array())
  {
    $this->preprocess_functions[$action] = array("name"=>$function, "arr_values"=>$arr_values);
  }

	// --------------------------------------------------------------------

  protected function exec_preprocess_functions($action)
  {
  	$this->preprocess_result = TRUE;
    if (isset($this->preprocess_functions[$action]))
    {
      $function = $this->preprocess_functions[$action];
      $arr_values = $function["arr_values"];
      (count($arr_values)>0)? array_unshift($arr_values, $this):$arr_values = array($this);
      $this->preprocess_result = call_user_func_array($function["name"], $arr_values);
      return $this->preprocess_result;
    }
  }

	// --------------------------------------------------------------------

  public function post_process($actions,$function,$arr_values=array())
  {
    $actions = (array)$actions;
    foreach ($actions as $action)
    {
      $this->postprocess_functions[$action] = array("name"=>$function, "arr_values"=>$arr_values);
    }
  }

	// --------------------------------------------------------------------

  protected function exec_postprocess_functions($action)
  {
    if (isset($this->postprocess_functions[$action]))
    {
      $function = $this->postprocess_functions[$action];
      $arr_values = $function["arr_values"];
      (count($arr_values)>0)? array_unshift($arr_values, $this):$arr_values = array($this);

      $this->action = $action;
      $this->postprocess_result = call_user_func_array($function["name"], $arr_values);
      return  $this->postprocess_result;
    }
  }

	// --------------------------------------------------------------------

  public function load($id)
  {
  	$this->preprocess_result = null;
    if (is_array( $id))
    {
      if (sizeof($id) != sizeof($this->pk))
      {
        $this->show_error("datamodel: not enough parameters");
        return false;
      } else {
        foreach ($this->pk as $keyfield=>$keyvalue){
          $this->pk[$keyfield] = $id[$keyfield];
        }
      }
    } else {
       $keys = array_keys($this->pk);
       $key = $keys[0];
       $this->pk[$key] = $id;
    }
    $this->db->getwhere($this->table, $this->pk);

    if ($this->db->num_rows()>1)
    {
      $this->show_error("datamodel: more than one result");
      return false;
    }
    elseif ($this->db->num_rows()==1)
    {
      $results = $this->db->result_array();
      $this->bind_data($results[0]);
      $this->loaded = true;
      $this->bind_rel();
      return true;
    }
    else
    {
      $this->loaded = false;
      return false;
    }
  }

	// --------------------------------------------------------------------

  protected function bind_data($data)
  {
    $this->data = $data;
  }

	// --------------------------------------------------------------------

  protected function bind_rel()
  {
    if (count($this->pk)>1) return;

    reset($this->pk);
    list($pk_name, $pk_value) = each($this->pk);
		$table_dot_pk = $this->table.".".$pk_name;
    $where = array ($table_dot_pk => $pk_value);

		if (count($this->one_to_one)>0)
    {
      foreach($this->one_to_one as $one_to_one)
      {
        $this->db->select($one_to_one["table_alias"].".*");
        $this->db->from($this->table);
        $one_to_one["on"] = str_replace("{pk}", $pk_name, $one_to_one["on"]);
        $this->db->join($one_to_one["table"], $one_to_one["on"]);
        $this->db->where($where);
        $this->db->get();

        if($this->db->num_rows()>0)
        {
         $results = $this->db->result_array();
         $this->data_rel[$one_to_one["id"]] = $results[0];
        }
      }
		}

		if (count($this->one_to_many)>0)
    {
      foreach($this->one_to_many as $one_to_many)
      {
        $this->db->select($one_to_many["table_alias"].".*");
        $this->db->from($this->table);
        $one_to_many["on"] = str_replace("{pk}", $pk_name, $one_to_many["on"]);
        $this->db->join($one_to_many["table"], $one_to_many["on"]);
        $this->db->where($where);
        $this->db->get();
        if($this->db->num_rows()>0)
        {
         $this->data_rel[$one_to_many["id"]] = $this->db->result_array();
        }
      }
		}

		if (count($this->many_to_many)>0)
    {
      foreach($this->many_to_many as $many_to_many)
      {
        $this->db->select($many_to_many["table_alias"].".*");
        $this->db->from($many_to_many["rel_table"]);
        $this->db->join($many_to_many["table"], $many_to_many["on"],"left");
        $on2 = $many_to_many["rel_table"].".".$pk_name." = ".$this->table.".".$pk_name;
        $this->db->join($this->table, $on2,"left");
        $this->db->where($where);
        $this->db->get();
        if($this->db->num_rows()>0)
        {
         $this->data_rel[$many_to_many["id"]] = $this->db->result_array();
        }
      }
		}
  }

	// --------------------------------------------------------------------

  public function save()
  {
    //INSERT
    if (!$this->loaded)
    {
  	  $pk_ai = true;
  	  foreach ($this->pk as $keyfield => $keyvalue)
      {
        if(isset($this->data[$keyfield]))
        {
        	$this->pk[$keyfield] = $this->data[$keyfield];
        	$pk_ai = false;
        }
      }
      $escape = $this->exec_preprocess_functions("insert");
      if ($escape !== false)
      {
        $result = $this->db->insert($this->table, $this->data);
        if($result && $pk_ai)
        {
          $keys = array_keys($this->pk);
          $key = $keys[0];
          $this->pk[$key] = $this->insert_id();
          $this->data[$key] = $this->pk[$key];
          $this->loaded = true;
          $this->bind_rel();
        }
        //exec post process function and store result in a property
        $this->postprocess_result = $this->exec_postprocess_functions("insert");
        return $result;
      } else {
        return false;
      }

    //UPDATE
    } else {

      $this->db->where($this->pk);
      $escape = $this->exec_preprocess_functions("update");

      foreach ($this->pk as $keyfield => $keyvalue)
      {
        if(isset($this->data[$keyfield])){
          $this->pk[$keyfield] = $this->data[$keyfield];
        }
      }

      if ($escape !== false)
      {
        if (count($this->new_data)>0)
        {
          $result = $this->db->update($this->table, $this->new_data);
        } else {
          $result = true;
        }
        //exec post process function and store result in a property
        $this->postprocess_result = $this->exec_postprocess_functions("update");
        return $result;
      } else {
        return false;
      }
    }
  }

	// --------------------------------------------------------------------

  public function insert_id()
  {
    return $this->db->insert_id();
  }

	// --------------------------------------------------------------------

  public function load_where($field, $value)
  {
    $this->db->where($field, $value);
    $this->db->get($this->table);
    if ($this->db->num_rows()>1)
    {
      $this->show_error("datamodel: more than one result");
      return false;
    } elseif($this->db->num_rows()===1) {
      $results = $this->db->result_array();
      $this->bind_data($results[0]);
      foreach ($this->pk as $keyfield=>$keyvalue)
      {
        $this->pk[$keyfield] = $results[0][$keyfield];
      }
      $this->loaded = true;
      $this->bind_rel();
      return true;

    } else {
      return false;
    }
  }

	// --------------------------------------------------------------------

  public function is_unique($field, $value)
  {
    $this->db->where($field, $value);
    $this->db->get($this->table);

    if($this->db->num_rows()>1)
    {
      return false;
    }
    elseif ($this->db->num_rows()===1)
    {
      if ($this->loaded)
      {
        return ($this->data[$field] == $value);
      } else {
        return false;
      }
    } else {
      return true;
    }
  }

	// --------------------------------------------------------------------

  public function are_unique($field)
  {
    if (is_array($field) && count($field)>0)
    {
      foreach($field as $fieldname => $value)
      {
        $this->db->where($fieldname, $value);
      }
    } else {
      return false;
    }
    $this->db->get($this->table);

    if ($this->db->num_rows()>1)
    {
      return false;
    }
    elseif ($this->db->num_rows()===1)
    {
      if ($this->loaded)
      {
        foreach($field as $fieldname => $value)
        {
          if($this->data[$fieldname] != $value) return false ;
        }
        return true;
      }
      return false;
    } else {
      return true;
    }
  }

	// --------------------------------------------------------------------

  public function get($field)
  {
    if (isset($this->data[$field]))
    {
      return $this->data[$field];
    } else {
      return null;
    }
  }

	// --------------------------------------------------------------------

  public function get_rel($rel_id, $field)
  {
    if (isset($this->data_rel[$rel_id][$field]))
    {
      return $this->data_rel[$rel_id][$field];
    } else {
      return null;
    }
  }

	// --------------------------------------------------------------------

  public function get_related($rel_id)
  {
    if (isset($this->data_rel[$rel_id]))
    {
      return $this->data_rel[$rel_id];
    } else {
      return null;
    }
  }

	// --------------------------------------------------------------------

  public function set($field, $value)
  {
		$field_meta = $this->field_meta[$field];
		if (in_array($field_meta->type,array("int","date")) && $value=="")
		{
		  $value = null;
		}

    //store only new values in a new array
    if (isset($this->data[$field]))
    {
      if ($value != $this->data[$field])
        $this->new_data[$field] = $value;
    } else {
      if (in_array($field,$this->field_names))
      {
        if ($this->loaded && is_null($value))
        {
          //is already null
        }
        else
        {
          $this->new_data[$field] = $value;
        }
      }
    }
    $this->data[$field] = $value;
  }

	// --------------------------------------------------------------------

  public function set_rel($rel_id, $field, $value)
  {
    $this->data_rel[$rel_id][$field] = $value;
  }

	// --------------------------------------------------------------------

  public function inc($field, $inc=1)
  {
    if (isset($this->data[$field]))
    {
      $this->data[$field] = $this->data[$field]+$inc;
    } else {
      $this->data[$field] = $inc;
    }
  }

	// --------------------------------------------------------------------

  public function dec($field, $dec=1, $positive=true)
  {
    if (isset($this->data[$field]))
    {
      if (($this->data[$field]-$dec < 0) && ($positive))
      {
        return false;
      } else {
        $this->data[$field] = $this->data[$field]-$dec;
      }
    } else {
      if ($positive)
      {
        return false;
      } else {
        $this->data[$field] = 0-$dec;
      }
    }
  }

	// --------------------------------------------------------------------

  public function get_all()
  {
    $data = $this->data;
    $data = array_merge($data, $this->data_rel);
    return $data;
  }

	// --------------------------------------------------------------------

  public function delete()
  {
    if ($this->loaded)
    {
      $this->db->where($this->pk);

      $escape = $this->exec_preprocess_functions("delete");
      if ($escape !== false)
      {
        $result = $this->db->delete($this->table);
        $this->postprocess_result = $this->exec_postprocess_functions("delete");
        return $result;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

	// --------------------------------------------------------------------

  public function delete_where($field,$value)
  {
    $this->db->where($field, $value);
    return $this->db->delete($this->table);
  }

	// --------------------------------------------------------------------

  public function rel_one_to_one($id, $table, $field_fk="{pk}", $field="", $cascade="")
  {
    if ($field=="") $field = $field_fk;
    $arr["id"] = $id;
    $arr["table"] = $table;  //table to join
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias;
    $arr["on"] = $alias.".".$field." = ".$this->table.".".$field_fk;  //join "on"
    $arr["cascade"] = $cascade;
    $this->one_to_one[$id] = $arr;
  }

	// --------------------------------------------------------------------

  public function rel_one_to_many ($id, $table, $field_fk="{pk}", $cascade="")
  {
    $arr["id"] = $id;
    $arr["table"] = $table;
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias;
    $arr["on"] = $alias.".".$field_fk." = ".$this->table.".".$field_fk;  //join "on"
    $arr["cascade"] = $cascade;
    $this->one_to_many[$id] = $arr;
  }

	// --------------------------------------------------------------------

  public function rel_many_to_many ($id, $table, $rel_table, $field, $cascade="")
  {
    $arr["id"] = $id;
    $arr["rel_table"] = $rel_table;
    $arr["table"] = $table;

    //non sto' capendo piu' niente.. ma qui devo procedere come nelle altre relazioni
    if (strpos($table," as")>0) {
      $alias = substr($table,strpos($table," as ")+4);
    } else {
      $alias = $table;
    }
    $arr["table_alias"] = $alias;
    $arr["on"] = $rel_table.".".$field." = ".$table.".".$field;  //join "on"
    $arr["cascade"] = $cascade;
    $this->many_to_many[$id]= $arr;
  }

}
