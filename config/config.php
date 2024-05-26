<?php

	if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
		die ('No direct access allowed.');
	}

	define('DOC_ROOT', dirname(__DIR__, 1));  // Includes any sub-directory.

  ////////////////////////
  // Load Config File ////
  ////////////////////////
	$cfg['user'] = get_current_user();
	$cfg['home_path'] = exec('echo ~');
	$cfg['db_user']  = '';
	$cfg['db_pass']  = '';
	$cfg['database'] = '';
	$cfg['hostspec'] = 'localhost';
	$cfg['timezone'] = '';
	date_default_timezone_set($cfg['timezone']);
  ////////////////////////

	$cfg['domain'] = '';
	$cfg['debug'] = false;  // Turns debug logging on or off.

	$cfg['sub_dir'] = '';
	if (sub_dir_bool_check()) {
		$cfg['sub_dir'] = '/'. basename(DOC_ROOT);
	}

	$cfg['error_log'] = DOC_ROOT . '/logs/php_error_log_obm';
	$cfg['debug_log'] = DOC_ROOT . '/logs/obm_d-bug.log';

	$cfg['locale'] = 'en-US';
	$cfg['user_agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/124.0.2478.109';

	$cfg['cookie'] = [
		'name'   => 'ob_cookie',
		'domain' => '',
		'path'   => '/',
		'seed'   => '4Xp2yHprO6oTy5',
		'expire' => time() + 31_536_000,
	];

  // Feel free to add values to this list as you like, according to the PHP documentation.
  ## http://www.php.net/manual/en/function.date.php
	$cfg['date_formats'] = [
		'd/m/Y',
		'Y-m-d',
		'm/d/Y',
		'd.m.Y',
		'F j, Y',
		'dS \o\f F Y',
		'dS F Y',
		'd F Y',
		'd. M Y',
		'Y F d',
		'F d, Y',
		'M. d, Y',
		'm/d/Y',
		'm-d-Y',
		'm.d.Y',
		'm.d.y',
	];

	$cfg['convert_favicons'] = true;
	$cfg['icon_size'] = '24x24';
	$cfg['convert']   = '/usr/bin/convert';
	$cfg['identify']  = '/usr/bin/identify';
	$cfg['timeout']   = 5;
	[$cfg['icon_w'], $cfg['icon_h']] = explode('x', $cfg['icon_size']);

	$folder_closed        = '<img src="'. $cfg['sub_dir'] .'/images/folder.gif" alt="">';
	$folder_opened        = '<img src="'. $cfg['sub_dir'] .'/images/folder_open.gif" alt="">';
	$folder_closed_public = '<img src="'. $cfg['sub_dir'] .'/images/folder_red.gif" alt="">';
	$folder_opened_public = '<img src="'. $cfg['sub_dir'] .'/images/folder_open_red.gif" alt="">';

	$plus           = '<img src="'. $cfg['sub_dir'] .'/images/plus.gif" alt=""> ';
	$minus          = '<img src="'. $cfg['sub_dir'] .'/images/minus.gif" alt=""> ';
	$neutral        = '<img src="'. $cfg['sub_dir'] .'/images/spacer.gif" width="13" height="1" alt=""> ';
	$edit_image     = '<img src="'. $cfg['sub_dir'] .'/images/edit.gif" title="%s" alt="">';
	$move_image     = '<img src="'. $cfg['sub_dir'] .'/images/move.gif" title="%s" alt="">';
	$delete_image   = '<img src="'. $cfg['sub_dir'] .'/images/delete.gif" title="%s" alt="">';
	$bookmark_image = '<img src="'. $cfg['sub_dir'] .'/images/bookmark.gif" alt="">';
	
	if ($cfg['debug']) {
		ini_set('display_errors', 1);
		ini_set('error_prepend_string', '<pre style="white-space: pre-wrap;">');
		ini_set('error_append_string', '</pre>');
	}


	function sub_dir_bool_check() {
		$root = $_SERVER['DOCUMENT_ROOT'];
		$file_path = dirname(__FILE__, 2);  // Gives path of config.php
// echo 'file_path: '. $file_path . '<br>';

		if ($root == $file_path) {
			return false;  // Installed in the root.
		}
		else {
			return true;  // Installed in a subdirectory.
		}
	}


/*
javascript:(function(){bmadd=window.open('https://obm.domain.com/bookmarks/new_bookmark.php?title='+encodeURIComponent(document.title)+'&url='+encodeURIComponent(location.href),'bmadd','toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=500,left=50,top=50');setTimeout(function(){bmadd.focus();});})();
*/
