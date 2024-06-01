<?php
	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	logged_in_only();
	

	$bmlist = set_post_num_list('bmlist');  // Returns an array.

	if (count($bmlist) == 0) {
		$bmlist = set_get_num_list('bmlist');  // Returns an array.
?>

	<h2 class="title">Move bookmarks to:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid .'&bmlist='. implode(',', $bmlist); ?>" method="POST">

				<div style="width:100%; height:330px; overflow:auto;">

				<?php
					require_once(realpath(DOC_ROOT . '/folders/folder.php'));
					$tree = new Folder();
					$tree->make_tree(0);
					$tree->print_tree('', implode(',', $bmlist));
				?>

				</div>
				<br>
				<input type="hidden" name="bmlist" value="<?php echo implode(',', $bmlist); ?>">
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
			SET `childof` = '%d' 
			WHERE `id` IN (%s) AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape(implode(',', $bmlist)),
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

	require_once(realpath(DOC_ROOT . '/footer.inc.php'));
?>

