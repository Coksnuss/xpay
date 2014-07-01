<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\models\base\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Account;
use frontend\models\DeleteForm;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view','update','predelete','delete'],
                'rules' => [
                    [
                        'actions' => ['view','update','predelete','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
    	$userModel = $this->findModel(Yii::$app->user->identity->id);
    	$accountModel = Account::findOne(['user_id'=>Yii::$app->user->identity->id]);
    	
    	return $this->render('view', [
            'userModel' => $userModel,'accountModel'=>$accountModel,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $userModel = $this->findModel(Yii::$app->user->identity->id);
        
        if ($userModel->load(Yii::$app->request->post()) && $userModel->save()) {
            return $this->redirect(['view', 'id' => Yii::$app->user->identity->id]);
        } else {
            return $this->render('update', [
                'userModel' => $userModel,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $model = $this->findModel(Yii::$app->user->identity->id);
        if(isset($model)){
        	$model->status = 0;
			$model->save();
        	Yii::$app->user->logout();
        }
        return $this->goHome();
    }
    
    /**
     * Set up predelete page to enter iban and bic to transfer remaining amount to.
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionPredelete(){
    	$deleteForm = new DeleteForm();

    	return $this->render('predelete', [
                'deleteModel' => $deleteForm,
            ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
