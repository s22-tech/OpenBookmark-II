<?php

	if (ini_get('register_globals')) {
		if (isset($_REQUEST['GLOBALS'])) {
			die('<a href="https://www.hardened-php.net/index.76.html">$GLOBALS overwrite vulnerability</a>');
		}

		$prohibited = [
			'GLOBALS',
			'_SERVER',
			'HTTP_SERVER_VARS',
			'_GET',
			'HTTP_GET_VARS',
			'_POST',
			'HTTP_POST_VARS',
			'_COOKIE',
			'HTTP_COOKIE_VARS',
			'_FILES',
			'HTTP_POST_FILES',
			'_ENV',
			'HTTP_ENV_VARS',
			'_REQUEST',
			'_SESSION',
			'HTTP_SESSION_VARS'
		];

		foreach ($_REQUEST as $name => $value) {
			if (in_array($name, $prohibited)) {
				header('HTTP/1.x 500 Internal Server Error');
				echo 'register_globals security paranoia: trying to overwrite superglobals... aborting.';
				die( -1 );
			}
			unset( $GLOBALS[$name] );
		}
	}


