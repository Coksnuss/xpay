<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'current_password')->passwordInput(['maxlength' => 32]) ?>
	<?= $form->field($model, 'new_password')->passwordInput(['maxlength' => 32]) ?>
    <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => 32]) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>