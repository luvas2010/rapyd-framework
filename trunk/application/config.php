<?php

/**
 * system configurations
 *
 */

$config['index_page'] = 'index.php'; //use 'index.php' if htaccess not allowed
$config['basename'] = ""; //it correspont to .htaccess RewriteBase  and should be "/foldername/" if you use the cms in subfolder of the docroot
$config['url_method'] = "uri"; //alternative: "qs"  define if rapyd will use uri or query string for its semantic
$config['default_controller'] = 'welcome'; //default controller class to instance
$config['default_method']     = 'index'; //default controller method to call
$config['output_compression']  = 0; //output compression gzip level (if zlib is enabled) suggested level is 5 (0 means disabled)

$config['include_paths'][]  = 'application';
$config['include_paths'][]  = 'modules/demo';


$config['root_path']  = getenv("DOCUMENT_ROOT"); // or './../../';

$config['include_paths'][]  = 'core';

$config['assets_path']     = RAPYD_PATH.'core/assets/';
$config['cache_path']      = RAPYD_ROOT.'cache/';
$config['locale_language'] = 'en_US';

$config['routes'] = array(
	//'product/(:num)/:str' => 'catalogmodule/product/$1';
);

$config['db']['hostname'] = "";
$config['db']['username'] = "";
$config['db']['password'] = "";
$config['db']['database'] = 'sqlite:'.RAPYD_ROOT.'modules/demo/db/demo.db';
$config['db']['dbdriver'] = "pdo";
$config['db']['dbprefix'] = "";
$config['db']['db_debug'] = true;
/**
 * custom configurations
 *
 */
