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

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <link rel="stylesheet" type="text/css" href="http://libreid.wsp.lab.sit.cased.de/static/lidbutton.css">
            <form action="https://libreid.wsp.lab.sit.cased.de/api/login/<?= Yii::$app->libreidapi->apiKey ?>/" method="post">
                <input type="text" name="message" value="<?= $message ?>" hidden="hidden" />
                <input type="text" name="return_url" value="<?= $returnUrl ?>" hidden="hidden" />
                <button onclick="submit();" id="lidbutton">
                    <img src="http://libreid.wsp.lab.sit.cased.de/static/lid_logo20.png" id="lidlogo"/>Login Using LibreID
                </button>
            </form>
            <?php /*$form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput(['autocomplete'=>'offe']) ?>
                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php ActiveForm::end(); */?>
        </div>
    </div>
</div>