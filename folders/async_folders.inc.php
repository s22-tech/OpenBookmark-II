<?php

	require_once(realpath(dirname(__DIR__, 1) . '/includes/async_header.inc.php'));
	logged_in_only();

	require_once(realpath(DOC_ROOT .'/folders/folder.php'));
	$tree = new Folder();
	$tree->make_tree(0);
	$tree->print_tree('index.php');
