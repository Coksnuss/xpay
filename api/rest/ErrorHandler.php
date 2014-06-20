<?php
namespace api\rest;

use Yii;
use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Ensures that fatal errors are also display as json/xml output.
     */
    protected function renderException($exception)
    {
        if (\Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
        } else {
            $response = new Response();
        }

        if ($this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);

            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        }

        $response->send();
    }
}
