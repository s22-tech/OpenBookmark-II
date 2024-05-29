<?php

require_once(realpath(__DIR__ . '/config/config.php'));
// require_once(realpath(DOC_ROOT . '/lib/webstart.php'));

require_once(realpath(DOC_ROOT . '/lib/mysql.php'));
$mysql = new Mysql();

require_once(realpath(DOC_ROOT .'/lib/auth.php'));
$auth = new Auth();

require_once(realpath(DOC_ROOT .'/lib/lib.php'));
require_once(realpath(DOC_ROOT .'/lib/login.php'));

class sidebar 
{
	public $tree;
	public $counter;
	public $bookmarks;

	function __construct() {
		global $username, $mysql;

	  // Collect the folder data.
		require_once(realpath(DOC_ROOT .'/folders/folder.php'));
		$this->tree = new Folder();
		$this->tree->folders[0] = ['id' => 0, 'childof' => null, 'name' => $GLOBALS['settings']['root_folder_name']];

		$this->counter = 0;

	  // Collect the bookmark data.
		$query = sprintf("
			SELECT `title`, `url`, `description`, `childof`, `id`, `favicon`
			FROM `obm_bookmarks`
			WHERE `user`='%s'
			AND `deleted`!='1' ORDER BY `title`",
				$mysql->escape($username));

		if ($mysql->query($query)) {
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				if (!isset($this->bookmarks[$row['childof']])) {
					$this->bookmarks[$row['childof']] = [];
				}
				array_push($this->bookmarks[$row['childof']], $row);
			}
		}
		else {
			message($mysql->error);
		}
	}

	function make_tree($folderid) {
		if (isset($this->tree->children[$folderid])) {
			$this->counter++;
			foreach ($this->tree->children[$folderid] as $value) {
				$this->print_folder($value);
				$this->make_tree($value);
				$this->print_folder_close($value);
			}
			$this->counter--;
		}
		$this->print_bookmarks($folderid);
	}

	function print_folder($folderid) {
		global $cfg;
		echo str_repeat('    ', $this->counter) . '<li class="closed"><img src="'. $cfg['sub_dir'] .'/includes/jquery/images/folder.gif" alt=""> ' . $this->tree->folders[$folderid]['name'] . "\n";
		if (isset($this->tree->children[$folderid]) || isset($this->bookmarks[$folderid])) {
			echo str_repeat('    ', $this->counter + 1) . '<ul>'. PHP_EOL;
		}
	}

	function print_folder_close($folderid) {
		if (isset($this->tree->children[$folderid]) || isset($this->bookmarks[$folderid])) {
			echo str_repeat('    ', $this->counter + 1) . '</ul>'. PHP_EOL;
		}
		echo str_repeat('    ', $this->counter) . '</li>'. PHP_EOL;
	}

	function print_bookmarks($folderid) {
		global $cfg;
		$spacer = str_repeat('    ', $this->counter);
		if (isset($this->bookmarks[$folderid])) {
			foreach ($this->bookmarks[$folderid] as $value) {
				if ($value['favicon'] && is_file($value['favicon'])) {
					$icon = '<img src="' . $value['favicon'] . '" width="16" height="16" border="0" alt="">';
				}
				else {
					$icon = '<img src="'. $cfg['sub_dir'] .'?>/includes/jquery/images/file.gif" alt="">';
				}
				echo $spacer .'    <li><a href="'. $value['url'] .'" target="_blank">'. $icon .' '. $value['title'] ."</a></li>\n";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8" />
	<meta name="robots" content="noindex,nofollow,noarchive" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title> OpenBookmark </title>
	<link rel="stylesheet" type="text/css" href="<?= $cfg['sub_dir'] ?>/includes/css/style.css">

	<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery.js"></script>
	<script src="<?= $cfg['sub_dir'] ?>/includes/jquery/jquery.treeview.js"></script>
	<script>
	$(document).ready(function() {
			 $("#browser").Treeview();
	});
	</script>
	<style>
		html, body {height:100%; margin: 0; padding: 0; }

		html>body {
			font-size: 1em;
/* 			font-size: 68.75%; */
		} /* Reset Base Font Size */

		body {
			font-family: Verdana, helvetica, arial, sans-serif;
			font-size: 68.75%;
			background: #fff;
			color: #333;
			padding-left: 20px;
		} /* Reset Font Size */

		.dir, .dir ul {
		padding: 0;
			margin: 0;
			list-style: none;
		}

		.treeview li {
			margin: 0;
			padding: 3px 0pt 3px 16px;
		}

		.dir li { padding: 2px 0 0 16px; list-style: none; }
		.dir ul { display: none; }
		.treeview.dir ul { display: block; }

		.treeview li { background: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-item.gif) 0 0 no-repeat; }
		.treeview .collapsable { background-image: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-collapsable.gif); }
		.treeview .expandable { background-image: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-expandable.gif); }
		.treeview .last { background-image: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-item-last.gif); }
		.treeview .lastCollapsable { background-image: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-collapsable-last.gif); }
		.treeview .lastExpandable { background-image: url(<?= $cfg['sub_dir'] ?>/includes/jquery/images/tv-expandable-last.gif); }

        </style>
		<?php echo ($settings['theme'] != '') ? '<link rel="stylesheet" type="text/css" href="'. $cfg['sub_dir'] .'/includes/css/style'. $settings['theme'] .'.css" />' : ''; ?>
</head>
<body id="sidebarBody">

<p><a href="./"> Back to OpenBookmark </a></p>

<?php

	logged_in_only();

	$sidebar = new sidebar;

	echo '<ul id="browser" class="dir">' . PHP_EOL;
	$sidebar->make_tree(0);
	echo '</ul>'. PHP_EOL;

	require_once(realpath(DOC_ROOT .'/footer.php'));
?>
