<?php
namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "currency".
 *
 * Check the base class at common\models\base\Currency in order to
 * see the column names and relations.
 */
class Currency extends \common\models\base\Currency
{
    /**
     * @return string The ISO 4217 code of the primary currency.
     */
    public static function getPrimaryCurrencyCode()
    {
        return "EUR";
    }

    /**
     * @return array A list containing the available currency codes in ISO 4217
     * format.
     */
    public static function getAvailableCurrencyCodes()
    {
        return static::find()->select('iso_4217_name')->column();
    }

    /**
     * Finds the primary key of a currency by its ISO 4217 code.
     *
     * @param string $code The ISO 4127 identifier of the currency
     * @return integer The primary key value of the currency.
     * @throws Exception If the currency could not be found.
     */
    public static function getIdByCode($code)
    {
        if (($currency = static::findOne(['iso_4217_name' => $code])) === null) {
            throw new Exception('Could not find the given currency.');
        }

        return $currency->id;
    }

    /**
     * Converts one currency into an other currency.
     *
     * @param double $amount The amount in the origin currency.
     * @param string $currencyCode The ISO 4217 code of the origin currency.
     * @param string $targetCurrencyCode The ISO 4217 code of the target.
     * currency. If not given, the convertion will be performed into euro.
     * @return double The converted amount in the target currency.
     * @throws Exception If the currency could not be found.
     */
    public static function convert($amount, $currencyCode, $targetCurrencyCode = 'EUR')
    {
        if ($currencyCode === $targetCurrencyCode) {
            return $amount;
        }

        if ($targetCurrencyCode === 'EUR') {
            $currency = static::findOne(['iso_4217_name' => $currencyCode]);

            if ($currency === null) {
                throw new Exception('Could not find the given currency.');
            }

            return $amount * $currency->eur_exchange_rate;
        }

        $currency = static::find(['iso_4217_name' => [$currencyCode, $targetCurrencyCode]])
            ->indexBy('iso_4217_name')
            ->all();

        if (count($currency) !== 2) {
            throw new Exception('Could not find the given currency.');
        }

        $foreignToEur = $amount * $currency[$currencyCode]->eur_exchange_rate;
        $eurToTarget  = $foreignToEur * (1 / $currency[$targetCurrencyCode]->eur_exchange_rate);

        return $eurToTarget;
    }
}
