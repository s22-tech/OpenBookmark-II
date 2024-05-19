<?php

	require_once($_SERVER['DOCUMENT_ROOT'] .'/async_header.php');
	logged_in_only();

	require_once($_SERVER['DOCUMENT_ROOT'] .'/folders/folder.php');
	$tree = new Folder();
	$tree->make_tree(0);
	$tree->print_tree('index.php');
