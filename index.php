<?php


//rapyd don't need magic quotes,
//in a production server feel free to change or remove this line (at your risc)
set_magic_quotes_runtime(0);


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
define('RAPYD_VERSION', '0.6');
define('RAPYD_BUILD_DATE', '2009-08-11');

unset($filepath,$cwd);
/**
 * core class
 */
include_once(RAPYD_ROOT.'rapyd/libraries/rapyd.php');


/**
 * autoload system
 */
//function __autoload($class_name) {
//    rpd::auto_load($class_name);
//}
spl_autoload_register(array('rpd', 'auto_load'));


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
