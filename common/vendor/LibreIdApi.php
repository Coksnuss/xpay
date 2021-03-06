<?php
namespace common\vendor;
//####################################################################################################################
//############                                                                                           #############
//############                                     LibreID PHP API r1                                    #############
//############                                                                                           #############
//####################################################################################################################
class LibreIdApi extends \yii\base\Object
{
    // Hier die Zugansdaten eintragen
    public $apiKey;
    public $secretKey;

    //####################################################################################################################
    //############               Die folgenden Funktionen sind zur einfachen Verwendung der API              #############
    //############           Die Funktionen und die Parameter sind auf der API Webseite beschrieben          #############
    //####################################################################################################################
    public function get_login_message($return_url) {
    	$timestamp = sprintf("%.2f", microtime(true));
        $message = json_encode(array("api_key" => $this->apiKey, "timestamp" => $timestamp, "return_url" => $return_url), JSON_UNESCAPED_SLASHES);
        return $this->encrypt_and_mac($this->secretKey, $message);
    }


    public function getdata($ticket, $requested_data) {
    	$timestamp = sprintf("%.2f", microtime(true));
    	$params = json_encode(array("ticket" => $ticket, "timestamp" => $timestamp, "requested_data" => $requested_data), JSON_UNESCAPED_SLASHES);
    	$message = $this->encrypt_and_mac($this->secretKey, $params);
    	$data = array('message' => $message);
    	$call_url = '/getdata/'.$this->apiKey.'/';
    	return json_decode($this->_process_response($this->_post_request($call_url, $data), $this->secretKey),true);
    }


    public function getloginstatus($ticket) {
    	$timestamp = sprintf("%.2f", microtime(true));
    	$params = json_encode(array( "ticket" => $ticket, "timestamp" => $timestamp), JSON_UNESCAPED_SLASHES);
    	$message = $this->encrypt_and_mac($this->secretKey, $params);
    	$data = array('message' => $message);
    	$call_url = '/getloginstatus/'.$this->apiKey.'/';
    	return json_decode($this->_process_response($this->_post_request($call_url, $data), $this->secretKey),true);
    }


    public function logout($ticket) {
    	$timestamp = sprintf("%.2f", microtime(true));
    	$params = json_encode(array( "ticket" => $ticket, "timestamp" => $timestamp), JSON_UNESCAPED_SLASHES);
    	$message = $this->encrypt_and_mac($this->secretKey, $params);
    	$data = array('message' => $message);
    	$call_url = '/logout/'.$this->apiKey.'/';
    	return json_decode($this->_process_response($this->_post_request($call_url, $data), $this->secretKey),true);
    }

    //####################################################################################################################
    //############         Basis für die obigen Funktionen. Vermutlich besser die Finger weg lassen!         #############
    //####################################################################################################################


    private $_LIBREID_ITERATIONS = 5000;
    private $_LIBREID_HASHFUNCTION = "sha512";
    private $_LIBREID_BASEURL = "http://libreid.wsp.lab.sit.cased.de/api";


    private function _decrypt_message($secret_key, $message) {
        $crypt_salt = substr($message, 0, 8);
        $crypt_temp = $this->pbkdf2($this->_LIBREID_HASHFUNCTION, $secret_key, $crypt_salt, $this->_LIBREID_ITERATIONS, 48, true);
        $crypt_key = substr($crypt_temp, 0, 32);
        $iv = substr($crypt_temp, 32, 16);
        $m = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $crypt_key, substr($message, 8), MCRYPT_MODE_CBC, $iv);
        $length = unpack("n*", substr($m, 0, 2))[1]-2;
        return substr($m, 2, $length);
    }


    private function _encrypt_message($secret_key, $message) {
    	$test = unpack("C*", pack("L", strlen($message) + 2));
    	$m = chr($test[2]).chr($test[1]).$message;
    	$crypt_salt = openssl_random_pseudo_bytes(8);
    	$crypt_nonce = $this->pbkdf2($this->_LIBREID_HASHFUNCTION, $secret_key, $crypt_salt, $this->_LIBREID_ITERATIONS, 48, true);
    	$crypt_key = substr($crypt_nonce, 0, 32);
    	$iv = substr($crypt_nonce, 32, 16);
    	return $crypt_salt.mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $crypt_key, $m, MCRYPT_MODE_CBC, $iv);
    }


    private function _sign_message($secret_key, $message) {
        $mac_salt = openssl_random_pseudo_bytes(8);
        $mac_key = $this->pbkdf2($this->_LIBREID_HASHFUNCTION, $secret_key, $mac_salt, $this->_LIBREID_ITERATIONS, 64, true);
        $mac = hash_hmac($this->_LIBREID_HASHFUNCTION, $message, $mac_key, true);
        return array($mac_salt, $mac);
    }


    private function _validate_message($secret_key, $message, $mac_salt, $mac) {
        $mac_key = $this->pbkdf2($this->_LIBREID_HASHFUNCTION, $secret_key, $mac_salt, $this->_LIBREID_ITERATIONS, 64, true);
        $new_mac = hash_hmac($this->_LIBREID_HASHFUNCTION, $message, $mac_key, true);
        return ($new_mac == $mac);
    }


    private function encrypt_and_mac($secret_key, $message) {
    	$c = $this->_encrypt_message($secret_key, $message);
        $mac_res = $this->_sign_message($secret_key, $c);
        return base64_encode(chr(1).$mac_res[0].$mac_res[1].$c);
    }


    public function validate_and_decrypt($message) {
        $m = base64_decode($message);
        if ($m[0] != "\x01") {
            return NULL;
        }
        if (!$this->_validate_message($this->secretKey, substr($m, 73), substr($m, 1, 8), substr($m, 9, 64))) {
        	echo NULL;
        }
        return $this->_decrypt_message($this->secretKey, substr($m, 73));
    }


    private function _post_request($call_url, $data) {
    	$url = $this->_LIBREID_BASEURL.$call_url;
    	$ch = curl_init($url);
     	curl_setopt($ch, CURLOPT_POST, 1);
     	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$response = curl_exec($ch);
    	curl_close($ch);
    	return $response;
    }


    private function _process_response($response, $secret_key) {
    	if (!$response) {
    		return NULL;
    	} else if (substr(base64_decode($response), 0, 1) == "\x01") {
    		return $this->validate_and_decrypt($response);
    	} else {
    		return base64_decode($response);
    	}
    }

    private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = strtolower($algorithm);
        if(!in_array($algorithm, hash_algos(), true))
            trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
        if($count <= 0 || $key_length <= 0)
            trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);

        if (function_exists("hash_pbkdf2")) {
            // The output length is in NIBBLES (4-bits) if $raw_output is false!
            if (!$raw_output) {
                $key_length = $key_length * 2;
            }
            return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }
}
?>
