<?php
namespace api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use api\helpers\ErrorCode;
use api\models\DoCheckoutForm;

/**
 * DoCheckout controller
 */
class DoCheckoutController extends \yii\rest\ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\Transaction';
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
                    // \yii\filters\auth\HttpBasicAuth::className(),
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findByUniqueKey'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
                'collectionOptions' => ['POST', 'OPTIONS'],
                'resourceOptions' => ['GET'],
            ],
        ];
    }

    /**
     * Implements the API endpoint for creating a new transaction, out of an
     * checkout that was approved by a user.
     */
    public function actionCreate()
    {
        $model = new DoCheckoutForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if (!$model->validate())
        {
            return [
                'result' => $model,
                'error' => [
                    'code' => ErrorCode::ERROR_CODE_VALIDATION,
                    'message' => implode(' ', $model->getFirstErrors())
                ],
            ];
        }

        $transaction = $model->performTransaction();

        if ($transaction->hasErrors()) {
            return [
                'result' => $transaction,
                'error' => [
                    'code' => ErrorCode::ERROR_CODE_VALIDATION,
                    'message' => implode(' ', $transaction->getFirstErrors()),
                ],
            ];
        }

        return $transaction;
    }

    /**
     * Finder method for \yii\rest\ViewAction to find a record with
     * a key which might differ from the primary key.
     * Also see $key.
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
