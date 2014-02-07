<?php
namespace Sopinet\Aboutmagic;

use Buzz\Browser;

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
		$browser = new Browser(new \Buzz\Client\Curl());
		$response = $browser->post($url, array(), $data);

		// Curl is disable? try it with file_get_contents
		if ($response->getContent() == "") {
			$first = true;
			foreach($data as $key => $value) {
				if ($first) { $first = false; $url .= '?'; }
				else $url .= '&';
				$url .= $key . "=" . $value;
			}
			return file_get_contents($url);
		} else {
			return $response->getContent();
		}
	}

	public function saveFileURL($file, $url) {
		$ch = curl_init($url);
		$fp = fopen($file, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

		// Curl is disable? try it with file_get_contents
		if (filesize($file) == 0) {
			file_put_contents($file, file_get_contents($url));
		}
	}	
}
