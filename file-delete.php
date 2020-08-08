<?php
require "index.php";
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	// AMBIL DAFTAR GAMBAR YANG EXPIRED
	$data = $DB->get($_CONFIG['TABLE_NAME'], ["id", "name", "extension"], array("id" => $_POST['id']));

	if(!empty($data))
	{
		unlink("./".$_CONFIG['FILE_STORAGE'].$data['name'].".".$data['extension']);
		$DB->delete($_CONFIG['TABLE_NAME'], ["id" => $data['id']]);
	}

	echo restApi::ok();
	exit;
}

echo restApi::error("Not allowed");