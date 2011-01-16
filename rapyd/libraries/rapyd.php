<?php
/**
 * main class
 */
class rpd
{
	public static $config;
	public static $working_path;
	public static $qs;
	public static $uri;
	public static $uri_string;
	public static $controller;
	public static $method;
	public static $params = array();
	public static $db;

	public static function init($config)
	{
		self::$config = $config;
		self::$qs = new rpd_url_helper(); //keep compatibility
		self::$uri = new rpd_url_helper();
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
		if (@$prefix != 'CI' AND (in_array($suffix,array('helper','field','driver','library','controller','model')) OR strpos(@$prefix,'rpd_')!==false)){
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

        self::$uri_string = rpd_url_helper::get_uri();

		if(!preg_match('/[^A-Za-z0-9\:\/\.\-\_\#]/i', self::$uri_string) || empty(self::$uri_string))
		{
			if (self::$uri_string == '')
				$segment_arr = array();
			else
				$segment_arr = explode('/', self::$uri_string);

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
					$controller = self::find_file('controller', $path);
					if($controller)
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
		$output = str_replace('{time}', self::benchmarks('time'), $output);
		$output = str_replace('{memory}', self::benchmarks('memory'), $output);
		return $output;
	}


	public static function benchmarks($aspect='time')
	{
		if ($aspect=='time')
		{
			return number_format((microtime(true) - RAPYD_BENCH_TIME),3) .'ms';
		} elseif ($aspect=='memory') {
			return number_format((memory_get_usage() - RAPYD_BENCH_MEMORY) / 1024 / 1024, 2).'MB';
		}
	}



	/**
	 * centralized error view (for 404 or custom errors)
	 *
	 */
	public static function error($message)
	{
                self::run('error','error_message',array($message));
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

                if (isset($controller) AND is_array($controller))
                {
                    $controller = $controller[1];
                    self::$uri_string = $controller;
                    //die($controller);
                }

                if (isset($controller) AND !isset($method) AND strpos($controller,'/'))
                {
                        
                        $segment_arr = explode('/', $controller);
                        self::$uri_string = $controller;
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

                                        $controller .= '_controller';
                                        self::$controller = new $controller(); //self::load('controller', $controller);
                                        self::$method = str_replace('-', '_', $method);
                                        self::$params = $params;

                                        break;
                                }
                        }

                }
		//controller called using parameters
		if (isset($controller, $method))
		{
			self::$uri_string = $controller.'/'.$method.'/'.implode('/',$params);
			$controller .= '_controller';
			self::$controller = new $controller(); //self::load('controller', $controller);
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

		if(is_object($controller))
		{
                    if (isset(self::$db))
				$controller->db = self::$db;
                    $controller->qs = self::$qs;
                    $controller->uri = self::$uri;
                    $controller->uri_string = self::$uri_string;

                    $cached = self::get_cache();
                    if ($cached!=''){
                            echo $cached;
                            exit;
                    }

                    if(method_exists($controller, $method))
                    {
			if (is_callable(array($controller, $method)))
			{
                                return call_user_func_array(array($controller, $method), $params);
			}
			else
			{
				self::error('404');
			}

                    }
                    elseif (is_callable(array($controller, 'remap')))
                    {
                            array_unshift($params, $method);
                            return call_user_func_array(array($controller, 'remap'), $params);
                    }
                }

		self::error('404');

	}


	public static function url($uri)
	{
		return rpd_url_helper::url($uri);
	}


	public static function uri($url)
	{
		return rpd_url_helper::uri($url);
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

	public function css($css, $external=false)
	{
		return rpd_html_helper::css($css, $external);
	}

	public function js($js, $external=false)
	{
		return rpd_html_helper::js($js, $external);
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






  	public static function get_cache()
	{

		if ( !is_dir(self::config('cache_path')) OR !is_writable(self::config('cache_path')))
		{
			return FALSE;
		}

		$cache_path = self::config('cache_path').str_replace('/', '.', self::$uri_string.'.cache');

		if ( ! @file_exists($cache_path))
		{
			return FALSE;
		}

		if ( ! $cp = @fopen($cache_path, 'rb'))
		{
			return FALSE;
		}

		flock($cp, LOCK_SH);

		$cache = '';
		if (filesize($cache_path) > 0)
		{
			$cache = fread($cp, filesize($cache_path));
		}

		flock($cp, LOCK_UN);
		fclose($cp);

		if ( ! preg_match("/(<tstamp>(\d+)<\/tstamp><callback>([A-Za-z0-9\_]*)<\/callback>)/", $cache, $match))
		{
			return FALSE;
		}

		if (time() >= $match['2'])
		{
			@unlink($cache_path);
			return FALSE;
		}
		$output = str_replace($match['0'], '', $cache);
		$output = str_replace('{time}', self::benchmarks('time'), $output);
		$output = str_replace('{memory}', self::benchmarks('memory'), $output);
                //$output = preg_replace("/{rpd::([^}]+)}/", '\\1', $output);
		/*if (preg_match('/{rpd::([^}]+)}/', $output, $dmatch))
		{
                        $output = str_replace($dmatch[0], rpd::run($dmatch[1]), $output);
			//$output = preg_replace_callback('/{rpd::([^}]+)}/', 'rpd::run', $output);
		}*/
		

                
		if ($match['3']!=''){
			$controller = self::$controller;
			$method = $match['3'];
			if (is_callable(array($controller, $method)))
			{
				$output = $controller->$method($output);
			}
		}

		return $output;
	}


	public static function set_cache($output, $expiration=0, $callback='')
	{
		$cache_path = rtrim(self::config('cache_path'),'/');

		if ( !is_dir($cache_path) OR !is_writable($cache_path))
		{
			die($cache_path);
			return FALSE;
		}

		$stamp = time()+$expiration;
		$cache_path .= '/'.str_replace('/', '.', self::$uri_string.'.cache');

		if ( ! $cp = fopen($cache_path, 'wb'))
		{
			return FALSE;
		}

		if (flock($cp, LOCK_EX))
		{
			fwrite($cp, '<tstamp>'.$stamp.'</tstamp><callback>'.$callback.'</callback>'.$output);
			flock($cp, LOCK_UN);
		}
		else
		{
			return FALSE;
		}
		fclose($cp);
		@chmod($cache_path, 0777);
		return TRUE;
	}

	public static function cache($output, $expiration, $callback='')
	{
		rpd::set_cache($output, $expiration, $callback);
		$controller = self::$controller;
                if ($callback!='' AND is_callable(array($controller, $callback)))
                {
                        $output = $controller->$callback($output);
                }
                //$output = preg_replace("/{rpd::([^}]*)}/", '\\1',   $output);
		$output = str_replace('{time}', self::benchmarks('time'), $output);
		$output = str_replace('{memory}', self::benchmarks('memory'), $output);
		return $output;
	}







}