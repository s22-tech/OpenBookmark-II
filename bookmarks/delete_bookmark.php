<?php

	require_once(realpath(dirname(__DIR__, 1) . '/includes/header.php'));
	logged_in_only();

	echo <<<CSS
<style>
	input[type=submit] {
		color: #fff;
		background-color: red;
	}

	input[type=submit]:hover {
		color: #fff;
		background-color: black;
	}

	input[type=submit]:active {
		background-color: #230545;
		box-shadow: 0 5px #e1d5ed;
		transform: translateY(4px);
	}
</style>
CSS;

	$bm_array = set_get_num_array('bmlist');

	$bm_cnt = count($bm_array);
	if ($bm_cnt == 0) {
		echo 'No Bookmarks selected.';
	}
	else {
		$bm_list = implode(',', $bm_array);
		$query = sprintf("
			SELECT `title`, `id`, `favicon`, `url`
			FROM `obm_bookmarks` 
			WHERE `id` IN (%s) AND `user`='%s' 
			ORDER BY `title`",
				$mysql->escape($bm_list),
				$mysql->escape($username)
		);
		if ($mysql->query($query)) {
			require_once(realpath(DOC_ROOT . '/bookmarks/bookmark.php'));
			$query_string = 'bmlist=' . implode(',', $bm_array);
?>

	<h2 class="title">Delete These Bookmarks?</h2>
		<div style="width:100%; height:330px; overflow:auto;">

<?php
			$bookmarks = [];
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				array_push($bookmarks, $row);
			}
			list_bookmarks(
				bookmarks: $bookmarks,
				show_checkbox: false,
				show_folder: false,
				show_icon: $settings['show_bookmark_icon'],
				show_link: false,
				show_desc: false,
				show_date: false,
				show_edit: false,
				show_move: false,
				show_delete: false,
				show_share: false,
				show_header: false
			);

			$bm_to_delete = [];
			foreach ($bookmarks as $key => $array) {
				$bm_to_delete[$array['id']] = $array['favicon'];
			}

			if ($bm_cnt > 1) { $plural = 's'; }
			else { $plural = ''; }
?>
		</div>

	<form name="bmdelete" method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
		<div>
			<div style="float:left">
			<input type="button" name="cancel" value=" Cancel " onclick="window.close()">
			</div>

			<div>
			<input type="hidden" name="bm_to_delete" value="<?php echo htmlspecialchars(json_encode($bm_to_delete), ENT_QUOTES, 'UTF-8') ?>">
			</div>

			<div style="float:right">
			<input type="submit" name="submit" value=" Delete Bookmark<?= $plural ?> ">
			</div>
		</div>
	</form>
	
	<br>
	<br>
<?php
		}
		else {
			message($mysql->error);
		}
	}

	if (isset($_POST['submit'])) {
		delete_bookmarks(json_decode($_POST['bm_to_delete']));
	}

	require_once(realpath(DOC_ROOT . '/includes/footer.inc.php'));


	function delete_bookmarks($bm_to_delete) {
		global $mysql, $username;

		$ids = $favicons = [];
		foreach ($bm_to_delete as $id => $favicon) {
			$ids[]      = $id;
			$favicons[] = $favicon;
		}

		$bm_list = implode(',', $ids);

		$query_delete = sprintf("
			DELETE FROM `obm_bookmarks` 
			WHERE `id` IN (%s) AND `user`='%s'",
				$mysql->escape($bm_list),
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

		foreach ($favicons as $favicon) {
			if (!str_contains($favicon, 'bookmark.png')) {
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
				else {
					debug_logger(name: 'ERROR -- Favicon was not found: ', variable: $favicon, file: __FILE__, function: __FUNCTION__);
				}
			}
		}
	  // Close the window.
		echo "<script> reloadclose(); </script>";
	}
?>