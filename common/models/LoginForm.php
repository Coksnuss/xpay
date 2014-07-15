<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login($checkAdminStatus = false, $casLogin = false)
    {
    	if(!$casLogin && !$this->validate()) {
    		//validate only if casLogin is false; return false if validation fails
    		return false;
    	} else {
    		if($checkAdminStatus) {
    			if(Yii::$app->user->getIsAdmin($this->email)){
    				return Yii::$app->user->login($this->getUser());
    			} else {
    				$this->addError('email', 'You do not have the rights to access this page.');
    				return false;
    			}
    		} else {
    			//manual validation needed here, because validation is skipped if casLogin is true
				if($this->getUser() === null) {
					return false;
				} else {
					if($this->checkFallbackNecessary($this->getUser())) {
						return Yii::$app->user->login($this->getUser());
					} else {
						$this->addError('email', 'At least one of your previously used CAS Services for logging in is reachable. Please use this to log in.');
					}	
				}
    		}
    	}
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    } 
    
    /**
     * Tests if a site is reachable
     * 
     * @param String $url
     */
    private function isSiteUp($url) {
    	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";$ch=curl_init();
    	curl_setopt ($ch, CURLOPT_URL,$url );
    	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt ($ch,CURLOPT_VERBOSE,false);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch,CURLOPT_SSLVERSION,3);
    	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
    	$page=curl_exec($ch);
    	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    	curl_close($ch);
    	if($httpcode>=200 && $httpcode<400)
    		return true;
    	else 
    		return false;
    }
    
    /**
     * Checks if the all CAS's which the user could use to login are down
     * 
     * @param unknown $user
     */
    private function checkFallbackNecessary($user) {
    	if($user->libreid_used == true && $this->isSiteUp('https://libreid.wsp.lab.sit.cased.de/'))
    		return false;
    	if($user->secauth_used == true && $this->isSiteUp('https://secauth.wsp.lab.sit.cased.de/'))
    		return false;
    	return true;
    }
}
