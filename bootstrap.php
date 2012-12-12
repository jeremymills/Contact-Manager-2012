<?php
/**
 * @ignore
 */
defined('IN_APPLICATION') or exit;

/**
 * Enable error reporting. This should be "silenced" in a production environment
 */
ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);

/**
 * The initial start time of the request (almost initial)
 */
define('START_TIME', microtime(true));

/**
 * Shorthand directory separator constant
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Absolute path to templates directory
 */
define('TEMPLATES_PATH', ROOT_PATH. 'templates' . DS);

/**
 * Absolute path to vendors directory
 */
define('VENDORS_PATH', ROOT_PATH . 'vendors' . DS);

/**
 * Include and require once the SplLoader class.
 */
require_once VENDORS_PATH . 'SplLoader.php';

/* Instantiate and initialize the SplLoader */
$g_autoloader = new ContactManager\SplLoader(array(VENDORS_PATH));

// require_once ROOT_PATH . 'config.init.php'; // commented out by Aaron McGowan - use this file to initialize items. Additionally, we will be using Doctrine.

ContactManager\Application::init(array(
    'config_file' => ROOT_PATH . 'config.php'
));