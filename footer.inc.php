
<?php
echo '<br>';

	if (admin_only() && settings['debug_mode']) {
		if (sub_dir_bool_check()) {
			echo '&bull; OBM is installed in a subdirectory:' . '<br>';
			echo '&nbsp; sub_dir: '. $cfg['sub_dir'] . '<br>';
		}
		else {
			echo '&bull; OBM is installed in the root directory:' . '<br>';
		}
		echo '&nbsp; <span style="font-size:0.9em">'. $_SERVER['DOCUMENT_ROOT'] . '</span>';
	}
?>

</body>
</html>

<?php
	exit ();
?>

