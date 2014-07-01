<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\bootstrap\ActiveForm */
?>


<div class="user-form">

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-6',
            'error' => '',
            'hint' => '',
        ],
    ],
]); ?>

    <?= $form->field($model, 'current_password')->passwordInput(['maxlength' => 32]) ?>
	<?= $form->field($model, 'new_password')->passwordInput(['maxlength' => 32]) ?>
    <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => 32]) ?>
    
    <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
