<?php
	require_once(realpath(dirname(__DIR__, 1) . '/includes/header.php'));
	logged_in_only();

	$folderid = set_get_folderid();  // This allows the root folder to be selected in the list.

	$bm_array = set_post_num_array('bmlist');  // Returns an array.

	if (count($bm_array) == 0) {
		$bm_array = set_get_num_array('bmlist');  // Returns an array from the URL.
?>

	<h2 class="title">Move bookmarks to:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" method="post">
				<div style="width:100%; height:330px; overflow:auto;">

				<?php
					require_once(realpath(DOC_ROOT . '/folders/folder.php'));
					$tree = new Folder();
					$tree->make_tree(0);
					$tree->print_tree('', implode('_', $bm_array));  // function set_get_num_array() explodes on '_'.
				?>

				</div>
				<br>
				<input type="hidden" name="bmlist" value="<?php echo implode('_', $bm_array); ?>">
				<input type="submit" value=" OK ">
				<input type="button" value=" Cancel " onclick="self.close()">
				<input type="button" value=" New Folder " onclick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

	</form>

	<script>
		document.bookmarksmove.bmlist.value = self.name;
	</script>

<?php
	}
	elseif ($folderid == '') {
		message('No destination Folder selected.');
	}
	else {
		$query = sprintf("
			UPDATE `obm_bookmarks` 
			SET `childof` = %d 
			WHERE `id` IN (%s) AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape(implode(',', $bm_array)),
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
			echo 'Bookmarks moved.<br>'. PHP_EOL;
			echo '<script> reloadclose(); </script>';
		}
		else {
			message($mysql->error);
		}
	}
	

	require_once(realpath(DOC_ROOT . '/includes/footer.inc.php'));
?>

