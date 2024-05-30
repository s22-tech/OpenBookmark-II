<?php

	require_once(realpath(dirname(__DIR__, 1) . '/header.min.php'));
	logged_in_only();

	if (!empty($username) && $username !== 'demo') {

		$qs = ltrim($_SERVER['QUERY_STRING'], '?');
		parse_str($qs, $qs_arr);

		$bmlist          = $qs_arr['bmlist'];     // Comma separated string of bookmark ID's, e.g. 4186,4193,5825
		$icons_to_delete = $qs_arr['bookmarks'];  // Array of favicon name's.

		$query_delete = sprintf("
			DELETE FROM `obm_bookmarks` 
			WHERE `id` IN (%s) AND `user`='%s'",
				$mysql->escape($bmlist),
				$mysql->escape($username)
		);
		if ($mysql->query($query_delete)) {
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
					SELECT COUNT(`id`) AS icon_cnt
					FROM `obm_bookmarks`
					WHERE `favicon` = '%s'",
						$mysql->escape($favicon)
				);
				if ($mysql->query($count_query)) {
					if (mysql_result($mysql->result, 0) > 1) {
					  // Skip deletion if more than one bookmark uses the same favicon.
						continue;
					}
				}

				if (is_file(DOC_ROOT .'/icons/'. $favicon)) {
					unlink(DOC_ROOT .'/icons/'. $favicon);
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
