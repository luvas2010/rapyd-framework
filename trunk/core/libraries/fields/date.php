<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');



class date_field extends field_field {

  public $type = "date";
  public $css_class = "input";
  public $clause = "where";

	// --------------------------------------------------------------------

  public function get_new_value()
  {
    parent::get_new_value();
    if (isset($_POST[$this->name]))
    {
      $this->new_value = rpd_date_helper::human2iso($this->new_value);
    }
  }

	// --------------------------------------------------------------------

  function build()
  {
		$output = "";
    rpd_html_helper::css('jquery/smoothness.datepick.css');
    rpd_html_helper::js('jquery/jquery.min.js');
    rpd_html_helper::js('jquery/jquery.datepick.pack.js');
	if (!in_array(rpd::get_lang('locale'),array('en_US','en_GB')))
		rpd_html_helper::js('jquery/jquery.datepick.'.rpd::get_lang('locale').'.js');

    if(!isset($this->size))
    {
      $this->size = 25;
    }

    if (parent::build() === false) return;

    switch ($this->status)
    {

      case "show":
        if (!isset($this->value))
        {
          $value = $this->layout['null_label'];
        } elseif ($this->value == ""){
          $value = "";
        } else {
          $value = rpd_date_helper::iso2human($this->value);
        }
        $output = $value;
        break;

      case "create":
      case "modify":

        $value = "";
        if ($this->value != ""){
           if ($this->is_refill){
             $value = $this->value;
           } else {
             $value = rpd_date_helper::iso2human($this->value);
           }
        }
        $this->attributes['type'] = 'input';
        $output  = rpd_form_helper::input($this->attributes, $value);
        $output .= rpd_html_helper::script('
			$(function() {
				$("#'.$this->name.'").datepick();
			});');

        break;

      case "disabled":
        //versione encoded
        $output = rpd_date_helper::iso2human($this->value);
        break;

      case "hidden":
        $output =  rpd_form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
