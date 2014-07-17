<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use frontend\models\ConfirmCheckoutForm;

class CheckoutController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * User must verify payment process
     */
    public function actionIndex($checkoutId)
    {
        $model = new ConfirmCheckoutForm();
        $model->checkoutId = $checkoutId;
        $model->userId = Yii::$app->user->id;

        // Validates only "non-user" stuff => Check if checkout exists
        // and is not approved already.
        if (!$model->validate()) {
            throw new NotFoundHttpException('Die angegebene Checkout ID ist ungültig.');
        }

        if (Yii::$app->request->isPost)
        {
            // Performs additional validation that regards user stuff
            // => Check if user has enough money to pay the checkout.
            if ($model->performCheckoutConfirmation()) {
                Yii::$app->session->setFlash('success', 'Zahlung erfolgreich bestätigt');
                return $this->render('redirect', [
                    'redirectUrl' => $model->checkout->return_url,
                ]);
            } else {
                Yii::$app->session->setFlash('error', current($model->getFirstErrors()));
                return $this->render('redirect', [
                    'redirectUrl' => $model->checkout->cancel_url,
                ]);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'checkout' => $model->checkout,
        ]);
    }
}
