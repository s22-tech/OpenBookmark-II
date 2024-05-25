<?php
	require_once(__DIR__ . '/header.php');

	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

	require_once(DOC_ROOT . '/lib/webstart.php');
	if (! is_file(DOC_ROOT . '/config/config.php')) {
		die ('You have to <a href="./install.php">install</a> OpenBookmark.');
	}
	else {
		require_once(DOC_ROOT . '/config/config.php');
	}
	require_once(DOC_ROOT . '/lib/mysql.php');
	$mysql = new mysql;

	require_once(DOC_ROOT . '/lib/auth.php');
	$auth = new Auth;

	require_once(DOC_ROOT . '/lib/lib.php');
	require_once(DOC_ROOT . '/lib/login.php');

	//if (is_file (DOC_ROOT . '/install.php')) {
	//	message ('Remove "install.php" before using OpenBookmark.');
	//}

	if ($display_login_form) {
		$auth->display_login_form();
		require_once(DOC_ROOT . '/footer.php');
	}

