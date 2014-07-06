<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use backend\models\ExchangeRateForm;
use common\models\Currency;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['login', 'logout', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'exchange-rate'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest && Yii::$app->user->getIsAdmin()) {
            return $this->redirect(['/site/exchange-rate']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login(true)) {
            return $this->redirect(['/site/exchange-rate']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionExchangeRate()
    {
    	$model = new ExchangeRateForm();
    	$currencyModel = Currency::findOne(['iso_4217_name'=>'USD']);
    	$model->rate = $currencyModel->eur_exchange_rate;
    	if ($model->load(Yii::$app->request->post()) && $model->setExchangeRate()) {
    		$this->redirect(['/site/exchange-rate']);
    	} else {
    		return $this->render('exchange', [
    			'model' => $model,
    		]);
    	}
    }
}
