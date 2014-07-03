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
use yii\filters\AccessControl;
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
    	$form->setUser();
    	if (isset($_POST['BlacklistForm']) && $form->load(Yii::$app->request->post())) {
    		if(isset($_POST['BlacklistForm']['shop1']))
    			$form->shop1 = $_POST['BlacklistForm']['shop1'];
    		if(isset($_POST['BlacklistForm']['shop2']))
    			$form->shop2 = $_POST['BlacklistForm']['shop2'];
    		if(isset($_POST['BlacklistForm']['shop3']))
    			$form->shop3 = $_POST['BlacklistForm']['shop3'];
    		if ($form->save())
    			return $this->redirect('../user/view');
    		else
    			return $this->render('manage',['model'=>$form]);
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
