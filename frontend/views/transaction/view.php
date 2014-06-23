<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Transaction */

$this->title = 'Details';// of Transaction '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Go back',['transaction/index'],['class' => 'btn btn-primary']) ?>
   <p>
       <!--      <?//= Html::a('Delete', ['delete', 'id' => $model->id], [
//             'class' => 'btn btn-danger',
//             'data' => [
//                 'confirm' => 'Are you sure you want to delete this item?',
//                 'method' => 'post',
//             ],
//         ]) ?>
   --> </p>	
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'transaction_id',
            //'uuid',
            ['attribute'=>'account_id',
				'label'=>'Account Number',
				'format'=>'raw',
				'value'=>$model->account->number,
			],
            'associated_account_number',
            'description',
			['attribute'=>'type',
				'value'=>$model->getType(),
			],
            ['attribute'=>'amount','format'=>'raw',
				'value'=>Html::tag('div',$model->getAmount(),['class'=>'amount-string']),
			],
            //'foreign_currency_id',
            'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
