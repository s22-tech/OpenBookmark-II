<?php
	require_once(realpath(dirname(__DIR__, 1) . '/includes/header.php'));
	logged_in_only();

	require_once(realpath(DOC_ROOT . '/folders/folder.php'));

	$sourcefolder = set_post_sourcefolder();
	$tree = new Folder();
	$parents = $tree->get_path_to_root($folderid);
	
// echo '$_SESSION[expand]: '. print_r($_SESSION['expand'], true) .'<br>'. PHP_EOL;
// echo '$sourcefolder: '. $sourcefolder .'<br>'. PHP_EOL;
// echo '$folderid: '. $folderid .'<br>'. PHP_EOL;
// echo '$parents: '. print_r($parents, true) .'<br>'. PHP_EOL;
// echo '$_POST: '. print_r($_POST, true) .'<br>'. PHP_EOL;

/*
	• A folder can't be moved up to the main level (folderid = 0).  Why???
	• You can't navigate to the top level, either.
*/

	if ($sourcefolder === '') {
?>

<h2 class="title"> Move Folder </h2>
<form action="<?php echo $_SERVER['SCRIPT_NAME'] . '?folderid=' . $folderid . '&expand=' . implode (',', $expand);?>" method="post" id="fmove">

	<div style="width:100%; height:330px; overflow:auto;">

		<?php
debug_logger( name:'form-folderid', variable: $folderid, file: __FILE__, function: __FUNCTION__ );
debug_logger( name:'form-expand', variable: $expand, file: __FILE__, function: __FUNCTION__ );
debug_logger( name:'form-POST', variable: $_POST, file: __FILE__, function: __FUNCTION__ );
debug_logger( name:'form-SESSION', variable: $_SESSION['expand'], file: __FILE__, function: __FUNCTION__ );
			$tree->make_tree(0);
			$tree->print_tree();
		?>

	</div>
	<br>
	<input type="hidden" name="sourcefolder" value="<?php echo $_SESSION['expand'][0]; ?>">
	<input type="submit" value=" OK ">
	<input type="button" value=" Cancel " onclick="self.close()">
	<input type="button" value=" New Folder " onclick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

</form>

<script>
	this.focus();
// 	document.getElementById('fmove').sourcefolder.value = self.name;  // Prints "foldermove".
	//console.log(self.name);
</script>

<?php
	}
	elseif ($sourcefolder == $folderid) {
		echo '<script>self.close();</script>';
	}
	elseif (in_array ($sourcefolder, $parents)) {
		message ('A folder cannot be moved to one of its own subfolders.');
	}
	elseif ($sourcefolder !== '' && $sourcefolder !== $folderid) {
		$query = sprintf ("
			UPDATE `obm_folders`
			SET `childof`= %d
			WHERE `id`= %d AND `user` = '%s'",
				$mysql->escape($folderid),
				$mysql->escape($sourcefolder),
				$mysql->escape($username)
			);

		if ($mysql->query ($query)) {
debug_logger( name:'success-query', variable: $query, file: __FILE__, function: __FUNCTION__ );
			echo 'Folder moved <br>' . PHP_EOL;
			echo '<script>reloadclose();</script>';
		}
		else {
debug_logger( name:'error-query', variable: $query, file: __FILE__, function: __FUNCTION__ );
			message ($mysql->error);
		}
	}
	
debug_logger( name:'------------', variable: 'separator', file: __FILE__, function: __FUNCTION__ );

	require_once(realpath(DOC_ROOT . '/includes/footer.inc.php'));
?>