<?php

	declare(strict_types = 1);  // When used, must be the first line called in a script.

	require_once(realpath(__DIR__ . '/header.php'));
	logged_in_only();
	
	if (isset($_GET['logout']) && $_GET['logout'] == 1) $auth->logout();

	$search = set_get_string_var('search');
	if ($search != '') {
		$search_mode = true;
	}
	else {
		$search_mode = false;
	}

	$order = set_get_order();
	
	$settings = $_SESSION['settings'];
?>

<?php if (!$search_mode) : ?>
	<script>
		<!--
		var selected_folder_id = 0;

		$(document).ready(function() {
		  // Setup collapsing menus.
			$('.mnu').click(function() {
				var options = {};
				$('#' + $(this).attr('target')).toggle('blind', options, 300);
			});

// 			setupFolderIntercepts();  // #1
			setupBookmarkIntercepts();

			$('#gsearchtext').focus();
		});

		function setupFolderIntercepts() {
			$('.folders').removeClass('loading-anim');
			$('.bookmarks').removeClass('loading-anim');

			$('.flink').click(function() {
				var url = $(this).attr('href');
				var folderurl   = url.replace('index.php', 'folders/async_folders.inc.php');
				var bookmarkurl = url.replace('index.php', 'bookmarks/async_bookmarks.inc.php');

				//if ($('#folders').hasClass('mobile'))
				//	$('#folders').toggle('blind',{},300);

				selected_folder_id = $(this).attr('folderid');

				$('.folders').addClass('loading-anim');    // Continuously redraws loading.gif  Why???
				$('.bookmarks').addClass('loading-anim');  //  ""     ""

				$('.folders').load(folderurl, setupFolderIntercepts);
				$('.bookmarks').load(bookmarkurl, setupBookmarkIntercepts);

				return false;
			});
		}

		function setupBookmarkIntercepts() {
			$('.bookmarks').removeClass('loading-anim');

			$('.blink').click(function() {
				var url = $(this).attr('href');
				var bookmarkurl = url.replace('index.php', 'bookmarks/async_bookmarks.inc.php');

// 				$('.bookmarks').addClass('loading-anim');  // Continuously redraws loading.gif  Why???
				$('.bookmarks').load(bookmarkurl, setupBookmarkIntercepts);

				return false;
			});
		}
		-->
	</script>
<?php endif ?>

<?php if (is_mobile_browser() && !$search_mode): ?>

	<script>
		<!--
		$(document).ready(function() {
		  // Make collapsible menu.
			$("#menu").hide();

		});
		-->
	</script>
	<link rel="stylesheet" type="text/css" href="/<?= basename(__DIR__); ?>/includes/css/mobile.css" />
	<?php echo ($settings['theme'] != '') ? '<link rel="stylesheet" type="text/css" href="/<?= basename(__DIR__); ?>/includes/css/mobile'.$settings['theme'].'.css" />' : ''; ?>

<?php endif; ?>

<h1 id="caption"><?php echo ucwords($username); ?>&#039;s Online Bookmarks</h1>

<!-- Wrapper starts here. -->
<div style="min-width: <?php echo 230 + $settings['column_width_folder']; ?>px;">
	<!-- Search box -->
	<div class="desktop" id="googlesearch">
		<form method="get" action="https://www.duckduckgo.com/?" target="_blank">
			<input type="text" id="gsearchtext"  name="q" size="15" maxlength="255" value="" />
			<input type="submit" value="DuckDuckGo" onclick="$('#gsearchtext').select();" />
		</form>
	</div>

	<!-- Menu starts here. -->
	<h2 id="menu-head" class="mobile nav mnu" target="menu">Actions</h2>
	<div id="menu">
		<div class="navblock">
		<h2 class="nav mnu" target="mnu_search">Filter</h2>
		<ul class="nav" id="mnu_search">
		  <li>
		  	<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="nav">
				<input type="text" name="search" size="7" value="<?php echo $search; ?>" />
				<input type="submit" value="Go" name="submit" />
		  	</form>
		  </li>
		</ul>
		</div>

		<div class="navblock">
		<h2 class="nav mnu" target="mnu_bookmarks">Bookmarks</h2>
		<ul class="nav" id="mnu_bookmarks">
<?php if ($search_mode) : ?>
		  <li><a href="<?= $cfg['sub_dir'] ?>/index.php"><?php echo $settings['root_folder_name']; ?></a></li>
<?php endif ?>
		  <li><a href="javascript:bookmarknew('<?= $cfg['sub_dir'] ?>', '<?= $folderid; ?>')">New Bookmark</a></li>
		  <li><a href="javascript:bookmarkedit('<?= $cfg['sub_dir'] ?>', checkselected())">Edit Bookmarks</a></li>
		  <li><a href="javascript:bookmarkmove('<?= $cfg['sub_dir'] ?>', checkselected())">Move Bookmarks</a></li>
		  <li><a href="javascript:bookmarkdelete('<?= $cfg['sub_dir'] ?>', checkselected())">Delete Bookmarks</a></li>
		  <li><a href="<?= $cfg['sub_dir'] ?>/shared.php">Shared Bookmarks</a></li>
		</ul>
		</div>

		<div class="navblock">
		<h2 class="nav mnu" target="mnu_folders">Folders</h2>
		<ul class="nav" id="mnu_folders">
			<li><a href="javascript:foldernew('<?= $cfg['sub_dir'] ?>', '<?= $folderid; ?>')">New Folder</a></li>
			<li><a href="javascript:folderedit('<?= $cfg['sub_dir'] ?>', '<?php echo $folderid; ?>')">Edit Folder</a></li>
			<li><a href="javascript:foldermove('<?= $cfg['sub_dir'] ?>', '<?php echo $folderid; ?>')">Move Folder</a></li>
			<li><a href="javascript:folderdelete('<?= $cfg['sub_dir'] ?>', '<?php echo $folderid; ?>')">Delete Folder</a></li>
			<li><a href="/index.php?expand=&amp;folderid=0">Collapse All</a></li>
		</ul>
		</div>

		<div class="navblock">
		<h2 class="nav mnu" target="mnu_tools">Tools</h2>
		<ul class="nav" id="mnu_tools">
<?php if (admin_only()) : ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/admin.php">Admin</a></li>
<?php endif ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/import.php">Import</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/export.php">Export</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/index.php?search=[dupe_check_bookmarks]">Find Duplicates</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/sidebar.php">View as Sidebar</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/settings.php">Settings</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/index.php?logout=1">Logout</a></li>
		</ul>
		</div>
	<!-- Menu ends here. -->
	</div>

	<!-- Main content starts here. -->
	<div id="main">

			<?php if ($search_mode): ?>

			<div style="height: <?php echo ($settings['table_height'] == 0) ? "auto" : $settings['table_height']; ?>; overflow:auto;">

				<div class="bookmark">
					<a class="f" href="/index.php"><img src="<?= $cfg['sub_dir'] ?>/images/folder_open.gif" alt=""> My Bookmarks</a>
				</div>

<?php
	require_once(realpath(DOC_ROOT . '/lib/boolean_search.php'));
/*
	$query from boolean_search.php is not being used here.  Why???
*/

	$searchfields = ['url', 'title', 'description'];

	if ($search == '[dupe_check_bookmarks]') {
		$query = "
			SELECT a.title, a.url, a.description, UNIX_TIMESTAMP(a.date_created) AS timestamp, a.childof, a.id, 
			a.favicon, a.public, f.name, f.id AS fid, f.public AS fpublic
			FROM `obm_bookmarks` AS a
			INNER JOIN `obm_bookmarks` AS b ON a.url = b.url and a.id <> b.id
			LEFT JOIN `obm_folders` AS f ON a.childof = f.id
			ORDER BY a.url";
	}
	else {
		$query = assemble_query($search, $searchfields);  // From boolean_search.php
	}

	if ($mysql->query($query)) {
		$bookmarks = [];
		while ($row = mysqli_fetch_assoc($mysql->result)) {
			array_push($bookmarks, $row);
		}

		if (count($bookmarks) > 0) {
			require_once(realpath(DOC_ROOT . '/bookmarks/bookmarks.php'));
			list_bookmarks(
				bookmarks: $bookmarks,
				show_checkbox: true,
				show_folder: true,
				show_icon: $settings['show_bookmark_icon'],
				show_link: true,
				show_desc: $settings['show_bookmark_description'],
				show_date: $settings['show_column_date'],
				show_edit: $settings['show_column_edit'],
				show_move: $settings['show_column_move'],
				show_delete: $settings['show_column_delete'],
				show_share: $settings['show_public'],
				show_header: false
			);
		}
		else {
			echo '<div id="content"> No Bookmarks found matching <b>' . $search . '</b>.</div>';
		}
	}
	else {
		message($mysql->error);
	}
?>

			</div>

<?php else : ?>

	<!-- Folders start here. -->
	<h2 id="folders-head" class="mobile nav mnu" target="folders"> Folders </h2>
	<div id="folders" class="folders mnu<?php echo (is_mobile_browser() ? ' mobile' : ''); ?>" style="width: <?php echo ($settings['column_width_folder'] == 0) ? 'auto' : $settings['column_width_folder']; ?>; height: <?php echo ($settings['table_height'] == 0) ? 'auto' : $settings['table_height']; ?>;">

<?php
		require_once(realpath(DOC_ROOT . '/folders/folder.php'));
		$tree = new Folder($username);
		$tree->make_tree(0);
		$tree->print_tree();
?>

	<!-- Folders ends here. -->

		<div>	
			<br> &nbsp; <br> &nbsp;
			<?php /* This gives extra space after the folders so the footer doesn't print over them. */ ?>
		</div>
	</div>

	<!-- Bookmarks starts here. -->
	<div class="bookmarks" style="height: <?php echo ($settings['table_height'] == 0) ? 'auto' : $settings['table_height']; ?>;">

<?php
	require_once(realpath(DOC_ROOT . '/bookmarks/bookmarks.php'));
	$query = sprintf("
		SELECT `title`, `url`, `description`, UNIX_TIMESTAMP(`last_visit`) AS timestamp, UNIX_TIMESTAMP(`date_created`) AS creation, `id`, `favicon`, `public`
		FROM `obm_bookmarks`
		WHERE `user` = '%s'
		AND `childof` = '%d'
		AND `deleted` != '1'
		ORDER BY $order[1]",
			$mysql->escape($username),
			$mysql->escape($folderid));

	if ($mysql->query($query)) {
		$bookmarks = [];
		while ($row = mysqli_fetch_assoc($mysql->result)) {
			array_push($bookmarks, $row);
		}
		list_bookmarks(
			bookmarks:$bookmarks,
			show_checkbox:true,
			show_folder:false,
			show_icon:$settings['show_bookmark_icon'],
			show_link:true,
			show_desc:$settings['show_bookmark_description'],
			show_date:$settings['show_column_date'],
			show_edit:$settings['show_column_edit'],
			show_move:$settings['show_column_move'],
			show_delete:$settings['show_column_delete'],
			show_share:$settings['show_public'],
			show_header:true);
	}
	else {
		message($mysql->error);
	}

?>
	<!--javascript:(function(){bmadd=window.open('https://domain.com/bookmarks/new_bookmark.php?title='+encodeURIComponent(document.title)+'&url='+encodeURIComponent(location.href),'bmadd','toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=500,left=50,top=50');setTimeout(function(){bmadd.focus();});})(); -->

	<!-- Bookmarks ends here. -->
	</div>

<?php endif ?>


	<!-- Main content ends here. -->
	</div>
<!-- Wrapper ends here. -->

</div>

<?php
	print_footer();
	require_once(realpath(DOC_ROOT . '/footer.php'));


	#1] This prevented GET variables from being accessible, e.g. $folderid.  Why???
?>