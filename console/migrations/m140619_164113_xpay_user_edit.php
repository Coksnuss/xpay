<?php

use yii\db\Schema;

class m140619_164113_xpay_user_edit extends \yii\db\Migration
{
    public function up()
    {
    	$this->addColumn('{{%user}}', 'preferred_currency', Schema::TYPE_STRING   . '(3) DEFAULT "EUR"');
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'preferred_currency');

        return true;
    }
}
