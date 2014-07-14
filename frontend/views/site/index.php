<?php
/**
 * @var yii\web\View $this
 */
$this->title = 'My Yii Application';

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome to <?= Yii::$app->name?>!</h1>
        <?= yii\authclient\widgets\AuthChoice::widget([
     'baseAuthUrl' => ['site/auth']
]) ?>

        <p class="lead"><?= Yii::$app->name?> is a payment service delevoped for <br>WASPL14 @ TU Darmstadt</p>

    </div>

    <div class="body-content">

    </div>
</div>
