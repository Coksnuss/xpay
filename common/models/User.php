<?php
namespace common\models;

use yii\base\NotSupportedException;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * Check the base class at common\models\base\User in order to
 * see the column names and relations.
 */
class User extends \common\models\base\User implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_USER = 10;

    /**
     * @inheritdoc
     */
     public function rules()
     {
        return [
            ['auth_key', 'unique'],

            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER]],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
         ];
     }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlacklistedShops()
    {
        return $this->hasMany(\common\models\Shop::className(), ['id' => 'shop_id'])
            ->viaTable('shop_blacklist', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(\common\models\Account::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($type === \yii\filters\auth\HttpBasicAuth::className()) {
            return static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
        } else {
            throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by email
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates a new API Token to be used for API requests.
     */
    public function generateApiToken()
    {
        $this->api_token = Yii::$app->security->generateRandomKey();
    }

    /**
     *
     */
    public function getPreferredCurrency()
    {
    	return $this->preferred_currency;
    }

    /**
     *
     */
    public function attributeLabels()
    {
    	return ['last_login_ip'=>'Last Login IP']+parent::attributeLabels();
    }
}
