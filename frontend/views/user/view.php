<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Shop;

/* @var $this yii\web\View */
/* @var $userModel common\models\User */

$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update User Settings', ['update'], ['class' => 'btn btn-primary']) ?>
        <?php /*= Html::a('Delete', ['delete', 'id' => $userModel->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $userModel,
        'attributes' => [
            'api_token:raw:API Token (für Paymentsystementwickler)',
            'email:email',
            'last_login_time',
            'last_login_ip',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

    <p>
    <?= Html::a('Update Account Settings', ['../account/update'], ['class' => 'btn btn-primary']) ?>
    <?=Html::a('Charge Amount', ['../account/transfer'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $accountModel,
        'attributes' => [
            'number',
            ['attribute'=>'balance',
				'format'=>'raw',
				'value'=>$accountModel->getBalanceLabel(false),
			],
            'iban',
            'bic',
            ['attribute'=>'status',
				'format'=>'raw',
				'value'=>$accountModel->getStatusLabel(true)
			],
            //'created_at',
            //'updated_at',
            //'preferred_currency',
        ],
    ]) ?>
    <p>
    <?php echo Html::a('Update Blacklist Settings', ['../shop-blacklist/manage'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php if (count(Shop::find()->all())>0):?>
    <?= DetailView::widget([
        'model' => $blacklistForm,
        'attributes' => [
            ['attribute'=>'shop1',
				'format'=>'raw',
				'value'=>$blacklistForm->getStatus(1),
				'visible'=>count(Shop::find()->all())>=1,
			],
            ['attribute'=>'shop2',
				'format'=>'raw',
				'value'=>$blacklistForm->getStatus(2),
				'visible'=>count(Shop::find()->all())>=2,
			],
			['attribute'=>'shop3',
				'format'=>'raw',
				'value'=>$blacklistForm->getStatus(3),
				'visible'=>count(Shop::find()->all())>=3,
			],
        ],
    ]) ?>
	<p>
	<?php endif;?>
        <?= Html::a('Delete Account', ['predelete'], [
            'class' => 'btn btn-danger',
            'data' => [
                //'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])?>
        <?php if ($accountModel->status == 1):?>
        <?= Html::a('Deactivate Account', ['../account/deactivate'], [
            'class' => 'btn btn-danger',
            'data' => [
                //'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])?>
        <?php else:?>
        <?= Html::a('Activate Account', ['../account/activate'], [
            'class' => 'btn btn-success',
            'data' => [
                //'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])?>
        <?php endif;?>
    </p>
</div>
