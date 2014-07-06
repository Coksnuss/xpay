<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\helpers\BaseUrl;

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
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
				$menuItems[] = [
					'label' => 'Exchange Rate', 
					'url' => ['/site/exchange-rate']
				];
                $menuItems[] = [
                    'label' => 'Logout (' . Yii::$app->user->identity->email . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
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
        <?php 
        	if(!Yii::$app->user->isGuest) {
				echo '<span class="timer" id="timer">Auto-logout in 20:00</span>';
			}
        ?>
        <?= $content ?>
        </div>
    </div>

    <script type="text/javascript">
	    var counter = 1200;
	    var interval;
	    window.onload = function() {
	    	interval = setInterval(timer, 1000);
	    };
	    function timer() {
		    counter--;
		    if(counter >= 0) {
			    var min, sec;
			    min = Math.floor(counter/60);
			    if(min < 10)
			    min = "0" + min;
			    sec = counter % 60;
			    if(sec < 10)
			    sec = "0" + sec;
			    $("#timer").html("Auto-logout in " + min + ":" + sec);
			} else {
			    clearInterval(interval);
		    }
	    } 
    </script>
    
    <footer class="footer">
        <div class="container">
        <p class="pull-left">
        	&copy; <?= Yii::$app->name." ".date('Y') ?>
        	&#124; 
        	<?php 
            	$url = BaseUrl::base(true); 
            	$urlArray = explode('backend.', $url, 2);
            	echo Html::a('About', $urlArray[0].$urlArray[1].'/site/about');
            ?>
        	&#124;
        	<?php 
            	$url = BaseUrl::base(true); 
            	$urlArray = explode('backend.', $url, 2);
            	echo Html::a('Contact', $urlArray[0].$urlArray[1].'/site/contact');
            ?>
        </p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
