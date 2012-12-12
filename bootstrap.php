<?php

defined('IN_APPLICATION') or exit;

ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('America/Toronto');

// ==================================
// load MySQL database authentication
$config = require_once ROOT_PATH . 'config.php';

define('START_TIME', microtime(true));

define('DS', DIRECTORY_SEPARATOR);

define('TEMPLATES_PATH', ROOT_PATH. 'templates' . DS);
define('VENDORS_PATH', ROOT_PATH . 'vendors' . DS);

require_once VENDORS_PATH . 'SplLoader.php';

$g_autoloader = new ContactManager\SplLoader(array(VENDORS_PATH));

// =========================================================================
// adding in simple copy and paste work from in class project work
// normalize / check the array this it is an array for "extra" measures here
if( is_array($config) ) {

	// =======================
	// connect to database ...
	try {
		$mysqli = new mysqli( $config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name'] );
		if( $mysqli->connect_error ) {
			trigger_error('An error has occured while connecting to the datbase. Error: [ ' . $mysqli->connect_errno . ' ] ' . $mysqli->connect_error, E_USER_ERROR);
		}//end if
	} catch( Exception $exception ) {
		print 'Can not connect to the database.<br />Error: '.$exception->getMessage() . '<br />';
	}
	
	// =======================================================
	// delete password from $config array for security reasons
	unset($config['db_pass']);
	
}//end if
else {
	trigger_error('Application configuration details appear to be invalid', E_USER_ERROR);
}

// ======================================================
// close the connection to the database shutdown function
function shutdown_func() {
	global $mysqli;

	if( $mysqli instanceof mysqli ) {
		$mysqli->close();
	}
	$mysqli = null;
}
// ============================================================
// register shutdown function to ensure garbage clean up is set
register_shutdown_function('shutdown_func');
