<?php
namespace api\actions\rest;

use Yii;
use yii\helpers\Url;

use api\helpers\ErrorCode;

/**
 * Allows to define a (non-composite) key, other than the primary key of a
 * record, which will be used to redirect the user after the successful
 * creation of a record.
 */
class CreateAction extends \yii\rest\CreateAction
{
    /**
     * @var string The attribute name which is used as identification for the
     * record. Can differ from the primary key. Non composite keys are not
     * supported.
     */
    public $key = 'id';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = parent::run();

        if (!$model->hasErrors()) {
            $id = $model->{$this->key};
            Yii::$app->getResponse()->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

            return [
                'transaction_id' => $model->transaction_id,
                'errorCode' => ErrorCode::ERROR_CODE_SUCCESS,
                'errorMessage' => '',
            ];
        }

        return $model;
    }
}
