<?php

function is_mobile_browser() {
  // Manual override to show mobile.
	if (isset($_GET['mobile']) && $_GET['mobile'] == '1')
		return true;

	// Detect mobile.
	$device = false;
	if ( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
		$device = true;
	}
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'],'ipod') || strstr($_SERVER['HTTP_USER_AGENT'],'ipod') ) {
		$device = true;
	}
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
		$device = true;
	}
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
		$device = true;
	}
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
		$device = true;
	}
	return $device;
}


###
### Prints a message and exits the application properly.
###
function message($message) {
	if (isset($message)) {
		echo '<p>' . $message . '</p>';
	}
	require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
}


/*
 * Checks whether the user is logged in.
 * Displays a link to login if not and exit application.
 */
function logged_in_only() {
	if (empty($_SESSION['logged_in']) || ! $_SESSION['logged_in']) {
		global $auth;
		$auth->display_login_form();
		require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
	}
}


function input_validation($data, $charset = 'UTF-8') {
	if (is_array($data)) {
		foreach ($data as $key => $value) {
			$data[$key] = input_validation($value);
		}
	}
	else {
		$data = htmlentities(trim($data), ENT_QUOTES, $charset);
	}
	return $data;
}


/*
 * Verify some GET variables
 */
###
### Setting the expand variable. If empty in _GET we use the one from _SESSION if available.
### Call this function only once, otherwise some strange things will happen.
###
function set_get_expand() {
	if (empty($_GET['expand'])) {
		if (isset($_SESSION['expand']) && is_array($_SESSION['expand'])) {
			$return = set_num_array($_SESSION['expand']);
		}
		else {
			$return = [];
		}
	}
	elseif ($_GET['expand'] == '') {
		$return = [];
	}
	else {
		$return = explode(',', $_GET['expand']);
		$return = set_num_array($return);
	}
	$return = input_validation($return);
	$_SESSION['expand'] = $return;
	return ($return);
}


function set_get_folderid() {
	if (empty($_GET['folderid']) || $_GET['folderid'] == '' || !is_numeric($_GET['folderid'])) {
		if (isset($_SESSION['folderid'])) {
			$return = $_SESSION['folderid'];
		}
		else {
			$return = 0;
		}
	}
	else {
		$return = $_GET['folderid'];
	}
	$return = input_validation($return);
	$_SESSION['folderid'] = $return;
	return ($return);
}




################## GET title and url are handled a bit special.


function set_get_title() {
	if (empty($_GET['title']) || $_GET['title'] == '') {
		$return = '';
	}
	else {
		$return = $_GET['title'];
	}
	return input_validation($return);
}


function set_get_url() {
	if (empty($_GET['url']) || $_GET['url'] == '') {
		$return = '';
	}
	else {
		$return = $_GET['url'];
	}
	return input_validation($return);
}


function set_session_title() {
	if (empty($_SESSION['title']) || $_SESSION['title'] == '') {
		$return = '';
	}
	else {
		$return = $_SESSION['title'];
	}
	return $return;
}


function set_session_url() {
	if (empty($_SESSION['url']) || $_SESSION['url'] == '') {
		$return = '';
	}
	else {
		$return = $_SESSION['url'];
	}
	return $return;
}


function set_title() {
	$get_title = set_get_title();
	$session_title = set_session_title();

	if ($get_title == '' && $session_title == '') {
		$return = '';
	}
	elseif ($get_title != '') {
		$_SESSION['title'] = $get_title;
		$return = $get_title;
	}
	elseif ($session_title != '') {
		$_SESSION['title'] = $session_title;
		$return = $session_title;
	}
	return $return;
}


function set_url() {
	$get_url = set_get_url();
	$session_url = set_session_url();

	if ($get_url == '' && $session_url == '') {
		$return = '';
	}
	elseif ($get_url != '') {
		$_SESSION['url'] = $get_url;
		$return = $get_url;
	}
	elseif ($session_url != '') {
		$_SESSION['url'] = $session_url;
		$return = $session_url;
	}
	return $return;
}


#############################################


function set_get_noconfirm() {
	if (empty($_GET['noconfirm']) || $_GET['noconfirm'] == '') {
		$return = false;
	}
	else {
		$return = true;
	}
	return $return;
}


function set_get_order() {
	if (empty($_GET['order']) || $_GET['order'] == '') {
		$return = ['titleasc', 'title ASC'];
	}
	elseif ($_GET['order'] == 'datedesc') {
		$return = ['datedesc', 'date DESC'];
	}
	elseif ($_GET['order'] == 'dateasc') {
		$return = ['dateasc', 'date ASC'];
	}
	elseif ($_GET['order'] == 'titledesc') {
		$return = ['titledesc', 'title DESC'];
	}
	elseif ($_GET['order'] == 'titleasc') {
		$return = ['titleasc', 'title ASC'];
	}
	else {
		$return = ['titleasc', 'title ASC'];
	}
	return $return;
}


/*
 * Verify some POST variables
 */


function set_post_childof() {
	if (empty($_POST['childof']) || $_POST['childof'] == '' || !is_numeric($_POST['childof'])) {
		$return = 0;
	}
	else {
		$return = $_POST['childof'];
	}
	return input_validation($return);
}


function set_post_title() {
	if (empty($_POST['title']) || $_POST['title'] == '') {
		$return = '';
	}
	else {
		$return = $_POST['title'];
	}
	return input_validation($return);
}


function set_post_url() {
	if (empty($_POST['url']) || $_POST['url'] == '') {
		$return = '';
	}
	else {
		$return = $_POST['url'];
	}
	return input_validation($return);
}


function set_post_description() {
	if (empty($_POST['description']) || $_POST['description'] == '') {
		$return = '';
	}
	else {
		$return = $_POST['description'];
	}
	return input_validation($return);
}


function set_post_foldername() {
	if (empty($_POST['foldername']) || $_POST['foldername'] == '') {
		$return = '';
	}
	else {
		$return = $_POST['foldername'];
	}
	return input_validation($return);
}


function set_post_sourcefolder() {
	if (empty($_POST['sourcefolder']) || $_POST['sourcefolder'] == '' || !is_numeric($_POST['sourcefolder'])) {
		$return = '';
	}
	else {
		$return = $_POST['sourcefolder'];
	}
	return input_validation($return);
}


function set_post_parentfolder() {
	if (empty($_POST['parentfolder']) || $_POST['parentfolder'] == '' || !is_numeric($_POST['parentfolder'])) {
		$return = 0;
	}
	else {
		$return = $_POST['parentfolder'];
	}
	return input_validation($return);
}


function set_post_browser() {
	if (empty($_POST['browser'])) {
		$return = '';
	}
	elseif ($_POST['browser'] == 'opera') {
		$return = 'opera';
	}
	elseif ($_POST['browser'] == 'netscape') {
		$return = 'netscape';
	}
	elseif ($_POST['browser'] == 'IE') {
		$return = 'IE';
	}
	else {
		$return = '';
	}
	return input_validation($return);
}


#########################################################


function return_charsets() {
	$charsets = [
		'ISO-8859-1',
		'ISO-8859-15',
		'UTF-8',
		'cp866',
		'cp1251',
		'cp1252',
		'KOI8-R',
		'BIG5',
		'GB2312',
		'BIG5-HKSCS',
		'Shift_JIS',
		'EUC-JP',
	];
	return $charsets;
}


function set_post_charset() {
	$charsets = return_charsets();

	if (empty($_POST['charset']) || $_POST['charset'] == '') {
		$return = 'UTF-8';
	}
	elseif (in_array($_POST['charset'], $charsets)) {
		$return = $_POST['charset'];
	}
	else {
		$return = 'UTF-8';
	}
	return $return;
}


function check_username($username) {
	$return = false;
	if (isset($username) || $username == '') {
		global $mysql;
		$query = sprintf("
			SELECT COUNT(*) 
			FROM `obm_users` 
			WHERE md5(`username`) = md5('%s')",
				$mysql->escape($username));
		if ($mysql->query($query)) {
			if (mysql_result($mysql->result, 0) == 1) {
				$return = true;
			}
		}
	}
	return input_validation($return);
}


function admin_only() {
	global $mysql, $username;
	$return = false;
	$query = sprintf("
    	SELECT COUNT(*) 
    	FROM `obm_users` 
    	WHERE admin='1' AND username='%s'",
			$mysql->escape($username));
    if ($mysql->query($query)) {
        if (mysql_result($mysql->result, 0) == '1') {
            $return = true;
        }
    }
    return input_validation($return);
}


function set_get_string_var($varname, $default = '') {
	if (empty($_GET[$varname]) || $_GET[$varname] == '') {
		$return = $default;
	}
	else {
		$return = $_GET[$varname];
	}
	return input_validation($return);
}


function set_post_string_var($varname, $default = '') {
	if (empty($_POST[$varname]) || $_POST[$varname] == '') {
		$return = $default;
	}
	else {
		$return = $_POST[$varname];
	}
	return input_validation($return);
}

function set_post_num_var($varname, $default = 0) {
	if (empty($_POST[$varname]) || $_POST[$varname] == '' || !is_numeric($_POST[$varname])) {
		$return = $default;
	}
	else {
		$return = intval($_POST[$varname]);
	}
	return input_validation($return);
}


function set_post_bool_var($varname, $default = true) {
	if (empty($_POST[$varname])) {
		$return = $default;
	}
	elseif (! $_POST[$varname] ) {
		$return = false;
	}
	elseif ($_POST[$varname] ) {
		$return = true;
	}
	else {
		$return = $default;
	}
	return $return;
}


function set_get_num_list($varname) {
	if (empty($_GET[$varname]) || $_GET[$varname] == '') {
		$return = [];
	}
	else {
		$return = set_num_array(explode('_', $_GET[$varname]));
	}
	return input_validation($return);
}


function set_post_num_list($varname) {
	if (empty($_POST[$varname]) || $_POST[$varname] == '') {
		$return = [];
	}
	else {
		$return = set_num_array(explode('_', $_POST[$varname]));
	}
	return input_validation($return);
}


/*
 * This function checks the values of each entry in an array.
 * It returns an array with unique and only numeric entries.
 */
function set_num_array($array){
	foreach ($array as $key => $value) {
		if ($value == '' || !is_numeric($value)) {
			unset($array[$key]);
		}
	}
	return array_unique($array);
}


function get_version() {
	return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/VERSION');
}

function check_for_new_version() {
	$remote_version = trim(file_get_contents('https://raw.githubusercontent.com/s22-tech/openbookmark/master/VERSION'));
	echo $remote_version;
	if (version_compare($remote_version, get_version(), '>')) {
		echo " -- <em>There's a <b>newer version</b> available!</em>";
	}
	else {
		echo " [ you're up-to-date ]";
	}
}


function print_footer() {
	echo '<div id="footer">';
	object_count();
	echo '<br>' . PHP_EOL;

	echo 'OBM v' . get_version();
	echo '&nbsp; PHP v'. phpversion();
	echo '&nbsp; &copy; <a class="footer" href="https://github.com/s22-tech/openbookmark">s22 Tech</a>';
	echo "</div>\n";
}


function object_count() {
	global $mysql, $username;
	$return = '';
	$query = sprintf("
		SELECT 
		(SELECT COUNT(*) FROM `obm_bookmarks` WHERE `user` = '%s') AS bookmarks,
		(SELECT COUNT(*) FROM `obm_folders`   WHERE `user` = '%s') AS folders",
			$mysql->escape($username),
			$mysql->escape($username));

	if ($mysql->query($query)) {
		if (mysqli_num_rows($mysql->result) == '1') {
			$row = mysqli_fetch_object($mysql->result);
			$return = 'You have '. number_format($row->bookmarks) .' bookmarks in '. number_format($row->folders) .' folders';
		}
	}
	else {
		$return = $mysql->error;
	}
	echo $return;
}


function assemble_query_string($data) {
	$return = [];
	foreach ($data as $key => $value) {
		array_push($return, $key.'='.$value);
	}
	return implode('&', $return);
}
	
	
function get_current_url($url) {
  // If an old URL gets redirected to a new one, returns the new URL.
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);
	
// 		echo '<pre>'; print_r($parts); echo '</pre>';
// 		$scheme = parse_url($code, PHP_URL_SCHEME);
	$host   = parse_url($code, PHP_URL_HOST);

	return $host;
}


function debug_logger($variable, $name, $file, $function, $newline=true, $time='') {
	global $cfg;
	if (is_array($variable)) {
		$variable = print_r($variable, true);
	}
	if (!empty($time)) {
		$time = (new DateTime('now', new DateTimeZone($cfg['timezone'])))->format('Y-m-d H:i:s') . ' ';
	}
	$eol = $newline ? PHP_EOL : '';
	error_log('• '. $time. basename($file) .'::'.$function.'()->$'. $name .': '. $variable . $eol . PHP_EOL, 3, $cfg['debug_log']);
}


if (!function_exists('mysql_result')) {
	function mysql_result($result, $number, $field=0) {
		mysqli_data_seek($result, $number);
		$row = mysqli_fetch_array($result);
		return $row[$field];
	}
}
