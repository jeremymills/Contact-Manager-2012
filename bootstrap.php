<?php

defined('IN_APPLICATION') or exit;

ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);

define('START_TIME', microtime(true));

define('DS', DIRECTORY_SEPARATOR);

define('TEMPLATES_PATH', ROOT_PATH. 'templates' . DS);
define('VENDORS_PATH', ROOT_PATH . 'vendors' . DS);

require_once VENDORS_PATH . 'SplLoader.php';

$g_autoloader = new ContactManager\SplLoader(array(VENDORS_PATH));