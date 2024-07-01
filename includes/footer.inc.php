
<?php
	// echo '__DIR__: '. __DIR__ . '<br>';
	// echo 'dirname(__DIR__, 1): '. dirname(__DIR__, 1) . '<br>';
	// echo 'dirname(__DIR__, 2): '. dirname(__DIR__, 2) . '<br>';
	// echo 'dirname(__FILE__, 1): '. dirname(__FILE__, 1) . '<br>';
	// echo 'dirname(__FILE__, 2): '. dirname(__FILE__, 2) . '<br>';
	echo '<br>';

	if (isset($_SESSION['settings']['debug_mode']) 
		&& $_SESSION['settings']['debug_mode'] == 1 
		&& admin_only()
	) {

		if (sub_dir_bool_check()) {
			echo '&bull; OBM is installed in a subdirectory:' . '<br>';
			echo '&nbsp; sub_dir: '. $cfg['sub_dir'] . '<br>';
		}
		else {
			echo '&bull; OBM is installed in the root directory:' . '<br>';
		}
	
		echo '<br> <br>';
	
		echo '<pre>';
		echo '$_SESSION: '. print_r($_SESSION, true) . '<br> <br>';

		echo '$_SERVER: '. print_r($_SERVER, true) . '<br> <br>';
		echo '</pre>';
	}
?>

</body>
</html>

