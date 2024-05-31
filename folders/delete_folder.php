<?php

	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	logged_in_only();

	$noconfirm = set_get_noconfirm();

	if (!empty($username) && $username !== 'demo') {
	  // The root folder cannot be deleted.
		if ($folderid == '' || $folderid == 0) {
			message ('No folder selected.');
		}
		elseif (!$settings['confirm_delete'] || $noconfirm) {
		  // Lets do the deletion if the confirm variable is set to FALSE, or after confirmation.
			require_once(realpath(DOC_ROOT . '/folders/folder.php'));
			$tree = new Folder();
			$tree->get_children($folderid);

		  // We need $parent_folders for the JavaScript code below.
			$parent_folders = $tree->get_path_to_root($folderid);
			if (count($parent_folders) > 1) {
				$parent_folder = $parent_folders[1];
			}
			else {
				$parent_folder = 0;
			}

			array_push($tree->get_children, $folderid);
			$folders = implode(',', $tree->get_children);
			
		  // First, delete all subfolders.
			$delete_subfolders_query = sprintf("
				DELETE FROM `obm_folders` 
				WHERE `childof` IN (%s) AND `user` = '%s'",
					$mysql->escape($folders),
					$mysql->escape($username)
			);
			if (!$mysql->query($delete_subfolders_query)) {
				message($mysql->error);
			}

/* ** Add code to delete favicons, as well. ** */

		  // Of course, we want to delete all bookmarks as well.
			$delete_bm_query = sprintf("
				DELETE FROM `obm_bookmarks` 
				WHERE `childof` IN (%s) AND `user` = '%s'",
					$mysql->escape($folders),
					$mysql->escape($username)
			);
			if (!$mysql->query($delete_bm_query)) {
				message($mysql->error);
			}

		  // Now delete the folder itself.
			$delete_folder_query = sprintf("
				DELETE FROM `obm_folders` 
				WHERE `id` = %d AND `user` = '%s'",
					$mysql->escape($folderid),
					$mysql->escape($username)
			);
			if (!$mysql->query($delete_folder_query)) {
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
			$query = sprintf("
				SELECT `name`, `public` 
				FROM `obm_folders` 
				WHERE `id` = '%d' AND `user` = '%s' AND `deleted` != '1'",
					$mysql->escape($folderid),
					$mysql->escape($username)
			);

			if ($mysql->query($query)) {
				if (mysqli_num_rows($mysql->result) == 0) {
					message("Folder '$folderid' does not exist");
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
	}
	else {
		echo 'Demo users cannot delete folders.<br>' . PHP_EOL;
		echo '<input type="button" value=" Cancel " onclick="self.close()">';
	}

	require_once(realpath(DOC_ROOT . '/footer.inc.php'));
?>