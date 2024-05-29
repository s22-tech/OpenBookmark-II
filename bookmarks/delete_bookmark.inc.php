<?php

	require_once(realpath(dirname(__DIR__, 1) . '/header.min.php'));
	logged_in_only();

	if (!empty($username) && $username !== 'demo') {
debug_logger(name:'SERVER[QUERY_STRING]', variable:$_SERVER['QUERY_STRING'], file:__FILE__, function:__FUNCTION__);

		$qs = ltrim($_SERVER['QUERY_STRING'], '?');
		parse_str($qs, $qs_arr);

		$icons_to_delete = $qs_arr['bookmarks'];

		$bmlist = $qs_arr['bmlist'];

debug_logger(name:'bmlist', variable:$bmlist, file:__FILE__, function:__FUNCTION__);
debug_logger(name:'icons_to_delete', variable:$icons_to_delete, file:__FILE__, function:__FUNCTION__);


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
				if (is_file(DOC_ROOT .'/icons/'. $favicon)) {
					if (!str_contains($favicon, 'bookmark')) {
						unlink(DOC_ROOT .'/icons/'. $favicon);
					}
				}
				else {
					debug_logger(name:'ERROR -- No favicon was found for deletion.', variable:$favicon, file:__FILE__, function:__FUNCTION__);
				}
			}
		}
	  // Close the window.
		echo "<script> reloadclose(); </script>";
	}
	else {
		echo 'Demo users cannot delete folders.<br><br>' . PHP_EOL;
		echo '<input type="button" value=" Cancel " onclick="self.close()">';
	}

