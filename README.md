# TimeTool Wrapper Class
Simple wrapper class around the login and time logging process of the TimeTool application.

Authenticate against the TimeTool server and use the current timestamp as arriving or leaving time by one single click of a button. 

Usage:
```
	$ttw = new TimeToolWrapper($username, $password);
	if ($ttw) {
		$ttw->printResult();
		$ttw->doTimestamp();
	}
```