<?php

	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

	if (!is_file(realpath(dirname(__DIR__, 1) . '/config/config.php'))) {
		die ('You need to <a href="/install/install.php">install</a> OpenBookmark II.');
	}
	else {
		require_once(realpath(dirname(__DIR__, 1) . '/config/config.php'));
	}

	require_once(realpath(DOC_ROOT .'/lib/mysql.php'));
	$mysql = new mysql;

	require_once(realpath(DOC_ROOT .'/lib/auth.php'));
	$auth = new Auth;

	require_once(realpath(DOC_ROOT .'/lib/lib.php'));
	require_once(realpath(DOC_ROOT .'/lib/login.php'));
	
	$settings['private_mode'] = set_post_bool_var('settings_private_mode', false);
