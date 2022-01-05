<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Helper\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


// VARIABEL INI BERISI KONFIGURASI MICROSERVICE
$_CONFIG = json_decode(file_get_contents("../config.json"), true);

// ENDPOINT UNTUK MENERIMA FILE
// FILE YANG DITERIMA ADALAH FILE DENGAN NAME YANG SUDAH DITENTUKAN DIDALAM FUNGSI
// FILE YANG DITERIMA MEMILIKI STATUS SEMENTARA SEHINGGA AKAN DIHAPUS DALAM JANGKA WAKTU 1 JAM JIKA TIDAK DISET KE PERMANENT
$router->post('/add', function () use ($router, $_CONFIG) {

	// INISIALISASI LOKASI FOLDER FILE
    $storage = new \Upload\Storage\FileSystem($_CONFIG['FILE_STORAGE']);

    // CEK APAKAH NAME DARI FILE YANG DIUPLOAD USER BERDASARKAN FORM_INPUT_NAME PADA CONFIG.JSON
    if(!empty($_FILES))
    {
	    foreach ($_CONFIG['FORM_INPUT'] as $value)
	    {
	    	if(!empty($_FILES[$value['name']]))
	    	{
	        	// CEK APAKAH FILE YANG DIUPLOAD KOSONG ATAU TIDAK
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
        }
    }


    if(!isset($file))
    {
        return response()->json(ApiResponse::Error("File tidak boleh kosong!"));
    }

    // ATUR NAMA FILE YANG AKAN DISIMPAN
    $nama_file = uniqid();
    $file->setName($nama_file);

    // SIMPAN INFORMASI FILE
    $data = array(
    	'id' 		 => $nama_file,
        'name'       => $file->getName(),
        'extension'  => $file->getExtension(),
        'mime'       => $file->getMimetype(),
        'size'       => $file->getSize(),
        'path'       => $_CONFIG['FILE_STORAGE']
    );

    // COBA SIMPAN FILE KE FOLDER
    try {
        // PROSES PEMINDAHAN FILE KE FOLDER
        $file->upload();

        // ATUR MASA BERLAKU FILE KARENA FILE YANG DISIMPAN AKAN BERSIFAT SEMENTARA
        $data['expired_at'] = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $_CONFIG['ACTIVE_PERIOD_FILE'] . " " . $_CONFIG['PERIOD_UNIT']));

        // SIMPAN INFORMASI FILE KE DATABASE
        DB::table('files')->insert($data);

        return response()->json(ApiResponse::Ok($data, 200, "Berkas berhasil diunggah!"));
    }
    catch (Exception $e)
    {
        return response()->json(ApiResponse::Error($e->getMessage(), 422));
    }
});

$router->get('/info/{id}', function ($id) use ($router) {
    // PERINTAH UNTUK MENGHAPUS GAMBAR YANG EXPIRED_AT TIDAK NULL ALIAS GAMBAR SEMENTARA
	$data = DB::table('files')->select("*")->where("id", $id)->first();

	if(!empty($data))
	{
		return response()->json(ApiResponse::Ok($data));
	}
	return response()->json(ApiResponse::NotFound("Berkas tidak ditemukan!"));
});

$router->get('/delete/{id}', function (Request $request, $id) use ($router) {
	$data = DB::table('files')->select("*")->where("id", $id)->first();

	if(!empty($data))
	{
		// hapus data dari database
		DB::table("files")->where("id", $id)->delete();

		// hapus file
		unlink("./files/".$data->name.".".$data->extension);

		return response()->json(ApiResponse::Ok("Berkas berhasil dihapus!"));
	}
	return response()->json(ApiResponse::NotFound("Berkas tidak ditemukan!"));
});

$router->get('/download/{id}[/{name}]', function (Request $request, $id, $name = null) use ($router) {
	$data = DB::table('files')->select("*")->where("id", $id)->first();

	if(!empty($data))
	{
		$file = "./files/".$data->id.".".$data->extension;

		if(empty($name))
		{
			$name = $data->id;
		}

		if(file_exists($file))
		{
		    header('Content-Description: File Transfer');
		    header('Content-Type: '.$data->mime);
		    header('Content-Disposition: attachment; filename="'.basename($name).".".$data->extension.'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
		    exit;
		}
	}
	return response()->json(ApiResponse::NotFound("Berkas tidak ditemukan!"));
});

$router->get('/view/{id}', function (Request $request, $id, $name = null) use ($router) {
	$data = DB::table('files')->select("*")->where("id", $id)->first();

	if(!empty($data))
	{
		$file = "./files/".$data->id.".".$data->extension;

		if(file_exists($file))
		{
			header('Content-type: '.$data->mime);
			header('Content-Disposition: inline; filename="'.basename($data->name).".".$data->extension.'"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
		    readfile($file);
		    exit;
		}
	}
	return response()->json(ApiResponse::NotFound("Berkas tidak ditemukan!"));
});


$router->post('/permanent', function (Request $request) use ($router) {
	$data = DB::table('files')->select("*")->where("id", $request->input("id", ""))->first();

	if(!empty($data))
	{
		DB::table("files")->where("id", $request->input("id", ""))->update(["expired_at" => null]);
		return response()->json(ApiResponse::Ok("Berkas berhasil dipermanenkan!"));
	}
	return response()->json(ApiResponse::NotFound("Berkas tidak ditemukan!"));
});


$router->get('/', function () use ($router) {
    return $router->app->version();
});
