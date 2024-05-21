<?php

	ini_set('display_errors', 1);
	ini_set('error_prepend_string', '<pre style="white-space: pre-wrap;">');
	ini_set('error_append_string', '</pre>');
	
	require_once(realpath(dirname(__FILE__, 2)) . '/header.php');
	logged_in_only();

debug_logger(name:'SERVER[QUERY_STRING]', variable:$_SERVER['QUERY_STRING'], file:__FILE__, function:__FUNCTION__);

	$qs = ltrim($_SERVER['QUERY_STRING'], '?');
	parse_str($qs, $qs_arr);

	$icons_to_delete = $qs_arr['bookmarks'];

	$bmlist = $qs_arr['bmlist'];

// debug_logger(name:'bmlist', variable:$bmlist, 'array', file:__FILE__, function:__FUNCTION__);
// debug_logger(name:'icons_to_delete', variable:$icons_to_delete, 'array', file:__FILE__, function:__FUNCTION__);


	$query = sprintf("
		DELETE FROM `obm_bookmarks` 
		WHERE `id` IN (%s) AND `user`='%s'",
			$mysql->escape($bmlist),
			$mysql->escape($username)
	);
	if ($mysql->query($query)) {
		$mysql->query("
			ALTER TABLE `obm_bookmarks` 
			AUTO_INCREMENT = 1"
		);
		echo '<script> reloadclose(); </script>';
	}
	else {
		message($mysql->error);
	}

// debug_logger(name:'bmlist', variable:$bmlist, 'array', file:__FILE__, function:__FUNCTION__);

	foreach ($icons_to_delete as $favicon) {
		if (is_file(APPLICATION_PATH .'/icons/'. $favicon)) {
			if (!str_contains($favicon, 'bookmark')) {
				unlink(APPLICATION_PATH .'/icons/'. $favicon);
			}
		}
		else {
			debug_logger(name:'ERROR -- No favicon was found for deletion.', variable:$favicon, file:__FILE__, function:__FUNCTION__);
		}
	}

  // Close the window.
	echo "<script> reloadclose(); </script>";

