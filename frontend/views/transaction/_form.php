<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Transaction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'transaction_id')->textInput(['maxlength' => 32]) ?>

    <?= $form->field($model, 'account_id')->textInput() ?>

    <?= $form->field($model, 'associated_account_number')->textInput(['maxlength' => 11]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => 11]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'foreign_currency_id')->textInput() ?>

    <?= $form->field($model, 'foreign_currency_amount')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'uuid')->textInput(['maxlength' => 32]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
