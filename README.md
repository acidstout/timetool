# TimeTool Wrapper Class
Simple wrapper class around the login and time logging process of the TimeTool application (www.timetool.ch).

Authenticate against the TimeTool server (www.ttcloud.ch) and use the current timestamp as arriving or leaving time by one single click of a button.

Requires a valid TimeTool account.

As the TimeTool relies on the clients clock, I noticed some issue while trying to use the current time. If both clocks (the client's one and the one of the server) are not 100% synchronized, you will get an error message as reply from the server, stating that you cannot arrive/leave with a future timestamp. So, I set up a tolerance range, which picks a random number und substracts it from the minutes of the current time. This will result in an timestamp which is always valid.

The tolerance range should be very small in order to still have proper results. I guess, no one will care if you log in 2-3 minutes earlier than you actually arrive, because the same applies to when you leave (e.g. then 2-3 minutes will be substracted from the current time).

But if you set a large tolerance range and/or modify the code to *add* time when you leave, you'll likely get into trouble with your boss. ;) Just keep that in mind.

Usage:
```
	$ttw = new TimeToolWrapper($username, $password);
	if ($ttw) {
		pre_r($ttw->getResult());
		$ttw->doTimestamp();
	}
```