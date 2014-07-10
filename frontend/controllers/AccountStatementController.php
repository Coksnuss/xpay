<?php

namespace frontend\controllers;

use Yii;
use common\models\AccountStatement;
use frontend\models\UserAccountStatementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Account;

/**
 * AccountStatementController implements the CRUD actions for AccountStatement model.
 */
class AccountStatementController extends Controller
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
     * Lists all AccountStatement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAccountStatementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountStatement model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
    	$account = Account::findOne(['user_id'=>Yii::$app->user->identity->id]);
    	$accountStatements = $account->accountStatements;
    	$accountStatement = null;
    	foreach($accountStatements as $statement){
    		if ($statement->id == $id){
    			$accountStatement = $statement;
    		}
    	}
    	if ($accountStatement !== null)
    		return Yii::$app->getResponse()->sendFile($accountStatement->filePath);
    	else
    		throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * Finds the AccountStatement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountStatement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountStatement::findOne($id)) !== null) {
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
