<?php
namespace common\components;

use Yii;
use yii\base\Application;
use yii\helpers\Url;

/**
 * Enforces HTTPS schema by redirecting the user if HTTP is used.
 */
class HTTPSBehavior extends \yii\base\Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [Application::EVENT_BEFORE_REQUEST => 'ensureHttps'];
    }

    public function ensureHttps($event)
    {
        if (!Yii::$app->request->isSecureConnection) {
            // TODO: Check if verb != GET. If yes: Print error message.
            Yii::$app->response->redirect(Url::to('', 'https'));
            Yii::$app->end(1);
        }
    }
}
