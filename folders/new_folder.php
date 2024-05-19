<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/header.php');
	logged_in_only();

	$foldername = set_post_foldername();
	$public = set_post_bool_var('public', false);

	if ($foldername == '') {
		?>

		<h2 class="title">New Folder</h2>
		<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" id="fnew" method="post">
		<p><input type=text name="foldername" size="50" value="<?php echo $foldername; ?>"></p>
		<p><input type="checkbox" name="public"> Public</p>
		<input type="submit" value=" OK ">
		<input type="button" value=" Cancel " onClick="self.close()">
		</form>
		<script>
			this.focus();
			document.getElementById('fnew').foldername.focus();
		</script>

		<?php
	}
	else {
		$query = sprintf ("
			INSERT INTO `obm_folders` (`childof`, `name`, `public`, `user`) 
			VALUES ('%d', '%s', '%d', '%s')",
				$mysql->escape ($folderid),
				$mysql->escape ($foldername),
				$mysql->escape ($public),
				$mysql->escape ($username));
		if ($mysql->query ($query)) {
			echo 'Folder successfully created.<br>' . PHP_EOL;
			echo '<script>reloadclose();</script>';
		}
		else {
			message ($mysql->error);
		}
	}

	require_once ($_SERVER['DOCUMENT_ROOT'].'/footer.php');
