<?php

	require_once(realpath(__DIR__ . '/header.php'));

	if ($_SESSION['logged_in']) {
		$user = set_get_string_var('user', $username);
	}
	else {
		$user = set_get_string_var('user');
	}
	$display_shared = false;

	if (isset ($_GET['user']) && check_username($user)) {
		$title = $user . '&#039;s Online Bookmarks';
	}
	else {
		$title = 'Shared OpenBookmark';
	}

	$order = set_get_order();
?>

<h1 id="caption"><?php echo $title; ?></h1>

<!-- Wrapper starts here. -->
<div style="min-width: <?php echo 230 + $settings['column_width_folder']; ?>px;">
	<!-- Menu starts here. -->
	<div id="menu">
		<h2 class="nav">Bookmarks</h2>
		<ul class="nav">
		  <li><a href="<?= $cfg['sub_dir'] ?>/index.php">My Bookmarks</a></li>
		  <li><a href="<?= $cfg['sub_dir'] ?>/shared.php">Shared Bookmarks</a></li>
		</ul>

		<h2 class="nav">Tools</h2>
		<ul class="nav">
<?php if (isset ($_SESSION['logged_in']) && $_SESSION['logged_in']) : ?>
	<?php if (admin_only()) : ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/admin.php">Admin</a></li>
	<?php endif ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/import.php">Import</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/export.php">Export</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/sidebar.php">View as Sidebar</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/settings.php">Settings</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/index.php?logout=1">Logout</a></li>
<?php else : ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/index.php">Login</a></li>
<?php endif ?>
		</ul>
	<!-- Menu ends here. -->
	</div>

	<!-- Main content starts here. -->
	<div id="main">


<?php
	if (isset($_GET['user']) && check_username($user)) {
?>


	<!-- Folders starts here. -->
	<div class="folders" style="width: <?php echo (($column_width_folder == 0) ? "auto" : $column_width_folder); ?>; height: <?php echo (($table_height == 0) ? "auto" : $table_height); ?>;">

<?php
		require_once(realpath(DOC_ROOT . '/folders/folder.php'));
		$tree = new Folder($user);
		$tree->make_tree(0);
		$tree->print_tree();
?>

	<!-- Folders ends here. -->
	</div>

	<!-- Bookmarks starts here. -->
	<div class="bookmarks" style="height: <?php echo (($table_height == 0) ? "auto" : $table_height); ?>;">

<?php
		require_once(realpath(DOC_ROOT . '/bookmarks/bookmarks.php'));
		$query = sprintf("
			SELECT `title`, `url`, `description`, UNIX_TIMESTAMP(date) AS timestamp, `id`, `favicon`
			FROM `obm_bookmarks`
			WHERE `user` = '%s'
			AND `childof` = '%d'
			AND `deleted` != '1'
			AND `public` = '1'
			ORDER BY $order[1]",
				$mysql->escape($user),
				$mysql->escape($folderid)
		);

		if ($mysql->query($query)) {
			$bookmarks = [];
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				array_push($bookmarks, $row);
			}
			list_bookmarks(
				bookmarks: $bookmarks,
				show_checkbox: false,
				show_folder: false,
				show_icon: $settings['show_bookmark_icon'],
				show_link: true,
				show_desc: $settings['show_bookmark_description'],
				show_date: $settings['show_column_date'],
				show_edit: false,
				show_move: false,
				show_delete: false,
				show_share: false,
				show_header: true,
				user: $user
			);
		}
		else {
			message($mysql->error);
		}
?>

	<!-- Bookmarks ends here. -->
	</div>

<?php
	}
	else {
		echo '<div id="content" style="height:' .  (($table_height == 0) ? "auto" : $table_height) . ';">' . PHP_EOL;
			$query = "
				SELECT `user`, SUM(`bookmarks`) AS bookmarks, SUM(`folders`) AS folders 
				FROM (
					SELECT `user`, 1 AS bookmarks, 0 AS folders 
					FROM `obm_bookmarks` 
					WHERE `public` = '1' AND `deleted` != '1'
					UNION ALL
					SELECT `user`, 0 AS bookmarks , 1 AS folders 
					FROM `obm_folders` 
					WHERE `public` = '1' AND `deleted` != '1'
				) AS tmp
				GROUP BY `user`";

		if ($mysql->query ($query)) {
			while ($row = mysqli_fetch_object ($mysql->result)) {
				echo '<p class="shared"><a href="' . $_SERVER['SCRIPT_NAME'] . '?user=' . $row->user . '&folderid=0"><b>' . $row->user . "</b><br>\n";
				echo "Shares {$row->folders} Folders and {$row->bookmarks} Bookmarks</a></p>\n";
			}
		}
		else {
			message($mysql->error);
		}
		echo '</div>';
	}
?>

	<!-- Main content ends here. -->
	</div>
<!-- Wrapper ends here. -->
</div>

<?php
	print_footer();
	require_once(realpath(DOC_ROOT . '/footer.php'));
?>
