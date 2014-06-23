<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "transaction_request".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\TransactionRequest.
 *
 * @property integer $id
 * @property string $uuid
 * @property string $sender_account_number
 * @property string $receiver_account_number
 * @property string $amount
 * @property string $currency
 * @property string $description
 * @property integer $type
 * @property string $transaction_id
 * @property string $created_at
 * @property string $updated_at
 */
abstract class TransactionRequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transaction_request}}';
    }

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
            [['uuid', 'sender_account_number', 'receiver_account_number', 'amount', 'currency', 'description', 'type'], 'required'],
            [['sender_account_number', 'receiver_account_number', 'type'], 'integer'],
            [['amount'], 'number'],
            [['uuid', 'description', 'transaction_id'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 3],
            [['uuid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'sender_account_number' => 'Sender Account Number',
            'receiver_account_number' => 'Receiver Account Number',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'description' => 'Description',
            'type' => 'Type',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
