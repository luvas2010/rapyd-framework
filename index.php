<?php


//rapyd don't need magic quotes,
//in a production server feel free to change or remove this line (at your risc)
ini_set('magic_quotes_runtime', 0);


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
if (!session_id() ) session_start();

/**
 * define root and version
 */
$filepath = str_replace('\\','/',__FILE__);
$cwd =  str_replace('\\','/',getcwd());
define('DOC_ROOT', substr($filepath, 0, strrpos($filepath, $_SERVER['SCRIPT_NAME'])));

define('RAPYD_ROOT', $cwd.DIRECTORY_SEPARATOR);
define('RAPYD_PATH', str_replace(DOC_ROOT,'',str_replace('\\','/',RAPYD_ROOT)));
define('RAPYD_VERSION', '1.0');
define('RAPYD_BUILD_DATE', '2011-10-29');

define('RAPYD_BENCH_TIME',  microtime(true));
define('RAPYD_BENCH_MEMORY', memory_get_usage());

unset($filepath,$cwd);


/**
 * utf8 compat. str functions (to handle mbstring if not loaded) borrowed from dokuwiki
 * remember to use in your code utf8_ prefix (utf8_strlen, utf8_substr, utf8_strtolower, ...) 
 */
include_once(RAPYD_ROOT . 'core/helpers/utf8.php');

/**
 * compatibility functions (intended to handle differences of PHP 5.* versions)
 */
include_once(RAPYD_ROOT . 'core/helpers/compat.php');

/**
 * core class
 */
include_once(RAPYD_ROOT.'core/libraries/rapyd.php');


/**
 * autoload system
 */
spl_autoload_register(array('rpd', 'auto_load'));


/**
 * error and exception handling
 */
set_exception_handler(array('rpd', 'exception_handler'));
set_error_handler(array('rpd', 'error_handler'));
register_shutdown_function(array('rpd', 'shutdown_handler'));



/**
 * configuration file
 */
include_once(RAPYD_ROOT.'application/config.php');


/**
 * bootstrap
 */

rpd::init($config);
rpd::connect();
rpd::run();
