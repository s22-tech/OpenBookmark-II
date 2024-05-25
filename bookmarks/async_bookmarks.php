<?php

	require_once(dirname(__DIR__, 1) . '/async_header.php');
	logged_in_only();

	$order = set_get_order();

	require_once(DOC_ROOT .'/bookmarks/bookmarks.php');
	
	$query = sprintf("
		SELECT `title`, `url`, `description`, UNIX_TIMESTAMP(`date`) AS timestamp, `id`, `favicon`, `public`
		FROM `obm_bookmarks`
		WHERE `user` = '%s'
		AND `childof` = '%d'
		AND `deleted` != '1'
		ORDER BY $order[1]",
			$mysql->escape($username),
			$mysql->escape($folderid)
	);

	if ($mysql->query($query)) {
		$bookmarks = [];
		while ($row = mysqli_fetch_assoc($mysql->result)) {
			array_push($bookmarks, $row);
		}
		list_bookmarks(
			bookmarks: $bookmarks,
			show_checkbox: true,
			show_folder: false,
			show_icon: $settings['show_bookmark_icon'],
			show_link: true,
			show_desc: $settings['show_bookmark_description'],
			show_date: $settings['show_column_date'],
			show_edit: $settings['show_column_edit'],
			show_move: $settings['show_column_move'],
			show_delete: $settings['show_column_delete'],
			show_share: $settings['show_public'],
			show_header: true,
			user: false,
			scriptname: 'index.php'
		);
	}
	else {
		message($mysql->error);
	}
