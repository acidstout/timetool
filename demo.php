<?php
/**
 * 
 * Demo of the TimeToolWrapper class.
 * 
 * Requires a valid account at ttcloud.ch
 * 
 * @author nrekow
 * 
 */
require_once 'timetoolwrapper.php';

// Put your TimeTool credentials in here.
$username = '<YOUR_USERNAME>';
$password = '<YOUR_PASSWORD>';

// Default request. This will automatically try to log you in.
$ttw = new TimeToolWrapper($username, $password);


// Optionally include tolerance in minutes.
/*
$minTolerance = 4;
$maxTolerance = 6;

$ttw = new TimeToolWrapper($username, $password, $minTolerance, $maxTolerance);
*/

if ($ttw) {
	// Print result of your last request (e.g. the login process).
	pre_r($ttw->getResult());
	
	// Set new timestamp.
	$ttw->doTimestamp();
	
	// Result will be empty upon success when setting a timestamp.
	pre_r($ttw->getResult());
}

function pre_r($mixed) {
	echo '<pre>' . print_r($mixed, true) . '</pre>';
}