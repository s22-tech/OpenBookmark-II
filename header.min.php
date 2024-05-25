<?php
	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

// 	require_once(DOC_ROOT . '/lib/webstart.php');

	if (!is_file(__DIR__ . '/config/config.php')) {
		die ('You need to <a href="/install.php">install</a> OpenBookmark II.');
	}
	else {
		require_once(__DIR__ . '/config/config.php');
	}

	require_once(DOC_ROOT . '/lib/mysql.php');
	$mysql = new mysql;

	require_once(DOC_ROOT . '/lib/auth.php');
	$auth = new Auth;

	require_once(DOC_ROOT . '/lib/lib.php');
	require_once(DOC_ROOT . '/lib/login.php');
?>

<script src="<?= $cfg['sub_dir'] ?>/lib/lib.js"></script>
<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery.js"></script>