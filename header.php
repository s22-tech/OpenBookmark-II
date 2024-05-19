<?php

	if (extension_loaded('zlib')) {
		ob_end_clean();
		ob_start('ob_gzhandler');
	}

	/*
	if (extension_loaded ('zlib')) {
		 ob_start('ob_gzhandler');
	}
	*/
// 	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/webstart.php');
	if (!is_file($_SERVER['DOCUMENT_ROOT'] .'/config/config.php')) {
		die ('You need to <a href="/install.php">install</a> OpenBookmark II.');
	}
	else {
		require_once($_SERVER['DOCUMENT_ROOT'] .'/config/config.php');
	}
	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/mysql.php');
	$mysql = new mysql;

	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/auth.php');
	$auth = new Auth;

	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/lib.php');
	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/login.php');
?>
<!DOCTYPE html>
<html lang="<?php echo $cfg['locale']; ?>">
	<head>
		<title> OpenBookmark </title>
		<meta charset="utf-8" />
		<?php if ($settings['private_mode'] == 1) : ?>
		<meta name="robots" content="noindex,nofollow,noarchive" />
		<?php else : ?>
		<meta name="robots" content="index,follow" />
		<?php endif; ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="Pragma" content="No-cache" />
		<link rel="icon" href="favicon.ico" />
		<script src="/lib/lib.js"></script>
		<script src="/includes/jquery/jquery.js"></script>
		<script src="/includes/jquery/jquery-ui.min.js"></script>

		<link rel="stylesheet" type="text/css" href="/includes/css/style.css"/>
		<?php echo ($settings['theme'] != '') ? '<link rel="stylesheet" type="text/css" href="/includes/css/style'. $settings['theme'] .'.css" />' : ''; ?>
	</head>
<body>

<?php

	//if (is_file ($_SERVER['DOCUMENT_ROOT'] . "install.php")) {
		//message ('Remove "install.php" before using OpenBookmark.');
	//}

	if ($display_login_form) {
		$auth->display_login_form ();
		require_once($_SERVER['DOCUMENT_ROOT'] .'/footer.php');
	}

?>