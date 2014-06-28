<?php

use yii\db\Schema;

class m140628_153523_add_to_account extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('{{%account}}', 'iban', Schema::TYPE_INTEGER . ' AFTER balance');
        $this->addColumn('{{%account}}', 'bic', Schema::TYPE_INTEGER . ' AFTER iban');
        $this->addColumn('{{%account}}', 'status', Schema::TYPE_SMALLINT . ' AFTER bic');
    }

    public function down()
    {
        echo "m140628_153523_add_to_account cannot be reverted.\n";

        return false;
    }
}
