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

// Credentials
$username = '<YOUR_USERNAME>';
$password = '<YOUR_PASSWORD>';

// Default request
$ttw = new TimeToolWrapper($username, $password);


// Optionally include tolerance in minutes.
/*
$minTolerance = 4;
$maxTolerance = 6;

$ttw = new TimeToolWrapper($username, $password, $minTolerance, $maxTolerance);
*/

if ($ttw) {
	// Print result (e.g. will likely be an array)
	$ttw->printResult();
	
	// Set new timestamp. Result will be empty upon success.
	$ttw->doTimestamp();
}