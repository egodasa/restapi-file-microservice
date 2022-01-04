<?php
require_once 'vendor/autoload.php';

use Medoo\Medoo;
 
// database connection
$DB = new Medoo([
    'database_type' => 'sqlite',
    'database_file' => './database/database.sqlite'
]);

// AMBIL DAFTAR GAMBAR YANG EXPIRED
$data_file = $DB->query("SELECT id, name, extension FROM files WHERE DATE(expired_at) < DATE()")->fetchAll(PDO::FETCH_ASSOC);

// HAPUS GAMBAR DAN HAPUS DATANYA DARI DATABASE
foreach ($data_file as $data)
{
	unlink("./files/".$data['name'].".".$data['extension']);
	$DB->delete("files", ["id" => $data['id']]);
	echo date("d-m-Y H:i:s")." : success delete ".$data['name']." \n";
}