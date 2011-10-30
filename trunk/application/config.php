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
//default language is the one with segment => ''
//for others, segment is the first uri-segment that set language:  [/it]/controller/method/...
$config['languages'] = array(
	array('index'=>1, 'name'=>'english',  'locale'=>'en_US', 'dateformat'=>'m/d/Y', 'segment'=>''),
	array('index'=>2, 'name'=>'italiano', 'locale'=>'it_IT', 'dateformat'=>'d/m/Y', 'segment'=>'it'),
	array('index'=>3, 'name'=>'française',  'locale'=>'fr_FR', 'dateformat'=>'d/m/Y', 'segment'=>'fr' ),
	array('index'=>4, 'name'=>'československé', 'locale'=>'cs_CZ', 'dateformat'=>'d.m.Y', 'segment'=>'cs'),
);


$config['routes'] = array(
//	'page/(:any)' => 'frontend/page/$1',
//	'spage/(:any)' => 'frontend/spage/$1',

);

$config['db']['hostname'] = "";
$config['db']['username'] = "";
$config['db']['password'] = "";
$config['db']['database'] = 'sqlite:'.RAPYD_ROOT.'modules/demo/db/demo.sqlite';
$config['db']['dbdriver'] = "pdo";
$config['db']['dbprefix'] = "";
$config['db']['db_debug'] = true;
/**
 * custom configurations
 *
 */

//$config['cms']['theme'] = RAPYD_ROOT.'application/views/themes/white/';
//$config['cms']['assets_path'] = RAPYD_PATH.'assets/';
