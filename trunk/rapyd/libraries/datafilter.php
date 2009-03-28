<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');

//rpd::load('component','dataform');

class datafilter_library extends dataform_library {


	public function __construct($config = array())
	{
    parent::__construct($config);
		$this->connect();

		if (isset($this->source))
    {
			$this->db->select('*');
      $this->db->from($this->source);
		}

    $this->status = 'create';
    //sniff current action
    $this->sniff_action();

  }

	// --------------------------------------------------------------------

  /*public function set_source($source)
	{
		if (is_string($source))
		{
			//$this->connect();
			$this->db->select('*');
      $this->db->from($source);
		}
		else
    {
      $this->show_error('"source" nel datafilter puo\' essere solo un table-name');
    }
  }*/

	// --------------------------------------------------------------------

  protected function sniff_action()
  {

    ##########
    /**
     *
     * todo trovare un modo per condividere il component_id fra filtri e dataset
     * penso basti aggiungere nel config del dataset/grid il riferimento al filtro
     * e non fare il get_id in questo caso.
     * prevederlo anche fra dataset/grid e dataedit?, .. mi sembra possibile.
     *
     **/
    $url = rpd_url_helper::remove_all();
    $this->reset_url = rpd_url_helper::append('reset', 1, $url);
    $this->process_url = rpd_url_helper::append('search', 1, $url);

    ///// search /////

    if (rpd_url_helper::value('search'))
    {
      $this->action = "search";

      // persistence
      rpd_sess_helper::save_persistence();

    }
    ///// reset /////
    elseif (rpd_url_helper::value("reset"))
    {
      $this->action = "reset";

      // persistence cleanup
			rpd_sess_helper::clear_persistence();
    }
    ///// show /////
    else
    {
      // persistence
			rpd_sess_helper::save_persistence();
    }
  }

	// --------------------------------------------------------------------

  function process()
  {
    $result = parent::process();

    switch($this->action)
    {
      case "search":

        // prepare the WHERE clause
        foreach ($this->fields as $field)
        {
          if ($field->value!="")
          {
						//die('qui: '.$field->db_name." ".$field->clause.' '.$field->value);
            if (strpos($field->name,"_copy")>0)
            {
              $name = substr($field->db_name,0,strpos($field->db_name,"_copy"));
            } else {
              $name = $field->db_name;
            }
            $field->get_value();
            $field->get_new_value();
            $value = $field->new_value;

            switch ($field->clause)
            {
                case "like":
                    $this->db->like($name, $value);
                break;

                case "orlike":
                    $this->db->orlike($name, $value);
                break;

                case "where":
                    $this->db->where($name." ".$field->operator, $value);
                break;

                case "orwhere":
                    $this->db->orwhere($name." ".$field->operator, $value);
                break;
            }
          }
        }
        $this->build_buttons();
        return $this->build_form();
      break;

      case "reset":
        //pulire sessioni

        $this->build_buttons();
        return $this->build_form();
      break;

      default:
        $this->build_buttons();
        return $this->build_form();
      break;
    }

  }

	// --------------------------------------------------------------------

  function search_button($config=null)
  {
    $caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.search');
    $this->submit("btn_submit", $caption, "BL");
  }

	// --------------------------------------------------------------------

  function reset_button($config=null)
  {
    $caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.reset');
    $action = "javascript:window.location='".$this->reset_url."'";
    $this->button("btn_reset", $caption, $action, "BL");
  }

	// --------------------------------------------------------------------

  function build()
  {

    //sniff and build fields
    $this->sniff_fields();

    //build fields
    $this->build_fields();

    //$this->_built = true;
    $this->output = $this->process();
  }

}
