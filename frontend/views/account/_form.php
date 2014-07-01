<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Account */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="account-form">

    <?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-4',
            'error' => '',
            'hint' => '',
        ],
    ],
]); ?>

    <?= $form->field($model, 'iban')->textInput() ?>

    <?= $form->field($model, 'bic')->textInput() ?>

   <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>

</div>
