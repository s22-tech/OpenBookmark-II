#!/usr/local/bin/php
<?php

	ini_set('display_errors', 1);
	require_once(realpath(dirname(__DIR__, 1) . '/config/config.php'));
	require_once(realpath(dirname(__DIR__, 1) . '/lib/mysql.php'));
	$mysql = new mysql;
	
	$param = $argv[1] ?? PHP_SAPI ?? '';

//////////////////////////////////
//////////////////////////////////
	$query = "
		SELECT * FROM `obm_bookmarks`
		GROUP BY `favicon` HAVING COUNT(`favicon`) >= 1;
	";
	
	if ($mysql->query($query)) {
		$favicons_sql = [];
		while ($row = mysqli_fetch_assoc($mysql->result)) {
			if (!empty($row['favicon'])) {
				$favicons_sql[$row['id']] = $row['favicon'];
			}
		}
	}
	

//////////////////////////////////
//////////////////////////////////
	$favicons = [];
	$icons_path = realpath(DOC_ROOT . '/icons/');
	foreach (glob($icons_path . '/*') as $filename) {
		if (!in_array(basename($filename), $favicons_sql)) {
			$favicons[] = $filename;
			unlink($filename);
		}
	}


//////////////////////////////////
//////////////////////////////////
	if ($param !== 'cron') {
		$deleted_icons = implode(PHP_EOL, $favicons);
		echo 'Deleted favicons:' . PHP_EOL;
		echo $deleted_icons . PHP_EOL;
	}

