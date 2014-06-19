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
                'findModel' => [$this, 'findByTransactionId'],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'api\actions\rest\CreateAction',
                'key' => 'transaction_id',
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
    public function findByTransactionId($id, $action)
    {
        $modelClass = $action->modelClass;
        $model = $modelClass::findOne(['transaction_id' => $id]);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }
}
