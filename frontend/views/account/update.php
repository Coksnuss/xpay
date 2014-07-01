<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = 'Update Account: ' . ' ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
