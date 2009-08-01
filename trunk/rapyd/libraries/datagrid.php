<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');



class datagrid_library extends dataset_library {

  protected $fields	= array();
  public $columns	= array();
  public $actions	= array();
  public $rows = array();
  public $checkbox_form = false;
  public $output = "";
  public $add_url = "";

  // --------------------------------------------------------------------

  protected function set_columns($columns)
  {
    foreach ($columns as $column)
    {
      $this->set_column($column);
    }
  }

  // --------------------------------------------------------------------

  public function set_column($column)
  {
     //share source with columns
    if (!isset($column['source']))
      $column['source'] = $this->source;

    //detect if is a checkbox column
    if (isset($column['checkbox']))
      $this->checkbox_form = true;

    $this->columns[] = new datagrid_column($column);
  }

  // --------------------------------------------------------------------

  function action_button($config=null)
  {
    $caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.delete');
    $action_name = (isset($config['name'])) ? $config['name'] : 'delete';
    $action = "javascript:document.forms['grid".$this->cid."'].grid_action.value='".$action_name."';document.forms['grid".$this->cid."'].submit()";
    $this->button("btn_".$action_name, $caption, $action, "TR");
  }

  // --------------------------------------------------------------------

  function add_button($config=null)
  {
    $caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.add');
    $url = null;
    if (isset($config['url']) OR  $this->add_url!="")
    {
      $url = (isset($config['url'])) ? $config['url'] : $this->add_url;
    }

    $url = rpd_url_helper::append('create'.$this->cid, 1, $url);
    $action = "javascript:window.location='".$url."'";
    $this->button("btn_add", $caption, $action, "TR");
  }

  // --------------------------------------------------------------------

  protected function sniff_action()
	{
    if (isset($_POST['grid_action']))
    {
      $action = $_POST['grid_action'];

      if (isset($this->actions[$action])) call_user_func($this->actions[$action]);
    }
  }

  // --------------------------------------------------------------------

  protected function build_grid()
  {
    rpd_html_helper::css('datagrid.css');

    $data = get_object_vars($this);

    $this->build_buttons();

    if ($this->checkbox_form)
    {
      rpd::load('helper','form');
      $attributes = array('class'=>'form', 'name'=>'grid');
      $data['form_begin'] = rpd_form_helper::open(rpd_url_helper::get_url(), $attributes);
      $data['form_end'] = rpd_form_helper::close();
      $data['hidden'] = rpd_form_helper::hidden('grid_action', 'true');
    } else {
      $data['hidden'] = "";
      $data['form_begin'] = "";
      $data['form_end'] = "";
    }
    $data['container'] = $this->button_containers();

    //table rows
    foreach ($this->data as $tablerow)
    {
      unset($row);
      foreach ($this->columns as $column)
      {
        unset($cell);
        $column->reset_pattern();
        $column->set_row($tablerow);

        $cell = get_object_vars($column);
        $cell["value"] = $column->get_value();
        $cell["type"] = $column->column_type;
        $row[] = $cell;
      }
      $data["rows"][] = $row;
    }

    $data["pagination"] = $this->pagination;
    $data["total_rows"] = $this->total_rows;

    return rpd::view('datagrid', $data);
  }

  // --------------------------------------------------------------------

  protected function build_excel()
  {
    $filename = $this->label.".xls";
    header ("Content-Type: application/vnd.ms-excel");
    header ("Content-Disposition: inline; filename=$filename");

    $data = get_object_vars($this);

    //table rows
    foreach ($this->data as $tablerow)
    {
      unset($row);
      foreach ($this->columns as $column)
      {
        unset($cell);
        $column->reset_pattern();
        $column->set_row($tablerow);

        $cell = get_object_vars($column);
        $cell["value"] = $column->get_value();
        $cell["type"] = $column->column_type;
        $row[] = $cell;
      }
      $data["rows"][] = $row;
    }
    $data["total_rows"] = $this->total_rows;
  return rpd::view('datagrid_excell', $data);
  }

  // --------------------------------------------------------------------

  protected function build_csv()
  {
    $output = '';
    $filename = $this->label.".csv";
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
    header("Content-type: csv/xml;");
    header("Content-Disposition: attachment; filename=" . $filename);

    $data = get_object_vars($this);

    foreach ($this->columns as $column)
    {
      $labels[] = $column->label;
    }
    $output .= implode(';',$labels)."\n";

    //rows
    foreach ($this->data as $tablerow)
    {
      unset($values);
      foreach ($this->columns as $column)
      {
        $column->reset_pattern();
        $column->set_row($tablerow);
        $values[] = str_replace('"','""',$column->get_value()); //quota "  come "" (notazione excel)
      }
      $rows[] = '"'.implode('";"',$values).'"';
    }
    $output .= implode("\n",$rows)."\n";
    return mb_convert_encoding($output, 'iso-8859-1', 'utf-8');
  }

  // --------------------------------------------------------------------

  public function build($method = 'grid')
  {
    parent::build();

    //sniff and perform action
    $this->sniff_action();

    foreach ($this->columns as &$column)
    {
      if (isset($column->orderby))
      {
        $column->orderby_asc_url = $this->orderby_link($column->orderby,'asc');
        $column->orderby_desc_url = $this->orderby_link($column->orderby,'desc');
      }
    }

    $method = 'build_'.$method;
    $this->output = $this->$method();
  }

}





class datagrid_column extends rpd_component_library {

  public $url = "";
  public $link = "";
  public $label = "";
  public $attributes = array();
  public $column_type = "normal"; //orderby, detail, ation

  public $orderby = false;
  public $checkbox = "";
  public $check = "";
  public static $checkbox_id = 1;
  public $orderby_asc_url;
  public $orderby_desc_url;

  protected $pattern = "";
  protected $pattern_type = null;

  protected $field = null;
  protected $field_name = null;
  protected $field_list = array();

  // --------------------------------------------------------------------

  public function __construct($config = array())
  {
    parent::__construct($config);
    $this->check_pattern();
  }

  // --------------------------------------------------------------------

  protected function set_pattern($pattern)
  {
    $this->pattern = is_object($pattern)? clone($pattern) : $pattern;
    $this->rpattern = is_object($pattern)? clone($pattern) : $pattern;
  }

  // --------------------------------------------------------------------

  protected function check_pattern()
  {
    if (is_object($this->pattern))
    {
      $this->pattern_type = "field_object";
      $this->field = $this->pattern;
      if ($this->orderby===true)
      {
        $this->orderby = $this->field->name;
      }
    }
    else
    {
      $this->field_list = parent::parse_pattern($this->pattern);
      if (is_array($this->field_list))
      {
        $this->pattern_type = "pattern";
        if ($this->orderby===true)
        {
          $this->orderby = $this->field_list[0];
        }
      }
      else
      {
        $this->pattern_type = "field_name";
        $this->field_name = $this->pattern;
        if ($this->orderby===true)
        {
          $this->orderby = (isset($this->orderby_field)) ? $this->orderby_field : $this->field_name;
        }
      }
    }

    if ($this->orderby)
    {
      $this->column_type = 'orderby';
    }
  }

  // --------------------------------------------------------------------

  function reset_pattern()
  {
    $this->rpattern = $this->pattern;
  }

  // --------------------------------------------------------------------

  function set_row($data_row)
  {

    switch($this->pattern_type)
    {
      case "field_object":
        if(isset($data_row[$this->field->name]))
        {
          $this->field->value = $data_row[$this->field->name];
        } else {
          $this->field->value = "";
        }
        break;

      case "pattern":
          $this->rpattern = $this->replace_pattern($this->rpattern,$data_row);
        break;

      case "field_name":
           if (isset($data_row[$this->field_name]))
           {
            $this->rpattern = $data_row[$this->field_name];
           }
           elseif (array_key_exists($this->field_name, $data_row))
           {
             $this->rpattern = "";
           }
        break;
    }
    if ($this->url)
    {
      if(!isset($this->attributes['style']))
        $this->attributes['style'] = 'width: 70px; text-align:center; padding-right:5px';
      $this->link = parent::replace_pattern($this->url,$data_row);
    }
    if ($this->checkbox!="")
    {
      $value = $data_row[$this->field_name];
      $attributes = array(
          'name'  => $this->field_name.'[]',
          'id'    => $this->field_name.(string)self::$checkbox_id++,
      );
      $this->check = rpd_form_helper::checkbox($attributes, $value);
    }

    $this->attributes = rpd_html_helper::attributes($this->attributes);
  }

  // --------------------------------------------------------------------

  function get_value()
  {
    switch($this->pattern_type)
    {
      case "field_object":
        $this->field->request_refill = false;
        $this->field->status = "show";
        $this->field->build();
        return $this->field->output;
        break;

      case "pattern":
        if ($this->rpattern == "")
        {
          $this->rpattern = "&nbsp;";
        }
        $this->rpattern = parent::replace_functions($this->rpattern); //todo with regex
        return $this->rpattern;
        break;

      case "field_name":
        $this->rpattern = nl2br(htmlspecialchars($this->rpattern));
        if ($this->rpattern == "")
        {
          $this->rpattern = "&nbsp;";
        }
        return $this->rpattern;
        break;
    }
  }

  // --------------------------------------------------------------------

  function orderby_link()
  {
    return str_replace(rawurlencode('{field}'), $column->orderby_field, $this->orderby_asc_url);
  }

}// End Datagrid Column Class