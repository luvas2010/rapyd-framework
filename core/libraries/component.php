<?php

if (!defined('RAPYD_PATH'))
	exit('No direct script access allowed');

/**
 * Component library, is the ancestor of all widgets
 * 
 *
 * @package    Core
 * @author     Felice Ostuni
 * @copyright  (c) 2011 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class rpd_component_library
{

	public static $identifier = 0;
	public $db;
	public $process_status = "idle";
	public $status = "idle";
	public $action = "idle";
	public $label = "";
	public $output = "";
	public $built = FALSE;
	public $button_container = array("TR" => array(), "BL" => array(), "BR" => array());
	public $buttons = array();
	public $url;

	public function __construct($config = array())
	{
		if (is_string($config))
		{
			$ext = end(explode('.', $config));
			switch ($ext)
			{
				case "php":
					include($config);
					break;

				default:
					$config = array();
			}
		}
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
	}

	/**
	 * called by constructor, a widget can be built using an associative array to prefill each widget property
	 * it works calling $widget->set_$key($val) for each item in config array.
	 * since each widget is initialized this way, you can for example build a complex "dataedit" 
	 * using a sigle multi-dimensional config array, defining form properties and fields properties
	 * (so this configuration can be stored or manipulated in an easy way)
	 *  
	 * <code>
	 * //edit
	 * $config = array	(
	 * 	'label'  => 'Manage Article',
	 * 	'source' => 'articles',
	 * 	'back_url' => $this->url('filtered_grid'),
	 * 	'fields' => array (
	 * 			array (
	 * 				'type'  => 'input',
	 * 				'name'  => 'title',
	 * 				'label' => 'Title',
	 * 				'rule'  => 'trim|required',
	 * 			),
	 * 			
	 * 		),
	 * 	'buttons'  => array('modify','save','undo','back'),
	 * );
	 * $edit = new dataedit_library($config);
	 * $edit->build();
	   * </code>
	 * 
	 * @param array $config 
	 */
	public function initialize($config = array())
	{
		$this->clear();
		foreach ($config as $key => $val)
		{
			$method = 'set_' . $key;
			$this->$method($val);
		}
	}

	/**
	 * connect to the database and fill $widget->db with database "active record" reference
	 */
	public function connect()
	{
		rpd::connect();
		$this->db = rpd::$db;
	}

	/**
	 * reset each property to default values
	 */
	public function clear()
	{
		$vars = get_class_vars(get_class($this));

		foreach ($vars as $property => $value)
		{
			if (!in_array($property, array("cid", "built")))
			{
				$this->$property = $value;
			}
		}
	}

	/**
	 * identifier is empty or a numeric value, it "identify" a single object instance.
	 * by default if you build 3 widget in a single controller/page their id will be:
	 * "" for ther first one
	 * 1 for the second one
	 * 2 for the third
	 * .. etcetera
	 * 
	 * identifiers are used to preserve uri/url segments consistence/isolation 
	 * so for example you can build 2 datagrid on a single controller without problem because uri-semantic will be:
	 * /controller/method/[[orderby/article_id/desc/pag/1]]/[[orderby1/anotherfield/desc/pag1/2]]
	 *  
	 * @return string identifier 
	 */
	protected function get_identifier()
	{
		if (self::$identifier < 1)
		{
			self::$identifier++;
			return "";
		}
		return (string) self::$identifier++;
	}

	/**
	 * dynamic getter & setter 
	 * 
	 * it's used basically to ensure method chaining and to get & set widget's properties
	 * it also enable "short array" syntax  so you can use $widget->method('param|param') and it will call
	 * $widget->set_method('param','param')
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return object $this 
	 */
	public function __call($method, $arguments)
	{
		$prefix = strtolower(substr($method, 0, 4));
		$property = strtolower(substr($method, 4));
		if (method_exists($this, 'set_' . $method))
		{
			return call_user_func_array(array($this, 'set_' . $method), $arguments);
		}

		if (empty($prefix) || empty($property))
		{
			return;
		}

		if ($prefix == "get_" && isset($this->$property))
		{
			return $this->$property;
		}

		if ($prefix == "set_")
		{
			if
			(
					!in_array($property, array('cell_template', 'pattern'))
					AND is_string($arguments[0])
					AND strpos($arguments[0], '|')
			)
			{
				//die ($property);
				$this->$property = explode('|', $arguments[0]);
			} else
			{
				$this->$property = $arguments[0];
			}
			return $this;
		}
	}

	/**
	 * shortcut for $widget->status == $status OR ....
	 * so you can use $widget->status_is($status) 
	 * where $status can be an array of valid status
	 * not very useful
	 * 
	 * @todo merge all "property_is/on" in one call
	 * @param string $status
	 * @return bool 
	 */
	public function status_is($status = "false")
	{
		if (is_array($status))
			return (bool) in_array($this->status, $status);
		return ($this->status == $status);
	}

	/**
	 * shortcut for $widget->process_status == $process_status OR ....
	 * so you can use $widget->on($process_status) 
	 * where $process_status can be an array of valid status
	 * 
	 * @todo merge all "property_is/on" in one call
	 * @param string $process_status
	 * @return bool 
	 */
	public function on($process_status = "false")
	{
		if (is_array($process_status))
			return (bool) in_array($this->process_status, $process_status);
		return ($this->process_status == $process_status);
	}

	/**
	 * shortcut for $widget->action == $action OR ....
	 * so you can use $widget->action_is($action) 
	 * where $process_status can be an array of valid actions
	 * not very useful
	 * 
	 * @todo merge all "property_is/on" in one call
	 * @param string $process_status
	 * @return bool 
	 */
	public function action_is($action = "false")
	{
		if (is_array($action))
			return (bool) in_array($this->action, $action);
		return ($this->action == $action);
	}

	/**
	 * alias for action_is
	 * 
	 * @param string $action
	 * @return bool 
	 */
	public function when($action = "false")
	{
		return $this->action_is($action);
	}

	/**
	 * important stuff, widgets support placeholders like: {placeholder}
	 * parse_pattern find all occurences and return a simple array of matches
	 * it's used for example to find "field" placeholders inside a datagrid column pattern
	 * 
	 * @param string $pattern
	 * @return array of matches {placeholders} 
	 */
	public static function parse_pattern($pattern)
	{
		if (preg_match_all('/\{(\w+)\}/is', $pattern, $matches))
		{
			return $matches[1];
		}
	}

	/**
	 * replace placeholders in a pattern like "bla bla.. {placeholder}"
	 * 
	 * @param string $pattern a string containing {placeholder}s
	 * @param array $values  associative array like array('placeholder'=>'value', 'placeholder2'=>'value2')
	 * @return string parsed output 
	 */
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
					$output = str_replace('{' . $field . '}', $values[$field], $output);
			}
		}
		return $output;
	}

	/**
	 * important stuff, widgets support function patterns like
	 * <function_name>param|param|{placeholder}</function_name>
	 * so if "function_name" is a valid "formatting_function" the widget will execute
	 * a callback splitting parameters using | (pipe)
	 * it's used for example to let you format datagrid columns in really easy way:
	 * 
	 * <code>
	 * $grid->column('<substr>0|30|{name}</substr>',"Name"); //will output a column with first 30 chars of field "name"
	 * </code>
	 * 
	 * @param string $content
	 * @param type $functions
	 * @return string 
	 */
	public function replace_functions($content, $functions='ALL')
	{
		$formatting_functions = array("rpd[^>]+",
			"htmlspecialchars", "htmlentities", "utf8_encode",
			"strtolower", "strtoupper","str_replace",
			"substr", "strpos", "nl2br", "number_format",
			"dropdown", "radiogroup", "date", "strtotime"
		);

		if (is_string($functions) AND $functions == 'ALL')
		{
			$arr = get_defined_functions();
			$functions = array_merge($formatting_functions, $arr["user"]);
		} elseif (is_array($functions))
		{
			$functions = array_merge($formatting_functions, $functions);
		} elseif (!isset($functions))
		{
			$functions = $formatting_functions;
		}

		$tags = join('|', $functions);
		while (preg_match_all("/(<($tags)>(.*)<\/\\2>)/isU", $content, $matches, PREG_SET_ORDER))
		{
			foreach ($matches as $found)
			{
				$params = $found[3];
				//check if a recustion is needed
				if (preg_match("/<\/$tags>/is", $params))
					$params = self::replace_functions($params, $functions);

				$toreplace = $found[0];
				$function = $found[2];
				$arguments = explode("£|£", $params);

				if (in_array($function, array('dropdown', 'radiogroup')))
				{
					$replace = call_user_func_array(array($this, $function), $arguments);
				}
				//static class/metdod
				elseif (strpos($function, "::") !== FALSE)
				{
					list($static_class, $static_method) = explode("::", $function);
					$replace = call_user_func_array(array($static_class, $static_method), $arguments);
				}
				//dynamic object/method
				elseif (strpos($function, ".") !== FALSE)
				{
					list($class, $method) = explode(".", $function);
					$replace = call_user_func_array(array($$class, $method), $arguments);
				} else
				{
					$replace = @call_user_func_array($function, $arguments);
				}

				$content = str_replace($toreplace, $replace, $content);
			}
		}
		return $content;
	}

	/**
	 * old stuff, however.. untested on new versions.. 
	 * basically let you use a dropdown instance as pattern for datagrid columns
	 * may be useful in datafilter+datagrid context where you already buit a dropdown..
	 * it can be uset directly as datagrid column (it work comparind all dropdown loaded options)
	 * not very useful, it's is a lot more clean perform joins
	 * 
	 * <code>
	 * $filter->field('dropdown','author_id','Author')->options('SELECT ....
	 * ...
	 * $grid->column($filter->fields['author_id'],"Name"); //if $dropdown is a  a column with first 30 chars of field "name"
	 * </code>
	 * 
	 * @param object $field
	 * @param string $id
	 * @return string 
	 */
	public function dropdown($field, $id)
	{
		if (is_object($this->source))
		{
			return $this->source->fields[$field]->options[$id];
		}
	}

	/**
	 * same concept of dropdown, deprecaded too
	 * 
	 * @param object $field
	 * @param string $id
	 * @return string 
	 */
	public function radiogroup($field, $id)
	{
		return $this->dropdown($field, $id);
	}

	/**
	 * basically it "flat" buttons array for each zone 
	 * used on build() to prepare button output
	 * zones are "TL","TR","BL","BR" (top-left, top-right, bottom-left, bottom-right)
	 * see button()
	 * 
	 * @todo i think it must be improved.. 
	 * @param string $container
	 * @return array 
	 */
	public function button_containers($container = null)
	{
		if (isset($container))
		{
			return join("&nbsp;", $this->button_container[$container]);
		} else
		{
			foreach ($this->button_container as $container => $content)
			{
				$containers[$container] = join("&nbsp;", $this->button_container[$container]);
			}
			return $containers;
		}
	}

	/**
	 * add a button to the button container in a given position 
	 * positions are "TL","TR","BL","BR" (top-left, top-right, bottom-left, bottom-right)
	 * 
	 * @param string $name button name attribute
	 * @param string $caption button label
	 * @param string $action the onclick event (all buttons currently works using window.location.href=...)
	 * @param string $position "TL","TR","BL","BR"
	 * @param string $class  css class
	 */
	function button($name, $caption, $action, $position="BL", $class="button")
	{
		$this->button_container[$position][] = rpd_html_helper::button($name, $caption, $action, "button", $class);
	}

	/**
	 * same of button() but it make a submit
	 * 
	 * @param string $name
	 * @param string $caption
	 * @param string $position 
	 */
	function submit($name, $caption, $position="BL")
	{
		$this->button_container[$position][] = rpd_html_helper::button($name, $caption, "", "submit", "button");
	}

	/**
	 *
	 * @param type $config 
	 */
	function action_button($config)
	{

		$caption = (isset($config['caption'])) ? $config['caption'] : "Azione";
		if (isset($config['popup']) || isset($config['target']))
		{
			$config['popup'] = (isset($config['popup'])) ? ",'mywin','" . $config['popup'] . "'" : "";
			$action = "javascript:window.open('" . $config['url'] . "'" . $config['popup'] . ")";
		} else
		{
			if (isset($config['confirm']))
			{
				$action = "javascript:if (confirm('" . addslashes($config['confirm']) . "')) { window.location='" . $config['url'] . "' }";
			} else
			{
				$action = "javascript:window.location='" . $config['url'] . "'";
			}
		}
		$position = (isset($config['position'])) ? $config['position'] : "TR";
		$class = (isset($config['class'])) ? $config['class'] : "button";
		$this->button("btn_act", $caption, $action, $position, $class);
	}


	/**
	 * it exppect parameters like a string, an array, a serialized array 'save|delete|undo...' 
	 * or an associative array, and fill the "$widget->buttons" array
	 * then buil_buttons will cycle this array to make buttons
	 * 
	 */
	public function set_buttons()
	{

		$buttons = func_get_args();

		//catch buttons => 'save|delete|undo...';
		if (is_string($buttons[0]) AND strpos($buttons[0], '|'))
		{
			$buttons = explode('|', $buttons[0]);
		}

		if (func_num_args() == 1 AND is_array($buttons[0]))
		{
			$buttons = $buttons[0];
		}

		foreach ($buttons as $name => $button)
		{
			if (is_numeric($name) and is_string($button))
			{
				if (strpos($button, '|'))
				{
					list($button['type'], $button['caption']) = explode('|', $button);
				} else
				{
					$button = array('type' => $button);
				}
			} else
			{
				if (is_string($button) AND strpos($button, '|'))
				{
					$btn = array();
					@list($btn['name'], $btn['caption'], $btn['url'], $btn['position'], $btn['class']) = explode('|', $button);
					$button = $btn;
				}
				if (!isset($button['type']))
					$button['type'] = $name;
			}

			$this->buttons[] = $button;
		}
	}


	/**
	 * this function cycle $widget->buttons array and call all "xxx_button()" methods
	 * 
	 * @todo I should really consider moving it from here
	 */
	function build_buttons()
	{
		foreach ($this->buttons as $button)
		{
			$build_button = $button['type'] . "_button";
			if (count($button) < 2)
			{
				$this->$build_button();
			} else
			{
				$this->$build_button($button);
			}
		}
		$this->buttons = array();
	}

	/**
	 * "echo $widget" automatically call build() it and display $widget->output
	 * however explicit build is preferred for a clean code
	 * 
	 * @return string 
	 */
	function __toString()
	{
		if ($this->output == "")
			$this->build();
		return $this->output;
	}

}

