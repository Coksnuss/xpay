<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $deleteModel common\models\User */

$this->title = 'Delete';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['../user/view']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($deleteModel, 'iban')->textInput(['maxlength' => 32]) ?>
	<?= $form->field($deleteModel, 'bic')->textInput(['maxlength' => 32]) ?>
    <p>
    If you do not provide a bank account for the remaining amount, we will spend the money on beer.
    </p>
    <div class="form-group">
        <?= Html::a('Delete Account', ['delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
