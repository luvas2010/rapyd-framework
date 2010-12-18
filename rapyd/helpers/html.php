<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class rpd_html_helper {

 public static $tags = array (
      'js'      => '<script language="javascript" type="text/javascript" src="%s"></script>',
      'script'  => '<script language="javascript" type="text/javascript">%s</script>',
      'css'     => '<link rel="stylesheet" href="%s" type="text/css" />',
      'img'     => '<img src="%s" %s />',
      'button'  => '<input type="%s" name="%s" value="%s" onclick="%s"  class="%s" />',
  );
	protected static $js = array();
  protected static $css = array('rapyd.css');
  protected static $scripts = array();

	// --------------------------------------------------------------------

	public static function attributes($attrs)
	{
		if (is_string($attrs))
			return ($attrs == FALSE) ? '' : ' '.$attrs;

		$compiled = '';
		foreach($attrs as $key => $val)
		{
			$compiled .= ' '.$key.'="'.$val.'"';
		}
		return $compiled;
	}

	// --------------------------------------------------------------------

  public function js ($js)
  {
    if(!in_array($js, self::$js)) self::$js[] = $js;
  }

	// --------------------------------------------------------------------

  public function css ($css)
  {
    if(!in_array($css, self::$css)) self::$css[] = $css;
  }

	// --------------------------------------------------------------------

  public function image ($src, $attrs=array())
  {
    return sprintf(self::$tags['img'], RAPYDASSETS.$src, self::attributes($attrs));
  }

	// --------------------------------------------------------------------

  public function script ($script)
  {
    return sprintf(self::$tags['script'], $script)."\n";
  }

	// --------------------------------------------------------------------

  public function head_script($script)
  {
    if(!in_array($script, self::$scripts)) self::$scripts[] = $script;
  }

	// --------------------------------------------------------------------

  public function button ($name, $value, $onclick="", $type="button", $class="button")
  {
    return sprintf(self::$tags['button'], $type, $name, $value, $onclick, $class)."\n";
  }

	// --------------------------------------------------------------------

	public function head()
	{
		$buffer = "";

                //css links
		foreach (self::$css as $item)
                {
                        $item = (strpos($item,'http')===false AND $item[0] != '/') ? RAPYDASSETS .$item : $item;
			$buffer .= sprintf(self::$tags['css'], $item )."\n";
		}
                //js links
		foreach (self::$js as $item)
                {
                        $item = (strpos($item,'http')===false AND $item[0] != '/') ? RAPYDASSETS .$item : $item;
			$buffer .= sprintf(self::$tags['js'], $item )."\n";
		}
                //javascript in page, head section
		$buffer .= sprintf(self::$tags['script'], implode("\n",self::$scripts) )."\n";

		return $buffer;
	}


}
