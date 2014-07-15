<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\Currency;
use common\models\Transaction;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \frontend\models\ConfirmCheckoutForm $model
 * @var \common\models\CheckoutRequest $checkout
 */
$this->title = 'Bezahlvorgang bestätigen';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checkout-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Bitte bestätigen sie nachfolgenden Zahlungsvorgang:</p>

    <div class="row">
        <div class="col-lg-8">
            <?= Html::beginForm(); ?>
                <table class="table">
                    <tr>
                        <th>Zahlungsart</th>
                        <td><?= Transaction::getHumanReadableTypeById($checkout->type) ?></td>
                    </tr>
                    <tr>
                        <th>Zahlungsbetrag</th>
                        <td><?= Yii::$app->formatter->asNumber($checkout->amount, 2) ?> <?= $checkout->currency; ?></td>
                    </tr>
                    <?php if (!$checkout->isPaidInPrimaryCurrency()): ?>
                    <tr>
                        <td>(in <?= Currency::getPrimaryCurrencyCode(); ?>)</td>
                        <td><?= Yii::$app->formatter->asNumber($checkout->getAmountInPrimaryCurrency(), 2) ?> <?= Currency::getPrimaryCurrencyCode(); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Zahlungszweck</th>
                        <td><?= $checkout->description ?></td>
                    </tr>
                    <tr>
                        <th>Empfängerkonto</th>
                        <td><?= $checkout->receiver_account_number ?></td>
                    </tr>
                    <?php if ($checkout->tax > 0): ?>
                    <tr>
                        <th>Steuer</th>
                        <td><?= ($checkout->tax * 100) ?>%</td>
                    </tr>
                    <tr>
                        <td>(in <?= $checkout->currency ?>)</td>
                        <td><?= Yii::$app->formatter->asNumber($checkout->amount * $checkout->tax, 2) ?> <?= $checkout->currency ?></td>
                    </tr>
                    <?php if (!$checkout->isPaidInPrimaryCurrency()): ?>
                    <tr>
                        <td>(in <?= Currency::getPrimaryCurrencyCode(); ?>)</td>
                        <td><?= Yii::$app->formatter->asNumber($checkout->getAmountInPrimaryCurrency() * $checkout->tax, 2) ?> <?= Currency::getPrimaryCurrencyCode(); ?></td>
                    </tr>
                    <?php endif; endif; ?>
                </table>

                <div class="form-group">
                    <?= Html::submitButton('Zahlung bestätigen', ['class' => 'btn btn-primary', 'name' => 'approve-button']) ?>
                </div>
            <?= Html::endForm(); ?>
        </div>
    </div>
</div>
