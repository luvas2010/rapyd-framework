<?php
/**
 * main class
 */
class rpd
{
	public static $config;
	public static $working_path;
	public static $qs;
	public static $controller;
	public static $method;
	public static $params = array();
	public static $db;

	public static function init($config)
	{
		self::$config = $config;
		self::$qs = new rpd_url_helper();
		if (!defined('RAPYDASSETS')) {
			define('RAPYDASSETS', $config['assets_path']);
		}
	}

	public static function config($item)
	{
		if ( ! isset(self::$config[$item]))
		{
			return FALSE;
		}
		return self::$config[$item];
	}

	/**
	 * class autoloader
	 *
	 **/
	public static function auto_load($class)
	{
		if (class_exists($class, FALSE))
			return TRUE;
		if (($suffix = strrpos($class, '_')) > 0)
		{
			// Find the class prefix and suffix
			$prefix = substr($class, 0, $suffix);
			$suffix = substr($class, $suffix + 1);
			
		}

		$path = '';
		$file = str_replace('rpd_','',str_replace('_'.$suffix,'',$class));

		if ($suffix === 'field')
		{
			$suffix = 'library';
			$path = 'fields/';
		}
		elseif ($suffix === 'driver')
		{
			$suffix = 'library';
			$path = 'drivers/';
		}
		elseif (in_array($class,array('dataset','datagrid')))
		{
			$suffix = 'library';
		}
                //hack for CI integration (nota.. i file creati in CI non devono avere suffisso _helper _field ecc..)
		if (@$prefix != 'CI' AND (in_array($suffix,array('helper','field','driver','library','controller')) OR strpos(@$prefix,'rpd_')!==false)){
			self::load($suffix, $path.$file);
		} else {
                    return FALSE;
                }
		return TRUE;
	}


	/**
	 * find and load a php file (if controller instancing it)
	 *
		*/
	public static function load($directory, $file_name)
	{
		if ($file_name == 'rpd') return null;

		$file = self::find_file($directory, $file_name);
		if($file)
		{
			require_once $file;

			//if we loaded a controller, need to save $working_path for speedup views inclusion
			if ($directory=='controller')
				self::$working_path = dirname($file) . '/';

			// If name contains segments, get last segment for function and class
			if(preg_match("/^.*\/(\w+)$/", $file_name,$matches ))
			{
				$file_name = $matches[1];
			}

			return null;
		}
		else
				self::error(ucfirst($directory). ' file doesn\'t exist: ' . $file_name);
	}


	/**
	 * plural helper function is needed because you can add custom folders in include_paths
	 * (application, modules/modulex, system), for example: "resources/"
	 * then find_file('resource', 'name') will search in:
	 * /application/resourcES/name.php
	 * /modules/modulex/resourcES/name.php
	 * /system/resourcES/name.php
	 *
	 */
	protected static function plural($str)
	{
		if (preg_match('/[sxz]$/', $str) OR preg_match('/[^aeioudgkprt]h$/', $str))
		{
			$str .= 'es';
		}
		elseif (preg_match('/[^aeiou]y$/', $str))
		{
			$str = substr_replace($str, 'ies', -1);
		}
		else
		{
			$str .= 's';
		}
		return $str;
	}

	/**
	 * find a file and returning full path (or false)
	 * according with configured include_paths and passed type and name of file
	 *
	 */
	public static function find_file($type, $file_name)
	{
		static $file_cache;


		if ($type!="") $type = self::plural($type);
                $search = $type.'/'.$file_name.'.php';

		if (isset($file_cache['paths'][$search]))
				return $file_cache['paths'][$search];

		$file_found = FALSE;
		foreach (self::config('include_paths') as $path)
		{

				if (is_file(RAPYD_ROOT.$path.'/'.$search))
				{
						$file_found = RAPYD_ROOT.$path.'/'.$search;
						break;
				}
		}

		return $file_cache['paths'][$search] = $file_found;
	}


	/**
	 * parses current application URL to determine which controller and methods to call.
	 *
	 */
	public static function router()
	{
              // get segments from URL
              //$url = trim(substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'], 'index.php') + 9), '/');
                $url = trim(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']), '/');

		if(!preg_match('/[^A-Za-z0-9\:\/\.\-\_\#]/i', $url) || empty($url))
		{
			$segment_arr = (!empty($url)) ? explode('/', $url) : array();

			// defaults
			$controller_name = self::config('default_controller');
			$method_name = self::config('default_method');
			$params = array();

			// if URL segments exist, overwrite defaults
			$controller = true;
			if(count($segment_arr) > 0)
			{
				$controller = false;
				$arr_segment = array();

				while($segment_arr)
				{
					$path = implode('/', $segment_arr);
					$arr_segments[] = array_pop($segment_arr);

					// starting from last segment.. searching for a valid controller
					// when is found, next segment is the method and others are params
					if($controller = self::find_file('controller', $path))
					{
						$controller_name = $path;

						$arr_segments = array_reverse($arr_segments);
						if(isset($arr_segments[1])) $method_name = $arr_segments[1];
						if(isset($arr_segments[2])) $params = array_slice($arr_segments, 2);

						break;
					}
				}
			}

			if(!$controller)
			{
				self::error('404');
			}
			else
			{
				$controller_name .='_controller';
				self::$controller =	new $controller_name;
				self::$method = str_replace('-', '_', $method_name);
				self::$params = $params;
			}
			return true;
		}
		else
			self::error('The URL you entered contains illegal characters.');
	}

	/**
	 * load a view file, passing it an associative array of
	 * php vars we need in page
	 *
	 */
	public static function view($file_name, $input_data=array())
	{
		$input_data = (array)$input_data;
		$view_path=str_replace('controllers','views',self::$working_path);

		if (!is_file($view_path))
		{
			$view_path = self::find_file('view', $file_name);
		}
		ob_start();
		extract($input_data, EXTR_SKIP);
		include $view_path;
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * centralized error view (for 404 or custom errors)
	 *
	 */
	public static function error($message)
	{
		if ($message=='404')
		{
			$page = '404';
		}
		else
		{
			$page = 'error';
			$message = array('error'=>$message);
		}
		echo self::view('errors/'.$page, $message);
		die();
	}


	public static function lang($key = null, $args = array(), $path = null)
	{
		static $language = array();

		if (count($language)<1)
		{
			// include language file anche cache messages
			include RAPYD_ROOT.'rapyd/'.'i18n/'.self::config('locale_language').'.php';
			$language = $lang;
		}
		if (isset($path) AND !isset($key))
		{
			include_once $path.'rapyd/'.'i18n/'.self::config('locale_language').'.php';
			$language = array_merge($language, $lang);
		}
		if (strpos($key,'.*'))
		{
			$namespace = str_replace('*','',$key);
			$array = array();
			foreach($language as $subkey => $value)
			{
				if (strpos($subkey,$namespace)!== false)
				{
					$subkey = str_replace($namespace,'',$subkey);
					$array[$subkey] = $value;
				}
			}
			return $array;
		}
		if ($key == '' OR ! isset($language[$key]))
		{
			return $key;
		}

		$string = $language[$key];
		if ($args == '')
		{
			return $string;
		}
		else if (is_array($args))
		{
			return vsprintf($string, $args);
		}
		else
		{
			return sprintf($line_string, $args);
		}

	}

	/**
	 * find and instance a controller (using parameters or detecting it by current url), call a method or throw 404
	 */
	public static function run($controller=null,$method=null,$params=array())
	{
		//controller called using parameters
		if (isset($controller, $method))
		{
			self::$controller =	new $controller.'_controller'; //self::load('controller', $controller);
			self::$method = str_replace('-', '_', $method);
			self::$params = $params;
		}
		//autodetect controller by router
		else
		{
			self::router();
		}

		$controller = self::$controller;
		$method = self::$method;
		$params = self::$params;

		if(is_object($controller) && method_exists($controller, $method))
		{
			//enable $this->db->.. inside controllers
			if (isset(self::$db))
				$controller->db = self::$db;
		    $controller->qs = self::$qs;
			if (is_callable(array($controller, $method)))
			{
				// Start validation of the controller
				$controller->$method($params);
			}
			else
			{
				self::error('404');
			}

		}
		else
		{
			self::error('404');
		}
	}

	/**
	 * take an 'application' URI (ie. controller/method/param)
	 * and return a full URL  (ie. http://host/rapydpath/[index.php]/controller/method/param)
	 *
	 */
	public static function url($uri)
	{
		$index = (self::config('index_page')) ? self::config('index_page').'/' : '';
                $basename = (self::config('basename')) ? trim(self::config('basename'),'/').'/' : '';
		//return rtrim('http://' . $_SERVER['HTTP_HOST'].RAPYD_PATH.$index.$uri,'/');
                return rtrim('http://' . $_SERVER['HTTP_HOST'].'/'.$basename.$index.$uri,'/');
	}


	/**
	 *reverse or url(), return an application uri removing [http://host/path/index.php/]uri
	 *
	 */
	public static function uri($url)
	{
		$index = (self::config('index_page')) ? self::config('index_page').'/' : '';
                $basename = (rtrim(self::config('basename'),'/')) ? rtrim(self::config('basename'),'/').'/' : '';
		//return rtrim(str_replace('http://' . $_SERVER['HTTP_HOST'].RAPYD_PATH.$index,'',$url),'/');
		return rtrim(str_replace('http://' . $_SERVER['HTTP_HOST'].$basename.$index,'',$url),'/');
	}

	public static function asset($resource)
	{
		return  RAPYD_PATH.dirname(str_replace(RAPYD_ROOT,'',self::$working_path)).'/assets/'.$resource;

		//CI fix
		//return RAPYDASSETS.$resource;
	}

	public function head()
	{
		return rpd_html_helper::head();
	}

	public static function connect()
	{

		if (isset(self::$db)) return;

		$db_class = 'rpd_database_'.self::$config['db']['dbdriver'].'_driver';
                                                           
		self::$db = new $db_class();
		self::$db->hostname = self::$config['db']['hostname'];
		self::$db->username = self::$config['db']['username'];
		self::$db->password = self::$config['db']['password'];
		self::$db->database = self::$config['db']['database'];
		self::$db->dbprefix = self::$config['db']['dbprefix'];
		self::$db->dbdriver = self::$config['db']['dbdriver'];
		$result = self::$db->connect();
		if ($result !==false)
		{
			self::$db->select_db();
		}
		return $result;

	}


}