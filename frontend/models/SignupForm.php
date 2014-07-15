<?php
namespace frontend\models;

use yii\base\Model;
use Yii;

use common\models\Currency;
use common\models\Account;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $email;
    public $password;
    public $firstName;
    public $lastName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->email = $this->email;
            $user->setPassword($this->password);
	     	$user->first_name = $this->firstName;
	     	$user->last_name = $this->lastName;
            $user->generateAuthKey();
            $user->generateApiToken();
            $user->save();

            $account = new Account();
            $account->number = Account::getNextAccountNumber();
            $account->preferred_currency = Currency::getIdByCode(Currency::getPrimaryCurrencyCode());
            $account->balance = 0;
            $user->link('accounts', $account);

            return $user;
        }

        return null;
    }
}
