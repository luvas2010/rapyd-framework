<?php
/**
 * rapyd framework
 *
 */


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
session_start();

/**
 * define root and version
 */
define('DOC_ROOT', substr(__FILE__, 0, strrpos(__FILE__, $_SERVER['SCRIPT_NAME'])));
define('RAPYD_ROOT', getcwd().'/');
define('RAPYD_PATH', str_replace(DOC_ROOT,'',RAPYD_ROOT));
define('RAPYD_VERSION', '0.5');
define('RAPYD_BUILD_DATE', '2009-03-28');


/**
 * core class
 */
include_once(RAPYD_ROOT.'rapyd/libraries/rapyd.php');


/**
 * autoload system
 */
function __autoload($class_name) {
    rpd::auto_load($class_name);
}


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
