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
class EditPasswordForm extends Model
{

    public $current_password = "";
    public $new_password = "";
    public $confirm_password = "";

    public $user;

    /**
     * (non-PHPdoc)
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
    	return [[['current_password','new_password','confirm_password'],'required'],
    		[['confirm_password'], 'compare', 'compareAttribute'=>'new_password'],
    		['current_password', 'validatePassword']]
    		+ parent::rules();
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
		if (!$this->hasErrors()) {
            $user = $this->user;
            if (!$user || !$user->validatePassword($this->current_password)) {
                $this->addError('current_password', 'Incorrect password.');
            }
        }
    }

    public function save(){
    	if ($this->validate()){
    		$user = User::findOne(['id'=>Yii::$app->user->identity->id]);
	    	$user->setPassword($this->new_password);
	    	return $user->save(false);
    	}
    	return false;
    }

}
