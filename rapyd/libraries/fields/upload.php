<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class upload_field extends field_field {

  public $type = "upload";
  public $css_class = "input";


  //user preferences
  public $delete_file = true;
  public $www_path;
  public $upload_path;
  public $upload_root;
  public $preview;
  public $overwrite = false;
  public $remove_spaces = true;

  public $upload_data; //array with all data of uploaded file
  public $upload_error; //error messages

  public $thumbnail; //array($maxwidth, $maxheight) for thumbnail creation (when file is a jpg)

  public $allowed_types;
  public $max_size;
  public $max_width;
  public $max_height;
  public $popup_params = ',width=500,height=500';

  protected function server_path($docroot)
  {
    $base = ($docroot != "")?$docroot:$_SERVER["DOCUMENT_ROOT"];
    return $base . $this->upload_path;
  }


  function draw_link()
  {
    if (isset($this->preview))
    {
      return $this->draw_preview_link();
    }
    elseif (isset($this->thumb))
    {
      $this->preview = $this->thumb;
      return $this->draw_preview_link();
    }
    return $this->draw_upload_link();
  }


  function draw_upload_link()
  {
    if ($this->www_path=="")
    {
      $this->www_path = $this->upload_path;
    }
    $action = "javascript:window.open('".$this->www_path.$this->value."','".$this->name."','".$this->popup_params.",');";
    return  '<a onclick="'.$action.'" href="javascript:void(0);">'.$this->value.'</a>';
  }


  function draw_preview_link()
  {
/*
    if (is_array($this->preview))
    {
      $width  = $this->preview[0];
      $height = $this->preview[1];
    } else {
      $width  = $this->preview;
      $height = "";
    }
    if ($this->www_path=="")
    {
      $this->www_path = $this->upload_path;
    }
    $action = "javascript:window.open('".$this->www_path.$this->value."','".$this->name."','".$this->winParams.",width=".$this->winWidth.",height=".$this->winHeight."');";
    $web_path = $this->www_path.$this->value;
    if (file_exists($this->server_path($this->upload_root)."/".thumb_name($this->value))){
        $web_path = $this->www_path.thumb_name($this->value);
    } else {
        $web_path = $this->www_path.$this->value;
    }
    return  '<a onclick="'.$action.'" href="javascript:void(0);"><img src="'.$web_path.'" width="'.$width.'" border="0" /></a>';
*/
  }




  function exec_upload()
  {
    $this->get_value();

    $config['upload_path'] = $this->upload_path; //$this->server_path($this->upload_root);
    $config['overwrite'] = $this->overwrite;
    $config['remove_spaces'] = $this->remove_spaces;

    if (isset($this->allowed_types)) $config['allowed_types'] = $this->allowed_types;
    if (isset($this->max_size)) $config['max_size']	= $this->max_size;
    if (isset($this->max_width)) $config['max_width']  = $this->max_width;
    if (isset($this->max_height))  $config['max_height']  = $this->max_height;

    $config['quality']  = 100;

    $this->upload->initialize($config);

    if ($this->upload->do_upload($this->name."_user_file"))
    {
      $this->upload_data = $this->upload->data();

/*      if (isset($this->thumb))
      {
        if (is_array($this->thumb))
        {
		      $config['width'] = $this->thumb[0];
		      $config['height'] = $this->thumb[1];
        } else {
		      $config['width'] = $this->thumb;
        }
		    $config['image_library'] = 'GD2';
		    $config['source_image'] = $this->server_path($this->upload_root).'/'.$this->upload_data["file_name"];
		    $config['create_thumb'] = TRUE;
		    $config['maintain_ratio'] = TRUE;

		    $this->image_lib->initialize($config);
		    $this->image_lib->resize();
		  }
*/
      return true;

    } else {
      $this->save_error = $this->label .": ".$this->upload->display_errors();
      return false;
    }



  }

  function exec_unlink()
  {
    $this->get_value();

    if ($this->delete_file)
    {
      $filename = $this->value;
      @unlink($this->upload_path.$filename);
      //@unlink($this->server_path($this->upload_root)."/".thumb_name($filename));
    }
  }


  function auto_update($store=false)
  {
    $this->get_value();

			//required
			if ( ($_POST[$this->name] == "") && ($_FILES[$this->name."_user_file"]["name"] == "") ||
         ((isset($_POST[$this->name."_checkbox"])) && ($_POST[$this->name."_checkbox"] == "True")) )
			{

				if (isset($this->rule) && ($this->rule=="required"))
				{
					$this->save_error = sprintf("Il campo \"%s\" deve contentere un valore.", $this->label);
					return false;
				}
			}

      if ((($this->action == "update") || ($this->action =="insert")) )
      {
        if ($_FILES[$this->name."_user_file"]["name"]=="")
        {
          if ( isset($_POST[$this->name."_checkbox"]) )
          {
            if ($_POST[$this->name."_checkbox"] == "True")
            {
              $this->exec_unlink();
              $this->new_value = null;
            }
          } else {
            $this->new_value = $this->value;
          }
        } else {
          if ($this->exec_upload())
          {
            $this->new_value = $this->upload_data["file_name"];
          } else {
            return false;
          }
        }

        if (isset($this->model) AND is_object($this->model))
        {
           $this->model->set($this->name,$this->new_value);
           if($store) $this->model->save();
        }
      }
      return true;
  }



  function build()
  {
    $output = "";

    if(!isset($this->style))
    {
      $this->style = "width:290px;";
    }
    if(!isset($this->size))
    {
      $this->size = null;
    }

    unset($this->attributes['type'],$this->attributes['size']);
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "show":
      case "disabled":

        if ((!isset($this->value)) || ($this->value == ""))
        {
          $output = $this->layout['null_label'];
        } else {
          $output = $this->draw_link();
        }
        break;

      case "create":
      case "modify":

        $output = '<div style="">';
        if (!(!isset($this->value) || ($this->value == "")) )
        {
          $output .= $this->draw_link();
          $output .= "&nbsp;-&nbsp;";
            $attributes = array(
                    'name'        => $this->name . "_checkbox",
                    'id'          => $this->name . "_checkbox",
                    'value'       => 'True',
                    'checked'     => false,
                    'style'       => "vertical-align:middle;");
          $output .= rpd_form_helper::checkbox($attributes)." rimuovi<br />\n";
        }

       $output .= "cerca<br />\n";
       $output .= rpd_form_helper::hidden($this->name, $this->value);

          $attributes = array(
            'name'        => $this->name . "_user_file",
            'id'          => $this->name . "_user_file",
            'size'        => $this->size,
            'onclick'     => $this->onclick,
            'onchange'    => $this->onchange,
            'class'       => $this->css_class,
            'style'       => $this->style);
        $output .= rpd_form_helper::upload($attributes);
        $output .= "</div>" .$this->extra_output;

        break;

      case "hidden":

        $output = rpd_form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}