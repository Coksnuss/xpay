<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use common\models\Account;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<?= Html::csrfMetaTags() ?>
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->params['title'],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $isGuest = Yii::$app->user->isGuest;
            $menuItems = [];
            //$menuItems[] = ['label' => 'Home', 'url' => ['/site/index'], 'visible'=>$isGuest];
            $menuItems[] = ['label' => 'Overview', 'url' => ['/transaction/index'], 'visible'=>!$isGuest];
            //$menuItems[] = ['label' => 'About', 'url' => ['/site/about']];
			//$menuItems[] = ['label' => 'Contact', 'url' => ['/site/contact']];
            $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup'], 'visible'=>$isGuest];
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login'], 'visible'=>$isGuest];
            
            if (!$isGuest) 
			{
				$menuItems[] = [
                    'label' => 'Settings',
					//'label' => 'Dropdown',
		            'items' => [
		                 ['label' => 'General', 'url' => ['/user/view']],
		                 ['label' => 'User Settings', 'url' => ['/user/update']],
		                 ['label' => 'Account Settings', 'url' => ['/account/update']],
		                 ['label' => 'Blacklist Settings', 'url' => ['/shop-blacklist/manage']],
		            ],
                    //'url' => ['/user/view','id'=>Account::findOne(['user_id'=>Yii::$app->user->identity->id])->id],
					'visible'=>!$isGuest
				];
				$menuItems[] = [
                    'label' => 'Logout (' . Yii::$app->user->identity->email . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'], 
					'visible'=>!$isGuest
           		];
			}
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">
        	&copy; <?= Yii::$app->name." ".date('Y') ?>
        	&#124; <?= Html::a('About',['site/about']) ?>
        	&#124; <?= Html::a('Contact',['site/contact']) ?>
        </p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
