<?php

// FIXME Display all errors.
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
#ini_set('display_errors', true);
ini_set("soap.wsdl_cache_enabled", "0");

function __autoload($class) {
	foreach(explode(PATH_SEPARATOR, get_include_path()) as $path) {
		if(file_exists("$path/$class.php")) {
			$filename = $class;
			break;
		} else {
			// FIXME Weird problem if you're logged into the old EVR system.
			$temp_class = strtolower($class);
			if(file_exists("$path/". $temp_class{0} . substr($class, 1) .".class.php")) {
				$filename = $temp_class{0} . substr($class, 1) .".class";
				break;
			}
		}
	}

	if(!isset($filename)) {
		$filename = strtolower(preg_replace("/([a-z])([A-Z])/", '\\1_\\2', $class));
	}

	@include_once("$filename.php");

	if(!class_exists($class, false)) {
		return false;
	}
}

$base = '/home/cclweb/docs/default/main';
set_include_path(get_include_path() . PATH_SEPARATOR . $base .'/controllers' . PATH_SEPARATOR . $base .'/models' . PATH_SEPARATOR . $base .'/views' . PATH_SEPARATOR . $base .'/helpers' . PATH_SEPARATOR . "/usr/local/www/lib". PATH_SEPARATOR . "/var/www/lib");

require_once('config.php');
$db = DB::connect($dsn);
#xecho $db->getDebugInfo();
$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("SET NAMES utf8");
?>
