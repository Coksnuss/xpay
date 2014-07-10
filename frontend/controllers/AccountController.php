<?php

namespace frontend\controllers;

use Yii;
use common\models\Account;
use common\models\base\AccountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\TransferForm;
use yii\web\BadRequestHttpException;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
{
    /**
     * (non-PHPdoc)
     * @see \yii\base\Component::behaviors()
     */
	public function behaviors()
    {
        return [
        	'access' => [
                'class' => AccessControl::className(),
                'only' => ['update','transfer','activate','deactivate'],
                'rules' => [
                    [
                        'actions' => ['update','transfer','activate','deactivate'],
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
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->user->identity->id);
    	if (isset($model)){
	    	if ($model->load(Yii::$app->request->post()) && $model->save()){
	        	return $this->redirect(['../user/view']); 
	        } else {
	            return $this->render('update', [
	                'model' => $model,
	            ]);
	        }
		}else{
    		throw new NotFoundHttpException();
    	}
    }
    
    /**
     * 
     * @param int $id
     * @return Ambigous <\yii\web\Response, \yii\web\static, \yii\web\Response>|string
     */
    public function actionTransfer()
    {
    	$model = $this->findModel(Yii::$app->user->identity->id);
    	if (isset($model)){
	    	$form = new TransferForm();
	    	$form->load(['TransferForm'=>['iban'=>$model->iban,'bic'=>$model->bic]]);
	    	if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->transfer($model)){
	    		return $this->redirect(['../user/view']);
	    	} else {
	    		return $this->render('transfer', [
	    				'model' => $form,
	    				]);
    		}
    	}else{
    		throw new NotFoundHttpException();
    	}
    }
    
    /**
     * Deactivates the account
     * 
     * @param int $id
     * @return Ambigous <\yii\web\Response, \yii\web\static, \yii\web\Response>
     */
    public function actionDeactivate()
    {
    	$model = $this->findModel(Yii::$app->user->identity->id);
    	if (isset($model)){
	    	$model->status = 0;
	    	$model->save(false);
    		return $this->redirect(['../user/view']);
    	}else{
    		throw new NotFoundHttpException();
    	}
    }
    
    /**
     * Activates the account
     * 
     * @param int $id
     * @return Ambigous <\yii\web\Response, \yii\web\static, \yii\web\Response>
     */
    public function actionActivate()
    {
    	$model = $this->findModel(Yii::$app->user->identity->id);
    	if (isset($model)){
	    	$model->status = 1;
	    	$model->save(false);
    		return $this->redirect(['../user/view']);
    	}else{
    		throw new NotFoundHttpException();
    	}
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
        if (($model = Account::findOne(['user_id'=>$id])) !== null) {
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
