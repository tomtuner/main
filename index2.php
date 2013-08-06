<?php
require_once('helpers/shared.php');

// Rename the session so we don't interfere with other services.
session_name('CCLMAIN');

// Kill evil magic quotes.
if(get_magic_quotes_gpc()) {
	function stripslashes_deep($value) {
		$value = (is_array($value)) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}

	$_POST = stripslashes_deep($_POST);
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

if(isset($_GET['controller'])) {
	define('CONTROLLER', $_GET['controller']);
} else if(isset($_POST['controller'])) {
	define('CONTROLLER', $_POST['controller']);
}

if(isset($_GET['action'])) {
	define('ACTION', $_GET['action']);
} else if(isset($_POST['action'])) {
	define('ACTION', $_POST['action']);
}

if(isset($_GET['id'])) {
	define('ID', intval($_GET['id']));
} else if(isset($_POST['id'])) {
	define('ID', intval($_POST['id']));
}
// dist
if (CONTROLLER == 'admin') {
	require_once('CAS/CAS.php');
	phpCAS::client(CAS_VERSION_2_0, 'webapps.rit.edu', 443, '/cas');
	phpCAS::forceAuthentication();
}

if(ACTION == 'logout') {
	require_once('CAS/CAS.php');
	$redirect = (CONTROLLER == 'admin') ? 'http://campuslife.rit.edu/main/admin/' : 'http://campuslife.rit.edu/main/';
	phpCAS::logout($redirect);
}

$controller_path = "controllers";
$controller_class = str_replace(' ', '', ucwords(str_replace('_', ' ', CONTROLLER))) .'Controller';
$controller_filename = "$controller_path/". CONTROLLER .'_controller.php';
if(strpos(realpath($controller_filename), realpath($controller_path)) !== false || true) { #Solo changed this to make it work

	// if the controller exists: load it and call the method on it
	if(is_file($controller_filename)) {

		include_once($controller_filename);

		$controller_object = new $controller_class();
		$action_method = preg_replace("/_([a-z])/e", "strtoupper('\\1')", ACTION);

		if (method_exists($controller_object,"__before")) {
	

			call_user_func(array($controller_object, "__before"));
		} else {
	

			// "__before does not exist in your controller!";
		}
		//
		call_user_func(array($controller_object, $action_method));
	} else {


		// controller doesnt exist!
		// load an instance of SmartController and call the desired method on it.
		// This will fall back to the super Controller method __call, which will determine
		// if this content exists in the database.
		// If it doesnt, then a 404 page is displayed.
		$controller_object = new SmartController();
		$action_method = preg_replace("/_([a-z])/e", "strtoupper('\\1')", ACTION);
		if (method_exists($controller_object,"__before")) {
		

			call_user_func(array($controller_object, "__before"));
		} else {
			// "__before does not exist in your controller!";
		

		}
		call_user_func(array($controller_object, $action_method));
	}
}

?>
