<?php
/**
 * 
 * Demo of the TimeToolWrapper class.
 * 
 * Requires a valid account at ttcloud.ch
 * 
 * Usage:
 * 
 * 		https://localhost/timetool/demo.php?user=<YOUR_USERNAME>&pass=<YOUR_PASSWORD>&min=<MIN_TOLERANCE>&max=<MAX_TOLERANCE>
 * 
 * GET and POST requests are accepted. Alternatively you may define your credentials and settings directly in this script.
 * 
 * Using GET requests to supply your credentials is a bad idea if you are using a public or shared computer.
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

// Escape inputs just for the sake of good order.
// Check for username ...
if (isset($_REQUEST['user']) && !empty($_REQUEST['user'])) {
	$username = htmlentities($_REQUEST['user'], ENT_QUOTES);
} else  if (!isset($username)) {
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

// Ask for credentials if at least one setting is empty and insert the one that's filled into the form. 
if (empty($username) || empty($password)) {
	askForCredentials($username, $password);
	die();
}

// Create and initialize new TimeTool object. Will try to log you in with the supplied credentials.
$ttw = new TimeTool\Wrapper(html_entity_decode($username, ENT_QUOTES), html_entity_decode($password, ENT_QUOTES));

if ($ttw) {
	// Check for custom tolerance settings. $_REQUEST overwrites preset in class and in this file.
	if (isset($_REQUEST['min']) && is_numeric($_REQUEST['min']) && ($_REQUEST['min'] <= $ttw->maxTolerance || (isset($_REQUEST['max']) && is_numeric($_REQUEST['max']) && $_REQUEST['min'] <= $_REQUEST['max']))) {
		$ttw->minTolerance = $_REQUEST['min'];
	} else if (isset($minTolerance) && is_numeric($minTolerance)) {
		$ttw->minTolerance = $minTolerance;
	}
	if (isset($_REQUEST['max']) && is_numeric($_REQUEST['max']) && ($_REQUEST['max'] >= $ttw->minTolerance || (isset($_REQUEST['min']) && is_numeric($_REQUEST['min']) && $_REQUEST['max'] >= $_REQUEST['min']))) {
		$ttw->maxTolerance = $_REQUEST['max'];
	} else if (isset($maxTolerance) && is_numeric($maxTolerance)) {
		$ttw->maxTolerance = $maxTolerance;
	}
	
	// Contains a human readable representation of the returned error code.
	$result = $ttw->getResult();
	echo $result['error'];
	
	// Check result of initialization
	if (isset($result['success']) && $result['success']) {
		// Set new timestamp. Arrive and leave action is set automatically by the application on the server.
		$ttw->doTimestamp();
		$result = $ttw->getResult();
		
		// Check result. Will be empty on success.
		if (count($result) == 0) {
			echo 'Ok.';
		} else {
			pre_r($result);
		}
	}
}

// Always die after main routine.
die();


/**
 * Pretty print an array
 * 
 * @param mixed $mixed
 */
function pre_r($mixed) {
	echo '<pre>' . print_r($mixed, true) . '</pre>';
}


/**
 * Load HTML template 
 */
function askForCredentials($username, $password) {
	require_once 'template.php';
}