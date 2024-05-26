<?php

	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	global $conn;

	$get_title = set_title();
	$get_url   = set_url();

	logged_in_only();

	$post_title			= set_post_title($persistent = true);
	$post_url			= set_post_url();
	$post_description	= set_post_description();
	$post_childof		= set_post_childof();
	$post_public		= set_post_bool_var('public', false);

	require_once(realpath(DOC_ROOT . '/folders/folder.php'));
	$tree = new Folder();
	$query_string = '?expand=' . implode(',', $tree->get_path_to_root($post_childof)) . '&amp;folderid=' . $post_childof;

	if ($post_title == '' || $post_url == '') {
		$path = $tree->print_path($folderid);
		if ($post_title != '') {
			$title = $post_title;
		}
		else {
			$title = $get_title;
		}
		if ($post_url != '') {
			$url = $post_url;
		}
		elseif ($get_url != '') {
			$url = $get_url;
		}
		else {
			$url = 'https://';
		}
		if (strtolower(basename($_SERVER['SCRIPT_NAME'])) === 'add_bookmark.php') {
			$js_onclick = 'history.back()';
		}
		else {
			$js_onclick = 'self.close()';
		}
	  // Continues below...
?>

	<h2 class="title">New Bookmark</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" id="bmnew" method="post">
		<p>Title
		<br>
		<input type=text name="title" size="50" value="<?php echo $title; ?>"></p>
		<p>URL
		<br>
		<input type=text name="url" size="50" value="<?php echo $url; ?>"></p>
		<p>Description
		<br>
		<textarea name="description" cols="50" rows="8"><?php echo $post_description; ?></textarea></p>
		<p><input type="button" value="Select folder" onclick="window.childof=document.forms['bmnew'].childof; window.path=document.forms['bmnew'].path; selectfolder('<?php echo $cfg['sub_dir']; ?>', '<?php echo $query_string; ?>')">
		<br>
		<input type="text" name="path" value="<?php echo $path; ?>" size="50" readonly>
		<input type="text" name="childof" value="<?php echo $folderid; ?>" size="4" class="invisible" readonly></p>
		<p>Tags
		<br>
		<input type=text name="tags" size="50" value="Not yet working"></p>
		<input type="submit" value=" OK ">
		<input type="button" value=" Cancel " onclick="<?php echo $js_onclick; ?>">
		Public <input type="checkbox" name="public" <?php echo $post_public ? 'checked' : '';?>>
	</form>
	<script>
		this.focus();
		document.getElementById('bmnew').title.focus();
	</script>

<?php
	}
	else {
		$query = sprintf("
			INSERT INTO `obm_bookmarks` (`user`, `title`, `url`, `description`, `childof`, `public`, `date_created`)
			VALUES ('%s', '%s', '%s', '%s', '%d', '%d', '%s')",
				$mysql->escape($username),
				$mysql->escape($post_title),
				$mysql->escape($post_url),
				$mysql->escape($post_description),
				$mysql->escape($post_childof),
				$mysql->escape($post_public),
				date('Y-m-d H:i:s')
		);
		if ($mysql->query($query)) {
			echo 'Bookmark successfully created.<br>' . PHP_EOL;
			$bm_id = mysqli_insert_id($mysql->conn);  // Returns the value generated for an AUTO_INCREMENT column by the last query.
			// https://www.php.net/manual/en/mysqli.insert-id.php
			// Using $mysql->insert_id produces "Undefined property: mysql::$insert_id".
// 			$bm_id = $pdo->lastInsertId();  // PDO equivalent -- https://www.php.net/pdo.lastinsertid
		}
		else {
			message($mysql->error);
		}
		unset($_SESSION['title'], $_SESSION['url']);

debug_logger(variable:$post_url, name:'post_url', file:__FILE__, function:__FUNCTION__);

///////////////////////////
// SAVE Favicon
// Saving the favicon in a separate second step is done because
// we want to make sure the bookmark is saved in any case,
// since the favicon is not as important.
///////////////////////////
		if ($settings['show_bookmark_icon']) {
			require_once(realpath(DOC_ROOT . '/favicon.php'));
			$favicon = new Favicon($post_url);
debug_logger(variable:print_r($favicon, true), name:'favicon-object', file:__FILE__, function:__FUNCTION__);

			if (!empty($favicon->favicon)) {
				$update_query = sprintf("
					UPDATE `obm_bookmarks` 
					SET `favicon` = '%s' 
					WHERE `user` = '%s' 
					AND `id` = '%d'",
						$mysql->escape($favicon->favicon),
						$mysql->escape($username),
						$mysql->escape($bm_id)
				);
debug_logger(variable:$update_query, name:'update-query', file:__FILE__, function:__FUNCTION__);
				if (!$mysql->query($update_query)) {
					message($mysql->error);
				}
				$icon = '<img src="/icons/'.$favicon->favicon.'">';
			}
			else {
				$icon = $bookmark_image;
debug_logger(variable:$icon, name:'favicon->favicon was NOT set', file:__FILE__, function:__FUNCTION__);
debug_logger(variable:debug_backtrace(), name:'debug_backtrace()', file:__FILE__, function:__FUNCTION__);
			}
		}

		if (strtolower(basename($_SERVER['SCRIPT_NAME'])) === 'add_bookmark.php') {
			echo 'Back to '.$icon.' <a href="'.$post_url.'">'.$post_title.'</a><br>' . PHP_EOL;
			echo 'Open '.$folder_opened.' <a href="./index.php'.$query_string.'">folder</a> containing new Bookmark<br>' . PHP_EOL;
		}
		else {
			echo '<script> reloadclose(); </script>';
			# I know, the following is ugly, but I found no other way to do it.
			# When creating a bookmark out of the personal toolbar, there is no
			# window.opener that can be closed.  Thus javascript exits with an error
			# without finishing itself (self.close()).
			echo '<script> self.close(); </script>';
		}
	}
	require_once(realpath(DOC_ROOT . '/footer.php'));
?>