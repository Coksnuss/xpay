<?php
namespace common\models;

use Yii;
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
    const TYPE_CHARGE = 4; // Aufladung

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
     * Finds a transaction by its transaction ID.
     *
     * @param string $transactionId The transaction ID.
     * @return static|null The corresponding Model, or null if it does not
     * exist.
     */
    public static function findByTransactionId($transactionId)
    {
        return static::findOne(['transaction_id' => $transactionId]);
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
            self::TYPE_REDEMPTION => 'redemption',
            self::TYPE_CHARGE=>'charge',
        ];
    }

    /**
     * @return array A list of all types in a human readable format, indexed by
     * their type ID.
     */
    public static function getHumanReadableTypeById($id)
    {
        switch ($id)
        {
            case self::TYPE_ORDER: return 'Bestellung';
            case self::TYPE_RECEIPT: return 'Zahlungseingang';
            case self::TYPE_REDEMPTION: return 'Rückbuchung';
            case self::TYPE_CHARGE: return 'Kontoaufladung';
            default: return 'Unbekannt';
        }
    }

    /**
     * @return string The type of this transaction in human readable form.
     */
    public function getHumanReadableType()
    {
        return self::getHumanReadableTypeById($this->type);
    }

    /**
     * Generates a new transaction id to be used for newly inserted records.
     */
    public function generateTransactionId()
    {
        $this->transaction_id = Yii::$app->security->generateRandomKey();
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
	public function getAmountString($right=true){
		$amountString = $this->amount." "."EUR";
    	if ($this->amount<0){
    		$amountString = Html::tag('div',$amountString,['class'=>'monospace amount-negativ'.(($right)?' right':'')]);
    	}else{
    		$amountString = Html::tag('div',$amountString,['class'=>'monospace'.(($right)?' right':'')]);
    	}
    	return $amountString;
    }
}
