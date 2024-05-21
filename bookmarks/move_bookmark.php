<?php
	require_once(realpath(dirname(__FILE__, 2)) . '/header.php');
	logged_in_only();

	$bmlist = set_post_num_list('bmlist');

	if (count($bmlist) == 0) {
?>

	<h2 class="title">Move bookmarks to:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" method="post" name="bookmarksmove">

				<div style="width:100%; height:330px; overflow:auto;">

				<?php
					require_once(APPLICATION_PATH . '/folders/folder.php');
					$tree = new Folder();
					$tree->make_tree(0);
					$tree->print_tree();
				?>

				</div>
				<br>
				<input type="hidden" name="bmlist">
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
		message ('No destination Folder selected.');
	}
	else {
		$query = sprintf("
			UPDATE `obm_bookmarks` 
			SET `childof` = '%d' 
			WHERE `id` IN (%s) AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape(implode (',', $bmlist)),
				$mysql->escape($username));
debug_logger(name:'move-bookmarks', variable:$query, file:__FILE__, function:__FUNCTION__);

		if ($mysql->query($query)) {
			echo 'Bookmarks moved.<br>'. PHP_EOL;
			echo '<script> reloadclose(); </script>';
		}
		else {
			message($mysql->error);
		}
	}

	require_once(APPLICATION_PATH . '/footer.php');
?>
