<?php

use yii\db\Schema;

class m140701_154456_edit_IBAN_and_BIC extends \yii\db\Migration
{
    public function up()
    {
        $this->alterColumn('{{%account}}', 'iban', Schema::TYPE_STRING   . '(34)');
        $this->alterColumn('{{%account}}', 'bic', Schema::TYPE_STRING   . '(34)');
    }

    public function down()
    {
        echo "m140701_154456_edit_IBAN_and_BIC cannot be reverted.\n";

        return false;
    }
}
