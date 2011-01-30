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
define('RAPYD_VERSION', '0.8');
define('RAPYD_BUILD_DATE', '2011-01-20');

define('RAPYD_BENCH_TIME',  microtime(true));
define('RAPYD_BENCH_MEMORY', memory_get_usage());

unset($filepath,$cwd);
/**
 * core class
 */
include_once(RAPYD_ROOT.'rapyd/libraries/rapyd.php');


/**
 * autoload system
 */
spl_autoload_register(array('rpd', 'auto_load'));


/**
 * error and exception handling
 */
set_exception_handler(array('rpd', 'exception_handler'));
set_error_handler(array('rpd', 'error_handler'));

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
