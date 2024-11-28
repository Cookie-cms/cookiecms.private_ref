<?php
// error_reporting(E_ALL);
// ini_set('display_errors', true);
// define('__RDt__', '/');
// define('__RD__', $_SERVER['DOCUMENT_ROOT']);
// define('__UD__', __RD__ . 'uploads/');
// // define('__CM__', __RD__ . '/engine/modules/');
// // define('__CML__', __RD__ . 'engine/modules/');
// define('__CD__', __RD__ . '/engine/');
// define('__CDL__', __RD__ . '/engine/');
// // define('__CF__', __CD__ . '../configs/');
// define('__ven__', __RD__ . '/../vendor/autoload.php');
// define('__hub__', __RD__ . '/../');
// define('__CI__', __RD__ . '/src/inc/');
// require_once __CI__ . "yamlReader.php";
// require_once __CI__ . "mail.php";
require $_SERVER['DOCUMENT_ROOT'] . "/src/vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/yamlReader.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/DiscordModule.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/src/inc/tools.php";
// var_dump($a);

define('__mysql__',$_SERVER['DOCUMENT_ROOT']. "/src/inc/mysql.php");
define('__config__',$_SERVER['DOCUMENT_ROOT']. "/configs/config.yml");
// define('__tools__',$_SERVER['DOCUMENT_ROOT']. "/src/inc/tools.php");
// define('__yamlReader__',$_SERVER['DOCUMENT_ROOT']. "/src/inc/yamlReader.php");



