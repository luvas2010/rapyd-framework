<?php

/**
 * system configurations
 *
 */

$config['index_page'] = 'index.php'; //use 'index.php' if htaccess not allowed
$config['basename'] = ""; //it correspont to .htaccess BASENAME
$config['url_method'] = "uri"; //alternative: "qs"  define if rapyd will use uri or query string for its semantic
$config['default_controller'] = 'welcome';
$config['default_method']     = 'index';


$config['include_paths'][]  = 'application';
$config['include_paths'][]  = 'modules/demo';
//$config['include_paths'][]  = 'modules/module2';

$config['root_path']  = getenv("DOCUMENT_ROOT"); // or './../../';

$config['include_paths'][]  = 'rapyd';

$config['assets_path']     = RAPYD_PATH.'rapyd/assets/';
$config['cache_path']      = RAPYD_ROOT.'cache/';
$config['locale_language'] = 'en_US';


$config['db']['hostname'] = "";
$config['db']['username'] = "";
$config['db']['password'] = "";
$config['db']['database'] = 'sqlite:'.RAPYD_ROOT.'modules/demo/db/demo.db';
$config['db']['dbdriver'] = "pdo";
$config['db']['dbprefix'] = "";

/**
 * custom configurations
 *
 */
