<?php

/* Old file - needs updating. */

	if (empty($_POST['browser']) || $_POST['browser'] == '' ||
				($_POST['browser'] != 'netscape' &&
				 $_POST['browser'] != 'opera' &&
				 $_POST['browser'] != 'IE')) {

	  // header.php is included here, because we want to print
	  // plain text when exporting bookmarks, so that browsers
	  // can handle results better.  header.php is needed only to
	  // display HTML.
		require_once(__DIR__ . '/header.php');
		logged_in_only();

		$folderid = set_get_folderid();

	  // Get the browser type for default setting below if possible.
		if ( preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT'])) {
			$default_browser = 'opera';
		}
		elseif (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT'])) {
			$default_browser = 'IE';
		}
		else {
			$default_browser = 'netscape';
		}
?>

<h1 id="caption">Export Bookmarks</h1>

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
<?php if (admin_only()) : ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/admin.php">Admin</a></li>
<?php endif ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/import.php">Import</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/export.php">Export</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/sidebar.php">View as Sidebar</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/settings.php">Settings</a></li>
			<li><a href="<?= $cfg['sub_dir'] ?>/index.php?logout=1">Logout</a></li>
		</ul>
	<!-- Menu ends here. -->
	</div>

	<!-- Main content starts here. -->
	<div id="main">
		<div id="content">

<form enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
  <table border="0">
    <tr>
      <td>
        Export Bookmarks to Browser:
      </td>
      <td width="<?php echo (($settings['column_width_folder'] == 0) ? "auto" : $settings['column_width_folder'])?>">
        <select name="browser">
          <option value="IE"<?php if ($default_browser == 'IE') { echo ' selected'; } ?>>Internet Explorer</option>
          <option value="netscape"<?php if ($default_browser == 'netscape') { echo ' selected'; } ?>>Netscape / Mozilla</option>
          <option value="opera"<?php if ($default_browser == 'opera') { echo ' selected'; } ?>>Opera .adr</option>
        </select>
      </td>
    </tr>

	<tr>
		<td>Character encoding</td>
		<td>
			<select name="charset">
<?php
			$charsets = return_charsets();
			foreach ($charsets as $value) {
				$selected = '';
				if ($value == 'UTF-8') { $selected = ' selected'; }
				echo '<option value="'.$value.'"'.$selected.'>'.$value.'</option>' . "\n";
			}
?>
			</select>
		</td>
	</tr>

    <tr>
      <td>
        Folder to export:
      </td>
      <td>
	<div style="width:<?php echo (($settings['column_width_folder'] == 0) ? "auto" : $settings['column_width_folder']); ?>; height:350px; overflow:auto;">

<?php
	require_once(DOC_ROOT . '/folders/folder.php');
	$tree = new Folder();
	$tree->make_tree(0);
	$tree->print_tree();
?>

	</div>
      </td>
    </tr>

    <tr>
      <td>
        <input type="hidden" name="folder" value="<?php echo $folderid; ?>">
        <input type="submit" value=" Export ">
        <input type="button" value=" Cancel " onclick="self.location.href='<?= $cfg['sub_dir'] ?>/index.php'">
      </td>
      <td>
      </td>
    </tr>
  </table>
</form>

		</div>
	<!-- Main content ends here. -->
	</div>
<!-- Wrapper ends here. -->
</div>

<?php
		print_footer();
		require_once(DOC_ROOT . '/footer.php');
	}
	else {
	  // These files are being included, because we do not want to include
	  // header.php since there is no reason for the http header to display.
		//require_once(DOC_ROOT . '/lib/webstart.php');
		require_once(DOC_ROOT . '/config/config.php');
		require_once(DOC_ROOT . '/lib/mysql.php');
		$mysql = new Mysql();
		require_once(DOC_ROOT . '/lib/auth.php');
		$auth = new Auth();
		require_once(DOC_ROOT . '/lib/lib.php');
		logged_in_only();
		require_once(DOC_ROOT . '/lib/login.php');

		$browser = set_post_browser();
		if ($browser == 'opera') {
			$filename = 'opera6.adr';
		}
		elseif ($browser == 'IE') {
			$filename = 'bookmark.htm';
		}
		elseif ($browser == 'netscape') {
			$filename = 'bookmarks.html';
		}
		else {
			$filename = 'bookmarks.html';
		}

		header("Content-Disposition: attachment; filename=$filename");
		header("Content-type: application/octet-stream");
		header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);     // HTTP/1.0
		header("Content-Type: text/html; charset=UTF-8");

		$folderid = set_get_folderid();
		if ($browser == 'netscape' || $browser == 'IE') {
			echo '<!DOCTYPE NETSCAPE-Bookmark-file-1>' . PHP_EOL;
			echo '<title>Bookmarks</title>' . PHP_EOL;
			echo '<h1>Bookmarks</h1>' . PHP_EOL;
			echo '<dl><p>' . PHP_EOL;
			$export = new Export();
			$export->make_tree($folderid);
			echo '</dl><p>' . PHP_EOL;
		}
		elseif ($browser == 'opera') {
			echo 'Opera Hotlist version 2.0' . PHP_EOL;
			echo 'Options: encoding = utf8, version=3' . PHP_EOL . PHP_EOL;
			$export = new Export();
			$export->make_tree($folderid);
		}
	}

	class Export
	{
		function export() {
			global $settings, $browser;
		  // Collect the folder data.
			require_once(DOC_ROOT . '/folders/folder.php');
			$this->tree = new Folder();
			$this->tree->folders[0] = ['id' => 0, 'childof' => null, 'name' => $settings['root_folder_name']];

			global $username, $mysql;
			$this->browser = $browser;

			$this->counter = 0;

			$this->charset = set_post_charset();

		  // Collect the bookmark data.
			$query = sprintf("
				SELECT `title`, `url`, `description`, `childof`, `id`
				FROM `obm_bookmarks`
				WHERE user = '%s'
				AND deleted != '1'",
					$mysql->escape($username)
			);

			if ($mysql->query($query)) {
				while ($row = mysqli_fetch_assoc($mysql->result)) {
					if (empty($this->bookmarks[$row['childof']])) {
						$this->bookmarks[$row['childof']] = [];
					}
					array_push($this->bookmarks[$row['childof']], $row);
				}
			}
			else {
				message($mysql->error);
			}
		}

		function make_tree($id) {
			if (isset($this->tree->children[$id])) {
				$this->counter++;
				foreach ($this->tree->children[$id] as $value) {
					$this->print_folder($value);
					$this->make_tree($value);
					$this->print_folder_close();
				}
				$this->counter--;
			}
			$this->print_bookmarks($id);
		}


		function print_folder($folderid) {
			$spacer = str_repeat('    ', $this->counter);
			$foldername = html_entity_decode($this->tree->folders[$folderid]['name'], ENT_QUOTES, $this->charset);
			if ($this->browser == 'netscape') {
				echo $spacer . '<dt><h3>' . $foldername . '</h3>'. PHP_EOL;
				echo $spacer . '<dl><p>'. PHP_EOL;
			}
			elseif ($this->browser == 'IE') {
				echo $spacer . '<dt><h3 FOLDED ADD_DATE="">' . $foldername . '</h3>'. PHP_EOL;
				echo $spacer . '<dl><p>'. PHP_EOL;
			}
			elseif ($this->browser == 'opera') {
				echo PHP_EOL .'#FOLDER'. PHP_EOL;
				echo "\t".'NAME=' . $foldername . PHP_EOL;
			}
		}

		function print_folder_close() {
			$spacer = str_repeat('    ', $this->counter);
			if ($this->browser == 'netscape' || $this->browser == 'IE') {
				echo $spacer . '</dl><p>'. PHP_EOL;
			}
			elseif ($this->browser == "opera") {
				echo "\n-\n";
			}
		}

		function print_bookmarks($folderid) {
			$spacer = str_repeat('    ', $this->counter);
			if (isset($this->bookmarks[$folderid])) {
				foreach ($this->bookmarks[$folderid] as $value) {
					$url   = html_entity_decode($value['url'],   ENT_QUOTES, $this->charset);
					$title = html_entity_decode($value['title'], ENT_QUOTES, $this->charset);
					if ($value['description'] != '') {
						$description = html_entity_decode($value['description'], ENT_QUOTES, $this->charset);
					}
					else {
						$description = '';
					}

					if ($this->browser == 'netscape') {
						echo $spacer . '    <dt><a href="' . $url . '">' . $title . "</a>\n";
						if ($description != '') {
							echo $spacer . '    <dd>' . $description . PHP_EOL;
						}
					}
					elseif ($this->browser == 'IE') {
						echo $spacer . '    <dt><a href="' . $url . '" ADD_DATE="" LAST_VISIT="" LAST_MODIFIED="">' . $title . '</a>'. PHP_EOL;
						// Unfortunately, description for bookmarks in MS Internet Explorer is not supported.
						// Thats why we just ignore the output of the description here.
					}
					elseif ($this->browser == 'opera') {
						echo PHP_EOL .'#URL'. PHP_EOL;
						echo "\t".'NAME=' . $title . PHP_EOL;
						echo "\t".'URL=' . $url . PHP_EOL;
						if ($description != '') {
						  // Opera cannot handle the \r\n character, so we fix this.
							$description = str_replace ("\r\n", ' ', $description);
							echo "\t".'DESCRIPTION=' . $description . PHP_EOL;
						}
					}
				}
			}
		}
	}
?>