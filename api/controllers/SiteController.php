<?php
namespace api\controllers;

use Yii;
use cebe\markdown\GithubMarkdown;

class SiteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $mdParser = new GithubMarkdown();
        $markdown = file_get_contents(Yii::getAlias('@api/data/DOCUMENTATION.md'));

        return $this->render('index', [
            'markdown' => $mdParser->parse($markdown),
        ]);
    }
}
