<?php
namespace common\vendor;

define('SECAUTH_CLIENT_VERSION', '0.1-alpha');

class SecAuthClient extends \yii\base\Object {

	const SAC = 'SecAuthClient';

	const SECONDS_BETWEEN_VALIDATION = 60;

	private $_service_url = null;

	private $_server = array(
		'hostname' => null,
		'port' => -1,
		'uri' => null);

	/**
	 * Constructor
	 *
	 * @param string $server_hostname	the server hostname
	 * @param string $server_port			the server port
	 * @param string $server_uri			the server base uri
	 */
	public function init_client($server_hostname, $server_port = 443, $server_uri = '') {
		if(session_status() == PHP_SESSION_NONE)
			session_start();

		$this->_server['hostname'] =  $server_hostname;
		$this->_server['port'] = $server_port;
		$this->_server['uri'] = preg_replace('/\/\//', '/', '/'.$server_uri.'/');

// 		if(!$this->_isSecureConnection())
// 			throw new Exception('You are not allowed to use secauth on an unencrypted channel. Please use https!');

		if(!isset($_SESSION[self::SAC]))
			$_SESSION[self::SAC] = array();

		if($this->_isCallback()) {
			$this->_setTicket($_GET['ticket']);
			$this->_validateTicket(true);

			$shortenedUrl = $this->_removeParamFromURL($_SERVER['REQUEST_URI'], 'ticket');
			$this->_redirect($shortenedUrl);
		}
	}

	/**
	 * Returns true if the connection is secure (https), otherwise false.
	 */
	protected function _isSecureConnection() {
		if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
      	return ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
      
		return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
	}

	/**
	 * Returns true if the client waits for a ticket from the secauth server.
	 */
	private function _isCallback() {
		return isset($_GET['ticket']);
	}

	/**
	 * Removes a given $param from an URL.
	 * 
	 * @param string $uri 				the uri 
	 * @param string $killparam 		the parameter that should get dropped from the url
	 */
	private function _removeParamFromURL($uri, $killparam) {
   	$uri_array = parse_url($uri);
   	if (isset($uri_array['query'])) {
      	// Do the chopping.
       	$params = array();
       	foreach (explode('&', $uri_array['query']) as $param) {
          	$item = explode('=', $param);
          	if (!($item[0] == $killparam)) {
            	$params[$item[0]] = isset($item[1]) ? $item[1] : '';
          	}
        	}
        	// Sort the parameter array to maximize cache hits.
        	ksort($params);
        	// Build new URL (no hosts, domains, or fragments involved).
        	$new_uri = '';
        	if ($uri_array['path']) {
          	$new_uri = $uri_array['path'];
        	}
        	if (count($params) > 0) {
          	// Wish there was a more elegant option.
          	$new_uri .= '?' . urldecode(http_build_query($params));
        	}
       	return $new_uri;
    	}
    	return $uri;
	}

	private function _validateTicket($force = false) {
		$ticket = $this->getTicket();

		if(empty($ticket)) // there is no ticket, we don't need to mess the server
			return;

		if($force || // force revalidation
			!isset($_SESSION[self::SAC]['LAST_VALIDATION_TIME']) || // ticket has never been validated
			(round(microtime(true) * 1000) - $_SESSION[self::SAC]['LAST_VALIDATION_TIME'] > // last validation older than x seconds
				(self::SECONDS_BETWEEN_VALIDATION * 1000))) {
			
			// update the last validation time
			$_SESSION[self::SAC]['LAST_VALIDATION_TIME'] = round(microtime(true) * 1000);

			$res = $this->_readAndEvaluate($this->getServerValidationURL().'?ticket='.$ticket);

			switch($res[0]) {
			case 200:
				$resArr = json_decode($res[1], true);
			
				if($resArr['valid'] == true) {
					$_SESSION[self::SAC]['USER'] = $resArr['user_id'];
					break;
				}
			default:
				$_SESSION[self::SAC] = array();
				break;
			}
		}
	}

	private function _readAndEvaluate($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, self::SAC.'/'.$this->getVersion());
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_CAINFO, 'cacert.pem');

		$content = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);

		return array($httpcode, $content);
	}

	private function _getClientURL() {
		$server_url = '';
      if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
         $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
         $server_url = $hosts[0];
      } else if (!empty($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
      	$server_url = $_SERVER['HTTP_X_FORWARDED_SERVER'];
      } else {
         if (empty($_SERVER['SERVER_NAME'])) {
      	   $server_url = $_SERVER['HTTP_HOST'];
         } else {
            $server_url = $_SERVER['SERVER_NAME'];
         }
      }
      if (!strpos($server_url, ':')) {
         if (empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
            $server_port = $_SERVER['SERVER_PORT'];
         } else {
            $ports = explode(',', $_SERVER['HTTP_X_FORWARDED_PORT']);
            $server_port = $ports[0];
         }

         if ($server_port!=443)
            $server_url .= ':' . $server_port;
	   }

   	return $server_url;
	}

	public function setServiceURL($url) {
		$this->_service_url = $url;
	}

	public function getServiceURL() {
		if(empty($this->_service_url)) {
			$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

			$url = 'https://' . $this->_getClientURL();
			$url .= $request_uri[0];
		
			if (isset($request_uri[1]) && $request_uri[1]) {
				$query_string = $request_uri[1];
				$query_string =  preg_replace("/&ticket(=[^&]*)?|^ticket(=[^&]*)?&?/", '', $query_string);
				
				$url .= $query_string;

				$this->_service_url = $url;
			}

			return $url;
		}
		
		return $this->_service_url;
	}

	protected function _getServerHostname() {
		return $this->_server['hostname'];
	}

	protected function _getServerPort() {
		return $this->_server['port'];
	}

	protected function _getServerUri() {
		return $this->_server['uri'];
	}

	public function getServerBaseURL() {
		if(empty($this->_server['base_url'])) {
			$url = 'https://' . $this->_getServerHostname();
			if($this->_getServerPort() != 443) {
				$url .= ':' . $this->_getServerPort();
			}
			$url .= $this->_getServerUri();
		
			$this->_server['base_url']	= $url;
		}
		return $this->_server['base_url'];
	}

	public function getServerLoginURL() {
		return $this->_buildAndStoreURL('login_url', 'user/signin');
	}

	public function getServerValidationURL() {
		return $this->_buildAndStoreURL('validation_url', 'service/validate', false);
	}

	public function getServerInvalidationURL() {
		return $this->_buildAndStoreURL('invalidation_url', 'service/invalidate', false);
	}

	public function getServerAttributesURL() {
		return $this->_buildAndStoreURL('attributes_url', 'service/attributes', false);
	}

	public function getServerLogoutURL() {
		return $this->_buildAndStoreURL('logout_url', 'user/signout');
	}

	private function _buildAndStoreURL($name, $path, $appendCallbackURL = true) {
		if(empty($this->_server[$name])) {
			$url = $this->getServerBaseURL();
			$url .= $path;

			if($appendCallbackURL)
				$url .= '?service='.urlencode($this->getServiceURL());

			$this->_server[$name] = $url;
		}

		return $this->_server[$name];
	}

	private function _redirect($url) {
		if(empty($url))
			return;

		session_write_close();
		header('Location: '.$url);
		exit();
	}

	public function authenticate() {
		if($this->isAuthenticated())
			return;

		$saLoginURL = $this->getServerLoginURL();
		$this->_redirect($saLoginURL);
	}

	public function logout($network = true) {
		$_SESSION[self::SAC] = array();

		if($network) {
			$this->_redirect($this->getServerLogoutURL());
		} else {
			$this->_redirect($this->getServiceURL());
		}
	}

	/**
	 * Revalidates the stored service ticket and the user authentication state.
	 *
	 * @return true iff authenticated
	 */
	public function isAuthenticated($revalidate = true) {
		if($revalidate)
			$this->_validateTicket(false);

		return $this->_hasTicket();
	}

	/**
	 * Returns either the username of the logged in user or null if no one is authenticated.
	 *
	 * @return the name of the logged in user
	 */
	public function getUsername() {
		return isset($_SESSION[self::SAC]['USER']) ? $_SESSION[self::SAC]['USER'] : null;
	}

	public function getAttributes($forceReload = false) {
		if(isset($_SESSION[self::SAC]['USER_DATA']) && $forceReload == false)
			return $_SESSION[self::SAC]['USER_DATA'];

		if(!$this->isAuthenticated(false))
			return array();

		$attributes = array();
		$res = $this->_readAndEvaluate($this->getServerAttributesURL().'?ticket='.$this->getTicket());

		switch($res[0]) {
		case 200:
			$attributes = json_decode($res, true);
			
			$_SESSION[self::SAC]['USER_DATA'] = $attributes;
			break;
		default:
			return array();
		}
		
		return $attributes;	
	}

	public function _hasTicket() {
		$getTicket = $this->getTicket();
		return !empty($getTicket);
	}

	/**
	 * Returns either the stored Service Ticket or null if it doesn't exist.
	 */
	public function getTicket() {
		return isset($_SESSION[self::SAC]['SERVICE_TICKET']) ? $_SESSION[self::SAC]['SERVICE_TICKET'] : null;
	}

	/**
	 * Stores the given $ticket in the users session.
	 * @param string $ticket	the service ticket
	 */
	public function _setTicket($ticket) {
		if(empty($ticket)) {
			unset($_SESSION[self::SAC]['SERVICE_TICKET']);
			return;
		}

		$_SESSION[self::SAC]['SERVICE_TICKET'] = $ticket;
	}

	/**
	 * Returns the version of the SecAuthClient.
	 */
	public function getVersion() {
		return SECAUTH_CLIENT_VERSION;
	}
}
