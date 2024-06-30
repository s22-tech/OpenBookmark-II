<?php

	require_once(realpath(__DIR__ . '/includes/app_header.inc.php'));

	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

// 	require_once(realpath(DOC_ROOT . '/lib/webstart.php'));
	if (! is_file(realpath(DOC_ROOT . '/config/config.php'))) {
		die ('You have to <a href="/install/install.php">install</a> OpenBookmark.');
	}
	else {
		require_once(realpath(DOC_ROOT . '/config/config.php'));
	}
	require_once(realpath(DOC_ROOT . '/lib/mysql.php'));
	$mysql = new mysql;

	require_once(realpath(DOC_ROOT . '/lib/auth.php'));
	$auth = new Auth;

	require_once(realpath(DOC_ROOT . '/lib/lib.php'));
	require_once(realpath(DOC_ROOT . '/lib/login.php'));

// 	if (is_file(realpath(DOC_ROOT . '/install/install.php'))) {
// 		message ('Remove "/install/install.php" before using OpenBookmark.');
// 	}

	if ($display_login_form) {
		$auth->display_login_form();
		require_once(realpath(DOC_ROOT . '/includes/footer.inc.php'));
	}

