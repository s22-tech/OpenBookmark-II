<?php

	if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
		die ('No direct access allowed.');
	}

	define('APPLICATION_PATH', realpath(dirname(__FILE__, 2)));  // Includes the sub-directory.

  ////////////////////////
  // Load Config File ////
  ////////////////////////
	$cfg['user'] = get_current_user();
	$cfg['home_path'] = exec('echo ~');
	$cfg['projects_path'] = $cfg['home_path'].'/projects';
	include_once($cfg['projects_path'].'/scripts/php/library/lib_functions_dtp.inc.php');
	$cfg['db_user']  = functions_dtp::global__get_variable('sql_username_html');
	$cfg['db_pass']  = functions_dtp::global__get_variable('sql_password_html');
	$cfg['database'] = $cfg['user'].'_s22_obm';
	$cfg['hostspec'] = 'localhost';
	$cfg['timezone'] = 'America/Los_Angeles';
	date_default_timezone_set($cfg['timezone']);
  ////////////////////////

	$cfg['domain'] = 's22.us';

	$cfg['error_log'] = APPLICATION_PATH . '/logs/php_error_log_obm';
	$cfg['debug_log'] = APPLICATION_PATH . '/logs/obm_d-bug.log';

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

	$folder_closed        = '<img src="./images/folder.gif" alt="">';
	$folder_opened        = '<img src="./images/folder_open.gif" alt="">';
	$folder_closed_public = '<img src="./images/folder_red.gif" alt="">';
	$folder_opened_public = '<img src="./images/folder_open_red.gif" alt="">';

	$plus           = '<img src="./images/plus.gif" alt=""> ';
	$minus          = '<img src="./images/minus.gif" alt=""> ';
	$neutral        = '<img src="./images/spacer.gif" width="13" height="1" alt=""> ';
	$edit_image     = '<img src="./images/edit.gif" title="%s" alt="">';
	$move_image     = '<img src="./images/move.gif" title="%s" alt="">';
	$delete_image   = '<img src="./images/delete.gif" title="%s" alt="">';
	$bookmark_image = '<img src="./images/bookmark.gif" alt="">';

	$cfg['sub_dir'] = '';
	if (sub_dir_bool_check()) {
		$cfg['sub_dir'] = '/'. basename(APPLICATION_PATH);
	}


	function sub_dir_bool_check() {
		$root = $_SERVER['DOCUMENT_ROOT'];
		$file_path = dirname($_SERVER['SCRIPT_FILENAME']);  // Gives path of "calling" file.

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
