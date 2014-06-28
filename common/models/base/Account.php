<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "account".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\Account.
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property string $balance
 * @property integer $iban
 * @property integer $bic
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $preferred_currency
 *
 * @property \common\models\Currency $preferredCurrency
 * @property \common\models\User $user
 * @property \common\models\AccountStatement[] $accountStatements
 * @property \common\models\CheckoutRequest[] $checkoutRequests
 * @property \common\models\Transaction[] $transactions
 */
abstract class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account}}';
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
            [['user_id', 'number', 'balance', 'preferred_currency'], 'required'],
            [['user_id', 'number', 'iban', 'bic', 'status', 'preferred_currency'], 'integer'],
            [['balance'], 'number'],
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
        return $this->hasOne(\common\models\Currency::className(), ['id' => 'preferred_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountStatements()
    {
        return $this->hasMany(\common\models\AccountStatement::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckoutRequests()
    {
        return $this->hasMany(\common\models\CheckoutRequest::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(\common\models\Transaction::className(), ['account_id' => 'id']);
    }
}
