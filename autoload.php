<?php

date_default_timezone_set('Asia/Taipei');
define('SYSTEM_DIR', __DIR__);
require_once SYSTEM_DIR.'/config.php';

#Set Error reporting base on mode
if(Config::getConfig('BASIC_CONFIG')['DEBUG_MODE']){
	ini_set("display_errors", "1");
	error_reporting(E_ALL);
}else{
	ini_set("display_errors", "0");
	error_reporting(0);
}

#Load vendor dependency via composer
if(file_exists(SYSTEM_DIR.'/vendor/autoload.php')){
    include_once(SYSTEM_DIR.'/vendor/autoload.php');
}

#Load class dependency via spl_autoload
spl_autoload_register('loadClassDependency');
function loadClassDependency($class){
	$class_route = array(
		'Router' => '/class/Router.class.php',
		'CdnDriver' => '/class/CdnDriver.class.php',
	);
	$class_path = SYSTEM_DIR.$class_route[$class];
	include_once $class_path;
}




