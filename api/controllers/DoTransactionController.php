<?php
namespace api\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use api\helpers\ErrorCode;
use api\models\TransactionForm;

class DoTransactionController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public $serializer = 'api\rest\Serializer';


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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
                'collectionOptions' => ['POST', 'OPTIONS'],
                'resourceOptions' => ['OPTIONS']
            ],
        ];
    }

    /**
     * Performs the requested transaction
     */
    public function actionCreate()
    {
        $model = new TransactionForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if (!$model->validate()) {
            return [
                'result' => $model,
                'error' => [
                    'code' => ErrorCode::ERROR_CODE_VALIDATION,
                    'message' => implode(' ', $model->getFirstErrors())
                ],
            ];
        }

        $transaction = $model->performTransaction();

        return $transaction;
    }
}
