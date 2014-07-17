<?php
namespace api\models;

use Yii;

use common\models\Account;
use common\models\Currency;
use common\models\Transaction;

/**
 * Transaction form
 */
class TransactionForm extends \yii\base\Model
{
    public $uuid;
    public $sender_account_number;
    public $receiver_account_number;
    public $amount;
    public $currency;
    public $description;
    public $type;
    public $reference;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'sender_account_number', 'receiver_account_number', 'amount', 'currency', 'description', 'type'], 'required'],
            ['uuid', 'match', 'pattern' => '/[a-f\d]{8}(-[a-f\d]{4}){3}-[a-f\d]{12}?/i'],
            ['uuid', 'filter', 'filter' => function ($val) { return str_replace('-', '', $val); }],
            ['uuid', 'unique', 'targetClass' => Transaction::className(), 'targetAttribute' => 'uuid', 'message' => 'Transaction with {attribute} "{value}" has already been performed.'],
            ['sender_account_number', 'checkIfAccountExternal'],
            ['receiver_account_number', 'checkIfAccountValidAndInternal'],
            ['amount', 'number', 'min' => 0],
            ['currency', 'default', 'value' => Currency::getPrimaryCurrencyCode()],
            ['currency', 'in', 'range' => [Currency::getPrimaryCurrencyCode()]],
            ['description', 'string', 'min' => 3],
            ['type', 'default', 'value' => Transaction::TYPE_RECEIPT],
            ['type', 'in', 'range' => [Transaction::TYPE_RECEIPT]],
            ['reference', 'string', 'max' => 32],
        ];
    }

    /**
     * Validator which checks for a given account number if it is valid and not
     * located at our service.
     *
     * @param string $attribute The attribute name which holds the account number.
     * @param array $params Not used.
     */
    public function checkIfAccountExternal($attribute, $params)
    {
        if ($this->$attribute < Account::GLOBAL_ACCOUNT_NUMBER_START ||
            $this->$attribute > Account::GLOBAL_ACCOUNT_NUMBER_END ||
            $this->$attribute >= Account::INTERNAL_ACCOUNT_NUMBER_START &&
            $this->$attribute <= Account::INTERNAL_ACCOUNT_NUMBER_END
        ) {
            $this->addError($attribute, 'Sender number is invalid.');
        }
    }

    /**
     * Validator which checks for a given account number if it is valid and the
     * account exists within our service.
     *
     * @param string $attribute The attribute name which holds the account number.
     * @param array $params Not used.
     */
    public function checkIfAccountValidAndInternal($attribute, $params)
    {
        if ($this->$attribute < Account::INTERNAL_ACCOUNT_NUMBER_START ||
            $this->$attribute > Account::INTERNAL_ACCOUNT_NUMBER_END ||
            Account::lookup($this->$attribute) === null
        ) {
            $this->addError($attribute, 'Receiver is unknown.');
        }
    }

    /**
     * Creates the requested transaction
     *
     * @return Transaction The newly created transaction.
     */
    public function performTransaction()
    {
        $account = Account::lookup($this->receiver_account_number);

        $transaction = new Transaction();
        $transaction->generateTransactionId();
        $transaction->uuid = $this->uuid;
        $transaction->associated_account_number = $this->sender_account_number;
        $transaction->type = $this->type;
        $transaction->amount = $this->amount;
        $transaction->reference = $this->reference;
        $transaction->transaction_user_id = Yii::$app->user->id;
        //$transaction->foreign_currency_amount;
        //$transaction->foreign_currency_id;
        $transaction->description = $this->description;

        $account->linkTransaction($transaction);
        return $transaction;
    }
}
