<?php

use yii\db\Schema;

class m140623_200446_drop_transaction_request_table extends \yii\db\Migration
{
    public function up()
    {
        $this->dropTable('{{transaction}}');
    }

    public function down()
    {
        echo "m140623_200446_drop_transaction_request_table cannot be reverted.\n";

        return false;
    }
}
