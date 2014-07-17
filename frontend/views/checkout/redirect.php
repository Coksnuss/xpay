<?php $this->registerMetaTag([
    'http-equiv' => 'refresh',
    'content' => sprintf('5;url=%s', $redirectUrl),
]); ?>

<p>
    Sie werden in 5 Sekunden zurück zum Anbieter weitergeleitet...<br>
    Falls die Weiterleitung nicht funktionieren sollte, klicken Sie
    <?= \yii\helpers\Html::a('hier', $redirectUrl); ?>
</p>
