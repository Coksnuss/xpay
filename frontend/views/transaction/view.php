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
	<p>
    <?= Html::a('Go back',['transaction/index'],['class' => 'btn btn-primary']) ?>
    </p>
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
            ['attribute'=>'description',
    			'format'=>'raw',
    			'value'=>Html::encode($model->description)
			],
			['attribute'=>'type',
				'value'=>$model->getType(),
			],
            ['attribute'=>'amount','format'=>'raw',
				'value'=>Html::tag('div',$model->getAmountString(false),['class'=>'amount-string']),
			],
            //'foreign_currency_id',
            'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
