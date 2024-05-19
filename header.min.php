<?php
	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}


// 	require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/webstart.php');

	if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/config/config.php')) {
		die ('You need to <a href="./install/install.php">install</a> OpenBookmark II.');
	}
	else {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/config/config.php');
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/mysql.php');
	$mysql = new mysql;

	require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/auth.php');
	$auth = new Auth;

	require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/lib.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/login.php');
?>

<script src="/lib/lib.js"></script>
<script src="/includes/jquery/jquery.js"></script>