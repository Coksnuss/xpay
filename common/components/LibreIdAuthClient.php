<?php
namespace common\components;

use Yii;
use yii\helpers\Url;

class LibreIdAuthClient extends \yii\authclient\BaseClient
{
    public function buildAuthUrl($returnUrl)
    {
        return Url::toRoute(['site/libre-id-login',
            'message' => Yii::$app->libreidapi->get_login_message($returnUrl),
            'return' => $returnUrl,
        ]);
    }

    /**
     * @inheritdoc
     */
    //public $authUrl = 'https://www.google.com/accounts/o8/id';

    /**
     * @inheritdoc
     */
    /*public $requiredAttributes = [
        'namePerson/first',
        'namePerson/last',
        'contact/email',
        'pref/language',
    ];*/

    /**
     * @inheritdoc
     */
    /*protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'first_name' => 'namePerson/first',
            'last_name' => 'namePerson/last',
            'email' => 'contact/email',
            'language' => 'pref/language',
        ];
    }*/

    /**
     * @inheritdoc
     */
    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 880,
            'popupHeight' => 520,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'libreid';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'LibreID';
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return [
            'id' => 12349392,
            'name' => 'Tralala',
        ];
    }
}
