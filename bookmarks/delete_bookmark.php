<?php

	ini_set('display_errors', 1);
	ini_set('error_prepend_string', '<pre style="white-space: pre-wrap;">');
	ini_set('error_append_string', '</pre>');
	
	require_once(realpath(dirname(__FILE__, 2)) . '/header.php');
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
			require_once(APPLICATION_PATH . '/bookmarks/bookmarks.php');
			$query_string = 'bmlist=' . implode(',', $bmlist);
?>

	<h2 class="title">Delete These Bookmarks?</h2>
	<div style="width:100%; height:330px; overflow:auto;">

<?php
			$bookmarks = [];
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				array_push($bookmarks, $row);
			}
			list_bookmarks($bookmarks,
				false,
				false,
				$settings['show_bookmark_icon'],
				false,
				false,
				false,
				false,
				false,
				false,
				false,
				false
			);

			$bm_string = '';
			foreach ($bookmarks as $bookmark) {
				$bm_string .= '&bookmarks[]='.$bookmark['favicon'];
			}
?>

	</div>
	
	<div style="float:right">
	<a href="<?= $cfg['sub_dir'] ?>/bookmarks/delete_bookmark.inc.php?<?= $query_string . $bm_string; ?>" class="button"> Delete Me </a>
	</div>
	<br>
	<br>
<?php

// debug_logger(name: 'bookmarks-2', variable: $bookmarks, file: __FILE__, function: __FUNCTION__);

		}
		else {
			message($mysql->error);
		}
	}

	require_once(APPLICATION_PATH . '/footer.php');
?>