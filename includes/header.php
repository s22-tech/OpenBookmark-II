<?php
	require_once(realpath(__DIR__ . '/app_header.inc.php'));
?>
<!DOCTYPE html>
<html lang="<?php echo $cfg['locale']; ?>">
	<head>
		<title> OpenBookmark </title>
		<meta charset="utf-8" />
<?php if ($settings['private_mode'] == 1): ?>
		<meta name="robots" content="noindex,nofollow,noarchive" />
<?php else: ?>
		<meta name="robots" content="index,follow" />
<?php endif; ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="Pragma" content="No-cache" />
		
		<link rel="icon" href="<?= $cfg['sub_dir'] ?>/favicon.ico" />
		<script src="<?= $cfg['sub_dir'] ?>/lib/lib.js"></script>
		<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery.js"></script>
		<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery-ui.min.js"></script>

		<link rel="stylesheet" type="text/css" href="<?= $cfg['sub_dir'] ?>/includes/css/style.css"/>
		<?php echo ($settings['theme'] != '') ? '<link rel="stylesheet" type="text/css" href="'. $cfg['sub_dir'] .'/includes/css/style'. $settings['theme'] .'.css" />' : ''; ?>
	</head>
<body>

<?php

// 	if (is_file(realpath(DOC_ROOT . '/install/install.php'))) {
// 		message ('Remove "/install/install.php" before using OpenBookmark.');
// 	}

	if ($display_login_form) {
		$auth->display_login_form();
		require_once(realpath(DOC_ROOT .'/footer.inc.php'));
	}

?>