<?php

use yii\helpers\Html;
use common\models\Shop;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\BlacklistForm */

$this->title = 'Manage Shop Blacklist';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Manage';
?>
<div class="shop-blacklist-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>Please select all shops you want to set on your blacklist</p>
    <?php $form = ActiveForm::begin(); ?>
	<?php $tmp = count(Shop::find()->all());?>
    <?php echo ($tmp>0)?$form->field($model, 'shop1')->checkbox():""; ?>
	<?php echo ($tmp>1)?$form->field($model, 'shop2')->checkbox():""; ?>
    <?php echo ($tmp>2)?$form->field($model, 'shop3')->checkbox():""; ?>
    
    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
