<?php
/**
 * Simple wrapper class around the login and time logging process of TimeTool.
 * 
 * Usage:
 * 
 * 		use TimeTool\Wrapper;
 * 		$ttw = new Wrapper($username, $password);
 * 
 * 		if ($ttw) {
 *			echo '<pre>' . print_r($ttw->getResult(), true) . '</pre>';
 * 			$ttw->doTimestamp();
 *			echo '<pre>' . print_r($ttw->getResult(), true) . '</pre>';
 * 		}
 * 
 * 
 * @author nrekow
 *
 */

namespace TimeTool;

class Wrapper {
	// Default tolerance settings. A random value between min/max tolerance will be substracted from the time prior posting it to the server.
	private $useTolerance = true;
	private $minTolerance = 2;
	private $maxTolerance = 2;
	
	// Will contain the result of the request.
	private $result = array();
	
	// Your TimeTool credentials. Leave this empty here and supply your credentials when creating a new TimeToolWrapper object.
	private $username = '';
	private $password = '';
	
	// Action which is send in the request to the server.
	private $action = '';
	
	// The URL to the script on the TimeTool server.
	private $tt_url = 'https://www.ttcloud.ch/cgi-bin/dhtml_appl_admin.cgi';


	/**
	 * Constructor
	 * 
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function __construct($username, $password) {
		if (!empty($username) && !empty($password)) {
			$this->username = $username;
			$this->password = $password;
			$this->doLogin();
		}
		
		if (!defined('TOLERANCE')) {
			define('TOLERANCE', $this->useTolerance);
		}
		
		if (!defined('MINTOLERANCE')) {
			define('MINTOLERANCE', $this->minTolerance);
		}

		if (!defined('MAXTOLERANCE')) {
			define('MAXTOLERANCE', $this->maxTolerance);
		}
		
		if (!isset($result['success']) || !$result['success']) {
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Simple function to pretty print the server's response.
	 */
	public function getResult() {
		return $this->result;
	}
	
	
	/**
	 * Tries to log the user in to the TimeTool application using the previously defined credentials.
	 * 
	 * $this->result will contain the server's response (e.g. JSON).
	 */
	public function doLogin() {
		// Action as expected by the TimeTool API.
		$this->action = 'login';
		
		// Prepare parameters which will be posted to the TimeTool API.
		$params = array(
				'cmd' => 'login',
				'login' => $this->username,
				'passwd' => $this->password
		);
		
		// Do the actual POST.
		$this->result = json_decode($this->_doCurlRequest($params), true);
	}
	
	
	/**
	 * Tries to set a calculated timestamp by sending a cURL request to the defined server.
	 * 
	 * $this->result will contain the server's response (e.g. JSON).
	 */
	public function doTimestamp() {
		// Set default timezone.
		date_default_timezone_set('Europe/Berlin');
		
		// Get current time.
		$time = date('H:i');
		
		// Action as expected by the TimeTool API.
		$this->action = 'addregi';
		
		// Check for custom tolerance settings.
		if (TOLERANCE === true) {
			if (is_numeric(minTolerance)) {
				$this->minTolerance = MINTOLERANCE;
			}
			if (is_numeric(maxTolerance)) {
				$this->maxTolerance = MAXTOLERANCE;
			}
			
			// Only use tolerance if it's greater than zero.
			if (!empty($this->minTolerance) && !empty($this->maxTolerance)) {
				$time = date('H:i', strtotime('-' . rand($this->minTolerance, $this->maxTolerance) . ' minutes'));
			}
		}		

		// Prepare parameters which will be posted to the TimeTool API.
		$params = array(
				'cmd' => $this->action,
				'badnum' => $this->result['badnum'],
				'login' => $this->result['login'],
				'tc_chef' => $this->result['login'],
				'ucat' => 0,
				'klkunit' => 'absent',
				'session' => $this->result['session'],
				'tc_session' => $this->result['session'],
				'date' => date('Ymd'),
				'time' => $time
		);
		
		// Do the actual POST.
		$this->result = json_decode($this->_doCurlRequest($params), true);
		
		// Return the set timestamp to the user.
		return $time;
	}
	
	
	/**
	 * Prepares and executes the actual cURL request using the supplied parameters. 
	 * 
	 * @param array $params
	 * @return mixed
	 */
	private function _doCurlRequest($params) {
		// These are our default options for our cURL request. 
		$defaults = array(
				CURLOPT_URL => $this->tt_url,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($params),
				CURLOPT_HEADER => false,
				CURLOPT_FRESH_CONNECT => true,
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_SSL_VERIFYPEER => false, // Not good, but required if your server does not support SSL.
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_VERBOSE => true // Debug: true, otherwise set false.
		);
		
		// Init our cURL session.
		$ch = curl_init();

		// Set cURL options as defined above.
		curl_setopt_array($ch, $defaults);
		
		// Do the cURL request ...
		if (!$result = curl_exec($ch)) {
			// ... and display its result.
			echo curl_error($ch);
		}
		
		// Close our cURL session
		curl_close($ch);
		
		return $result;
	}// END: curl_request()
}