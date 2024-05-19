<?php

class SimplePDO {
	private static $_instance = null;
	private $_stmt;

	public function __construct() {
		global $cfg;
		try {
			$this->dbhost = new PDO(
				'mysql:host=' . $cfg['hostspec'] . ';dbname=' . $cfg['database'],
				$cfg['db_user'],
				$cfg['db_pass'],
			);
			// $this->dbhost = new PDO('mysql:host=localhost;dbname=marccole_s22_obm', 'username', 'password');
		}
		catch(PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new SimplePDO();
		}
		return self::$_instance;
	}

	public function query($query) {
		$this->_stmt = $this->dbhost->prepare($query);
	}

	public function bind($param, $value, $type = null) {
		if (is_null($type)) {
			$type = match(true) {
				is_int($value) => PDO::PARAM_INT,
				is_bool($value) => PDO::PARAM_BOOL,
				is_null($value) => PDO::PARAM_NULL,
				default => PDO::PARAM_STR,
			};
		}
		$this->_stmt->bindValue($param, $value, $type);
	}

	public function execute_pdo() {
		return $this->_stmt->execute();
	}

	public function result_set() {
		$this->execute_pdo();
		return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function row_count() {
		return $this->_stmt->rowCount();
	}

	public function single() {
		$this->execute_pdo();
		return $this->_stmt->fetch(PDO::FETCH_ASSOC);
	}
}