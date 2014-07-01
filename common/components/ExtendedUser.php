<?php

namespace common\components;

use Yii;
use common\models\User;

/**
 * This class extended the User component class (formerly CWebUser)
 */
class ExtendedUser extends \yii\web\User
{
	public $isAdmin;
	
	/**
	 *   Updates the authentication status using the information from session and cookie.
	 *
	 *   This method will try to determine the user identity using the [[idParam]] session variable.
	 *
	 *   If [[authTimeout]] is set, this method will refresh the timer.
	 *
	 *   If the user identity cannot be determined by session, this method will try to [[loginByCookie()|login by cookie]]
	 *   if [[enableAutoLogin]] is true.
	 *
	 * Overwritten to provide a flash message on timeout logout
	 */
	protected function renewAuthStatus()
	{
		$session = Yii::$app->getSession();
		$id = $session->getHasSessionId() || $session->getIsActive() ? $session->get($this->idParam) : null;
	
		if ($id === null) {
			$identity = null;
		} else {
			/** @var IdentityInterface $class */
			$class = $this->identityClass;
			$identity = $class::findIdentity($id);
		}
	
		$this->setIdentity($identity);
	
		if ($this->authTimeout !== null && $identity !== null) {
			$expire = $session->get($this->authTimeoutParam);
			if ($expire !== null && $expire < time()) {
				$session->setFlash('autoLogout','You were logged out after 20 minutes of inactivity.');
				$this->logout(false);
			} else {
				$session->set($this->authTimeoutParam, time() + $this->authTimeout);
			}
		}
	
		if ($this->enableAutoLogin) {
			if ($this->getIsGuest()) {
				$this->loginByCookie();
			} elseif ($this->autoRenewCookie) {
				$this->renewIdentityCookie();
			}
		}
	}
	
    /**
     * Checks if a user has admin rights
     * @param string $email has to be given if you want to check admin status before login
     * @return boolean whether the user has admin rights or not
     */
    public function getIsAdmin($email = null) 
    {
        $userEmail = $email;
        if($userEmail !== null) {
        	$userModel = User::findOne(['email'=>$userEmail]);
        	if($userModel !== null && $userModel->role == 0) {
        		$this->isAdmin = true;
        		return true;
        	} else {
        		return false;
        	}
        } else {
        	//already logged in
        	return $this->isAdmin;
        }
        
    }
        
}
