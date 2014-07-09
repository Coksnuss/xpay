<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\base\AccountStatementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Statements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-statement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'date',
            ['attribute'=>'email_notification_send',
				'format'=>'raw',
    			'value'=>function($model){return ($model->email_notification_send)?'YES':'NOT YET';},
			],
            //'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>

</div>
