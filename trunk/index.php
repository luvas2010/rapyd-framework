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
define('RAPYD_BUILD_DATE', '2011-08-25');//planned release

define('RAPYD_BENCH_TIME',  microtime(true));
define('RAPYD_BENCH_MEMORY', memory_get_usage());

unset($filepath,$cwd);


/**
 * compatibility functions (mbstring, etc..)
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
 * locale settings
 */
setlocale(LC_TIME, $config['locale_language'], $config['locale_language'].".utf8");


/**
 * bootstrap
 */

rpd::init($config);
rpd::connect();
rpd::run();
