<?php

namespace frontend\controllers;

use Yii;
use common\models\Transaction;
use common\models\Account;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\OverviewTransactionSearch;
use yii\filters\AccessControl;
use frontend\models\UserAccountStatementSearch;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
{
    public function behaviors()
    {
        return [
        	'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view'],
                'rules' => [
                    [
                        'actions' => ['index','view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
       	$model = Account::findOne(['user_id'=>Yii::$app->user->identity->id]);
    	if ($model !== NULL){
    		$searchModel = new OverviewTransactionSearch();
	        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	        return $this->render('index', [ 'searchModel' => $searchModel,
	        		'dataProvider' => $dataProvider, 'model' => $model,
	        		]);
	    }else{
	    	throw new NotFoundHttpException();
	    }
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (isset($model)){
    		return $this->render('view', [
        	    'model' => $model,
        	]);
        }else{
        	throw new NotFoundHttpException();
        }
    }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null && $model->account_id === User::findOne(['id'=>Yii::$app->user->identity->id])->accounts[0]->id) {
        	return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
}
