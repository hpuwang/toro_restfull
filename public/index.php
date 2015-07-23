<?php
ini_set('display_errors',1);
define("ROOT_PATH", str_replace("public","",__DIR__));
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define("ENVIRONMENT", isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : "test");
require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . "/lib/tr/init.php";
tr_init::getInstance()->create();