<?php
namespace api\controllers;

use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;

use api\helpers\ErrorCode;

/**
 * API controller
 */
class ApiController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
        ];
    }

    /**
     * The default error handler.
     *
     * @return array An error array containing the error code and the
     * corresponding error message indexed by the keys 'errorCode' and
     * 'errorMessage'.
     */
    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            return [
                'errorCode' => ErrorCode::ERROR_CODE_UNKNOWN,
                'errorMessage' => 'Unknown error',
            ];
        }

        if ($exception instanceof \yii\web\HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        if ($exception instanceof \yii\base\UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }

        return [
            'errorCode' => $code,
            'errorMessage' => $message,
        ];
    }
}
