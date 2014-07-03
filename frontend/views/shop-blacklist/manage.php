<?php

use yii\helpers\Html;
use common\models\Shop;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\BlacklistForm */

$this->title = 'Manage Shop Blacklist';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['../user/view']];
$this->params['breadcrumbs'][] = 'Manage';
?>
<div class="shop-blacklist-update"> 
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if (count(Shop::find()->all())>0):?>
    
    <p>Please select all shops you want to set on your blacklist</p>
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
	<?php $tmp = count(Shop::find()->all());?>
    <p><?php echo ($tmp>0)?$form->field($model, 'shop1')->dropDownList(['Not blocked','Blocked']):""; ?></p>
	<p><?php echo ($tmp>1)?$form->field($model, 'shop2')->dropDownList(['Not blocked','Blocked']):""; ?></p>
   	<p><?php echo ($tmp>2)?$form->field($model, 'shop3')->dropDownList(['Not blocked','Blocked']):""; ?></p>
    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?php else:?>
    
    <p>There are no shops to put on your blacklist.</p>
    
    <?= Html::a('Go back',['user/view'],['class' => 'btn btn-primary']) ?>
        
    <?php endif;?>    

</div>
