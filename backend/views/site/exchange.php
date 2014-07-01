<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Exchange Rate';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchange">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>Set the value of one EUR in USD here:</p>
        
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rate')->input('text', ['value'=>$model->rate]) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Set', ['class' => 'btn btn-primary', 'name' => 'rate-submit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>