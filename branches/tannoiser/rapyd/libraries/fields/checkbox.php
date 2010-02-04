<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class checkbox_field extends field_field {

  public $type = "checkbox";
  public $size = null;
  public $checked = false;
  public $css_class = "checkbox";
  public $checked_value = 1;
  public $unchecked_value = 0;

  //per il css  "vertical-align:middle";

  function get_value()
  {
    parent::get_value();

    if (!isset($_POST[$this->name]))
    {
      $this->value = $this->unchecked_value;
    }

    $this->checked = (bool)($this->value == $this->checked_value);
  }

  function get_new_value()
  {
    parent::get_new_value();
    if (!isset($_POST[$this->name]))
    {
     $this->new_value = $this->unchecked_value;
    }
  }

  function build()
  {
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":
        if (!isset($this->value)){
          $output = $this->layout['null_label'];
        } elseif ($this->value == ""){
          $output = "";
        } else {
          $output = $this->value;
        }
        break;

      case "create":
      case "modify":
            $output = form::checkbox($this->attributes, $this->checked_value , $this->checked).$this->extra_output;
        break;

      case "hidden":
            $output = form::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
