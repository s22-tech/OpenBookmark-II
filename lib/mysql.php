<?php

class mysql {

	public $result = false;
	public $error = '';
	public $conn;
	

	public function __construct() {
		global $cfg;
		require_once(APPLICATION_PATH . '/config/config.php');
		
		$this->conn = mysqli_connect($cfg['hostspec'], $cfg['db_user'], $cfg['db_pass'], $cfg['database']);

		if (!$this->conn) {
			die('*** Connect Error: ' . mysqli_connect_error());
		}
		
	// Check if server is alive.
		if (mysqli_ping($this->conn)) {
			//printf("Our connection is ok!\n");
		}
		else {
			printf("Error: %s\n", mysqli_error($this->conn));
		}

		if (!$this->conn) {
			$this->error = mysqli_error($this->conn);
		}
// 		if (! mysqli_select_db($this->con, $cfg['dsn']['database'])) {
// 			$this->error = mysqli_error($this->conn);
// 		}
		return $this->conn;
	}

	public function query($query) {
		if ($this->result = mysqli_query($this->conn, $query)) {
			return true;
		}
		else {
			$this->error = mysqli_error($this->conn);
			return false;
		}
	}

	public function escape($string) {
		return mysqli_real_escape_string($this->conn, $string);
	}

}