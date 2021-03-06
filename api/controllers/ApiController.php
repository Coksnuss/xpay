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
        $response = ['result' => new \stdClass()];

        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $response['error'] = [
                'code' => ErrorCode::ERROR_CODE_UNKNOWN,
                'message' => 'Unknown error',
            ];

            return $response;
        }

        if ($exception instanceof \yii\web\HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        if ($exception instanceof \yii\base\UserException) {
            $message = $exception->getMessage();
        } else if ($exception instanceof \yii\base\Exception) {
            $message = $exception->getName();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }

        $response['error'] = [
            'code' => $code,
            'message' => $message,
        ];

        return $response;
    }
}
