<?php

use yii\db\Schema;

class m140623_162220_change_foreign_currency_amount_type extends \yii\db\Migration
{
    public function up()
    {
		$this->alterColumn('{{%transaction}}', 'foreign_currency_amount', Schema::TYPE_DECIMAL  . '(11,2)');
    }

    public function down()
    {
        echo "m140623_162220_change_foreign_currency_amount_type cannot be reverted.\n";

        return false;
    }
}
