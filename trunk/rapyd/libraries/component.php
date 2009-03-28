<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class rpd_component_library {

	public static $identifier = 0;
	public $db;

	public $process_status = "idle";
	public $status = "idle";
	public $action = "idle";

	public $label = "";
	public $output = "";
	public $built = FALSE;
	public $button_container = array( "TR"=>array(), "BL"=>array(), "BR"=>array() );
	public $buttons = array();

	public function __construct($config = array())
	{
		if (is_string($config))
		{
			$ext = end(explode('.', $config));
			switch($ext)
			{
				case "php":
					  include($config);
					break;

				/*case "yaml":
					  rpd::load_library('vendors/spyc');
					  $config = Spyc::YAMLLoad($config);
					break;*/
				default:
				  $config = array();
			}
		}
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
	}

  // --------------------------------------------------------------------

	public function initialize($config = array())
	{
		$this->clear();
		foreach ($config as $key => $val)
		{
			$method = 'set_'.$key;
			$this->$method($val);
		}
	}

  // --------------------------------------------------------------------

	public function connect()
	{
		rpd::connect();
		$this->db = rpd::$db;
	}


  // --------------------------------------------------------------------

	public function clear()
	{
		$vars = get_class_vars(get_class($this));

		foreach($vars as $property=>$value)
		{
			if (!in_array($property,array("cid","built")))
			{
				$this->$property = $value;
			}
		}
	}

  // --------------------------------------------------------------------

	protected function get_identifier()
	{
		if (self::$identifier<1)
		{
			self::$identifier++;
			return "";
		}
		return (string)self::$identifier++;
	}

  // --------------------------------------------------------------------

	//dynamic getter & setter  set_property(value);  get_property();
  public function __call($method, $arguments)
	{
		$prefix = strtolower(substr($method, 0, 4));
		$property = strtolower(substr($method, 4));

		if (empty($prefix) || empty($property)) {
				return;
		}

		if ($prefix == "get_" && isset($this->$property)) {
				return $this->$property;
		}

		if ($prefix == "set_") {
			  if
				(
					!in_array($property,array('cell_template','pattern'))
					AND is_string($arguments[0])
					AND strpos($arguments[0],'|')
				)
				{
					//die ($property);
					$this->$property = explode('|',$arguments[0]);
				}
				else
				{
					$this->$property = $arguments[0];
				}

		}
	}

  // --------------------------------------------------------------------

	public function status_is($status = "false")
	{
		if (is_array($status)) return (bool)in_array($this->status, $status);
		return ($this->status == $status);
	}

  // --------------------------------------------------------------------

	public function on($process_status = "false")
	{
		if (is_array($process_status)) return (bool)in_array($this->process_status, $process_status);
		return ($this->process_status == $process_status);
	}

  // --------------------------------------------------------------------

	public function action_is($action = "false")
	{
		if (is_array($action)) return (bool)in_array($this->action, $action);
		return ($this->action == $action);
	}

	public function when($action = "false")
	{
    return action_is($action);
	}

  // --------------------------------------------------------------------

  public static function parse_pattern($pattern)
	{
		if (preg_match_all('/\{(\w+)\}/is', $pattern, $matches))
		{
			return $matches[1];
    }
  }

  public static function replace_pattern($pattern, $values)
	{
		$output = $pattern;
    $output = str_replace('|', '£|£', $output); //fix for params separator
		$matches = self::parse_pattern($pattern);
		if ($matches)
		{
			foreach ($matches as $field)
			{
				if (isset($values[$field]) or is_null($values[$field]))
				$output = str_replace('{'.$field.'}', $values[$field], $output);
			}
		}
		return $output;
  }

  // --------------------------------------------------------------------

  public function replace_functions($content, $functions='ALL')
  {
    $formatting_functions = array("htmlspecialchars","htmlentities",
                                  "strtolower","strtoupper",
                                  "substr","strpos","nl2br", "number_format",
                                  "dropdown", "radiogroup"
                                  );

    if  (is_string($functions) AND $functions=='ALL')
    {
      $arr = get_defined_functions();
      $functions = array_merge($formatting_functions,$arr["user"]);
    }
    elseif(is_array($functions))
    {
      $functions = array_merge($formatting_functions,$functions);
    }
    elseif(!isset($functions))
    {
      $functions = $formatting_functions;
    }

    $tags = join('|',$functions);
    while (preg_match_all("/(<($tags)>(.*)<\/\\2>)/isU", $content, $matches,PREG_SET_ORDER))
    {
      foreach($matches as $found)
      {
        $params = $found[3];
        //check if a recustion is needed
        if (preg_match("/<\/$tags>/is",$params)) $params = self::replace_functions($params, $functions);

        $toreplace = $found[0];
        $function = $found[2];
        $arguments = explode("£|£",$params);

        if (in_array($function,array('dropdown','radiogroup')))
        {
          $replace = call_user_func_array(array($this, $function), $arguments);
        }
        elseif (strpos($function,"::")!==FALSE)
        {
          list($static_class,$static_method) = explode($function);
          $replace = call_user_func_array(array($static_class,$static_method), $arguments);
        }
        else
        {
          $replace = call_user_func_array($function, $arguments);
        }

        $content = str_replace($toreplace, $replace, $content);

      }
    }
    return $content;
  }

  // --------------------------------------------------------------------

  public function dropdown($field,$id)
  {
    if(is_object($this->source) )
    {
      return $this->source->fields[$field]->options[$id];
    }
  }

  // --------------------------------------------------------------------

  public function radiogroup($field,$id)
  {
    return $this->dropdown($field,$id);
  }

  // --------------------------------------------------------------------

  public function button_containers($container = null)
	{
		if (isset($container))
		{
		  return join("&nbsp;", $this->button_container[$container]);
		}
		else
		{
			foreach ($this->button_container as $container => $content)
			{
			  $containers[$container] = join("&nbsp;", $this->button_container[$container]);
			}
			return $containers;
		}

  }

	// --------------------------------------------------------------------

  function button($name, $caption, $action, $position="BL", $class="button")
  {
    $this->button_container[$position][] = rpd_html_helper::button($name, $caption, $action, "button", $class);
  }

	// --------------------------------------------------------------------

  function submit($name, $caption, $position="BL")
  {
     $this->button_container[$position][] = rpd_html_helper::button($name, $caption, "", "submit", "button");
  }

  // --------------------------------------------------------------------

  function action_button($config)
  {

    $caption = (isset($config['caption'])) ? $config['caption'] : "Azione";
    if (isset($config['popup']) || isset($config['target']))
    {
      $config['popup'] = (isset($config['popup'])) ?  ",'mywin','".$config['popup']."'" : "";
      $action = "javascript:window.open('".$config['url']."'".$config['popup'].")";
    }
    else
    {
      if (isset($config['confirm']))
      {
        $action = "javascript:if (confirm('".addslashes($config['confirm'])."')) { window.location='".$config['url']."' }";
      }
      else
      {
        $action = "javascript:window.location='".$config['url']."'";
      }

    }
    $position = (isset($config['position'])) ? $config['position'] : "TR";
    $class = (isset($config['class'])) ? $config['class'] : "button";
    $this->button("btn_act", $caption, $action, $position, $class);
  }


	// --------------------------------------------------------------------

  public function set_buttons()
  {

    $buttons = func_get_args();

		//catch buttons => 'save|delete|undo...';
		if (is_string($buttons[0]) AND strpos($buttons[0],'|'))
		{
			$buttons = explode('|', $buttons[0]);
		}
		else
		{
      $buttons = $buttons[0];
    }


    foreach($buttons as $name=>$button)
    {
      if (is_numeric($name) and is_string($button))
      {
				if (strpos($button,'|'))
				{
					list($button['type'],$button['caption']) = explode('|',$button);
				}
				else
				{
				  $button = array('type'=>$button);
				}
      }
      else
      {
				if (is_string($button) AND strpos($button,'|'))
				{
					$btn = array();
					@list($btn['name'],$btn['caption'],$btn['url'],$btn['position'],$btn['class']) = explode('|',$button);
					$button = $btn;
				}
        if (!isset($button['type'])) $button['type'] = $name;
      }

			$this->buttons[] = $button;
    }
  }

  // --------------------------------------------------------------------

  function build_buttons()
  {
    foreach($this->buttons as $button)
    {
      $build_button = $button['type']."_button";
      if (count($button)<2){
        $this->$build_button();
      } else {
        $this->$build_button($button);
      }

    }
    $this->buttons = array();
  }

  // --------------------------------------------------------------------

  function __toString()
  {
    if ($this->output == "")
			$this->build();
		return $this->output;
  }


}
