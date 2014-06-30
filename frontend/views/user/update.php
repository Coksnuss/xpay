<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $userModel common\models\User */

$this->title = 'Update User: ' . ' ' . $userModel->email;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $userModel->id, 'url' => ['view', 'id' => $userModel->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $userModel,
    ]) ?>

</div>
