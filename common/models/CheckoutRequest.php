<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Security;

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
            ['transaction_id', 'filter', 'filter' => [$this, 'generateApiKeyIfNotExists']],
            ['currency', 'in', 'range' => Currency::getAvailableCurrencyCodes()],
            ['receiver_account_number', 'integer', 'min' => 100000, 'max' => 500000],
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
     * Ensures that transaction_id is flagged as an unsafe attribute for all
     * scenarios, i.e. it can not be set by a user.
     *
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        foreach ($scenarios as $scenario => $attributes) {
            $key = array_search('transaction_id', $scenarios[$scenario]);

            if ($key !== false) {
                $scenarios[$scenario][$key] = '!transaction_id';
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
            'transaction_id'          => 'transaction_id',
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
}
