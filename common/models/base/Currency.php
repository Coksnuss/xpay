<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "currency".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\Currency.
 *
 * @property integer $id
 * @property string $iso_4217_name
 * @property double $eur_exchange_rate
 * @property string $created_at
 * @property string $updated_at
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iso_4217_name', 'eur_exchange_rate'], 'required'],
            [['eur_exchange_rate'], 'number'],
            [['iso_4217_name'], 'string', 'max' => 3],
            [['iso_4217_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'iso_4217_name' => 'Iso 4217 Name',
            'eur_exchange_rate' => 'Eur Exchange Rate',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
