<?php
if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
	die ('No direct access allowed');
}

function list_bookmarks($bookmarks, $show_checkbox, $show_folder, $show_icon, $show_link, $show_desc, 
								$show_date, $show_edit, $show_move, $show_delete, $show_share, $show_header, 
								$user=false, $scriptname='') {
	global $folderid,
		$expand,
		$settings,
		$column_width_folder,
		$bookmark_image,
		$edit_image,
		$move_image,
		$delete_image,
		$folder_opened,
		$folder_opened_public,
		$cfg,
		$order,
		$mysql;

	$tab = chr(9);

	if ($scriptname == '') $scriptname = $_SERVER['SCRIPT_NAME'];

  // Print the bookmark header if enabled.
  // Yes, it's ugly PHP code, but beautiful HTML code.
	if ($show_header) {
		if ($order[0] == 'titleasc') {
			$sort_t = 'titledesc';
			$img_t = '<img src="'. $cfg['sub_dir'] .'/images/ascending.gif" alt="">';
		}
		elseif ($order[0] == 'titledesc') {
			$sort_t = 'titleasc';
			$img_t = '<img src="'. $cfg['sub_dir'] .'/images/descending.gif" alt="">';
		}
		else {
			$sort_t = 'titleasc';
			$img_t = '<img src="'. $cfg['sub_dir'] .'/images/descending.gif" alt="" class="invisible">';
		}

		if ($order[0] == 'dateasc') {
			$sort_d = 'datedesc';
			$img_d = '<img src="'. $cfg['sub_dir'] .'/images/ascending.gif" alt="">';
		}
		elseif ($order[0] == 'datedesc') {
			$sort_d = 'dateasc';
			$img_d = '<img src="'. $cfg['sub_dir'] .'/images/descending.gif" alt="">';
		}
		else {
			$sort_d = 'dateasc';
			$img_d = '<img src="'. $cfg['sub_dir'] .'/images/descending.gif" alt="" class="invisible">';
		}

		echo '<div class="bookmarkcaption">' . PHP_EOL;
		if ($show_folder) {
			echo "\t" . '<div style="width:' . $column_width_folder . '; float: left;">&nbsp;</div>' . PHP_EOL;
		}

		if ($show_checkbox) {
			echo "\t\t" . '<div class="bmleft">' . PHP_EOL;
			echo "\t\t\t" . '<input type="checkbox" name="CheckAll" onclick="selectthem(\'checkall\', this.checked)">' . PHP_EOL;
			echo "\t\t" . '</div>' . PHP_EOL;
		}

		if ($show_date) {
			$query_data = [
				'folderid' => $folderid,
				'expand'   => implode(',', $expand),
				'order'    => $sort_d,
			];
			if ($user) {
				$query_data['user'] = $user;
			}
			$query_string = assemble_query_string($query_data);

			echo "\t\t" . '<div class="bmright">' . PHP_EOL;
			echo "\t\t\t" . '<span class="date">' . PHP_EOL;
			echo "\t\t\t\t" . '<a href="' . $scriptname . '?' . $query_string . '" class="f blink"> Date ' . $img_d . '</a>' . PHP_EOL;
			echo "\t\t\t" . '</span>' . PHP_EOL;

			if ($show_edit) {
				echo "\t\t\t" . '<img src="'. $cfg['sub_dir'] .'/images/edit.gif"   alt="" class="invisible">' . PHP_EOL;
			}
			if ($show_move) {
				echo "\t\t\t" . '<img src="'. $cfg['sub_dir'] .'/images/move.gif"   alt="" class="invisible">' . PHP_EOL;
			}
			if ($show_delete) {
				echo "\t\t\t" . '<img src="'. $cfg['sub_dir'] .'/images/delete.gif" alt="" class="invisible">' . PHP_EOL;
			}
			echo "\t\t" . '</div>' . PHP_EOL;
		}
		
		echo "\t\t" . '<div class="link">' . PHP_EOL;
		if ($show_icon) {
			echo "\t\t\t" . '<img src="'. $cfg['sub_dir'] .'/images/bookmark.gif" alt="" class="invisible">' . PHP_EOL;
		}
		$query_data ['order'] = $sort_t;
		$query_string = assemble_query_string($query_data);

		echo "\t\t\t" . '<a href="' . $scriptname . '?' . $query_string . '" class="f blink"> Title ' . $img_t . '</a>' . PHP_EOL;
		echo '<a id="openAll" style="font-weight:800;float:right; margin-right: 60px;" href="javascript:openAll();">[Open All]</a>';
		echo "\t\t" . '</div>' . PHP_EOL;
		echo "\t" . '</div>' . PHP_EOL . PHP_EOL;
	}


	if ($show_folder) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/folders/folder.php');
		$tree = new Folder();
	}

	echo '<form name="bookmarks" action="" class="nav">' . PHP_EOL;

// debug_logger(variable:$bookmarks, name:'BOOKMARKS--$bookmarks', file:__FILE__, function:__FUNCTION__);

	foreach ($bookmarks as $value) {
		echo '<div class="bookmark">' . PHP_EOL;

	  // The folders -- only needed when searching for bookmarks.
		if ($show_folder) {
			if ($value['fid'] == null) {
				$value['name'] = $settings['root_folder_name'];
				$value['fid'] = 0;
			}
			if ($value['fpublic']) {
				$folder_image = $folder_opened_public;
			}
			else {
				$folder_image = $folder_opened;
			}
			$expand = $tree->get_path_to_root($value['fid']);
			// (($settings['column_width_folder'] == 0) ? "auto" : $settings['column_width_folder'])
			echo "\t" . '<div style="width:200px; float:left;">';
			echo '<a class="f flink" href="./index.php?expand=' . implode(',', $expand) .'&folderid='. $value['fid'] .'#'. $value['fid'] .'&test=BOOKMARKS">';
			echo $folder_image . ' ' . $value['name'] . '</a>';
			echo '</div>' . PHP_EOL;
		}

	  // The checkbox and favicon section.
		echo "\t" . '<div class="bmleft">' . PHP_EOL;
	  // The checkbox.
		if ($show_checkbox) {
			echo "\t\t" . '<input type="checkbox" name="' . $value['id'] . '">' . PHP_EOL;
		}
		echo PHP_EOL . "\t</div>" . PHP_EOL;

	  // The share, date, and edit/move/delete icon section.
		echo "\t" . '<div class="bmright">' . PHP_EOL;
		if ($show_share) {
			$share = $value['public'] ? 'public' : 'private';
			echo "\t\t" . '<span class="' . $share . '">' . $share . '</span>' . PHP_EOL;
		}

		if ($show_date) {
			echo "\t\t" . '<span class="date">';
			echo date($cfg['date_formats'][$settings['date_format']], $value['timestamp']);
			echo "\t</span>" . PHP_EOL;
		}

	  // The edit column.
		if ($show_edit) {
			echo "\t\t" . "<a href=\"javascript:bookmarkedit('" . $cfg['sub_dir'] ."', '" . $value['id'] . "')\">";
			echo sprintf($edit_image, 'Edit');
			echo '</a>' . PHP_EOL;
		}

	  // The move column.
		if ($show_move) {
			echo "\t\t" . "<a class=\"bookmark-move\" href=\"javascript:bookmarkmove('". $cfg['sub_dir'] ."', '" . $value['id'] . "', '" . 'expand=' . implode(',', $expand) .'&amp;folderid='. $folderid ."')\">";
			echo sprintf($move_image, 'Move');
			echo '</a>' . PHP_EOL;
		}

	  // The delete column.
		if ($show_delete) {
			echo "\t\t" . "<a href=\"javascript:bookmarkdelete('". $cfg['sub_dir'] ."', '" . $value['id'] . "')\">";
			echo sprintf($delete_image, 'Delete');
			echo '</a>' . PHP_EOL;
		}
		echo "\t</div>" . PHP_EOL;

	  // The favicon.
		echo "\t" . '<div class="link">' . PHP_EOL;
		echo "\t\t";
// echo '<pre>'; print_r($value); echo '</pre>';  //:debug
		if ($show_icon) {
			if ($value['favicon']) {  /*  && is_file($value['favicon']) */
				echo '<img src="'. $cfg['sub_dir'] .'/icons/'. $value['favicon'] .'" width="'. $cfg['icon_w'] .'" height="'. $cfg['icon_h'] .'" alt="">'. PHP_EOL;
			}
			else {
				echo $bookmark_image . PHP_EOL;
			}
		}

	  // The link.
		if ($settings['open_new_window']) {
			$target = ' target="_blank"';
		}
		else {
			$target = null;
		}

// *** Trigger this link with JavaScript to set the `last_visit` field in the bookmark table.
		if ($show_link) {
			$link = '<a class="bookmark_href" href="'. $value['url'] .'" title="'. $value['url'] .'"'. $target .'>'. $value['title'] .'</a>';
		}
		else {
			$link = $value['title'];
		}
		echo "\t\t$link" . PHP_EOL;
		echo "\t</div>" . PHP_EOL;

	  // The description, if not empty.
		if ($show_desc && $value['description'] != '') {
			if ($show_folder) {
				$css_extension = ' style="margin-left: ' . $column_width_folder . ';"';
			}
			else {
				$css_extension = '';
			}
			echo "\t" . '<div class="description"'. $css_extension .'>'. $value['description'] .'</div>' . PHP_EOL;
		}

		echo '</div>' . PHP_EOL;
	}
	echo '</form>' . PHP_EOL;
}
