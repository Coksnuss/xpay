<?php
namespace frontend\models;

use yii\base\Model;
use Yii;

use common\models\Transaction;
use common\models\Currency;
use common\models\Account;
use common\models\User;

/**
 * Signup form
 */
class TransferForm extends Model
{
    public $iban;
    public $bic;
	public $amount;

    public $account;

    /**
     * (non-PHPdoc)
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
    	return [[['iban', 'bic'], 'required'],
    	[['amount'], 'number','min'=>0.0, 'max'=>10000.00]]+parent::rules();
    }

    /**
     * Simulates transaction of remaining amount to another bank account.
     *
     * @return true
     */
    public function transfer($account)
    {
        if (isset($account->bic) && isset($account->iban) && $this->bic===$account->bic && $this->iban===$account->iban){
            $account->charge($this->amount);
    		return true;
        }else{
        	return false;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
    	return ['iban'=>'IBAN', 'bic'=>'BIC']+parent::attributeLabels();
    }
}
