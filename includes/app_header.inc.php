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

	if ($username) {
		$query = sprintf("
			SELECT * FROM `obm_users`
			WHERE `username` = '%s'",
				$mysql->escape($username)
		);

		if ($mysql->query($query)) {
			$row = mysqli_fetch_assoc($mysql->result);
		
			$settings['private_mode'] = $row['private_mode'];
		}
	}
	else {
		$settings['private_mode'] = 1;
	}
	
