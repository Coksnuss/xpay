<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * Check the base class at common\models\base\Currency in order to
 * see the column names and relations.
 */
class Currency extends \common\models\base\Currency
{
    /**
     * @return array A list containing the available currency codes in ISO 4217
     * format.
     */
    public static function getAvailableCurrencyCodes()
    {
        return static::find()->select('iso_4217_name')->column();
    }
}
