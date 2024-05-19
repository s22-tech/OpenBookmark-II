<?php

class Auth {

	private $username = '';
	private $password = '';

	function __construct() {
		if (!session_id()) {
			session_start();
		}

		if ($this->check_auth()) {
			$_SESSION['logged_in'] = true;
		}
		else {
			$_SESSION['logged_in'] = false;
		}
	}

	function check_auth() {
		if (session_id()
			&& isset($_SESSION['challengekey'])
			&& strlen($_SESSION['challengekey']) === 32
			&& isset($_SESSION['username'])
			&& $_SESSION['username'] != ''
			&& isset($_SESSION['logged_in'])
			&& $_SESSION['logged_in']) {
			return true;
		}
		elseif ($this->check_cookie()) {
			return true;
		}
		return false;
	}

	function assign_data() {
		if (isset($_POST['username'])
			&& isset($_POST['password'])
			&& $_POST['username'] != ''
			&& $_POST['password'] != '') {
				$this->username = $_POST['username'];
				$this->password = $_POST['password'];
				return true;
		}
		return false;
	}

	function login() {
		global $cfg, $mysql;
		$_SESSION['logged_in'] = false;
		if ($this->assign_data()) {
			$query = sprintf("
				SELECT COUNT(*) 
				FROM `obm_users` 
				WHERE md5(`username`) = md5('%s') AND `password` = md5('%s')",
					$mysql->escape($this->username),
					$mysql->escape($this->password)
			);
			if ($mysql->query($query) && mysql_result($mysql->result, 0) === '1') {
				if (isset($_POST['remember'])) {
					$cfg['cookie']['data'] = serialize([$this->username, md5($cfg['cookie']['seed'] . md5($this->password))]);
					setcookie($cfg['cookie']['name'],
								 $cfg['cookie']['data'],
								 $cfg['cookie']['expire'],
								 $cfg['cookie']['path'],
								 $cfg['cookie']['domain']);
				}
				$this->set_login_data($this->username);
			}
			else {
				$this->logout();
			}
		}
		unset($_POST['password']);
		unset($this->password);
	}

	function logout() {
		global $cfg;
		unset($_SESSION['challengekey']);
		unset($_SESSION['username']);
		setcookie($cfg['cookie']['name'], '', time() - 1, $cfg['cookie']['path'], $cfg['cookie']['domain']);
		$_SESSION['logged_in'] = false;
	}

	function set_login_data($username) {
		$_SESSION['challengekey'] = md5($username . microtime());
		$_SESSION['username'] = $username;
		$_SESSION['logged_in'] = true;
	}

	function check_cookie() {
		global $cfg, $mysql;
		if (isset($cfg['cookie']['name'])
			&& $cfg['cookie']['name'] != ''
			&& isset($_COOKIE[$cfg['cookie']['name']])) {
			[$cfg['cookie']['username'], $cfg['cookie']['password_hash']] = unserialize($_COOKIE[$cfg['cookie']['name']]);
			$query = sprintf("
				SELECT COUNT(*) 
				FROM `obm_users` 
				WHERE `username` = '%s' AND MD5(CONCAT('%s', password))='%s'",
					$mysql->escape($cfg['cookie']['username']),
					$mysql->escape($cfg['cookie']['seed']),
					$mysql->escape($cfg['cookie']['password_hash'])
			);
			if ($mysql->query($query) && mysql_result($mysql->result, 0) === '1') {
				$this->set_login_data($cfg['cookie']['username']);
				return true;
			}
			else {
				$this->logout();
				return false;
			}
		}
		return false;
	}

	function display_login_form() {
?>

			<p style="text-align:center; font-size:1.5em;"> Openbookmark Login </p>

			<form name="loginform" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
			<center>
				<table border="0"  style="text-align:left;">
					<tr>
						<td>Username:</td>
						<td><input name="username" type="text" value="" tabindex="1"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input name="password" type="password" value="" tabindex="2"></td>
					</tr>
					<tr>
						<td>Remember login:</td>
						<td><input type="checkbox" name="remember" tabindex="3"></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="Login" tabindex="4"></td>
					</tr>
				</table>

			<?php
				if (strtolower(basename($_SERVER['SCRIPT_NAME'])) == 'index.php') {
					echo '<br><div><a href="./shared.php">Users Sharing Bookmarks</a></div>';
				}
			?>

			</center>
			</form>

		<script>
			document.loginform.username.focus();
		</script>

<?php
	}
}  // END class auth

if (!function_exists('mysqli_result')) {
	function mysqli_result($res, $row, $field=0) {
		$res->data_seek($row);
		$datarow = $res->fetch_array();
		return $datarow[$field];
	}
}

if (!function_exists('mysql_result')) {
	function mysql_result($result, $number, $field=0) {
		mysqli_data_seek($result, $number);
		$row = mysqli_fetch_array($result);
		return $row[$field];
	}
}

?>