<?php
namespace api\models;

use common\models\Account;
use common\models\Currency;

abstract class RemoteTransaction extends \yii\base\Model
{
    public $receiver_account_number;
    public $sender_account_number;
    public $amount;
    public $currency;
    public $description;
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiver_account_number', 'sender_account_number', 'amount', 'currency', 'description', 'type'], 'required'],
            ['sender_account_number', 'checkAccountIsInternalAndValid'],
            ['amount', 'number', 'min' => 0],
            ['currency', 'in', 'range' => Currency::getAvailableCurrencyCodes()],
            ['description', 'string', 'min' => 3],
        ];
    }


    /**
     * Validator which checks for a given account number if it belongs to our
     * service and is valid.
     *
     * @param string $attribute The attribute name which holds the account
     * number to check.
     * @param array $params Not used.
     */
    public function checkAccountIsInternalAndValid($attribute, $params)
    {
        if (!Account::isInternalAccountNumber($this->$attribute)
            || Account::lookup($this->$attribute) === null
        ) {
            $this->addError($attribute, 'Sender account number is invalid.');
        }
    }

    /**
     * Prefills the model attributes with respect to the given checkout.
     *
     * @return static The model with the prefilled attributes.
     */
    public static function byCheckout($checkout)
    {
        $model = new static();
        $model->receiver_account_number = $checkout->receiver_account_number;
        $model->sender_account_number   = $checkout->account->number;
        $model->amount                  = $checkout->getAmountInPrimaryCurrency();
        $model->currency                = Currency::getPrimaryCurrencyCode();
        $model->description             = $checkout->description;
        $model->type                    = static::translateType($checkout->type);

        return $model;
    }

    /**
     * Translates the checkout type to the type of the remote payment system.
     *
     * @return integer The transaction type of the remote payment system that
     * corresponds the the local given type.
     */
    abstract public static function translateType($checkoutType);

    /**
     * Performs the remote transaction
     *
     * @return TODO
     */
    abstract public function performTransaction();
}
