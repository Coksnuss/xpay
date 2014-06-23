<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transactions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Transaction', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'transaction_id',
            //'uuid',
            //'account_id',
            'associated_account_number',
    		'description',
            ['attribute'=>'amount',
				'value'=>function($model){
					$s = $model->amount;
					$preferredCurrency = $model->account->preferredCurrency;
					if ($preferredCurrency->iso_4217_name !== 'EUR')
						$s = $model->foreign_currency_amount;
					return $s." ".$preferredCurrency->iso_4217_name;
			}],
            // 'type',
            // 'foreign_currency_amount',
            // 'foreign_currency_id',
            'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
