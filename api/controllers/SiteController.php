<?php
namespace api\controllers;

use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'transaction_id' => md5('test'),
        ];
    }
}
