<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');
class dataform_library extends rpd_component_library {
	public $model;
	public $output = "";
	protected $source;
	public $fields = array();
	protected $errors = array();
	//form action, enctype, scripts
	protected $process_url = "";
	protected $multipart = false;
	protected $default_group;
	protected $attributes = array('class' => 'form');
	protected $error_string;
	protected $form_scripts;
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->cid = parent::get_identifier();
		$this->validation = new rpd_validation_library();
		$this->process_url = rpd_url_helper::append('process', 1);
		if (isset($this->model)) {
			$this->status = "create";
			if (isset($this->model)) {
				$this->status = "create";
				$this->validation->model = $this->model;
			}
		}
	}
	// --------------------------------------------------------------------
	protected function set_fields($fields) {
		foreach($fields as $field) {
			$this->set_field($field);
		}
	}
	// --------------------------------------------------------------------
	public function set_field() {
		$field = array();
		if (func_num_args() == 3) {
			list($field['type'], $field['name'], $field['label']) = func_get_args();
		}
		if (func_num_args() == 1) {
			$field = func_get_arg(0);
		}
		if (isset($field['field'])) {
			list($field['type'], $field['name'], $field['label']) = explode('|', $field['field']);
		}
		$field_name = $field["name"];
		//load and instance field
		$field_file = strtolower($field["type"]);
		$field_class = $field_file . '_field';
		$field_obj = new $field_class($field);
		if ($field_obj->type == "upload") {
			$this->multipart = true;
			if (!isset($this->upload)) {
				$this->upload = new rpd_upload_helper();
			}
			$field_obj->upload = $this->upload;
		}
		//share model
		if (isset($this->model)) {
			$field_obj->model = $this->model;
		}
		//default group
		if (isset($this->default_group) && !isset($field_obj->group)) {
			$field_obj->group = $this->default_group;
		}
		$this->fields[$field_name] = $field_obj;
		return $field_obj; //method chaining

	}
	// --------------------------------------------------------------------
	protected function set_style($style) {
		$this->set_attributes(array('style' => $style));
	}
	// --------------------------------------------------------------------
	public function set_source($source) {
		//instance or reuse a model
		if (is_object($source) and (get_class($source) == 'datamodel' OR is_subclass_of($source, "datamodel"))) {
			$this->model = $source;
		} elseif (is_string($source)) {
			$this->model = new datamodel_model($source);
		} else {
			$this->show_error('datamodel non valido');
			die();
		}
		if (count($this->fields)) {
			foreach($this->fields as $field_obj) {
				if (in_array($field_obj->name, $this->model->field_names())) {
					$field_obj->model = $this->model;
				}
			}
		}
		return $this->model; //method chaining

	}
	// --------------------------------------------------------------------
	public function load($id) {
		if (isset($this->model)) {
			$this->model->load($id);
		}
	}
	// --------------------------------------------------------------------
	public function build_fields() {
		foreach($this->fields as $field) {
			//share status
			$field->status = $this->status;
			$field->build();
		}
	}
	// --------------------------------------------------------------------
	function pre_process($action, $function, $arr_values = array()) {
		$this->model->pre_process($action, $function, $arr_values);
	}
	function post_process($action, $function, $arr_values = array()) {
		$this->model->post_process($action, $function, $arr_values);
	}
	// --------------------------------------------------------------------
	protected function build_form() {
		rpd_html_helper::css('dataform.css');
		$data = get_object_vars($this);
		$data['container'] = $this->button_containers();
		$form_type = 'open';
		// See if we need a multipart form
		foreach($this->fields as $field_obj) {
			if ($field_obj instanceof upload_field) {
				$form_type = 'open_multipart';
				break;
			}
		}
		// Set the form open and close
		if ($this->status_is('show')) {
			$data['form_begin'] = '<div class="form">';
			$data['form_end'] = '</div>';
		} else {
			$data['form_begin'] = rpd_form_helper::$form_type($this->process_url, $this->attributes);
			$data['form_end'] = rpd_form_helper::close();
		}
		$data['fields'] = $this->fields;
		return rpd::view('dataform', $data);
	}
	// --------------------------------------------------------------------
	public function build($method = 'form') {
		//detect form status (output)
		if (isset($this->model)) {
			$this->status = ($this->model->loaded) ? "modify" : "create";
		} else {
			$this->status = "create";
		}
		//build fields
		$this->build_fields();
		//process only if instance is a dataform
		if (is_a($this, 'dataform_library')) {
			//build buttons
			$this->build_buttons();
			//sniff action
			if (isset($_POST) && (rpd_url_helper::value('process'))) {
				$this->action = ($this->status == "modify") ? "update" : "insert";
			}
			//process
			$this->process();
		}
		$method = 'build_' . $method;
		$this->output = $this->$method();
	}
	// --------------------------------------------------------------------
	protected function is_valid() {
		//some fields mode can disable or change some rules.
		foreach($this->fields as $field) {
			$field->action = $this->action;
			$field->get_mode();
			if (isset($field->rule)) {
				if (($field->type != "upload") && $field->apply_rules) {
					$fieldnames[$field->name] = $field->label;
					$rules[$field->name] = $field->rule;
				} else {
					$field->required = false;
				}
			}
		}
		if (isset($rules)) {
			$this->validation->set_rules($rules);
			$this->validation->set_fields($fieldnames);
			if (count($_POST) < 1) {
				$_POST = array(1);
			}
		} else {
			return true;
		}
		$result = $this->validation->run();
		$this->error_string = $this->validation->error_string;
		return $result;
	}
	// --------------------------------------------------------------------
	protected function process() {
		//database save
		switch ($this->action) {
			case "update":
			case "insert":
				//validation failed
				if (!$this->is_valid()) {
					$this->process_status = "error";
					foreach($this->fields as $field) {
						$field->action = "idle";
					}
					return false;
				} else {
					$this->process_status = "success";
				}
				foreach($this->fields as $field) {
					$field->action = $this->action;
					$result = $field->auto_update();
					if (!$result) {
						$this->process_status = "error";
						$this->error_string = $field->save_error;
						return false;
					}
				}
				if (isset($this->model)) {
					$return = $this->model->save();
				} else {
					$return = true;
				}
				if (!$return) {
					if ($this->model->preprocess_result === false) {
						if ($this->action_is("update")) {
							$this->error_string.= $this->model->errors['pre_update'];
						} else {
							$this->error_string.= $this->model->errors['pre_insert'];
						}
					}
					$this->process_status = "error";
				}
				return $return;
			break;
			case "delete":
				$return = $this->model->delete();
				if (!$return) {
					if ($this->model->preprocess_result === false) {
						$this->error_string.= $this->model->errors['pre_delete'];
					}
					$this->process_status = "error";
				} else {
					$this->process_status = "success";
				}
			break;
			case "idle":
				$this->process_status = "show";
				return true;
			break;
			default:
				return false;
		}
	}
	// --------------------------------------------------------------------
	public function save_button($config = null) {
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.save');
		$this->submit("btn_submit", $caption, "BL");
	}
	// --------------------------------------------------------------------
	protected function show_error($message) {
		echo '<p>' . implode('</p><p>', (!is_array($message)) ? array($message) : $message) . '</p>';
	}
	// --------------------------------------------------------------------
	public function nest($field_id, $content) {
		if ($this->output != "") {
			$nesting_point = 'id="' . $field_id . '">';
			$this->output = str_replace($nesting_point, $nesting_point . $content, $this->output);
		}
	}
}
