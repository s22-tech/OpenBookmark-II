<?php

if (basename ($_SERVER['SCRIPT_NAME']) == basename (__FILE__)) {
	die ('No direct access allowed.');
}

class Folder
{
	public $username;
	public $folderid;
	public $expand;
	public $folders;
	public $tree;
	public $get_children;
	public $level;
	public $foreign_username;
	public $children;

	function __construct($user=false) {
		global $settings, $username, $folderid, $expand;

		$this->username = $username;
		$this->folderid = $folderid;
		$this->expand = $expand;
		$this->folders = [];
		$this->tree = [];
		$this->get_children = [];
		$this->level = 0;
		$this->foreign_username = false;
// echo '$user: '. $user . '<br>';

// This section collapses the folder tree upon bookmark deletions, etc.  Why???
		if ($user) {
			$this->get_shared_data($user);
		}
		else {
			$this->get_user_data();
		}
		$this->get_user_data();

		if ($settings['simple_tree_mode']) {
			$this->expand = $this->get_path_to_root($this->folderid);
		}

	  // Check for invalid folderid in GET variable.
		if (!array_key_exists($this->folderid, $this->folders)) {
			$this->folderid = 0;
		}

	  // Check for invalid expand entries.
		foreach ($this->expand as $key => $value) {
			if (!array_key_exists($value, $this->folders)) {
				unset($this->expand[$key]);
			}
		}
	}

	function get_user_data() {
		global $mysql;
		$query = sprintf("
			SELECT `id`, `childof`, `name`, `public`
			FROM `obm_folders`
			WHERE `user` = '%s' AND `deleted` != '1'
			ORDER BY `name`",
				$mysql->escape($_SESSION['username'])
		);
// echo '<pre>Result:' . PHP_EOL;
// print_r(mysqli_fetch_assoc($mysql->result));
// echo 'username: '. $_SESSION['username'] . PHP_EOL;
// echo '</pre>';	
		if ($mysql->query($query)) {
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				$this->folders[$row['id']] = $row;
				if (empty($this->children[$row['childof']])) {
					$this->children[$row['childof']] = [];
				}
				array_push($this->children[$row['childof']], $row['id']);
			}
		}
		else {
			message($mysql->error);
		}
	}

	function get_shared_data($user) {
		global $mysql, $username;

	  // Does the user exist in the database?
		if (check_username($user)) {
				$this->foreign_username = $user;
		}
		else {
				$this->foreign_username = $username;
		}

	  // Get all shared folders for the given user.
		$query = "
			SELECT `id`, `childof`, `name`, `public` 
			FROM `obm_folders` 
			WHERE `public` = '1' AND `deleted` != '1' AND `user` = '$this->foreign_username' 
			ORDER BY `name`";
	
		if ($mysql->query($query)) {
		  // Make two arrays:
		  // 1) $children containing arrays with children. The keys of these arrays are the id's of the parents.
		  // 2) $folders containing arrays with folder settings (id, childof, name, public).
			$shared_children = [];
			while ($row = mysqli_fetch_assoc($mysql->result)) {
				$this->folders[$row['id']] = $row;
				if (empty($this->children[$row['childof']])) {
					$this->children[$row['childof']] = [];
				}
				array_push($this->children[$row['childof']], $row['id']);
				array_push($shared_children, $row['id']);
			}

			$this->children[0] = [];
		  // The 'childof' fields of each folder with no parent are being set to 0, so each becomes a child of the root folder.
			foreach ($this->folders as $value) {
				if (in_array($value['childof'], $shared_children)) {
					continue;
				}
				else {
					array_push($this->children[0], $value['id']);
					$this->folders[$value['id']]['childof'] = 0;
				}
			}
		}
		else {
			message($mysql->error);
		}
	}


  // Assembles the tree.
	function make_tree($id) {
		if (isset($this->children)) {
			$this->level++;
			if (isset($this->children[$id])) {
				foreach ($this->children[$id] as $value) {
					array_push($this->tree, [
						'level'  => $this->level,
						'id'		=> $value,
						'name'   => $this->folders[$value]['name'],
						'public'	=> $this->folders[$value]['public'],
					]);
				  // Check for children.
					$symbol = &$this->tree[count($this->tree) - 1]['symbol'];
					if (isset($this->children[$value])) {
						if (in_array($value, $this->expand)) {
							$symbol = 'minus';
							$this->make_tree($value);
						}
						else {
							$symbol = 'plus';
						}
					}
					else {
						$symbol = '';
					}
				}
			}
			$this->level--;
		}
	}

  // Draws the tree.
	function print_tree($scriptname='', $bmlist='') {
		global $settings, $folder_opened, $folder_closed, $folder_opened_public, $folder_closed_public, $plus, $minus, $neutral;

		if ($scriptname == '') $scriptname = $_SERVER['SCRIPT_NAME'];  // e.g. index.php

	  // Depending on who's bookmarks are being displayed, we set some variables differently.
		if (!empty($this->foreign_username)) {
			$root_folder_name = ucwords($this->foreign_username) . "'s Bookmarks";
			$user_var = "&amp;user=$this->foreign_username";
		}
		else {
			$root_folder_name = $settings['root_folder_name'];
			$user_var = '';
		}

		$root_folder = [
			'level'  => 0,
			'id'     => 0,
			'name'   => $root_folder_name,
			'symbol' => null,
			'public' => 0,
		];
		if (isset($this->tree)) {
		  // Add root folder to tree.
			array_unshift($this->tree, $root_folder);
		}

	  // The top folder shows up too high at the top. Draw a little space there.
		echo '<div class="foldertop"></div>' . PHP_EOL;

// usort($this->tree, function ($folder1, $folder2) {
//     return $folder1['name'] <=> $folder2['name'];
// });
// array_multisort(array_column($this->tree, 'name'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $this->tree);
// echo '<pre>'; print_r($this->tree); echo '</pre>';

		foreach ($this->tree as $key => $value) {
		  // This is the begining of the line that shows a folder
		  // with the symbol (plus, minus, or neutral).
			$spacer = '<div style="margin-left:' . $value['level'] * 20 . 'px;">';
			echo $spacer;

// echo 'v: '. $value['id'] .' &mdash; f: '. $this->folderid . '<br>';
			if ($value['id'] == $this->folderid) {
				$folder_name = '<span class="active">' . $value['name'] . '</span>';
				if (!$this->foreign_username && $value['public']) {
					$folder_image = $folder_opened_public;
				}
				else {
					$folder_image = $folder_opened;
				}
			}
			else {
				$folder_name = $value['name'];
				if (!$this->foreign_username && $value['public']) {
					$folder_image = $folder_closed_public;
				}
				else {
					$folder_image = $folder_closed;
				}
			}

			if ($key > 5) {
				$anchor = '#' . $this->tree[$key - 5]['id'];
			}
			else {
				$anchor = '';
			}

			$scroll = '';
			if ($value['symbol'] == 'plus' || $value['symbol'] == 'minus') {
				if ($value['symbol'] == 'plus') {
					$symbol = $plus;
					$expand_s = $this->add_to_expand_list($value['id']);
					if ($settings['fast_folder_plus']) {
						$expand_f = $expand_s;
					}
					else {
						$expand_f = $this->expand;
					}
				}
				elseif ($value['symbol'] == 'minus') {
					$symbol = $minus;
					$expand_s = $this->remove_from_expand_list($value['id']);
					if ($settings['fast_folder_minus'] && $value['id'] == $this->folderid) {
						$expand_f = $expand_s;
					}
					else {
						$expand_f = $this->expand;
					}
				}
				if ($settings['fast_symbol']) {
					$folderid = $value['id'];
				}
				else {
					$folderid = $this->folderid;
				}

			  // This prints the plus or minus symbol with it's appropriate link.
				echo '<a folderid="'. $folderid .'" class="f flink" href="' . $scriptname . '?expand=' . implode(',', $expand_s);
				echo '&folderid=' . $folderid  . $user_var . $anchor . '">' . $symbol . '</a>';
			}
			else {
			  // When there are no sub-folders.
				if (str_contains($_SERVER['PHP_SELF'], 'index.php')) $scroll = '&scroll=top';
				$symbol = $neutral;
				$expand_f = $this->expand;
				echo $symbol;
			}

		  // This prints the folder name with it's appropriate HTML link...
			$bm_list = '';
			if (!empty($bmlist)) $bm_list = '&bmlist='. $bmlist;
			echo '<a folderid="'.$value['id'] .'" class="f flink" href="'. $scriptname .'?expand='. implode(',', $expand_f);
			echo $bm_list . '&folderid=' . $value['id'] . $user_var . $anchor . $scroll .'" name="'. $value['id'] . '">' . $folder_image .' '. $folder_name . '</a>';
			echo '</div>' . PHP_EOL;
		}
	}

	###
	### Removes a value from the expand list.
	###
	function remove_from_expand_list($id) {
		$expand = $this->expand;
		foreach ($expand as $key => $value) {
			if ($value == $id) {
				unset($expand[$key]);
			}
		}
		return $expand;
	}

	###
	### Adds a value to the expand list.
	###
	function add_to_expand_list($id) {
		$expand = $this->expand;
		array_push($expand, $id);
		return $expand;
	}

	###
	### Returns an array containing all folder id's from
	### a given folder up to the root folder.
	###
	function get_path_to_root($id) {
// echo '$id: '. $id . '<br>';
		$path = [];
		while ($id > 0) {
			array_push($path, $id);

// echo '<pre>';
// print_r($path);
// echo 'x: '. $this->folders[$id] . '<br>';
// echo '</pre>';

			if (empty($this->folders[$id])) {
				echo "Folder #{$id} does not have a parent";  //:debug
				return [];
			}
			else {
				$id = $this->folders[$id]['childof'];
			}
		}
		return $path;
	}

	###
	### Prints a path.
	###
	function print_path($id) {
		global $settings, $cfg;
		$parents = $this->get_path_to_root($id);
		$parents = array_reverse($parents);
		// The following if condition has been disabled. Could be enabled to
		// allow the "show_root_folder" function.
		$path = DIRECTORY_SEPARATOR . $settings['root_folder_name'];
		foreach ($parents as $value) {
			$path .= DIRECTORY_SEPARATOR . $this->folders[$value]['name'];
		}
		return $path;
	}

	###
	### Returns an array containing all folder id's that
	### are children from a given folder.
	###
	function get_children($id) {
		if (isset($this->children[$id])) {
			foreach ($this->children[$id] as $value) {
				array_push($this->get_children, $value);
				$this->get_children($value);
			}
		}
	}
}
