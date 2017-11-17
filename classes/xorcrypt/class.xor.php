<?php
namespace XORcrypt;

/**
 * Simple XOR cryptor class.
 * 
 * NOTICE: Encrypting strings using XOR method is not secure, but at least it's much more secure than using plain text.
 *         Using something like AES or Blowfish is recommended.
 *
 * @author nrekow
 *        
 */
class XORcrypt {

	private $_data  = '';
	private $_key   = '';
	
	/**
	 * Constructor
	 */
	public function __construct($data, $key = '') {
		$this->_data = $data;
		
		if (!empty($key)) {
			$this->_key = $key;
		}
	}

	
	/**
	 * Setter method
	 * 
	 * @param array|string $data
	 */
	public function setData($data) {
		$this->_data = $data;
	}
	
	
	/**
	 * Getter method
	 * 
	 * @return string|array[]|string[]|boolean
	 */
	public function getData() {
		return $this->_data;
	}
	
	
	/**
	 * XOR decrypt a string into an array
	 *
	 * @param string $token
	 * @return boolean|array
	 */
	public function decrypt() {
		if (empty($this->_data)) {
			return false;
		}
		
		// We use rawurldecode which does not decode "+" signs as spaces.
		$this->_data = rawurldecode($this->_data);
		$this->_data = base64_decode($this->_data);
		$this->_data = $this->_xor($this->_data);
		$this->_data = json_decode($this->_data, true);

		return $this->_data;
	}
	
	
	/**
	 * XOR encrypt an array into a string
	 *
	 * @param array $data
	 * @return boolean|string
	 */
	public function encrypt() {
		if (empty($this->_data)) {
			return false;
		}
		
		// Make sure to have a really clean JSON instead of escaped crap.
		$this->_data = json_encode($this->_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		$this->_data = $this->_xor($this->_data);
		$this->_data = base64_encode($this->_data);
		
		// We use rawurlencode which does not encode spaces as "+" signs.
		$this->_data = rawurlencode($this->_data);
		
		return $this->_data;
	}
	
	
	/**
	 * XOR encrypt/decrypt strings
	 *
	 * @param string $string
	 * @return string
	 */
	private function _xor($str) {
		if (empty($this->_key)) {
			return false;
		}
		
		$input  = $str;
		$output = '';
		
		// Iterate through each character
		for ($i = 0; $i < strlen($input);) {
			for ($j = 0; ($j < strlen($this->_key) && $i < strlen($input)); $j++, $i++) {
				$output .= $input{$i} ^ $this->_key{$j};
				//error_log('i=' . $i . ', ' . 'j=' . $j . ', ' . $output{$i}, 0);
			}
		}
		
		return $output;
	}
}