<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontModel extends Model
{

	public static function callPostCurl($url, $params)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $params,
		));

		$json_response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($status != 200) {
			$error = curl_error($curl);
			$errorNumber = curl_errno($curl);
			curl_close($curl);
			throw new \RuntimeException("HTTP request failed with status $status (cURL $errorNumber): $error");
		}

		curl_close($curl);
		$result    = json_decode($json_response, true);

		return $result;
	}

	//curl to send response with file
	public static function callFilePostCurl($url, $params)
	{

		// curl connection
		$ch = curl_init();
		// set curl url connection
		$curl_url = $url;
		// pass curl url
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		// image upload Post Fields
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		// set CURL ETURN TRANSFER type
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_result = curl_exec($ch);
		curl_close($ch);

		$result    = json_decode($server_result, true);

		return $result;

	}
}
