<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "transaction".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\Transaction.
 *
 * @property integer $id
 * @property string $transaction_id
 * @property string $uuid
 * @property integer $account_id
 * @property string $associated_account_number
 * @property integer $type
 * @property string $amount
 * @property string $foreign_currency_amount
 * @property integer $foreign_currency_id
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Account $account
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transaction}}';
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
            [['transaction_id', 'account_id', 'associated_account_number', 'type', 'amount', 'description'], 'required'],
            [['account_id', 'associated_account_number', 'type', 'foreign_currency_id'], 'integer'],
            [['amount', 'foreign_currency_amount'], 'number'],
            [['transaction_id', 'uuid'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 255],
            [['transaction_id'], 'unique'],
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
            'transaction_id' => 'Transaction ID',
            'uuid' => 'Uuid',
            'account_id' => 'Account ID',
            'associated_account_number' => 'Associated Account Number',
            'type' => 'Type',
            'amount' => 'Amount',
            'foreign_currency_amount' => 'Foreign Currency Amount',
            'foreign_currency_id' => 'Foreign Currency ID',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }
}
