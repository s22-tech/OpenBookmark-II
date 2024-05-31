<?php
	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	logged_in_only();

	$foldername = set_post_foldername();
	$public  = set_post_bool_var('public', false);
	$inherit = set_post_bool_var('inherit', false);

	if ($folderid == '' || $folderid == '0') {
		message ('No Folder selected -- edit_folder.php.');
	}
	elseif ($foldername == '') {
		$query = sprintf("
			SELECT `name`, `public` 
			FROM `obm_folders` 
			WHERE `id` = '%d' AND `user` = '%s' AND `deleted` != '1'",
				$mysql->escape($folderid),
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
			if (mysqli_num_rows($mysql->result) == 1) {
				$row = mysqli_fetch_object($mysql->result);
			}
			else {
				message('No folder to edit.');
			}
		}
		else {
			message($mysql->error);
		}
?>

	<h2 class="title">Edit Folder</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" id="fedit" method="post">
	<p><input type=text name="foldername" size="50" value="<?php echo $row->name; ?>"> <?php echo $row->public ? $folder_opened_public : $folder_opened; ?></p>
	<p><input type="checkbox" name="public" <?php if ($row->public) echo 'checked' ?>> Public</p>
	<p><input type="checkbox" name="inherit"> Inherit Public Status to all Subfolders and Bookmarks</p>
	<input type="submit" value=" OK ">
	<input type="button" value=" Cancel " onclick="self.close()">
	</form>
	<script>
		this.focus();
		document.getElementById('fedit').foldername.focus();
	</script>

<?php
	}
	else {
		$query = sprintf ("
			UPDATE `obm_folders` 
			SET `name` = '%s', `public` = '%d' 
			WHERE `id` = '%d' AND `user` = '%s'",
				$mysql->escape($foldername),
				$mysql->escape($public),
				$mysql->escape($folderid),
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
			if ($inherit) {
				require_once(realpath(DOC_ROOT . '/folders/folder.php'));
				$tree = new Folder();
				$tree->get_children($folderid);
				if (count($tree->get_children) > 0) {
					$sub_folders = implode(',', $tree->get_children);

				  // Set subfolders to public.
					$query = sprintf("
						UPDATE `obm_folders` 
						SET `public` = '%d' 
						WHERE `id` IN (%s) AND `user` = '%s'",
							$mysql->escape($public),
							$mysql->escape($sub_folders),
							$mysql->escape($username)
					);
					if (! $mysql->query($query)) {
						message($mysql->error);
					}

					$sub_folders .= ',' . $folderid;
					# Set bookmarks to public as well.
					$query = sprintf("
						UPDATE `obm_bookmarks` 
						SET `public` = '%d' 
						WHERE `childof` IN (%s) AND `user` = '%s'",
							$mysql->escape($public),
							$mysql->escape($sub_folders),
							$mysql->escape($username)
					);
					if ($mysql->query($query)) {
						echo '<script> reloadclose(); </script>';
					}
					else {
						message($mysql->error);
					}
				}
				else {
					$query = sprintf("
						UPDATE `obm_bookmarks` 
						SET `public` = '%d' 
						WHERE `childof` = '%d' AND `user` = '%s'",
							$mysql->escape($public),
							$mysql->escape($folderid),
							$mysql->escape($username)
					);
					if ($mysql->query($query)) {
						echo '<script> reloadclose(); </script>';
					}
					else {
						message($mysql->error);
					}
				}
			}
			echo '<script>reloadclose();</script>';
		}
		else {
			message($mysql->error);
		}
	}

	require_once(realpath(DOC_ROOT . '/footer.inc.php'));
?>