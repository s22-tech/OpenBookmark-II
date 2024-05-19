<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
	logged_in_only();

	$noconfirm = set_get_noconfirm();

  // The root folder cannot be deleted.
	if ($folderid == '' || $folderid == 0) {
		message ('No folder selected.');
	}
	elseif (!$settings['confirm_delete'] || $noconfirm) {
	  // Lets do the deletion if the confirm variable is set to FALSE or after confirmation.
		require_once($_SERVER['DOCUMENT_ROOT'] . '/folders/folder.php');
		$tree = new Folder();
		$tree->get_children($folderid);

	  // We need $parent_folders for JavaScript code below.
		$parent_folders = $tree->get_path_to_root($folderid);
		if (count($parent_folders) > 1) {
			$parent_folder = $parent_folders[1];
		}
		else {
			$parent_folder = 0;
		}

		array_push($tree->get_children, $folderid);
		$folders = implode(',', $tree->get_children);
	  // First delete all subfolders.
		$query = sprintf("
			DELETE FROM `obm_folders` 
			WHERE `childof` IN (%s) AND `user` = '%s'",
				$mysql->escape($folders),
				$mysql->escape($username)
		);
		if (!$mysql->query($query)) {
			message($mysql->error);
		}

	  // Of course, we want to delete all bookmarks as well.
		$query = sprintf("
			DELETE FROM `obm_bookmarks` 
			WHERE `childof` IN (%s) AND `user` = '%s'",
				$mysql->escape($folders),
				$mysql->escape($username)
		);
		if (!$mysql->query($query)) {
			message($mysql->error);
		}

	  // Now delete the folder itself.
		$query = sprintf("
			DELETE FROM `obm_folders` 
			WHERE `id`=%d AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape($username)
		);
		if (!$mysql->query($query)) {
			message($mysql->error);
		}
?>

<script>
<!--
	function reloadparentwindow() {
		var path = window.opener.document.URL;
		searchstring = /(folderid=[0-9]*)/gi;
		result = searchstring.test(path);

		if (result == false) {
			urlparams = window.opener.location.search;
			if (urlparams == '') {
				result = path + "?folderid=<?php echo $parent_folder; ?>";
			}
			else {
				result = path + "&folderid=<?php echo $parent_folder; ?>";
			}
		}
		else {
			result = path.replace(searchstring, "folderid=<?php echo $parent_folder; ?>");
		}
		window.opener.location = result;
		window.close();
	}
	reloadparentwindow();
//-->
</script>

<?php
	}
	else {
	  // If there was no confirmation, as to _really_ delete the whole stuff,
	  // print the verification form.
		$query = sprintf ("
			SELECT `name`, `public` 
			FROM `obm_folders` 
			WHERE `id` = '%d' AND `user`='%s' AND `deleted` != '1'",
				$mysql->escape($folderid),
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
			if (mysqli_num_rows($mysql->result) == 0) {
				message('Folder does not exist');
			}
			$row = mysqli_fetch_object($mysql->result);
?>

	<h2 class="title">Delete this Folder?</h2>
	<p><?php echo $row->public ? $folder_opened_public : $folder_opened; echo ' ' . $row->name; ?></p>

	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=". $folderid ."&amp;noconfirm=1"; ?>" method="post" name="fdelete">
	<input type="submit" value=" OK ">
	<input type="button" value=" Cancel " onclick="self.close()">
	</form>

<?php
		}
		else {
			message($mysql->error);
		}
	}

	require_once ($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
?>