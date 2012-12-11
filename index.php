<?php

define('IN_APPLICATION', true);
define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require_once ROOT_PATH . 'bootstrap.php';

$view = new ContactManager\View(TEMPLATES_PATH . 'example.php');
print $view;