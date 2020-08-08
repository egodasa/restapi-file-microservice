<?php
require_once 'vendor/autoload.php';
require_once "restapi-helper.php";

use Medoo\Medoo;

// load config.json file for configuration
$_CONFIG = json_decode(file_get_contents("config.json"), true);
 
// database connection
$DB = new Medoo([
	'database_type' => $_CONFIG['DB_TYPE'],
	'database_file' => $_CONFIG['DB_DATABASE']
]);