<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property string $balance
 * @property string $iban
 * @property string $bic
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $preferred_currency
 *
 * @property Currency $preferredCurrency
 * @property User $user
 * @property AccountStatement[] $accountStatements
 * @property CheckoutRequest[] $checkoutRequests
 * @property Transaction[] $transactions
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'number', 'balance', 'created_at', 'updated_at', 'preferred_currency'], 'required'],
            [['user_id', 'number', 'status', 'preferred_currency'], 'integer'],
            [['balance'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['iban', 'bic'], 'string', 'max' => 34],
            [['number'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'number' => 'Number',
            'balance' => 'Balance',
            'iban' => 'Iban',
            'bic' => 'Bic',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'preferred_currency' => 'Preferred Currency',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreferredCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'preferred_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountStatements()
    {
        return $this->hasMany(AccountStatement::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckoutRequests()
    {
        return $this->hasMany(CheckoutRequest::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['account_id' => 'id']);
    }
}
