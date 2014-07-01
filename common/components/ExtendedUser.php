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
