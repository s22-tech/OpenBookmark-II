<?php

	require_once(dirname(__DIR__, 1) . '/header.min.php');
	logged_in_only();

	if (!empty($username) && $username !== 'demo') {
debug_logger(name: 'SERVER[QUERY_STRING]', variable: $_SERVER['QUERY_STRING'], file: __FILE__, function: __FUNCTION__);

		$qs = ltrim($_SERVER['QUERY_STRING'], '?');
		parse_str($qs, $qs_arr);

		$bmlist          = $qs_arr['bmlist'];     // Comma separated string of bookmark ID's, e.g. 4186,4193,5825
		$icons_to_delete = $qs_arr['bookmarks'];  // Array of favicon name's.

debug_logger(name: 'bmlist', variable: $bmlist, file: __FILE__, function: __FUNCTION__);
debug_logger(name: 'icons_to_delete', variable: $icons_to_delete, file: __FILE__, function: __FUNCTION__);


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

		foreach ($icons_to_delete as $favicon) {
			if (! str_contains($favicon, 'bookmark.gif')) {
				$count_query = sprintf("
					SELECT COUNT(*)
					FROM `obm_bookmarks`
					WHERE `favicon` = '%d'
					AND `user` = '%s'",
						$mysql->escape($favicon),
						$mysql->escape($username)
				);
				if ($mysql->query($count_query)) {
					if (mysql_result($mysql->result, 0) >= 1) {
					  // Skip deletion if more than one bookmark uses the same favicon.
						continue;
					}
				}

				if (is_file(DOC_ROOT .'/icons/'. $favicon)) {
					unlink(DOC_ROOT .'/icons/'. $favicon);
				}
				else {
					debug_logger(name: 'ERROR -- No favicon was found for deletion.', variable: $favicon, file: __FILE__, function: __FUNCTION__);
				}
			}
		}
	  // Close the window.
		echo "<script> reloadclose(); </script>";
	}
	else {
		echo 'Demo users cannot delete bookmarks.<br><br>' . PHP_EOL;
		echo '<input type="button" value=" Cancel " onclick="self.close()">';
	}

