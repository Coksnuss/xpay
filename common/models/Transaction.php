<?php
namespace common\models;

use Yii;
use yii\helpers\Security;
use yii\helpers\Html;

/**
 * This is the model class for table "transaction".
 *
 * Check the base class at common\models\base\Transaction in order to
 * see the column names and relations.
 */
class Transaction extends \common\models\base\Transaction
{
    /**
     * The available transaction types.
     */
    const TYPE_ORDER = 1; // Bezahlung
    const TYPE_RECEIPT = 2; // Eingang von Geld, alternativ Einzahlung = DEPOSIT oder Gutschrift = CREDIT
    const TYPE_REDEMPTION = 3; // Rückbuchung

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'transaction_id' => 'transaction_id',
            //'account' => function ($field, $model) { return $model->account; },
            'amount' => 'amount',
        ];
    }

    /**
     * @return array A list of transaction types indexed by the corresponding
     * type ID.
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_ORDER => 'order', 
            self::TYPE_RECEIPT=>'receipt', 
            self::TYPE_REDEMPTION => 'redemption'
        ];
    }

    /**
     * Generates a new transaction id to be used for newly inserted records.
     */
    public function generateTransactionId()
    {
        $this->transaction_id = Security::generateRandomKey();
    }

    /**
     * Ensures (as good as possible) that a record is not saved directly.
     * Instead of saving a transaction directly it should be saved by linking
     * it to an account. Also see Account::linkTransaction.
     *
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($runValidation) {
            throw new Excpetion('Transactions must be linked against an account.');
        }

        return parent::save($runValidation, $attributeNames);
    }

    public function attributeLabels(){
    	return ['created_at' => 'Booked at']+parent::attributeLabels();
    }

    public $receiver;
    public $sender;
    public $time;
    /**
     * 
     * @return Ambigous <multitype:, multitype:string >
     */
    public function getType()
    {
    	return $this->getTypeList()[$this->type];
    }
	
    /**
     * 
     * @return string Amount as String with preferred currency
     */
	public function getAmountString(){
		$preferredCurrency = $this->account->preferredCurrency;
		$value = (($preferredCurrency->iso_4217_name !== 'EUR')?$this->foreign_currency_amount:$this->amount);
		$amountString = $value." ".$preferredCurrency->iso_4217_name;
    	if ($value<0){
    		$amountString = Html::tag('div',$amountString,['class'=>'monospace amount-negativ right']);
    	}else{
    		$amountString = Html::tag('div',$amountString,['class'=>'monospace right']);
    	}
    	return $amountString;
    }
}
