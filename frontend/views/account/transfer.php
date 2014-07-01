<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Charge Amount';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['../user/view']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($model->iban==null || $model->bic==null):?>
    
    <p>You do not provide any bank account information. Please check your Account settings.</p>
    
    <?= Html::a('Update Account Settings', ['../account/update'], ['class' => 'btn btn-primary']) ?>
        
    <?php else:?>
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
	
    <?= $form->field($model, 'iban')->textInput(['maxlength' => 32,'disabled'=>true]) ?>
	<?= $form->field($model, 'bic')->textInput(['maxlength' => 32,'disabled'=>true]) ?>
    <?= $form->field($model, 'amount')->textInput(['placeholder'=>'Type in a positiv amount']) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Charge Amount', ['class' => 'btn btn-primary']) ?>
    </div>
	
    <?php ActiveForm::end(); ?>
    <?php endif;?>
</div>
