<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Overview';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <div class="right"><?php echo "Account Balance: ".$model->getBalanceLabel();?><br></div>
    </p>

	<br>
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
    		['attribute'=>'description',
				'options'=>['width'=>'40%'],
			],
            ['attribute'=>'amount',
    		'format'=>'raw',
				'value'=>function($model){
					return $model->getAmountString();
			}],
			['attribute'=>'type',
    		'format'=>'raw',
				'value'=>function($model){
					return $model->getType();
			}],
            // 'foreign_currency_amount',
            // 'foreign_currency_id',
            ['attribute'=>'created_at',
    		'format'=>'raw',
            	'value'=>function($model){
					return Html::tag('div',$model->created_at,['class'=>'right monospace']);
			}
			],
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}']
        ],
    ]); ?>


    <p>
        <?= Html::a('Download Account Statement', ['../account-statement/view','id'=>$accountStatementId], ['class' => 'btn btn-success']) ?>
    	<?= Html::a('Archive', ['../account-statement/index'], ['class' => 'btn btn-primary']) ?>
    </p>
</div>
