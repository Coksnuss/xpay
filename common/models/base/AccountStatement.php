<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "account_statement".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\AccountStatement.
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $date
 * @property integer $email_notification_send
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Account $account
 */
class AccountStatement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account_statement}}';
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
            [['account_id', 'date', 'email_notification_send'], 'required'],
            [['account_id', 'email_notification_send'], 'integer'],
            [['date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'date' => 'Date',
            'email_notification_send' => 'Email Notification Send',
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
