<?php
	require_once(realpath(dirname(__DIR__, 1) . '/header.php'));
	logged_in_only();
	
debug_logger( name:'------------', variable: '-------', file: __FILE__, function: __FUNCTION__ );

	$bmlist = set_post_num_list('bmlist');  // Returns an array.
debug_logger( name:'bmlist-1', variable: print_r($bmlist, true), file: __FILE__, function: __FUNCTION__ );

	if (count($bmlist) == 0) {
		$bmlist = set_get_num_list('bmlist');  // Returns an array.
debug_logger( name:'bmlist-2', variable: print_r($bmlist, true), file: __FILE__, function: __FUNCTION__ );
?>

	<h2 class="title">Move bookmarks to:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid .'&bmlist='. implode(',', $bmlist); ?>" method="POST">

				<div style="width:100%; height:330px; overflow:auto;">

				<?php
debug_logger( name:'bmlist-form', variable: $bmlist, file: __FILE__, function: __FUNCTION__ );
debug_logger( name:'folderid-form', variable: $folderid, file: __FILE__, function: __FUNCTION__ );
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
debug_logger( name:'folderid-empty', variable: $folderid, file: __FILE__, function: __FUNCTION__ );
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
debug_logger( name:'success', variable: $query, file: __FILE__, function: __FUNCTION__ );
			echo 'Bookmarks moved.<br>'. PHP_EOL;
			echo '<script> reloadclose(); </script>';
		}
		else {
debug_logger( name:'error', variable: $query, file: __FILE__, function: __FUNCTION__ );
			message($mysql->error);
		}
	}

	require_once(realpath(DOC_ROOT . '/footer.php'));
?>
