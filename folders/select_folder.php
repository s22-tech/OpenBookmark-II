<?php
	require_once(realpath(dirname(__FILE__, 2)) . '/header.php');
	logged_in_only();
?>

<h2 class="title">Select Folder</h2>

	<div style="width:100%; height:330px; overflow:auto;">

<?php
	require_once(APPLICATION_PATH . '/folders/folder.php');
	$tree = new Folder();
	$tree->make_tree(0);
	$tree->print_tree();
	$path = $tree->print_path($folderid);
?>

	</div>
	<br>
	<input type="submit" value=" OK " onclick="javascript:opener.childof.value = '<?php echo $folderid; ?>';opener.path.value = '<?php echo $path; ?>'; self.close()">
	<input type="button" value="Cancel" onclick="window.close()">
	<input type="button" value=" New Folder " onclick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

<?php
	require_once(APPLICATION_PATH . '/footer.php');
?>
