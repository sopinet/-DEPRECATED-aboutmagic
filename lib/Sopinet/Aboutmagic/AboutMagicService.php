<?php
namespace Sopinet\Aboutmagic;

use Sopinet\Aboutmagic\AboutMagicHelper;

class AboutMagicService
{	
	private function processProfile($profile, $ops) {
		$profile = trim($profile);
		$url_data = "https://api.about.me/api/v2/json/user/view/".$profile;
		$dir = $ops['dir'];
		//$dir = $this->container->get('kernel')->getRootDir() . "/../web/profiles/";
		if (!file_exists($dir)) mkdir($dir);
		$file = $dir . md5($url_data);
	
		if (!file_exists($file) || (time() - filemtime($file) > ($ops['cache_time']  + rand(0,1000)))) {
			$data = array(
					"extended" => "true",
					"client_id" => $ops['about_key']
			);
	
			$aboutmagichelper = new AboutMagicHelper();
			$ret = $aboutmagichelper->post_to_url($url_data, $data);
			$fp = fopen($file, 'w');
			fwrite($fp, $ret);
			fclose($fp);
		}
		return $this->getArray($file);
	}
	
	function proccessIMG($url, $ops) {
		// ORIGINAL:			return $url;
	
		// TODO: Hacer configurable desde fuente
		$dir = $ops['dir'];
		//$dir = $this->container->get('kernel')->getRootDir() . "/../web/profiles/";
		if (!file_exists($dir)) mkdir($dir);
		$file = $dir . md5($url) . ".jpg";
		if (!file_exists($file) || (time() - filemtime($file) > ($ops['cache_time']  + rand(0,1000)))) {
			$aboutmagichelper = new AboutMagicHelper();
			$aboutmagichelper->saveFileURL($file, $url);
	
			// WEB INTERESANTE: http://www.rpublica.net/imagemagick/artisticas.html#sepia-tone
	
			$file_efx = $dir . md5($url). "_ex.jpg";
			/* pencil  $command_thumb = "convert -define jpeg:size=300x300 ".$file." -thumbnail 600x400^ -colorspace Gray -negate -edge 1 -negate -blur 0x.5 -gravity center -extent 600x400 ".$file_efx;
			 // sepia:   $command_thumb = "convert -define jpeg:size=300x300 ".$file." -monochrome -thumbnail 600x400^ -sepia-tone 80% -gravity center -extent 600x400 ".$file_efx;
			// azul + sepia: $command_thumb = "convert -define jpeg:size=300x300 ".$file." -thumbnail 300x220^ -sepia-tone 70% -fill blue -tint 80% -gravity center -extent 300x220 ".$file_efx;
			// azul: $command_thumb = "convert -define jpeg:size=300x300 ".$file." -thumbnail 300x220^ -fill blue -tint 60% -gravity center -extent 300x220 ".$file_efx;
			/* sketch: $command_thumb = "convert -define jpeg:size=300x300 ".$file." -thumbnail 600x400^ -sketch 0x20+120 -gravity center -extent 600x400 ".$file_efx;
			// solarize: $command_thumb = "convert -define jpeg:size=300x300 ".$file." -thumbnail 300x220^ -solarize 55 -gravity center -extent 300x220 ".$file_efx;
			*
			*/
			$command_thumb = "convert -define jpeg:size=600x600 ".$file." -thumbnail 600x400^ -colorspace gray -gravity center -extent 600x400 ".$file_efx;
			/*echo $command_thumb;
			 exit();*/
			exec($command_thumb, $output);
		}
		// profiles/
		$ret = md5($url). "_ex.jpg";
		return $ret;
	}
	
	public function getAvatar($profile, $ops) {
		if ($profile['avatar'] == "") $url = $profile["thumbnail_291x187"];
		else $url = $profile["avatar"];
		return $this->proccessIMG($url, $ops);
	}
	
	public function getProfiles($nicknames, $ops) {
		$profiles = explode(",",$nicknames);
		$i = 0;
		foreach($profiles as $pro) {
			$temp_ok = $this->processProfile($pro, $ops);
			if ($temp_ok['first_name'] != "") {
				$profiles_data[$i] = $this->processProfile($pro, $ops);
				$profiles_data[$i]['avatarOK'] = $this->getAvatar($profiles_data[$i], $ops);
				$i++;
			}
		}
		return $profiles_data;
	}
}
