<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "checkoutRequest".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\CheckoutRequest.
 *
 * @property integer $id
 * @property string $transaction_id
 * @property string $amount
 * @property string $currency
 * @property string $receiver_account_number
 * @property double $tax
 * @property string $return_url
 * @property string $cancel_url
 * @property string $description
 * @property string $reference
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 */
class CheckoutRequest extends \yii\db\ActiveRecord
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
            [['transaction_id', 'amount', 'currency', 'receiver_account_number', 'return_url', 'cancel_url', 'description', 'type'], 'required'],
            [['amount', 'tax'], 'number'],
            [['receiver_account_number', 'type'], 'integer'],
            [['transaction_id'], 'string', 'max' => 32],
            [['currency'], 'string', 'max' => 3],
            [['return_url', 'cancel_url', 'description', 'reference'], 'string', 'max' => 255],
            [['transaction_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'receiver_account_number' => 'Receiver Account Number',
            'tax' => 'Tax',
            'return_url' => 'Return Url',
            'cancel_url' => 'Cancel Url',
            'description' => 'Description',
            'reference' => 'Reference',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
