<?php
// error_reporting(0); // agar semua pesan error masuk kedalam JSON
require_once "index.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$storage = new \Upload\Storage\FileSystem($_CONFIG['FILE_STORAGE']); // FILE TERLETAK DI FOLDER FILES

	// CEK APAKAH NAME DARI FILE YANG DIUPLOAD USER BERDASARKAN FORM_INPUT_NAME PADA CONFIG.JSON

	foreach ($_CONFIG['FORM_INPUT'] as $value)
	{
		if(!empty($_FILES))
		{
			if($_FILES[$value['name']]['size'] > 0)
			{
				$file = new \Upload\File($value['name'], $storage); // NAME DARI FILE DARI HTTP POST ATAU FORM DATA
				$file->addValidations(array(
					new \Upload\Validation\Mimetype($value['allowed_mimetype']), // validasi jenis file
					new \Upload\Validation\Size($value['max_size']) // batas ukuran file
				));
				break;
			}
		}
		else
		{
			echo restApi::error("No file were uploaded");
			exit;
		}
	}

	// STOP PROSES JIKA VARIABEL $file KOSONG KARENA TIDAK ADA FILE YANG DIKIRIM
	if(!isset($file))
	{
		echo restApi::error("No file were uploaded");
		exit;
	}

	// Optionally you can rename the file on upload
	$nama_file = uniqid() . time();
	$file->setName($nama_file);

	// Access data about the file that has been uploaded
	$data = array(
		'name'       => $file->getName(),
		'extension'  => $file->getExtension(),
		'mime'       => $file->getMimetype(),
		'size'       => $file->getSize(),
		'path'		 => $_CONFIG['FILE_STORAGE']
	);

	// Try to upload file
	try {
		// Success!
		$file->upload();

		// set images expired after upload
		$data['expired_at'] = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $_CONFIG['ACTIVE_PERIOD_FILE'] . " " . $_CONFIG['PERIOD_UNIT']));

		// save images data into database
		// images data are stored inside "_files" table
		$DB->insert($_CONFIG['TABLE_NAME'], $data);
		$data["id"] = $DB->id();
		$data['base_url'] = $_CONFIG['BASE_URL'] . $_CONFIG['FILE_STORAGE'];

		echo restApi::ok($data);
	} catch (Exception $e) {
		// Fail!
		echo restApi::error($file->getErrors());
	}
} else {
	echo restApi::error("Not allowed!");
}
