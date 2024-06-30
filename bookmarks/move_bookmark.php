<?php
	require_once(realpath(dirname(__DIR__, 1) . '/includes/header.php'));
	logged_in_only();

	$folderid = set_get_folderid();  // This allows the root folder to be selected in the list.

	$bm_array = set_post_num_array('bmlist');  // Returns an array.
debug_logger( name:'bm_array-1', variable: print_r($bm_array, true), file: __FILE__, function: __FUNCTION__ );

	if (count($bm_array) == 0) {
		$bm_array = set_get_num_array('bmlist');  // Returns an array from the URL.
debug_logger( name:'bm_array-2', variable: print_r($bm_array, true), file: __FILE__, function: __FUNCTION__ );
?>

	<h2 class="title">Move bookmarks to:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" method="post">
				<div style="width:100%; height:330px; overflow:auto;">

				<?php
debug_logger( name:'form-bm_array', variable: $bm_array, file: __FILE__, function: __FUNCTION__ );
debug_logger( name:'form-folderid', variable: $folderid, file: __FILE__, function: __FUNCTION__ );
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
debug_logger( name:'folderid-empty', variable: $folderid, file: __FILE__, function: __FUNCTION__ );
		message('No destination Folder selected.');
	}
	else {
debug_logger( name:'3rd-bm_array', variable: $bm_array, file: __FILE__, function: __FUNCTION__ );
		$query = sprintf("
			UPDATE `obm_bookmarks` 
			SET `childof` = %d 
			WHERE `id` IN (%s) AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape(implode(',', $bm_array)),
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
debug_logger( name:'success', variable: $query, file: __FILE__, function: __FUNCTION__ );
			echo 'Bookmarks moved.<br>'. PHP_EOL;
			echo '<script> reloadclose(); </script>';
		}
		else {
debug_logger( name:'error', variable: $query, file: __FILE__, function: __FUNCTION__ );
			message($mysql->error);
		}
	}
	
debug_logger( name:'------------', variable: 'separator', file: __FILE__, function: __FUNCTION__ );

	require_once(realpath(DOC_ROOT . '/includes/footer.inc.php'));
?>
