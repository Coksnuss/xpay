<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $userModel common\models\User */

$this->title = 'Update User: ' . ' ' . $userModel->email;
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['view', 'id' => $userModel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $userModel,
    ]) ?>

</div>
