<?php
namespace common\models;

use Yii;
use yii\base\Exception;
use yii\bootstrap\Button;
use yii\helpers\Html;

/**
 * This is the model class for table "account".
 *
 * Check the base class at common\models\base\Account in order to
 * see the column names and relations.
 */
class Account extends \common\models\base\Account
{
    /**
     * Account number ranges.
     * Used by validators throughout the application.
     */
    const GLOBAL_ACCOUNT_NUMBER_START   = 101000000;
    const GLOBAL_ACCOUNT_NUMBER_END     = 105999999;
    const INTERNAL_ACCOUNT_NUMBER_START = 101000000;
    const INTERNAL_ACCOUNT_NUMBER_END   = 101999999;

	public $amount;
    /**
     * Checks whether a given account number belongs to our service.
     * This method does not verify the existence of the corresponding account!
     *
     * @param integer|string $number The account number
     * @return boolean Whether the account number belongs to our own service. If
     * false, the number does either belong to an other service or is invalid.
     */
    public static function isInternalAccountNumber($number)
    {
        return intval($number) >= self::INTERNAL_ACCOUNT_NUMBER_START &&
               intval($number) <= self::INTERNAL_ACCOUNT_NUMBER_END;
    }

    /**
     * Finds an account by account number
     *
     * @param integer|string $number The account number
     * @return Account ActiveRecord instance of the matching account or null if
     * no match could be found.
     */
    public static function lookup($number)
    {
        return static::findOne(['number' => $number]);
    }

    /**
     * Returns an unused account number.
     * Account numbers are allocated in ascending order.
     *
     * @return integer An unused account number.
     * @throws HttpException if no account number could be found.
     */
    public static function getNextAccountNumber()
    {
        $number = self::find()
            ->select('number')
            ->orderBy(['number' => SORT_DESC])
            ->scalar();

        if ($number === false) {
            return self::INTERNAL_ACCOUNT_NUMBER_START;
        }

        if ($number == self::INTERNAL_ACCOUNT_NUMBER_END) {
            throw new Exception('Could not find a free account number.');
        }

        return $number + 1;
    }

    /**
     * Links a transaction to this account if the account has an adequate
     * balance.
     * Adds an error to the transaction if the account cannot be charged.
     * In case of a successful linking, the account balance is updated
     * accordingly.
     *
     * IMPORTANT: This method should always be used to link transactions. A
     * transaction should never be linked manually to an account or saved
     * manually!
     *
     * @param Transaction $transaction The transaction to be linked. It should
     * be valid and a new record.
     */
    public function linkTransaction($transaction)
    {
        if (($newBalance = $this->balance + $transaction->amount) < 0) {
            $transaction->addError('amount', 'Cannot debit from account because it does not have enough funds.');
        } else {
            $this->link('transactions', $transaction);
            $this->balance += $transaction->amount;
            $this->save();
        }
    }

    /**
     * Charges a customer account by the given amount.
     *
     * @param float $amount The amount to charge.
     */
    public function charge($amount)
    {
        $transaction = new Transaction;
        $transaction->generateTransactionId();
        $transaction->account_id = $this->id;
        $transaction->associated_account_number = self::GLOBAL_ACCOUNT_NUMBER_END + 1;
        $transaction->type = Transaction::TYPE_CHARGE;
        $transaction->amount = $amount;
        $transaction->description = sprintf('Kundenaufladung über IBAN: %s (BIC: %s)', $this->iban, $this->bic);
        $this->linkTransaction($transaction);
    }

    public function getBalanceLabel($type=true){

    	//$preferredCurrency = Currency::findOne($condition);
		$value = $this->balance;
		$balanceString = $value." "."EUR";
    	if($type){
    		if ($value<0){
    			return Button::widget([
			    	'label' => $balanceString,
			    	'options' => ['class' => 'btn-danger monospace','width'=>250,'disabled'=>true]]);
    		}else{
    			return Button::widget([
			    	'label' => $balanceString,
			    	'options' => ['class' => 'btn-primary monospace','width'=>250,'disabled'=>true]]);
    		}
    	}


    	return $balanceString;
    }

    public function getStatusLabel($type=true){

    	//$preferredCurrency = Currency::findOne($condition);
    	$statusString = ($this->status)?"ACTIVATED":"DEACTIVATED";
    	if($type){
    		if ($this->status){
    			return Html::tag('div',$statusString,['class'=>'status-activated']);
    		}else{
    			return Html::tag('div',$statusString,['class'=>'status-deactivated']);
    		}
    	}


    	return $statusString;
    }

    public function attributeLabels(){
    	return ['iban'=>'IBAN', 'bic'=>'BIC','amount'=>'Amount to Transfer']+parent::attributeLabels();
    }

    public function rules()
    {
    	return [[['iban', 'bic'], 'required']]+parent::rules();
    }
}
