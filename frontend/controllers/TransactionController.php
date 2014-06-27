<?php

namespace frontend\controllers;

use Yii;
use common\models\Transaction;
use common\models\Account;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\OverviewTransactionSearch;
use yii\filters\AccessControl;

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
                //'only' => ['index','view'],
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
    	$searchModel = new OverviewTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
            	/*
            	 * $sql = 'SELECT * ';
            	$sql .= 'FROM ';
            	$sql .= '(';
            	$sql .= 'SELECT t1.transaction_id as transaction_id, ';
            	$sql .= 't1.associated_account_number as receiver, ';
            	$sql .= 't1.created_at as time, ';
            	$sql .= 't2.associated_account_number as sender, ';
            	$sql .= 't1.type as type,t1.amount as amount ';
            	$sql .= 'FROM ';
            	$sql .= '(SELECT * FROM transaction AS t WHERE t.type = 1 AND t.account_id = 1) as t1 ';
            	$sql .= 'INNER JOIN ';
            	$sql .= '(SELECT * FROM transaction AS t WHERE t.type = 2 AND t.account_id <> 1) as t2 ';
            	$sql .= 'WHERE ';
            	$sql .= 't1.transaction_id = t2.transaction_id';
            	$sql .= ') as x1 ';
            	$sql .= 'UNION ALL ';
            	$sql .= 'SELECT * ';
            	$sql .= 'FROM ';
            	$sql .= '(';
            	$sql .= 'SELECT ';
            	$sql .= 't1.transaction_id as transaction_id, ';
            	$sql .= 't2.associated_account_number as receiver, ';
            	$sql .= 't1.created_at as time, ';
            	$sql .= 't1.associated_account_number as sender, ';
            	$sql .= 't1.type as type, ';
            	$sql .= 't1.amount as amount ';
            	$sql .= 'FROM ';
            	$sql .= '(SELECT * FROM transaction AS t WHERE t.type = 2 AND t.account_id = 1) as t1 ';
            	$sql .= 'INNER JOIN ';
            	$sql .= '(SELECT * FROM transaction AS t WHERE t.type = 1 AND t.account_id <> 1) as t2 ';
            	$sql .= 'WHERE ';
            	$sql .= 't1.transaction_id = t2.transaction_id) as x2';*/

        return $this->render('index', [ 'searchModel' => $searchModel,
        		'dataProvider' => $dataProvider, 'model' => $model,
        		]);
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//     public function actionCreate()
//     {
//         $model = new Transaction();

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             return $this->redirect(['view', 'id' => $model->id]);
//         } else {
//             return $this->render('create', [
//                 'model' => $model,
//             ]);
//         }
//     }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//     public function actionUpdate($id)
//     {
//         $model = $this->findModel($id);

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             return $this->redirect(['view', 'id' => $model->id]);
//         } else {
//             return $this->render('update', [
//                 'model' => $model,
//             ]);
//         }
//     }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//     public function actionDelete($id)
//     {
//         $this->findModel($id)->delete();

//         return $this->redirect(['index']);
//     }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
