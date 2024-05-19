<?php

	ini_set('display_errors', 1);
	ini_set('error_prepend_string', '<pre style="white-space: pre-wrap;">');
	ini_set('error_append_string', '</pre>');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
	logged_in_only();

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

<?php if (!$search_mode): ?>
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
				var folderurl   = url.replace('index.php', 'folders/async_folders.php');
				var bookmarkurl = url.replace('index.php', 'bookmarks/async_bookmarks.php');

				//if ($('#folders').hasClass('mobile'))
				//	$('#folders').toggle('blind',{},300);

				selected_folder_id = $(this).attr('folderid');

// 				$('.folders').addClass('loading-anim');
// 				$('.bookmarks').addClass('loading-anim');

				$('.folders').load(folderurl, setupFolderIntercepts);
				$('.bookmarks').load(bookmarkurl, setupBookmarkIntercepts);

				return false;
			});
		}

		function setupBookmarkIntercepts() {
			$('.bookmarks').removeClass('loading-anim');

			$('.blink').click(function() {
				var url = $(this).attr('href');
				var bookmarkurl = url.replace('index.php', 'bookmarks/async_bookmarks.php');

				$('.bookmarks').addClass('loading-anim');
				$('.bookmarks').load(bookmarkurl, setupBookmarkIntercepts);

				return false;
			});
		}
		-->
	</script>
<?php endif; ?>

<?php if (is_mobile_browser() && !$search_mode): ?>

	<script>
		<!--
		$(document).ready(function() {
		  // Make collapsible menu.
			$("#menu").hide();

		});
		-->
	</script>
	<link rel="stylesheet" type="text/css" href="/includes/css/mobile.css" />
	<?php echo ($settings['theme'] != '') ? '<link rel="stylesheet" type="text/css" href="/includes/css/mobile'.$settings['theme'].'.css" />' : ''; ?>

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
		  <?php if ($search_mode) { ?>
			 <li><a href="/index.php"><?php echo $settings['root_folder_name']; ?></a></li>
		  <?php } ?>
		  <li><a href="javascript:bookmarknew('<?php echo $folderid; ?>')">New Bookmark</a></li>
		  <li><a href="javascript:bookmarkedit(checkselected())">Edit Bookmarks</a></li>
		  <li><a href="javascript:bookmarkmove(checkselected())">Move Bookmarks</a></li>
		  <li><a href="javascript:bookmarkdelete(checkselected())">Delete Bookmarks</a></li>
		  <li><a href="/shared.php">Shared Bookmarks</a></li>
		</ul>
		</div>

		<div class="navblock">
		<h2 class="nav mnu" target="mnu_folders">Folders</h2>
		<ul class="nav" id="mnu_folders">
			<li><a href="javascript:foldernew('<?php echo $folderid; ?>')">New Folder</a></li>
			<li><a href="javascript:folderedit('<?php echo $folderid; ?>')">Edit Folder</a></li>
			<li><a href="javascript:foldermove('<?php echo $folderid; ?>')">Move Folder</a></li>
			<li><a href="javascript:folderdelete('<?php echo $folderid; ?>')">Delete Folder</a></li>
			<li><a href="/index.php?expand=&amp;folderid=0">Collapse All</a></li>
		</ul>
		</div>

		<div class="navblock">
		<h2 class="nav mnu" target="mnu_tools">Tools</h2>
		<ul class="nav" id="mnu_tools">
			<?php if (admin_only()) { ?>
			<li><a href="/admin.php">Admin</a></li>
			<?php } ?>
			<li><a href="/import.php">Import</a></li>
			<li><a href="/export.php">Export</a></li>
			<li><a href="/index.php?search=[dupe_check_bookmarks]">Find Duplicates</a></li>
			<li><a href="/sidebar.php">View as Sidebar</a></li>
			<li><a href="/settings.php">Settings</a></li>
			<li><a href="/index.php?logout=1">Logout</a></li>
		</ul>
		</div>
	<!-- Menu ends here. -->
	</div>

	<!-- Main content starts here. -->
	<div id="main">

			<?php if ($search_mode): ?>

			<div style="height: <?php echo ($settings['table_height'] == 0) ? "auto" : $settings['table_height']; ?>; overflow:auto;">

				<div class="bookmark">
					<a class="f" href="/index.php"><img src="/images/folder_open.gif" alt=""> My Bookmarks</a>
				</div>

<?php
	require_once($_SERVER['DOCUMENT_ROOT'] .'/lib/boolean_search.php');
/*
	$query from BooleanSearch.php is not being used here.  Why???
*/

	$searchfields = ['url', 'title', 'description'];

	if ($search == '[dupe_check_bookmarks]') {
		$query = "
			SELECT a.title, a.url, a.description, UNIX_TIMESTAMP(a.date) AS timestamp, a.childof, a.id, 
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
			require_once($_SERVER['DOCUMENT_ROOT'] .'/bookmarks/bookmarks.php');
			list_bookmarks(
				bookmarks:$bookmarks,
				show_checkbox:true,
				show_folder:true,
				show_icon:$settings['show_bookmark_icon'],
				show_link:true,
				show_desc:$settings['show_bookmark_description'],
				show_date:$settings['show_column_date'],
				show_edit:$settings['show_column_edit'],
				show_move:$settings['show_column_move'],
				show_delete:$settings['show_column_delete'],
				show_share:$settings['show_public'],
				show_header:false
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

<?php else: ?>

	<!-- Folders start here. -->
	<h2 id="folders-head" class="mobile nav mnu" target="folders"> Folders </h2>
	<div id="folders" class="folders mnu<?php echo (is_mobile_browser() ? ' mobile' : ''); ?>" style="width: <?php echo ($settings['column_width_folder'] == 0) ? 'auto' : $settings['column_width_folder']; ?>; height: <?php echo ($settings['table_height'] == 0) ? 'auto' : $settings['table_height']; ?>;">

<?php
		require_once($_SERVER['DOCUMENT_ROOT'] .'/folders/folder.php');
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
	require_once($_SERVER['DOCUMENT_ROOT'] .'/bookmarks/bookmarks.php');
	$query = sprintf ("
		SELECT `title`, `url`, `description`, UNIX_TIMESTAMP(date) AS timestamp, `id`, `favicon`, `public`
		FROM `obm_bookmarks`
		WHERE `user`='%s'
		AND `childof`='%d'
		AND `deleted`!='1'
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

// 	if (empty($folderid)) {
// 		echo "<script> alert('Hello'); </script>";
// 		echo '<script> window.location.reload(); </script>';
// 	}

// 	echo '$_GET: ';
// 	echo '<pre>'; print_r($_GET); echo '</pre>';
// 	echo '$_SESSION: ';
// 	echo '<pre>'; print_r($_SESSION); echo '</pre>';
// 	echo '$folderid is not being set when clicking on the folders.  Why??? <br>
// 	Clicking a folder twice doesn\'t change anything, but <br>
// 	if I reload the page using Cmd-R, it works. <br>
// 	';
?>
	<!--javascript:(function(){bmadd=window.open('https://domain.com/bookmarks/new_bookmark.php?title='+encodeURIComponent(document.title)+'&url='+encodeURIComponent(location.href),'bmadd','toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=500,height=500,left=50,top=50');setTimeout(function(){bmadd.focus();});})(); -->

	<!-- Bookmarks ends here. -->
	</div>

<?php endif; ?>


	<!-- Main content ends here. -->
	</div>
<!-- Wrapper ends here. -->

</div>

<?php
// echo '<pre>';
// print_r($settings);
// echo '</pre>';

	print_footer();
	require_once($_SERVER['DOCUMENT_ROOT'] .'/footer.php');

	__halt_compiler();

	#1] This prevented GET variables from being accessible, e.g. $folderid
?>