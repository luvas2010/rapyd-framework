<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');



class rpd_url_helper {


  public static function get_qs()
  {
    $query_string = '';

		if (!empty($_GET))
		{
			$query_string = '?';
			foreach($_GET as $key => $val)
			{
				if (is_array($val))
				{
					foreach($val as $sub_key => $sub_val)
					{
						// Integer subkeys are numerically indexed arrays
						$sub_key = is_int($sub_key) ? '[]' : '['.rawurlencode($sub_key).']';
						$query_string .= $key.$sub_key.'='.rawurlencode($sub_val).'&';
					}
				}
				else
				{
					$query_string .= $key.'='.rawurlencode($val).'&';
				}
      }
      $query_string = rtrim($query_string, '&');
    }
    return $query_string;
  }

	// --------------------------------------------------------------------

  public static function get_url()
  {
    return $_SERVER["REQUEST_URI"];
  }

	// --------------------------------------------------------------------


  public static function get_self()
  {
    $url = self::get_url();
    if (strpos($url, '?') === false)  return $url;
    return substr($url, 0, strpos($url,'?'));
  }


	// --------------------------------------------------------------------

  //l'opposto di parse_str() in php non esiste come funziona nativa
  public static function unparse_str($array)
  {
    $query_string = '?';
    foreach($array as $key => $val)
    {
      if (is_array($val))
      {
        foreach($val as $sub_key => $sub_val)
        {
          // Integer subkeys are numerically indexed arrays
          $sub_key = is_int($sub_key) ? '[]' : '['.$sub_key.']';
          $query_string .= $key.rawurlencode($sub_key).'='.rawurlencode($sub_val).'&';
        }
      }
      else
      {
        $query_string .= $key.'='.rawurlencode($val).'&';
      }
    }
    $query_string = rtrim($query_string, '&');
    return $query_string;
  }

	// --------------------------------------------------------------------

  //vedere se è il caso di usare le rwrules per ovviare ai problemi con i namespace

  public static function append($key, $value, $url=null)
  {
    $qs_array = array();
    $url = (isset($url)) ? $url : self::get_url();
    if (strpos($url, '?') !== false)
    {
      $qs = substr($url, strpos($url,'?')+1);
      $url = substr($url, 0, strpos($url,'?'));
      parse_str($qs, $qs_array);
    }
		$qs_array[$key] = $value;

    $query_string = self::unparse_str($qs_array);

		return ($url . $query_string);
  }

	// --------------------------------------------------------------------

  function remove($keys, $url=null)
  {
    $qs_array = array();
    $url = (isset($url)) ? $url : self::get_url();
    if (strpos($url, '?') === false)  return $url;

    $qs = substr($url, strpos($url,'?')+1);
    $url = substr($url, 0, strpos($url,'?'));
    parse_str($qs, $qs_array);

    if (!is_array($keys))
    {
      if ($keys=='ALL')
        return $url;
      $keys = array($keys);
    }
    foreach ($keys as $key)
    {
      unset($qs_array[$key]);
    }
    $query_string = self::unparse_str($qs_array);

		return ($url . $query_string);
  }

	// --------------------------------------------------------------------

  function remove_all($cid=null, $url=null)
  {
    $semantic = array(  'search', 'reset',   'checkbox',
                        'pag',    'orderby', 'show',
                        'create', 'modify',  'delete',
                        'insert', 'update',  'do_delete' );

    if (isset($cid))
    {
      foreach ($semantic as $key)
      {
        $keys[] = $key.$cid;
      }
      $semantic = $keys;
    }
    return self::remove($semantic, $url);
  }

	// --------------------------------------------------------------------

  function replace($key, $newkey, $url=null)
  {
    $qs_array = array();
    $url = (isset($url)) ? $url : self::get_url();

    if (strpos($url, '?') !== false)
    {
      $qs = substr($url, strpos($url,'?')+1);
      $url = substr($url, 0, strpos($url,'?'));
      parse_str($qs, $qs_array);
    }
    if (isset($qs_array[$key]))
    {
      $qs_array[$newkey] = $qs_array[$key];
      unset($qs_array[$key]);
    }
    $query_string = self::unparse_str($qs_array);
		return ($url . $query_string);
  }

	// --------------------------------------------------------------------

	function value($key, $default=FALSE)
	{
    if (strpos($key,'|'))
		{
      $keys = explode('|',$key);
      foreach ($keys as $k)
      {
        $v = self::value($k, $default);
        if ($v != $default) return $v;
      }
      return $default;
    }

		if (strpos($key,'.'))
		{
			list($namespace, $subkey) = explode('.',$key);
			return (isset($_GET[$namespace][$subkey])) ?  $_GET[$namespace][$subkey] : $default;
		}
		else
		{
		  return (isset($_GET[$key])) ? $_GET[$key] : $default;
		}
	}
}
