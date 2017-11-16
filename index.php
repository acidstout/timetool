<?php
/**
 *
 * Demonstration of the TimeTool\Wrapper class.
 *
 * Requires a valid account at ttcloud.ch
 *
 * Usage:
 *
 *      1) Modify the secret key in config.php before generating tokens.
 *
 *      2) Generate a token by calling
 *
 *             https://localhost/timetool/
 *
 *         Enter your credentials when prompted and click generate.
 *         A token will be generated and displayed. By default the token
 *         is valid for two months. You may want to change this in the
 *         config.php file.
 *
 *      3) Login using the generated token by calling
 *
 *             https://localhost/timetool/?token=<YOUR_TOKEN>
 *
 *         GET and POST requests are accepted.
 *
 * @author nrekow
 *
 */


////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Include configuration file if it exists.
if (file_exists('config.php')) {
	include_once 'config.php';
}

// Include required classes.
require_once 'classes/timetool/wrapper.php';
require_once 'classes/xorcrypt/class.xor.php';

use TimeTool\Wrapper;
use XORcrypt\XORcrypt;


$username = '';		// Clear username.
$password = '';		// Clear password.
$token    = '';		// Clear token.
$expired  = false;	// True if the token is expired.
$demokey  = false;	// True if the secret key is the insecure demo key.


/**
 * Check if proper secret key has been defined.
 */
if (!defined('KEY')) {
	define('KEY', 'secret');
}
if (KEY == 'secret') {
	$demokey = true;
}


/**
 * Check if lifetime of token has been defined.
 */
if (!defined('LIFETIME')) {
	define('LIFETIME', 'now +2 months');
}


// Escape inputs just for the sake of good order.
/**
 * Check for username
 */
if (isset($_REQUEST['user']) && !empty($_REQUEST['user'])) {
	$username = htmlentities($_REQUEST['user'], ENT_QUOTES);
}


/**
 * Check password
 */
if (isset($_REQUEST['pass']) && !empty($_REQUEST['pass'])) {
	$password = htmlentities($_REQUEST['pass'], ENT_QUOTES);
}


/**
 * Check token
 */
if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {
	$token = htmlentities($_REQUEST['token'], ENT_QUOTES);
	$token = new XORcrypt($token, KEY);
	$credentials = $token->decrypt();
	
	// Check if decryption returned valid values. 
	if (isset($credentials['user']) && isset($credentials['pass']) && isset($credentials['expires'])) {
		$username = $credentials['user'];
		$password = $credentials['pass'];
		$expires  = $credentials['expires'];
		$time = new DateTime('now');
		$timestamp = $time->format('YmdHis');
		$timestamp = strtotime($timestamp);
		
		// Check if timestamp is valid.
		if ($expires < $timestamp) {
			$expired = true;
			askForCredentials($expired, $demokey);
			die();
		}
	}
} else if (!empty($username) && !empty($password)) {
	/**
	 * Generate new token.
	 */
	$time = new DateTime(LIFETIME);
	$timestamp = $time->format('YmdHis');
	$token = new XORcrypt(array('user' => $username, 'pass' => $password, 'expires' => strtotime($timestamp)), KEY);
	$token = $token->encrypt();
	//echo 'Ihr Token lautet <input id="token" type="text" readonly value="' . $token . '" onclick="this.select();"/><br/>';
	echo '<button type="button" onclick="copyTextToClipboard(\'' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?token=' . $token . '\');">Link mit generierten Token in die Zwischenablage kopieren</button>';
	die();
}


/**
 * Ask for credentials if at least one setting is empty and insert the one that's filled into the form.
 */
if (empty($username) || empty($password)) {
	askForCredentials($expired, $demokey);
	die();
}



/**
 * Create new TimeTool\Wrapper object. Will try to log you in with the supplied credentials.
 */
$ttw = new Wrapper(html_entity_decode($username, ENT_QUOTES), html_entity_decode($password, ENT_QUOTES));

if ($ttw) {
	$result = $ttw->getResult();	// Contains a human readable representation of the returned error code.
	echo $result['error'];
	
	// Check result of login
	if (isset($result['success']) && $result['success']) {
		// Post current timestamp to the TimeTool API server.
		// Arrive and leave action is set automatically by the application on the server.
		$time = $ttw->doTimestamp();
		$result = $ttw->getResult();
		
		// Check result. Will be empty on success.
		if (count($result) == 0) {
			echo ' Zeitstempel (' . $time . ') gesetzt.';
		} else {
			echo pre_r($result);
			echo ' Verwendeter Zeitstempel: ' . $time;
		}
	}
}

// Always die after main routine.
die();


/**
 * Pretty print an array
 *
 * @param mixed $mixed
 * @return string
 */
function pre_r($mixed) {
	return '<pre>' . print_r($mixed, true) . '</pre>';
}


/**
 * Load HTML template
 *
 * @param boolean $expired
 * @param boolean $demokey
 */
function askForCredentials($expired, $demokey) {?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>TimeTool Wrapper</title>
		<link rel="stylesheet" href="css/style.css"/>
		<script src="js/jquery.min.js" defer></script>
		<script src="js/ajax.js" defer></script>
	</head>
	<body>
		<div class="fixed-center">
			<h2>Token generieren</h2>
			<div id="login">
				<form action="#" method="post" autocomplete="off">
					<input type="password" style="display:none;" name="disable-autocomplete-1" value="disable-autocomplete-1"/>
					<input type="password" style="display:none;" name="disable-autocomplete-2" value="disable-autocomplete-2"/>
					<input type="text" id="user" required value="" autocomplete="off" placeholder="Benutzername"/>
					&nbsp;<input type="password" id="pass" required value="" autocomplete="off" placeholder="Passwort"/>
					&nbsp;<button type="button" id="submit">Generieren</button>
				</form>
				<div id="result"><?php
					echo (isset($demokey) && $demokey) ? 'Bitte geheimen Schl&uuml;ssel &auml;ndern! ' : null;
					echo (isset($expired) && $expired) ? 'Ihr Token ist abgelaufen.' : null;
				?></div>
			</div>
		</div>
	</body>
</html><?php
}
