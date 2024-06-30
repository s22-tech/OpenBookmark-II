<?php

if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
	die ('No direct access allowed!');
}

require_once(realpath(__DIR__ . '/includes/app_header.inc.php'));
include_once(realpath(DOC_ROOT . '/vendor/simplehtmldom/HtmlWeb.php'));
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
		global $cfg, $settings, $mysql;

	  // Commonly saved favicons.  No need to download them again.
		include_once(realpath(__DIR__ . '/includes/favicons_list.php'));
		foreach($favicons_array as $k => $v) {
			if (str_contains($url, $k)) {
				$this->favicon = $v;
				return $this->favicon;
			}
		}

		$this->url = $url;
		$this->notes = '';

		if ($settings['show_bookmark_icon']) {
debug_logger(name:'--------', variable: '-------------------------', file: '--------', function: '--------');
debug_logger(name:'URL', variable: $url, newline: false, file: __FILE__, function: __FUNCTION__, time: true);

			if ($this->parsed_url = $this->return_parse_url($url)) {
				if ($this->favicon_url = $this->get_favicon_url()) {
					$this->download_favicon_image();
					$this->icon_name = $this->rename_favicon($url . '/'. $this->temp_icon_name);
					$favicon_url_path = DOC_ROOT .'/icons/'. $this->icon_name;

debug_logger(name:'this->temp_icon_name', variable: $this->temp_icon_name, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'this->favicon_url',    variable: $this->favicon_url, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'this->icon_name',      variable: $this->icon_name,   newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'favicon_url_path',     variable: $favicon_url_path, file: __FILE__, function:__FUNCTION__);

					$tmp_file = DOC_ROOT . '/tmp/'. $this->temp_icon_name;
					[$fav_ext, $ident, $icons] = $this->identify_fav($tmp_file);
debug_logger(name:'tmp_file', variable: $tmp_file, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'fav_ext',  variable: $fav_ext, file: __FILE__, function:__FUNCTION__);

					if ($cfg['convert_favicons']) {
						$this->favicon = $this->convert_favicon($tmp_file, $fav_ext, $ident, $icons);
debug_logger(name:'converted favicon', variable: $this->favicon, file: __FILE__, function:__FUNCTION__);
					}
					else {
					  // Move the file from the tmp dir.
						rename($tmp_file, $favicon_url_path);
						$this->favicon = $favicon_url_path;
debug_logger(name:'no conversion favicon', variable: $this->favicon, file: __FILE__, function:__FUNCTION__);
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

debug_logger(name:'ident-stat', variable: $ident, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'tmp_file',   variable: $tmp_file, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'tmp_ext',    variable: $tmp_ext, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'fav_ext',    variable: $fav_ext, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'icons',      variable: $icons, newline: false, file: __FILE__, function:__FUNCTION__);

		$new_name = $this->icon_name;
debug_logger(name:'new_name1', variable: $new_name, newline: false, file: __FILE__, function:__FUNCTION__);
		$converted_file_path = DOC_ROOT . '/tmp/'. $new_name;
		$save_path_name = DOC_ROOT . '/icons/'. $new_name;

		if ($ident && $fav_ext !== 'svg') {
			$new_name = str_replace($tmp_ext, $new_ext, $new_name);
debug_logger(name:'new_name2', variable: $new_name, newline: false, file: __FILE__, function:__FUNCTION__);
			$converted_file_path = DOC_ROOT . '/tmp/'. $new_name;
debug_logger(name:'converted_file_path', variable: $converted_file_path, newline: false, file: __FILE__, function:__FUNCTION__);
			$convert = "{$cfg['convert']} $tmp_file -resize {$cfg['icon_size']}\> -unsharp 0x1 $converted_file_path";
debug_logger(name:'convert-cmd', variable: $convert, newline: false, file: __FILE__, function:__FUNCTION__);

		  // Convert image to .png, and resize to $cfg['icon_size'] if original is different.
		  ## https://legacy.imagemagick.org/Usage/resize/
			system($convert, $status);
debug_logger(name:'SUCCESS--conversion status', variable: $status, file: __FILE__, function:__FUNCTION__);

			if (count($icons) > 1) {
				$multi_ico_name = str_replace('.'.$new_ext, '-0.'.$new_ext, $converted_file_path);
				rename($multi_ico_name, $save_path_name); 
			}
			else {
				rename($converted_file_path, $save_path_name); 
			}
		}
		else {
			if (!empty($tmp_ext) && !str_ends_with($tmp_file, $tmp_ext)) {
				$tmp_file .= '.'. $tmp_ext;
			}
			$rename = rename($tmp_file.'.png', $save_path_name);  // Move & rename the file.
debug_logger(name:'MOVED--no conversion', variable: $this->icon_name, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'tmp_file2',            variable: $tmp_file, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'save_path_name2',      variable: $save_path_name, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'rename-move2',         variable: $rename, file: __FILE__, function:__FUNCTION__);
		}
		
		if (file_exists($tmp_file)) {
			debug_logger(name:'tmp_file_exists - YES', variable:'', file: __FILE__, function:__FUNCTION__);
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
debug_logger(name:'this->favicon_url', variable: $this->favicon_url, newline: false, file: __FILE__, function: __FUNCTION__);
debug_logger(name:'temp_icon_name', variable: $save_path_name, newline: false, file: __FILE__, function: __FUNCTION__);

		if (str_contains($this->favicon_url, 'base64')) {
			$this->notes = 'This favicon was in base64.';
			$data = preg_replace("#$this->url#", '', $this->favicon_url);
			file_put_contents($save_path_name.'.png', file_get_contents($data));
debug_logger(name:'data', variable: $data, newline: false, file: __FILE__, function: __FUNCTION__);
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
debug_logger(name:'response', variable: $response, file: __FILE__, function: __FUNCTION__);
		}

		if (admin_only()) {
// 			$bytes = file_put_contents($save_path_name, $response);
// debug_logger(name:'bytes saved', variable: $bytes, file: __FILE__, function: __FUNCTION__);
		}

		return true;
	}



	function get_favicon_url() {
		$host_url = $this->parsed_url['scheme'] . '://' . $this->parsed_url['host'];
		$host_url = rtrim($host_url, '/');
debug_logger(name:'host_url', variable: $host_url, newline: false, file: __FILE__, function:__FUNCTION__);

	  ## https://github.com/simplehtmldom/simplehtmldom
	  ## https://sourceforge.net/p/simplehtmldom/bugs/
	  ## https://simplehtmldom.sourceforge.io/docs/1.9/
		$dom = new HtmlWeb();
		$html = $dom->load($host_url);

// DOES THIS SECTION DO ANYTHING BENEFICIAL?
		if (empty($html)) {
// 			sleep(3);
// 			$html_string = $this->scrape_html($host_url);
// 			$html = str_get_html($html_string);
// 			if (empty($html)) {
				echo '<span style="color:red">&bull; favicon.php &mdash; $html is blank.<br></span>';
				return;
// 			}
		}

debug_logger(name:'html', variable: $html, file: __FILE__, function:__FUNCTION__);

		$favicon_url = '';
		foreach ($html->find('link') as $e) {
			if (!empty($e->rel)) {
				if (strtolower(trim($e->rel)) === 'shortcut icon' || strtolower(trim($e->rel)) === 'icon') {
debug_logger(name:'••• e->rel', variable: $e->rel, newline: false, file: __FILE__, function:__FUNCTION__);
					$favicon_url = $e->href;
					break;
				}
				elseif (strtolower(trim($e->rel)) === 'apple-touch-icon') {
debug_logger(name:'••• e->rel', variable: $e->rel, newline: false, file: __FILE__, function:__FUNCTION__);
					$favicon_url = $e->href;
					break;
				}
			}
		}
		debug_logger(name:'get-favicon--ORIG', variable: $favicon_url, newline: false, file: __FILE__, function:__FUNCTION__);

		if (empty($favicon_url)) return;  // So as not to populate the /tmp/ directory.

	  // Remove extraneous parameters.
		if (str_contains($favicon_url, '?')) {
			[$favicon_url] = explode('?', $favicon_url);
		}

	  // If link doesn't start with http...
		if (str_starts_with($favicon_url, '//')) {
			$favicon_url = 'https:'. $favicon_url;
			debug_logger(name:'get-favicon--2', variable: $favicon_url, newline: false, file: __FILE__, function: __FUNCTION__);
		}
		elseif (str_starts_with($favicon_url, '/')) {
			$favicon_url = $host_url . $favicon_url;
			debug_logger(name:'get-favicon--3', variable: $favicon_url, newline: false, file: __FILE__, function: __FUNCTION__);
		}
		elseif (!str_starts_with($favicon_url, 'http')) {
			$favicon_url = $host_url .'/'. $favicon_url;
			debug_logger(name:'get-favicon--4', variable: $favicon_url, newline: false, file: __FILE__, function: __FUNCTION__);
		}

		$html->clear();
		unset($html);

		debug_logger(name:'get-favicon--FINAL', variable: $favicon_url, file: __FILE__, function: __FUNCTION__);
		
		$this->temp_icon_name = basename(parse_url($favicon_url, PHP_URL_PATH));

		return $favicon_url;
	}


	function scrape_html($url) {
		global $cfg;
	  // If a saved URL changes and gets redirected to a new one...
			$options = [
				CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
				CURLOPT_URL            => $url,
				CURLOPT_FAILONERROR    => true,
				CURLOPT_RETURNTRANSFER => true,
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
				debug_logger(name:'parsed[host]', variable: $parsed['host'], file: __FILE__, function: __FUNCTION__);
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
// 		$parts = explode('-', $host_name);
// 		$last = array_pop($parts);
// 		$parts = [implode('-', $parts), $last];  // Get domain w/o the domain extension.
// 		$host_name = $parts[0];
// debug_logger(name:'•••host_name', variable: $host_name, newline: false, file: __FILE__, function:__FUNCTION__);
		$host_name = str_replace('www-', '', $host_name);
		$ext = pathinfo($this->favicon_url, PATHINFO_EXTENSION);
		$ext = strlen($ext) < 3 ? 'png' : $ext;
debug_logger(name:'•••favicon_name', variable: $host_name . '.' . $ext, file: __FILE__, function:__FUNCTION__);
		return $host_name . '.' . $ext;
	}
	
	
	function identify_fav($tmp_file) {
		global $cfg;
		$tmp_ext  = pathinfo($tmp_file, PATHINFO_EXTENSION);
		exec($cfg['identify'] .' '. $tmp_file, $output);
debug_logger(name:'identify output',    variable: $output, newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'identify tmp_file',  variable: $tmp_file, newline: false, file: __FILE__, function:__FUNCTION__);

		$icons = [];
		if ($output) {
			foreach ($output as $lines) {
				$parts = explode(' ', $lines);
					$icons[] = $parts[0];
			}
			$idents = explode(' ', $output[0]);
			$file_ext = strtolower($idents[1]);
debug_logger(name:'identify idents[0]', variable: $idents[0], newline: false, file: __FILE__, function:__FUNCTION__);
debug_logger(name:'identify idents[1]', variable: $file_ext, newline: false, file: __FILE__, function:__FUNCTION__);
			$file_ext = ($file_ext === 'jpeg') ? 'jpg' : $file_ext;
debug_logger(name:'identify file_ext',  variable: $file_ext, newline: false, file: __FILE__, function:__FUNCTION__);

			return [$file_ext, true, $icons];
		}
		else {
debug_logger(name:'identify return', variable:'', newline: false, file: __FILE__, function:__FUNCTION__);
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

__halt_compiler();

Test Icons
• hostinger.com has an ICI file with multiple images
• some site ??? had a base64 image  --  Which one was it?

debug_logger(name:$name, variable: $variable, $type, $file, $function)
prints out:
error_log('• '. basename($file) .':'.$function.'()->$'. $name .': '. $variable . PHP_EOL, 3, $cfg['error_log']);
e.g.:
• favicon.php:__construct()->$__construct1: https://www.invizbox.com/products/invizbox-2-pro/#select-plan

NOTES:
• If the site's favicon is an svg image, DO NOT convert it.  It looks terrible as a png.
• Don't convert icons to jpg.  JPEG doesn't offer transparency, so there may be black banding if the image isn't square.
