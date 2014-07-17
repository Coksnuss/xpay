<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \common\models\LoginForm $model
 */
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->session->hasFlash('autoLogout')):?>
        <div class="has-error">
            <div class="help-block">
                <?php echo Yii::$app->session->getFlash('autoLogout'); ?>
            </div>
        </div>
    <?php endif; ?>


    <div class="row">
        <div class="col-lg-5">
            <link rel="stylesheet" type="text/css" href="http://libreid.wsp.lab.sit.cased.de/static/lidbutton.css">
            <form action="<?php echo $url ?>" method="get">
                <button onclick="submit();" id="secauthbutton">Login using SecAuth.</button>
            </form>
        </div>
    </div>
    <p>If the CAS is not available, use the <a href="/site/login">fallback</a>.</p>
</div>
