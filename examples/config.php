<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', '_cms');
define('DB_PORT', 3306);
define('DB_CHARSET', "utf8");

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

require_once ROOT."vendor".DIR."autoload.php";