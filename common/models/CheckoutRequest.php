<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Security;

use common\models\Account;
use common\models\Currency;
use common\models\Transaction;

/**
 * This is the model class for table "checkout_request".
 *
 * Check the base class at common\models\base\CheckoutRequest in order to
 * see the column names and relations.
 */
class CheckoutRequest extends \common\models\base\CheckoutRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            ['checkout_id', 'filter', 'filter' => [$this, 'generateApiKeyIfNotExists']],
            ['amount', 'number', 'min' => 0],
            ['currency', 'default', 'value' => Currency::getPrimaryCurrencyCode()],
            ['currency', 'in', 'range' => Currency::getAvailableCurrencyCodes()],
            ['receiver_account_number', 'integer', 'min' => Account::GLOBAL_ACCOUNT_NUMBER_START, 'max' => Account::GLOBAL_ACCOUNT_NUMBER_END],
            ['receiver_account_number', 'checkIfAccountValidIfInternal'],
            ['tax', 'number', 'min' => 0, 'max' => 1],
            [['return_url', 'return_url'], 'url'],
            ['description', 'string', 'min' => 3],
            ['type', 'in', 'range' => array_keys(Transaction::getTypeList())],
        ], parent::rules());
    }

    /**
     * Filter validator action which sets an api key none is present.
     *
     * @param string|null $key The current API key.
     * @return string The new API key, or the old if it was already set.
     */
    public function generateApiKeyIfNotExists($key)
    {
        if ($key === null) {
            return Security::generateRandomKey();
        }

        return $key;
    }

    /**
     * Validator which checks for a given account number if it is valid.
     * Only works for internal account numbers.
     *
     * @param string $attribute The attribute name which holds the account number.
     * @param array $params Not used.
     */
    public function checkIfAccountValidIfInternal($attribute, $params)
    {
        if (Account::isInternalAccountNumber($this->$attribute) &&
            Account::lookup($this->$attribute) === null
        ) {
            $this->addError($attribute, 'Unknown receiver.');
        }
    }

    /**
     * Ensures that checkout_id is flagged as an unsafe attribute for all
     * scenarios, i.e. it can not be set by a user.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        foreach ($scenarios as $scenario => $attributes) {
            $key = array_search('checkout_id', $scenarios[$scenario]);

            if ($key !== false) {
                $scenarios[$scenario][$key] = '!checkout_id';
            }
        }

        return $scenarios;
    }

    /**
     * Only return these fields when a record is requests from the API.
     */
    public function fields()
    {
        return [
            'checkout_id'             => 'checkout_id',
            'amount'                  => 'amount',
            'currency'                => 'currency',
            'receiver_account_number' => 'receiver_account_number',
            'tax'                     => 'tax',
            'return_url'              => 'return_url',
            'cancel_url'              => 'cancel_url',
            'description'             => 'description',
            'reference'               => 'reference',
            'type'                    => 'type',
        ];
    }

    /**
     * Finds a checkout by it's checkout id.
     *
     * @param string $checkoutId The checkout id of the record
     * @return CheckoutRequest The corresponding ActiveRecord instance or null
     * if not found.
     */
    public static function findByCheckoutId($checkoutId)
    {
        return static::findOne(['checkout_id' => $checkoutId]);
    }

    /**
     * @return boolean Whether this checkout has been approved by a user.
     */
    public function isApprovedByUser()
    {
        return $this->account_id !== null;
    }

    /**
     * @return boolean Whether this checkout is performed in the primary
     * currency.
     */
    public function isPaidInPrimaryCurrency()
    {
        return $this->currency === Currency::getPrimaryCurrencyCode();
    }

    /**
     * @return integer The primary key value of the currency that is used for
     * this checkout.
     */
    public function getCurrencyId()
    {
        return Currency::getIdByCode($this->currency);
    }

    /**
     * @return double The amount of this checkout after converted into the value
     * of the primary currency.
     */
    public function getAmountInPrimaryCurrency()
    {
        return Currency::convert($this->amount, $this->currency);
    }

    /**
     * @return boolean Wether the receiver account number belongs to our
     * service. This does not take into account whether the account exists.
     */
    public function isInternalReceiver()
    {
        return Account::isInternalAccountNumber($this->receiver_account_number);
    }
}
