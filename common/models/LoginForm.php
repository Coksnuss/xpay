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
			if($this->getUser() === null) {
				return false;
			} else {
				return Yii::$app->user->login($this->getUser());			}
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
}
