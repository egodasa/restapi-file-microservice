<?php
require_once 'vendor/autoload.php';

use Medoo\Medoo;
 
// database connection
$DB = new Medoo([
    'database_type' => 'sqlite',
    'database_file' => './database/database.sqlite'
]);

$DB->query("DROP TABLE IF EXISTS files");

$DB->create("files", [
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

echo "Table successfully created!";
