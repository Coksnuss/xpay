<?php

use yii\db\Schema;
use yii\db\Expression;

class m140619_211001_register_currency extends \yii\db\Migration
{
    public function up()
    {
        $this->insert('{{%currency}}', [
            'iso_4217_name' => 'EUR',
            'eur_exchange_rate' => '1',
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);

        $this->insert('{{%currency}}', [
            'iso_4217_name' => 'USD',
            'eur_exchange_rate' => '0.734926654',
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);
    }

    public function down()
    {
        echo "m140619_211001_register_currency cannot be reverted.\n";

        return false;
    }
}
