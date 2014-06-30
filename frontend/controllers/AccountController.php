<?php

namespace frontend\controllers;

use Yii;
use common\models\Account;
use common\models\base\AccountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
{
    public function behaviors()
    {
        return [
        	'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','update','create'],
                'rules' => [
                    [
                        'actions' => ['index','view','update','create'],
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
     * Lists all Account models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Account model.
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
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Account();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()){
        	return $this->redirect(['../user/view', 'id' => $model->user_id]); 
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionTransfer($id)
    {
    	$model = $this->findModel($id);
    	if ($model->load(Yii::$app->request->post()) && $model->saveWithTransfer($id)){
    		return $this->redirect(['../user/view', 'id' => $model->user_id]);
    	} else {
    		return $this->render('update', [
    				'model' => $model,
    				]);
    	}
    }

    /**
     * Deletes an existing Account model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionDeactivate($id)
    {
    	$model = $this->findModel($id);
    	$model->status = 0;
    	$model->save();
    	
    	return $this->redirect(['../user/view','id'=>$model->user_id]);
    }
    
    public function actionActivate($id)
    {
    	$model = $this->findModel($id);
    	$model->status = 1;
    	$model->save();
    	 
    	return $this->redirect(['../user/view','id'=>$model->user_id]);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
