<?php

namespace frontend\controllers;

use Yii;
use common\models\ShopBlacklist;
use common\models\Shop;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\BlacklistForm;

/**
 * ShopBlacklistController implements the CRUD actions for ShopBlacklist model.
 */
class ShopBlacklistController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['manage'],
                'rules' => [
                    [
                        'actions' => ['manage'],
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
    
    public function actionManage()
    {
    	$form = new BlacklistForm();
    	$form->setUser(Yii::$app->user->identity->id);
    	
    	if ($form->load(Yii::$app->request->post()) && $form->save()) {
    		return $this->redirect('../user/view');
    	} else {
    		return $this->render('manage',['model'=>$form]);
    	}
    }

    /**
     * Finds the ShopBlacklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShopBlacklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopBlacklist::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
