<?php

	require_once(realpath(dirname(__DIR__, 1) . '/config/config.php'));
	require_once(realpath(DOC_ROOT . '/lib/mysql.php'));
	$mysql = new mysql;

	$id  = $_GET['id'];

	set_last_visit($id);


	function set_last_visit($id) {
		global $mysql;
		$update_query = sprintf("
			UPDATE `obm_bookmarks` 
			SET `last_visit` = '%s'
			WHERE `id` = '%d'",
				date('Y-m-d H:i:s'),
				$mysql->escape($id)
		);
		$mysql->query($update_query);
	}

__halt_compiler();

