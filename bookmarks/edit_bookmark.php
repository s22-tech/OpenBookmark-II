<?php
	require_once(realpath(dirname(__FILE__, 2)) . '/header.php');
	logged_in_only();

	$bmlist           = set_get_num_list('bmlist');

	$post_title       = set_post_title();
	$post_url         = set_post_url();
	$post_description	= set_post_description();
	$refresh_icon     = set_post_bool_var('favicon', false);
	$post_childof     = set_post_childof();
	$post_public      = set_post_bool_var('public', false);

	if (count($bmlist) > 1) {
	  // If there is more than one bookmark to edit, we just care about the public/projects field.
		if (!isset($_POST['public'])) {
			$qbmlist = implode(',', $bmlist);
			$query = sprintf("
				SELECT `title`, `id`, `public`, `favicon` 
				FROM `bookmark` 
				WHERE `id` IN (%s) AND `user`='%s' 
				ORDER BY `title`",
				$mysql->escape($qbmlist),
				$mysql->escape($username)
			);
			if ($mysql->query($query)) {
				require_once(BASE_DIR . '/bookmarks/bookmarks.php');
				$query_string = '?bmlist=' . implode('_"', $bmlist);
?>

	<h2 class="title">Change public state:</h2>
	<div style="width:100%; height:330px; overflow:auto;">

			<?php
				$bookmarks = [];
				while ($row = mysqli_fetch_assoc($mysql->result)) {
					array_push($bookmarks, $row);
				}
				list_bookmarks($bookmarks,
					false,
					false,
					$settings['show_bookmark_icon'],
					false,
					false,
					false,
					false,
					false,
					false,
					true,
					false
				);
			?>

			</div>

			<br>
			<form action="<?php echo $_SERVER['SCRIPT_NAME'] . $query_string; ?>" method="post" name="bmedit">
			<p>
				<select name="public">
				<option value="1">public</option>
				<option value="0">private</option>
				</select>
			</p>
			<input type="submit" value=" OK ">
			<input type="button" value=" Cancel " onclick="self.close()">
			</form>

<?php
			}
			else {
				message($mysql->error);
			}
		}
		else {
			$bmlist = implode(',', $bmlist);
			$query = sprintf("
				UPDATE `obm_bookmarks` 
				SET `public` = '%d'
				WHERE `id` IN (%s)
				AND `user` = '%s'",
					$mysql->escape($post_public),
					$mysql->escape($bmlist),
					$mysql->escape($username)
			);
			if ($mysql->query($query)) {
				echo 'Bookmark successfully updated<br>' . PHP_EOL;
				echo '<script>reloadclose();</script>';
			}
			else {
				message($mysql->error);
			}
		}

	}
	elseif (count($bmlist) < 1) {
		message('No Bookmark to edit.');
	}
	elseif ($post_title == '' || $post_url == '' || $refresh_icon) {
		$query = sprintf("
			SELECT `title`, `url`, `description`, `childof`, `id`, `favicon`, `public`
			FROM `obm_bookmarks`
			WHERE `id` = '%d'
			AND `user` = '%s'
			AND `deleted` != '1'",
				$mysql->escape($bmlist[0]),
				$mysql->escape($username)
		);
		$icon = $new_fav = '';
		if ($mysql->query($query)) {
			if (mysqli_num_rows($mysql->result) != 1) {
				message('No Bookmark to edit');
			}
			else {
				$row = mysqli_fetch_object($mysql->result);
				
				require_once(BASE_DIR . '/folders/folder.php');
				$tree = new Folder();
				$query_string = '?expand=' . implode(',', $tree->get_path_to_root($row->childof)) . '&amp;folderid=' . $row->childof;
				$path = $tree->print_path($row->childof);
				
				if (!empty($refresh_icon) && $settings['show_bookmark_icon']) {
					$current_url = get_current_url($row->url);
					if (parse_url($post_url, PHP_URL_HOST) !== $current_url) {
						message('&bull; It looks like the URL for <b><em>'.$row->title .'</em></b> ' .
						'<br>&nbsp; has changed to:' .
						'<br><br>&nbsp; https://'. $current_url .
						'<br><br>&nbsp; Change it and try again.');
					}
				  // If the Refresh Icon button is clicked...
					if ($post_url) {
						$used_url = $post_url;
					}
					else {
						$used_url = $row->url;
					}
					require_once(BASE_DIR . '/favicon.php');
					$favicon = new Favicon($used_url);
					$new_fav = $favicon->favicon;
debug_logger(name:'favicon->favicon >>', variable:$new_fav, file:__FILE__, function:__FUNCTION__);
					if ($new_fav) {
						$update_query = sprintf("
							UPDATE `obm_bookmarks` 
							SET `favicon` = '%s' 
							WHERE `user` = '%s' AND `id` = '%d'",
								$mysql->escape($new_fav),
								$mysql->escape($username),
								$mysql->escape($bmlist[0])
						);
debug_logger(name:'bm-query', variable:$update_query, file:__FILE__, function:__FUNCTION__);
						if (!$mysql->query($update_query)) {
							message($mysql->error);
						}
						if (!empty($row->favicon)) {  /* && is_file($row->favicon) */
							@unlink(BASE_DIR .'/icons/'. $row->favicon);
						}
						$icon = '<img src="/icons/'. $new_fav .'" width="'.$cfg['icon_w'].'" height="'.$cfg['icon_h'].'" alt="">';
					}
					else {
						message('&bull; edit_bookmark.php &mdash; no favicon was retrieved.');
					}
				}
				elseif ($row->favicon) {  /*  && is_file($row->favicon) */
					$icon = '<img src="/icons/' . $row->favicon . '" width="'. $cfg['icon_w'] .'" height="'. $cfg['icon_h'] .'" alt="">';
				}
			}
		}
		else {
			message($mysql->error);
		}
?>

	<h2 class="title">Edit Bookmark</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?bmlist=" . $row->id; ?>" id="bmedit" method="post">
	<p>Title<br>
	<input type=text name="title" size="50" value="<?php echo $row->title; ?>"> <?php echo $settings['show_bookmark_icon'] ? $icon : ''; ?>
	
<br> 
<?php 
	if ($row->favicon) {
		echo "<span style=\"font-size:0.9em\">Saved favicon: $row->favicon</span>";
	}
	elseif (strlen($new_fav) > 22) {
		echo strlen($new_fav) . " <span style=\"font-size:0.9em\">New favicon: '$new_fav'</span>";
	}
	else {
		echo '';
	}
	echo '<br>folderid: '. $folderid;
?>

	</p>
	<p>URL<br>
	<input type=text name="url" size="50" value="<?php echo $row->url; ?>">
	<p>Description<br>
	<textarea name="description" cols="50" rows="8"><?php echo $row->description; ?></textarea></p>
	<p><input type="button" value="Select folder" onclick="window.childof=document.forms['bmedit'].childof; window.path=document.forms['bmedit'].path; selectfolder('<?php echo $query_string; ?>')"><br>
	<input type="text" name="path" value="<?php echo $path; ?>" size="50" readonly>
	<input type="text" name="childof" value="<?php echo $row->childof; ?>" size="4" class="invisible" readonly></p>
	<p>Tags<br>
	<input type=text name="tags" size="50" value="Not yet working"></p>
	<input type="submit" value=" OK ">
	<input type="button" value=" Cancel " onclick="self.close()">
	<?php if ($settings['show_bookmark_icon']) : ?><input type="submit" value="Refresh Icon" name="favicon"><?php endif; ?>
	Public <input type="checkbox" name="public" <?php echo $row->public ? "checked" : "";?>>
	</form>
	<script>
		this.focus();
		document.getElementById('bmedit').title.focus();
	</script>

<?php
	}
	else {
		$query = sprintf("
			UPDATE `obm_bookmarks` 
			SET `title`='%s', `url`='%s', `description`='%s', `childof`='%d', `public`='%d'
			WHERE `id`='%d'
			AND `user`='%s'",
				$mysql->escape($post_title),
				$mysql->escape($post_url),
				$mysql->escape($post_description),
				$mysql->escape($post_childof),
				$mysql->escape($post_public),
				$mysql->escape($bmlist[0]),
				$mysql->escape($username)
		);
		if ($mysql->query($query)) {
			echo 'Bookmark successfully updated.<br>'. PHP_EOL;
			echo '<script> reloadclose(); </script>';
		}
		else {
			message($mysql->error);
		}
	}

	require_once(BASE_DIR . '/footer.php');
?>
