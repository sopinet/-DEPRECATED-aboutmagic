<?php
namespace Sopinet\Aboutmagic;

class AboutMagicHelper
{
	public function getArray($file) {
		return json_decode($this->getFile($file), true);
	}	
	
	public function getFile($file) {
		$string = file_get_contents($file);
		return $string;
	}
	
	public function post_to_url($url, $data) {
		$browser = new Buzz\Browser();
		$response = $browser->post($url, null, $data);
		return $response;
	}

	public function saveFileURL($file, $url) {
		// TODO: Se puede activar... file_put_contents($file, file_get_contents($url));
		$ch = curl_init($url);
		$fp = fopen($file, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}	
}