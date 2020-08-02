<?php
require_once "index.php";
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == "POST")
{

	$storage = new \Upload\Storage\FileSystem($_ENV['FILE_STORAGE']); // FILE TERLETAK DI FOLDER FILES
	$file = new \Upload\File($_ENV['FORM_INPUT_NAME'], $storage); // NAME DARI FILE DARI HTTP POST ATAU FORM DATA

	// Optionally you can rename the file on upload
	$nama_file = uniqid().time();
	$file->setName($nama_file);

	// Access data about the file that has been uploaded
	$data = array(
	    'name'       => $file->getName(),
	    'extension'  => $file->getExtension(),
	    'mime'       => $file->getMimetype(),
	    'size'       => $file->getSize(),
	    'path'		 => $_ENV['FILE_STORAGE']
	);

	// Try to upload file
	try {
	    // Success!
	    $file->upload();
	    
	    // set images expired after upload
	    $data['expired_at'] = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." + ".$_ENV['ACTIVE_PERIOD_FILE']." ".$_ENV['PERIOD_UNIT']));

	    // save images data into database
	    // images data are stored inside "_files" table
	    $DB->insert($_ENV['TABLE_NAME'], $data);
	    $data["id"] = $DB->id();
	    $data['base_url'] = $_ENV['BASE_URL'].$_ENV['FILE_STORAGE'];

		echo restApi::ok($data);

	} catch (Exception $e) {
	    // Fail!
		echo restApi::error($file->getMessage());
	}
}
else
{
	echo restApi::error("Not allowed!");
}