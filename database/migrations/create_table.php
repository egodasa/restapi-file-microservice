<?php
require_once 'vendor/autoload.php';

use Medoo\Medoo;

// load config.json file for configuration
$_CONFIG = json_decode(file_get_contents("config.json"), true);
 
// database connection
$DB = new Medoo([
    'database_type' => 'sqlite',
    'database_file' => './database/database.sqlite'
]);

$DB->query("DROP TABLE IF EXISTS ".$_ENV['TABLE_NAME']);

$DB->create($_ENV['TABLE_NAME'], [
    "id" => [
        "text",
        "NOT NULL",
        "PRIMARY KEY"
    ],
    "name" => [
        "text",
        "NOT NULL"
    ],
    "extension" => [
        "text",
        "NOT NULL"
    ],
    "mime" => [
        "text",
        "NOT NULL"
    ],
    "size" => [
        "integer",
        "NOT NULL"
    ],
    "path" => [
        "text",
        "NOT NULL"
    ],
    "expired_at" => [
        "text",
        "NULL"
    ]
]);

echo "Table '".$_ENV['TABLE_NAME']."' successfully created!";
