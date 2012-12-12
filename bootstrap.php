<?php

defined('IN_APPLICATION') or exit;

ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('America/Toronto');

define('START_TIME', microtime(true));

define('DS', DIRECTORY_SEPARATOR);

define('TEMPLATES_PATH', ROOT_PATH. 'templates' . DS);
define('VENDORS_PATH', ROOT_PATH . 'vendors' . DS);

require_once VENDORS_PATH . 'SplLoader.php';
require_once ROOT_PATH . 'config.init.php';

$g_autoloader = new ContactManager\SplLoader(array(VENDORS_PATH));