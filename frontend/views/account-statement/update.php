<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AccountStatement */

$this->title = 'Update Account Statement: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Account Statements', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="account-statement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
