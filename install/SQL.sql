CREATE TABLE `obm_bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `childof` int(11) NOT NULL DEFAULT 0,
  `user` char(20) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT '',
  `url` varchar(512) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `favicon` varchar(1048) DEFAULT NULL,
  `private` enum('0','1') DEFAULT NULL,
  `public` enum('0','1') NOT NULL DEFAULT '0',
  `last_visit` datetime DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`url`,`description`)
) ENGINE=Aria AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PAGE_CHECKSUM=1;


CREATE TABLE `obm_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `childof` int(11) NOT NULL DEFAULT 0,
  `name` char(70) NOT NULL DEFAULT '',
  `user` char(20) NOT NULL DEFAULT '',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `public` enum('0','1') NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=Aria AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PAGE_CHECKSUM=1;


CREATE TABLE `obm_users` (
  `username` char(50) NOT NULL DEFAULT '',
  `password` char(50) NOT NULL DEFAULT '',
  `theme` varchar(50) NOT NULL DEFAULT '',
  `admin` enum('0','1') NOT NULL DEFAULT '0',
  `language` char(20) NOT NULL DEFAULT '',
  `root_folder_name` char(50) NOT NULL DEFAULT 'My Bookmarks',
  `column_width_folder` smallint(3) NOT NULL DEFAULT 0,
  `column_width_bookmark` smallint(3) NOT NULL DEFAULT 0,
  `table_height` smallint(3) NOT NULL DEFAULT 0,
  `confirm_delete` enum('0','1') NOT NULL DEFAULT '1',
  `open_new_window` enum('0','1') NOT NULL DEFAULT '1',
  `show_bookmark_description` enum('0','1') NOT NULL DEFAULT '1',
  `show_bookmark_icon` enum('0','1') NOT NULL DEFAULT '1',
  `show_column_date` enum('0','1') NOT NULL DEFAULT '1',
  `date_format` smallint(6) NOT NULL DEFAULT 0,
  `show_column_edit` enum('0','1') NOT NULL DEFAULT '1',
  `show_column_move` enum('0','1') NOT NULL DEFAULT '1',
  `show_column_delete` enum('0','1') NOT NULL DEFAULT '1',
  `fast_folder_minus` enum('0','1') NOT NULL DEFAULT '1',
  `fast_folder_plus` enum('0','1') NOT NULL DEFAULT '1',
  `fast_symbol` enum('0','1') NOT NULL DEFAULT '1',
  `simple_tree_mode` enum('0','1') NOT NULL DEFAULT '0',
  `show_public` enum('0','1') NOT NULL DEFAULT '1',
  `private_mode` enum('0','1') NOT NULL DEFAULT '1',
  `notes` varchar(512) DEFAULT NULL,
  UNIQUE KEY `id` (`username`)
) ENGINE=Aria DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PAGE_CHECKSUM=1;
