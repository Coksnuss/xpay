<?php

use yii\db\Schema;

class m140708_143049_account_status_default extends \yii\db\Migration
{
    public function up()
    {
    	$this->alterColumn('{{%account}}', 'status', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
    	$this->alterColumn('{{%user}}', 'last_login_ip', Schema::TYPE_STRING .'(9)');
    }

    public function down()
    {
        echo "m140708_143049_account_status_default cannot be reverted.\n";

        return false;
    }
}
