<?php
/**
 * @var yii\web\View $this
 */
$this->title = 'My Yii Application';

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="site-index">

    <div class="jumbotron">
    <?php 
    	if(Yii::$app->user->isGuest) {
			echo '<h1>Welcome to '.Yii::$app->name.'!</h1>';
			echo '<p class="lead">'.Yii::$app->name.' is a payment service delevoped for <br>WASPL14 @ TU Darmstadt</p>';
		} else {
			echo '<h1>Welcome to '.Yii::$app->name.',<br>'.$first_name.' '.$last_name.'!</h1>';
			echo '<p class="lead">'.Yii::$app->name.' is a payment service delevoped for <br>WASPL14 @ TU Darmstadt</p>';
			if($loginTime === null)
				echo '<p class="lead">This is your first time loggin in.</p>';
			else 
				echo '<p class="lead">Your last login was on '.$loginTime.'.</p>';
		}		
    ?>

    </div>

    <div class="body-content">

    </div>
</div>
