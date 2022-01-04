<?php
namespace App\Helper;

class ApiResponse
{
	public static function Ok($data, $code = 200, $message = "Ok")
	{
		return array(
			"status" => true,
			"message" => $message,
			"code" => $code,
			"data" => $data
		);
	}

	public static function Error($data, $code = 503, $message = "Error")
	{
		return array(
			"status" => false,
			"message" => $message,
			"code" => $code,
			"data" => $data
		);
	}

	public static function NotFound($message)
	{
		return ApiResponse::Error(null, 404, $message);
	}

	public static function NotAllowed($message)
	{
		return ApiResponse::Error(null, 401, $message);
	}

	public static function Forbidden($message)
	{
		return ApiResponse::Error(null, 403, $message);
	}
}