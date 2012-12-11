<?php
/*
 * @ignore
 */
defined('IN_APPLICATION') or exit;

// ===============================================
// load all configuration items that may be needed
$config = require_once ABS_PATH . 'config.php';

// ==========================
// set default date time zone
date_default_timezone_set('America/Toronto');  

// =========================================================================
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
		print $exception->getMessage() . '<br />';
	}
	
	// ==================================
	// delete password from $config array
	unset($config['db_pass']);
	
	//print 'successful connection';
}//end if
else {
	trigger_error('Application configuration details appear to be invalid', E_USER_ERROR);
}
//return $mysqli;

// ======================================================
// close the connection to the database shutdown function
function shutdown_func() {
	global $mysqli;

	// if( $mysqli ) {
	if( $mysqli instanceof mysqli ) {
		$mysqli->close();
		//print 'MySQLi is now closed.<br />';
	}
	$mysqli = null;
}
register_shutdown_function('shutdown_func');

