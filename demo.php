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
 * @author nrekow
 * 
 */

require_once 'timetoolwrapper.php';

// Default credentials. Put your TimeTool credentials in here.
$username = '<YOUR_USERNAME>';
$password = '<YOUR_PASSWORD>';

// Default tolerance in minutes. Overwrites preset in class.
$minTolerance = 4;
$maxTolerance = 6;


// No need to escape this here, because the external application takes care of that.
if (isset($_REQUEST['user']) && !empty($_REQUEST['user'])) {
	$username = $_REQUEST['user'];
}
if (isset($_REQUEST['pass']) && !empty($_REQUEST['pass'])) {
	$password = $_REQUEST['pass'];
}


// Create and initialize new TimeTool object. Will try to log you in with the supplied credentials.
$ttw = new TimeToolWrapper($username, $password);

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
	
	$result = $ttw->getResult();
	
	// Contains a human readable representation of the returned error code.
	echo $result['error'];
	
	// Check result of initialization
	if (isset($result['success']) && $result['success']) {
		// Set new timestamp. Arrive and leave action is set automatically by the application on the server.
		$ttw->doTimestamp();
		$result = $ttw->getResult();
		
		// Check result. Will be empty on success.
		if (count($result) == 0) {
			echo '<br/>Ok.';
		} else {
			pre_r($result);
		}
	}
}


/**
 * Pretty print an array
 * 
 * @param mixed $mixed
 */
function pre_r($mixed) {
	echo '<pre>' . print_r($mixed, true) . '</pre>';
}