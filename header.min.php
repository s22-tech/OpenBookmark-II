<?php
	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

// 	require_once(BASE_DIR . '/lib/webstart.php');

	if (!is_file(realpath(dirname(__FILE__, 1)) . '/config/config.php')) {
		die ('You need to <a href="/install.php">install</a> OpenBookmark II.');
	}
	else {
		require_once(realpath(dirname(__FILE__, 1)) . '/config/config.php');
	}

	require_once(BASE_DIR . '/lib/mysql.php');
	$mysql = new mysql;

	require_once(BASE_DIR . '/lib/auth.php');
	$auth = new Auth;

	require_once(BASE_DIR . '/lib/lib.php');
	require_once(BASE_DIR . '/lib/login.php');
?>

<script src="<?= $cfg['sub_dir'] ?>/lib/lib.js"></script>
<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery.js"></script>