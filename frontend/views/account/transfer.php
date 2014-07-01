<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Charge Amount';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['../user/view']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'iban')->textInput(['maxlength' => 32,'disabled'=>true]) ?>
	<?= $form->field($model, 'bic')->textInput(['maxlength' => 32,'disabled'=>true]) ?>
    <?= $form->field($model, 'amount')->textInput() ?>
    
    <div class="form-group">
        <?= Html::submitButton('Charge Amount', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
