# TimeTool Wrapper Class
Simple wrapper class around the login and time logging process of the TimeTool application (www.timetool.ch).

Authenticate against the TimeTool server (www.ttcloud.ch) and use the current timestamp as arriving or leaving time by one single click of a button.

Requires a valid TimeTool account.

As TimeTool relies on the client's clock, I noticed some issue while trying to use the current time. If both clocks (the client's one and the one of the server) are not 100% synchronized, you'll get an error message as reply from the server, stating that you cannot arrive/leave with a future timestamp. So, I set up a tolerance range, which picks a random number und substracts it from the minutes of the current time. This will result in a timestamp which is always valid.

The tolerance range should be very small in order to still have proper results. I guess, no one will care if you log in 2-3 minutes earlier than you actually arrive, because the same applies when you leave (e.g. 2-3 minutes will be substracted from the current time). By default the tolerance range is enabled. If you don't want to use it, you may disable it in the config.php file. 

If you set a large tolerance range, you'll likely get into trouble with your boss. ;) Just keep that in mind.

## Usage

Set your defaults in config.php file and then use the following:

```
	use TimeTool\Wrapper;
	$ttw = new Wrapper($username, $password);
	if ($ttw) {
		$time = date('H:i');
		echo '<pre>' . print_r($ttw->getResult(), true) . '</pre>';
		$ttw->doTimestamp($time);
		echo '<pre>' . print_r($ttw->getResult(), true) . '</pre>';
	}
```