<?php
namespace api\rest;

use Yii;
use yii\helpers\Url;

use api\helpers\ErrorCode;

/**
 * Set the location header to a customized view url, using a (non-composite)
 * key, other than the primary key of a record.
 * Also see CheckoutController::$key.
 */
class CreateAction extends \yii\rest\CreateAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = parent::run();

        if (!$model->hasErrors()) {
            $id = $model->{$this->controller->key};
            Yii::$app->getResponse()->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

            return $model;
        }

        return [
            'response' => $model,
            'error' => ['code' => ErrorCode::ERROR_CODE_VALIDATION, 'message' => implode(' ', $model->getFirstErrors())],
        ];
    }
}
