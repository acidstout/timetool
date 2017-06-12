<?php
/**
 * Simple wrapper class around the login and time logging process of TimeTool.
 * 
 * Usage:
 * 
 * 		$ttw = new TimeTool\Wrapper($username, $password);
 * 
 * 		if ($ttw) {
 *			pre_r($ttw->getResult());
 * 			$ttw->doTimestamp();
 * 		}
 * 
 * 
 * @author nrekow
 *
 */

namespace TimeTool;

class Wrapper {

	// Default range of tolerance in minutes. A random value will be substracted from the time prior posting it to the server.
	public $minTolerance = 3;
	public $maxTolerance = 5;
	
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
		$this->action = 'login';
		
		$params = array(
				'cmd' => 'login',
				'login' => $this->username,
				'passwd' => $this->password
		);
		
		$this->result = json_decode($this->_doCurlRequest($params), true);
	}
	
	
	/**
	 * Tries to set a calculated timestamp by sending a cURL request to the defined server.
	 * 
	 * $this->result will contain the server's response (e.g. JSON).
	 */
	public function doTimestamp() {
		$this->action = 'addregi';
		
		date_default_timezone_set('Europe/Berlin');
		$time = date('H:i', strtotime('-' . rand($this->minTolerance, $this->maxTolerance) . ' minutes'));
		
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
		
		$this->result = json_decode($this->_doCurlRequest($params), true);
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