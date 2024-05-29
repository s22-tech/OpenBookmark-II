<?php

if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
	die ('No direct access allowed!');
}

require_once(realpath(__DIR__ . '/header.php'));
include_once(realpath(DOC_ROOT . '/vendor/simplehtmldom/HtmlWeb.php'));
// include_once(realpath(DOC_ROOT . '/vendor/autoload.php'));
use simplehtmldom\HtmlWeb;

class Favicon
{
	public $url;
	public $favicon;
	public $icon_name;
	public $parsed_url;
	public $favicon_url;
	public $temp_icon_name;
	public $notes;

	function __construct($url) {
		global $cfg, $settings;

		$this->url = $url;
		$this->notes = '';

		if ($settings['show_bookmark_icon']) {

			if ($this->parsed_url = $this->return_parse_url($url)) {
				if ($this->favicon_url = $this->get_favicon_url()) {
					$this->download_favicon_image();
					$this->icon_name = $this->rename_favicon($url . '/'. $this->temp_icon_name);
					$favicon_url_path = DOC_ROOT . '/icons/'. $this->icon_name;


					$tmp_file = DOC_ROOT . '/tmp/'. $this->temp_icon_name;
					[$fav_ext, $ident, $icons] = $this->identify_fav($tmp_file);

					if ($cfg['convert_favicons']) {
						$this->favicon = $this->convert_favicon($tmp_file, $fav_ext, $ident, $icons);
					}
					else {
					  // Move the file from the tmp dir.
						rename($tmp_file, $favicon_url_path);
						$this->favicon = $favicon_url_path;
					}
				}
				else {
					echo '<span style="color:red">&bull; favicon.php &mdash; no favicon was retrieved.<br></span>';
				}
			}
		}
		if (isset($this->favicon)) {
			return $this->favicon;
		}
		else {
			return false;
		}
	}

  ###
  ### Check the image type, and convert/resize it if required.
  ### Returns the absolute path of the converted file
  ### Does not convert jpg or svg files, as they are smaller than png.
  ###
	function convert_favicon($tmp_file, $fav_ext, $ident, $icons) : string {
		global $cfg;
		$tmp_ext = pathinfo($tmp_file, PATHINFO_EXTENSION);
		$new_ext = 'png';


		$new_name = $this->icon_name;
		$converted_file_path = DOC_ROOT . '/tmp/'. $new_name;
		$save_path_name = DOC_ROOT . '/icons/'. $new_name;

		if ($ident && $fav_ext !== 'svg') {
			$new_name = str_replace($tmp_ext, $new_ext, $new_name);
			$converted_file_path = DOC_ROOT . '/tmp/'. $new_name;
			$convert = "{$cfg['convert']} $tmp_file -resize {$cfg['icon_size']}\> -unsharp 0x1 $converted_file_path";

		  // Convert image to .png, and resize to $cfg['icon_size'] if original is different.
		  ## https://legacy.imagemagick.org/Usage/resize/
			system($convert, $status);

			if (count($icons) > 1) {
				$multi_ico_name = str_replace('.'.$new_ext, '-0.'.$new_ext, $converted_file_path);
				rename($multi_ico_name, $save_path_name); 
			}
			else {
				rename($converted_file_path, $save_path_name); 
			}
		}
		else {
			$rename = rename($tmp_file, $save_path_name);  // Move & rename the file.
		}
		
		if (file_exists($tmp_file)) {
			unlink($tmp_file);
		}
		if (count($icons) > 1) {
			foreach ($icons as $key => $icon) {
				$icon = rtrim($icon, ']');
				[$path, $num] = explode('[', $icon);
				$path = str_replace($tmp_file, $converted_file_path, $path);
				$path = str_replace('.'. $new_ext, '-'.$num . '.' . $new_ext, $path);
				@unlink($path);
			}
		}
		return basename($save_path_name);
	}


  ///////////////////////////////
  // Download and Save Favicon
  // https://www.php.net/manual/en/function.curl-setopt.php
  ///////////////////////////////
	function download_favicon_image() {
		global $cfg;
		$save_path_name = DOC_ROOT . '/tmp/'. $this->temp_icon_name;

		if (str_contains($this->favicon_url, 'base64')) {
			$this->notes = 'This favicon was in base64.';
			$data = preg_replace("#$this->url#", '', $this->favicon_url);
			file_put_contents($save_path_name.'.png', file_get_contents($data));
		}
		else {
			$open_file_in_binary = fopen($save_path_name, 'wb');
			$options = [
				CURLOPT_URL            => $this->favicon_url,
				CURLOPT_USERAGENT      => $cfg['user_agent'],
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CONNECTTIMEOUT => 2,
				CURLOPT_TIMEOUT        => 5,
				CURLOPT_FILE           => $open_file_in_binary,
				CURLOPT_HEADER         => 0,
			  /* Lets you use this script when there is redirect on the server. */
				CURLOPT_FOLLOWLOCATION => true,
			  /* Auto detect encoding for the response | identity deflation and gzip */
				CURLOPT_ENCODING       => '',
			];
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$response = curl_exec($ch);
			curl_close($ch);

		  // Close the file pointer.
			fclose($open_file_in_binary);
		}

		if (admin_only()) {
// 			$bytes = file_put_contents($save_path_name, $response);
		}

		return true;
	}



	function get_favicon_url() {
		$host_url = $this->parsed_url['scheme'] . '://' . $this->parsed_url['host'];
		$host_url = rtrim($host_url, '/');

	  ## https://github.com/simplehtmldom/simplehtmldom
	  ## https://sourceforge.net/p/simplehtmldom/bugs/
	  ## https://simplehtmldom.sourceforge.io/docs/1.9/
		$dom = new HtmlWeb();
		$html = $dom->load($host_url);


		if (empty($html)) {
			echo '<span style="color:red">&bull; favicon.php &mdash; $html is blank.<br></span>';
			return;
		}

		$favicon_url = '';
		foreach ($html->find('link') as $e) {
			if (!empty($e->rel)) {
				if (strtolower(trim($e->rel)) === 'shortcut icon' || strtolower(trim($e->rel)) === 'icon') {
					$favicon_url = $e->href;
					break;
				}
				elseif (strtolower(trim($e->rel)) === 'apple-touch-icon') {
					$favicon_url = $e->href;
					break;
				}
			}
		}

		if (empty($favicon_url)) return;  // So as not to populate the /tmp/ directory.

	  // Remove extraneous parameters.
		if (str_contains($favicon_url, '?')) {
			[$favicon_url] = explode('?', $favicon_url);
		}

	  // If link doesn't start with http...
		if (str_starts_with($favicon_url, '//')) {
			$favicon_url = 'https:'. $favicon_url;
		}
		elseif (str_starts_with($favicon_url, '/')) {
			$favicon_url = $host_url . $favicon_url;
		}
		elseif (!str_starts_with($favicon_url, 'http')) {
			$favicon_url = $host_url .'/'. $favicon_url;
		}

		$html->clear();
		unset($html);

		
		$this->temp_icon_name = basename(parse_url($favicon_url, PHP_URL_PATH));

		return $favicon_url;
	}


	function get_html_dom($url) {
		global $cfg;
	  // If a saved URL changes and gets redirected to a new one...
			$options = [
				CURLOPT_URL            => $url,
				CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible;  MSIE 7.01; Windows NT 5.0)',
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CONNECTTIMEOUT => 2,
				CURLOPT_TIMEOUT        => 10,
			  /* Lets you use this script when there is redirect on the server. */
				CURLOPT_FOLLOWLOCATION => true,
			  /* Auto detect encoding for the response | identity deflation and gzip */
				CURLOPT_ENCODING       => '',
			];
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}


	###
	### Returns an array with parts of the given URL.
	###
	function return_parse_url($url) {
		if ($parsed = parse_url($url)) {
			if (empty($parsed['scheme']) || $parsed['scheme'] == '') {
				$parsed['scheme'] = 'https';
			}
			if (empty($parsed['host']) || $parsed['host'] == '') {
				return false;
			}
			if (empty($parsed['port']) || $parsed['port'] == '') {
				$parsed['port'] = 80;
			}
			if (empty($parsed['path']) || $parsed['path'] == '') {
				$parsed['path'] = '/';
			}
			return ($parsed);
		}
		else {
			return false;
		}
	}
	
	
	function rename_favicon($domain) {
		$parsed = parse_url($domain);
		$host_name = $parsed['host'];
		$host_name = str_replace('.', '-', $host_name);
		$parts = explode('-', $host_name);
		$last = array_pop($parts);
		$parts = [implode('-', $parts), $last];  // Get domain w/o the domain extension.
		$host_name = $parts[0];
		$host_name = str_replace('www-', '', $host_name);
		$ext = pathinfo($this->favicon_url, PATHINFO_EXTENSION);
		$ext = strlen($ext) < 3 ? 'png' : $ext;
		return $host_name . '.' . $ext;
	}
	
	
	function identify_fav($tmp_file) {
		global $cfg;
		$tmp_ext  = pathinfo($tmp_file, PATHINFO_EXTENSION);
		exec($cfg['identify'] .' '. $tmp_file, $output);

		$icons = [];
		if ($output) {
			foreach ($output as $lines) {
				$parts = explode(' ', $lines);
					$icons[] = $parts[0];
			}
			$idents = explode(' ', $output[0]);
			$file_ext = strtolower($idents[1]);
			$file_ext = ($file_ext === 'jpeg') ? 'jpg' : $file_ext;

			return [$file_ext, true, $icons];
		}
		else {
			return ['png', false, $icons];
		}
	}
	
	
	function get_current_url($url) {
		global $cfg;
	  // If a saved URL changes and gets redirected to a new one...
			$options = [
				CURLOPT_URL            => $url,
				CURLOPT_USERAGENT      => $cfg['user_agent'],
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CONNECTTIMEOUT => 2,
				CURLOPT_TIMEOUT        => 10,
			  /* Lets you use this script when there is redirect on the server. */
				CURLOPT_FOLLOWLOCATION => true,
			  /* Auto detect encoding for the response | identity deflation and gzip */
				CURLOPT_ENCODING       => '',
			];
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);

		return $code;
	}

}
