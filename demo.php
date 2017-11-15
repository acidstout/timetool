<?php
/**
 * 
 * Demo of the TimeToolWrapper class.
 * 
 * Requires a valid account at ttcloud.ch
 * 
 * Usage:
 * 
 * 		1) Set secret key in config.php before trying to generate tokens.
 * 
 * 		2) Generate a token (which is valid for two months) by calling
 * 		
 * 			https://localhost/timetool/
 * 
 * 		   Enter your credentials when prompted and click generate. A token will be generated and displayed.
 * 
 * 		3) Login using the generated token by calling
 *
 * 			https://localhost/timetool/demo.php?token=<YOUR_TOKEN>
 * 
 * 		4) Optionally append min/max parameters to define a custom tolerance setting (if enabled):
 *  
 * 			https://localhost/timetool/demo.php?token=<YOUR_TOKEN>&min=<MIN_TOLERANCE>&max=<MAX_TOLERANCE>
 * 
 * GET and POST requests are accepted. Alternatively you may define your credentials and settings directly in the configuration file.
 * 
 * @author nrekow
 * 
 */


////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Include configuration file
if (file_exists('config.php')) {
	include_once 'config.php';
}

// Include required TimeToolWrapper class.
require_once 'classes/timetool/wrapper.php';
require_once 'classes/xorcrypt/class.xor.php';

use TimeTool\Wrapper;
use XORcrypt\XORcrypt;


// Escape inputs just for the sake of good order.
// Check for username ...
if (isset($_REQUEST['user']) && !empty($_REQUEST['user'])) {
	$username = htmlentities($_REQUEST['user'], ENT_QUOTES);
} else if (!isset($username)) {
	// ... and default to empty username.
	$username = '';
}


// Check password ...
if (isset($_REQUEST['pass']) && !empty($_REQUEST['pass'])) {
	$password = htmlentities($_REQUEST['pass'], ENT_QUOTES);
} else if (!isset($password)) {
	// ... and default to empty password.
	$password = '';
}


if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {
	$token = htmlentities($_REQUEST['token'], ENT_QUOTES);
	$token = new XORcrypt($token, KEY);
	$credentials = $token->decrypt();
	
	if (isset($credentials['user']) && isset($credentials['pass']) && isset($credentials['expires'])) {
		$username = $credentials['user'];
		$password = $credentials['pass'];
		$expires  = $credentials['expires'];
		$time = new DateTime('now');
		$timestamp = $time->format('YmdHis');
		$timestamp = strtotime($timestamp);
		
		if ($expires < $timestamp) {
			askForCredentials($username, $password, true);
			die();
		}
	}
} else if (!empty($username) && !empty($password)) {
	$time = new DateTime('now +2 months');
	$timestamp = $time->format('YmdHis');
	$token = new XORcrypt(array('user' => $username, 'pass' => $password, 'expires' => strtotime($timestamp)), KEY);
	$token = $token->encrypt();
	echo 'Ihr Token lautet <input id="token" type="text" readonly value="' . $token . '" onclick="this.select();"/>';
	die();
} else {
	$token = '';
}


// Ask for credentials if at least one setting is empty and insert the one that's filled into the form. 
if (empty($username) || empty($password)) {
	askForCredentials($username, $password);
	die();
}



// Create and initialize new TimeTool object. Will try to log you in with the supplied credentials.
$ttw = new Wrapper(html_entity_decode($username, ENT_QUOTES), html_entity_decode($password, ENT_QUOTES));

if ($ttw) {
	// Check for custom tolerance settings. $_REQUEST overwrites preset in class and in this file.
	if (isset($_REQUEST['min']) && is_numeric($_REQUEST['min']) && ($_REQUEST['min'] <= $ttw->maxTolerance || (isset($_REQUEST['max']) && is_numeric($_REQUEST['max']) && $_REQUEST['min'] <= $_REQUEST['max']))) {
		$ttw->minTolerance = $_REQUEST['min'];
	} else if (defined(minTolerance) && is_numeric(minTolerance)) {
		$ttw->minTolerance = minTolerance;
	}
	if (isset($_REQUEST['max']) && is_numeric($_REQUEST['max']) && ($_REQUEST['max'] >= $ttw->minTolerance || (isset($_REQUEST['min']) && is_numeric($_REQUEST['min']) && $_REQUEST['max'] >= $_REQUEST['min']))) {
		$ttw->maxTolerance = $_REQUEST['max'];
	} else if (defined(maxTolerance) && is_numeric(maxTolerance)) {
		$ttw->maxTolerance = maxTolerance;
	}
	
	// Contains a human readable representation of the returned error code.
	$result = $ttw->getResult();
	echo $result['error'];
	
	// Check result of initialization
	if (isset($result['success']) && $result['success']) {
		// Set new timestamp. Arrive and leave action is set automatically by the application on the server.
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
 * @param string $username
 * @param string $password 
 */
function askForCredentials($username, $password, $expired = false) {
	require_once 'index.php';
}
