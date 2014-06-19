<?php
namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user".
 * Please do not add custom code to this file, as it is supposed to be overriden
 * by the gii model generator. Custom code belongs to common\models\User.
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $api_token
 * @property integer $role
 * @property integer $status
 * @property string $last_login_time
 * @property string $last_login_ip
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Account[] $accounts
 * @property ShopBlacklist[] $shopBlacklists
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['first_name', 'last_name', 'email', 'auth_key', 'password_hash', 'api_token'], 'required'],
            [['role', 'status', 'last_login_ip'], 'integer'],
            [['last_login_time'], 'safe'],
            [['first_name', 'last_name', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key', 'api_token'], 'string', 'max' => 32],
            [['email'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'api_token' => 'Api Token',
            'role' => 'Role',
            'status' => 'Status',
            'last_login_time' => 'Last Login Time',
            'last_login_ip' => 'Last Login Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopBlacklists()
    {
        return $this->hasMany(ShopBlacklist::className(), ['user_id' => 'id']);
    }
}
