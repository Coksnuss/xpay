<?php
namespace api\controllers;

use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Checkout controller
 */
class CheckoutController extends \yii\rest\ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\CheckoutRequest';
    /**
     * @inheritdoc
     */
    public $serializer = 'api\rest\Serializer';
    /**
     * @var string The attribute name which is used as identification for the
     * record. Can differ from the primary key. Non composite keys are not
     * supported.
     */
    public $key = 'transaction_id';


    /**
     * Enable HTTP Basic Access Authentication.
     * Also see User::findIdentityByAccessToken.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'authMethods' => [
                    \yii\filters\auth\HttpBasicAuth::className(),
                ],
            ],
        ]);
    }

    /**
     * Do only allow read (GET/view) and write (POST/create) requests to the API
     * and disallow updating or deleting of records.
     */
    public function actions()
    {
        return [
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findByUniqueKey'],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'api\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
                'collectionOptions' => ['POST', 'OPTIONS'],
                'resourceOptions' => ['GET', 'HEAD', 'OPTIONS'],
            ],
        ];
    }

    /**
     * Finder method for api\actions\rest\CreationAction to find a record with
     * a key which might differ from the primary key.
     */
    public function findByUniqueKey($key, $action)
    {
        $modelClass = $action->modelClass;
        $model = $modelClass::findOne([$this->key => $key]);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $key");
        }
    }
}
