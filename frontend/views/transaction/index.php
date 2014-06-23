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
        <?=""//Html::a('Create Transaction', ['create'], ['class' => 'btn btn-success']) ?>
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
    		'description',
            ['attribute'=>'amount',
    		'format'=>'raw',
				'value'=>function($model){
					return $model->getAmount();
			}],
            // 'type',
            // 'foreign_currency_amount',
            // 'foreign_currency_id',
            'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}']
        ],
    ]); ?>

</div>
