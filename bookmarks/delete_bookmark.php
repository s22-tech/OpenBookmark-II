<?php

	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	logged_in_only();

	echo <<<CSS
	<style>
		.button {
			text-decoration: none;
			padding: 15px 25px;
			font-size: 1.1em;
			cursor: pointer;
			text-align: center;
			text-decoration: none;
			outline: none;
			color: #fff;
			background-color: #450775;
			border: none;
			border-radius: 10px;
		}

		.button:hover {
			background-color: #220440;
			color: #fff;
		}

		.button:active {
			background-color: #230545;
			box-shadow: 0 5px #e1d5ed;
			transform: translateY(4px);
		}
	</style>
	CSS;

	$bmlist = set_get_num_list('bmlist');
echo '<pre>'; print_r($bmlist); echo '</pre>';

	if (count($bmlist) == 0) {
		echo 'No Bookmarks selected.';
	}
	else {
		$bmlistq = implode(',', $bmlist);
		$query = sprintf("
			SELECT `title`, `id`, `favicon`, `url`
			FROM `obm_bookmarks` 
			WHERE `id` IN (%s) AND `user`='%s' 
			ORDER BY `title`",
				$mysql->escape($bmlistq),
				$mysql->escape($username)
		);
		if ($mysql->query($query)) {
			require_once(realpath(DOC_ROOT . '/bookmarks/bookmarks.php'));
			$query_string = 'bmlist=' . implode(',', $bmlist);
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

			$bm_string = '';
			foreach ($bookmarks as $bookmark) {
				$bm_string .= '&bookmarks[]='.$bookmark['favicon'];
			}
echo '$bm_string: '. $bm_string . '<br>';
?>

	</div>

	<div>
		<div style="float:left">
		<input type="button" value="Cancel" onclick="window.close()">
		</div>

		<div style="float:right">
		<a href="<?= $cfg['sub_dir'] ?>/bookmarks/delete_bookmark.inc.php?<?= $query_string . $bm_string; ?>" class="button"> Delete Me </a>
		</div>
	</div>
	<br>
	<br>
<?php

debug_logger(name: 'bookmarks-2', variable: $bookmarks, file: __FILE__, function: __FUNCTION__);

		}
		else {
			message($mysql->error);
		}
	}
// echo 'sub_dir: '. $cfg['sub_dir'] . '<br>';
// echo $_SERVER['DOCUMENT_ROOT'] . '<br>';
// echo sub_dir_bool_check() . '<br>';

	require_once(realpath(DOC_ROOT . '/footer.php'));
?>