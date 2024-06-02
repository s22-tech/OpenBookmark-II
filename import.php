<?php
	require_once(realpath(__DIR__ . '/header.php'));
	logged_in_only();
?>

<h1 id="caption">Import Bookmarks</h1>

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
			<?php if (admin_only()) { ?>
			<li><a href="<?= $cfg['sub_dir'] ?>/admin.php">Admin</a></li>
			<?php } ?>
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

<?php

	if (empty ($_FILES['importfile']['tmp_name']) || $_FILES['importfile']['tmp_name'] === null) {
	  // Get the browser type for default setting below if possible.
		if ( preg_match ('/opera/i', $_SERVER['HTTP_USER_AGENT'])) {
			$default_browser = 'opera';
		}
		else {
			$default_browser = 'netscape';
		}
		?>

		<form enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
		  <table border="0">
			 <tr>
				<td>
				  from Browser:
				</td>
				<td>
				  <select name="browser">
					 <option value="netscape"<?php if ($default_browser == 'netscape') { echo ' selected'; } ?>>Netscape / Mozilla / IE</option>
					 <option value="opera"<?php if ($default_browser == 'opera') { echo ' selected'; } ?>>Opera .adr</option>
				  </select>
				</td>
			 </tr>

			 <tr>
				<td>
				  Select File:
				</td>
				<td>
				  <input type="file" name="importfile">
				</td>
			 </tr>

			<tr>
				<td>Character encoding:</td>
				<td>
					<select name="charset">
					<?php
					$charsets = return_charsets();
					foreach ($charsets as $value) {
						$selected = '';
						if ($value === 'UTF-8') {$selected = ' selected';}
						echo '<option value="'.$value.'"'.$selected.'>'.$value.'</option>' . "\n";
					}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<td>Make them:</td>
				<td>
					<select name="public">
					<option value="1">public</option>
					<option value="0" selected>private</option>
					</select>
				</td>
			</tr>

			 <tr>
				<td valign="top">
				  Destination Folder:
				</td>
				<td>
			 <div style="width:<?php echo (($settings['column_width_folder'] === 0) ? "auto" : $settings['column_width_folder']); ?>; height:350px; overflow:auto;">

			<?php
				require_once(realpath(DOC_ROOT . '/folders/folder.php'));
				$tree = new Folder();
				$tree->make_tree(0);
				$tree->print_tree();
			?>

			</div>
				</td>
			 </tr>

			 <tr>
				<td>
					<p><input type="button" value=" New Folder " onclick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'"></p>
				  <input type="hidden" name="parentfolder" value="<?php echo $folderid; ?>">
				  <input type="submit" value=" Import ">
				  <input type="button" value=" Cancel " onclick="self.location.href='<?= $cfg['sub_dir'] ?>/index.php'">
				</td>
				<td>
				</td>
			 </tr>

		  </table>
		</form>

		<?php
	}
	else {
		if (empty($_POST['browser']) || $_POST['browser'] === '') {
			message('No browser selected');
		}

		$parentfolder = set_post_parentfolder();
		$import = new Import();

		if ($_POST['browser'] === "opera") {
			$import->import_opera();
		}
		elseif ($_POST['browser'] === "netscape") {
			$import->import_netscape();
		}
		echo "{$import->count_folders} folders and {$import->count_bookmarks} bookmarks imported.<br>" . PHP_EOL;
		echo '<a href="'. $cfg['sub_dir'] .'/index.php">My Bookmarks</a>';
	}

?>

		</div>
	<!-- Main content ends here. -->
	</div>
<!-- Wrapper ends here. -->
</div>

<?php

	class Import
	{
		function __construct() {
			global $username, $parentfolder, $mysql;

			# Open the importfile.
			$this->fp = fopen($_FILES['importfile']['tmp_name'], 'r');
			if ($this->fp === null) {
				message('Failed to open file.');
			}

			$this->charset = set_post_charset();
			$this->public = set_post_bool_var('public', false);

			$this->count_folders = 0;
			$this->count_bookmarks = 0;

			$this->username = $username;
			$this->parent_folder = $parentfolder;
			$this->current_folder = $this->parent_folder;

			$this->folder_depth = [];

			$this->mysql = $mysql;
		}

		function import_opera() {
			while (!feof($this->fp)) {
				$line = trim (fgets($this->fp, 4096));

			  // A folder has been found.
				if ($line === '#FOLDER') {
					$item = 'Folder';
				}
			  // A bookmark has been found.
				elseif ($line === '#URL') {
					$item = 'Bookmark';
				}
			  // If a line starts with NAME= ...
				elseif (substr ($line, 0, strlen('NAME=')) === 'NAME=') {
					$line = substr ($line, strlen ('NAME='));
				  // ... depending on the value of "$item" we assign the name to
				  // either folder or bookmark.
					if ($item === 'Folder') {
						$this->name_folder = input_validation($line, $this->charset);
					}
					elseif ($item === 'Bookmark') {
						$this->name_bookmark = input_validation($line, $this->charset);
					}
				}
			  // Only bookmarks can have a description or/and an url.
				elseif (substr ($line, 0, strlen ('DESCRIPTION=')) === 'DESCRIPTION=') {
					$this->description = substr(input_validation($line, $this->charset), strlen('DESCRIPTION='));
				}
				elseif (substr ($line, 0, strlen ('URL=')) === 'URL=') {
					$this->url = substr(input_validation ($line, $this->charset), strlen('URL='));
				}
			  // Process the corresponding item, if there is an empty line found.
				elseif ($line === '') {
					if (isset ($item) && $item === 'Folder') {
						$this->folder_new();
						unset($item);
					}
					elseif (isset ($item) && $item === 'Bookmark') {
						$this->bookmark_new();
						unset($item);
					}
				}
			  // This indicates that the folder is being closed.
				elseif ($line === '-') {
					$this->folder_close();
				}
			}
		}

		function import_netscape() {
			while (!feof($this->fp)) {
				$line = trim(fgets($this->fp));
				# Netscape seems to store html encoded values.
				$line = html_entity_decode($line, ENT_QUOTES, $this->charset);

			  // A folder has been found.
				if (preg_match ("/<DT><H3/", $line)) {
					$this->name_folder = input_validation(preg_replace ("/^( *<DT><[^>]*>)([^<]*)(.*)/", '\\2', $line), $this->charset);
					$this->folder_new ();
				}
			  // A bookmark has been found.
				elseif (preg_match('/<DT><A/', $line)){
					$this->name_bookmark = input_validation(preg_replace ("/^( *<DT><[^>]*>)([^<]*)(.*)/", '\\2', $line), $this->charset);
					$this->url = input_validation(preg_replace ("/([^H]*HREF=\")([^\"]*)(\".*)/", '\\2', $line), $this->charset);
					$this->bookmark_new();
					$insert_id = mysql_insert_id();
				}
			  // This is a description. It is only being saved
			  // if a bookmark has been saved previously.
				elseif (preg_match("/<DD>*/", $line)) {
					if (isset ($insert_id)) {
						$this->description = input_validation (preg_replace ("/^( *<DD>)(.*)/", '\\2', $line), $this->charset);
						$query = sprintf("UPDATE `obm_bookmarks` SET `description`='%s' WHERE `id`='%d' AND `user`='%s'",
							$this->mysql->escape($this->description),
							$this->mysql->escape($insert_id),
							$this->mysql->escape($this->username));

						$this->mysql->query($query);
						unset($this->description);
						unset($insert_id);
					}
				}
			  // This indicates, that the folder is being closed.
				elseif ($line === '</dl><p>') {
					$this->folder_close ();
				}
			}
		}

		function folder_new() {
			if (empty($this->name_folder)) {
				$this->name_folder === '';
			}
			$query = sprintf("INSERT INTO `obm_folders` (childof, name, user, public) VALUES ('%d', '%s', '%s', '%d')",
				$this->mysql->escape($this->current_folder),
				$this->mysql->escape($this->name_folder),
				$this->mysql->escape($this->username),
				$this->mysql->escape($this->public));

			if ($this->mysql->query($query)) {
				$this->current_folder = mysql_insert_id();
				array_push($this->folder_depth, $this->current_folder);
				unset($this->name_folder);
				$this->count_folders++;
			}
			else {
				message($this->mysql->error);
			}
		}

		function bookmark_new() {
			if (empty($this->name_bookmark)) {
				$this->name_bookmark = '';
			}
			if (empty($this->url)) {
				$this->url = '';
			}
			if (empty($this->description)) {
				$this->description = '';
			}
			$query = sprintf ("
				INSERT INTO `obm_bookmarks` (`user`, `title`, `url`, `description`, `childof`, `public`)
				VALUES ('%s', '%s', '%s', '%s', '%d', '%d')",
					$this->mysql->escape($this->username),
					$this->mysql->escape($this->name_bookmark),
					$this->mysql->escape($this->url),
					$this->mysql->escape($this->description),
					$this->mysql->escape($this->current_folder),
					$this->mysql->escape($this->public)
			);

			if ($this->mysql->query($query)) {
				unset($this->name_bookmark, $this->url, $this->description);
				$this->count_bookmarks++;
			}
			else {
				message($this->mysql->error);
			}
		}

		function folder_close() {
			if (count($this->folder_depth) <= 1) {
				$this->folder_depth = [];
				$this->current_folder = $this->parent_folder;
			}
			else {
			  // Remove the last folder from the folder history.
				unset($this->folder_depth[count($this->folder_depth) - 1]);
				$this->folder_depth = array_values($this->folder_depth);
			  // Set the last folder to the current folder.
				$this->current_folder = $this->folder_depth[count($this->folder_depth) - 1];
			}
		}
	}

	print_footer();
	require_once(realpath(DOC_ROOT . '/footer.inc.php'));
?>
