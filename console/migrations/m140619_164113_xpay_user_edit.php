<?php

use yii\db\Schema;

class m140619_164113_xpay_user_edit extends \yii\db\Migration
{
    public function up()
    {
    	$this->addColumn('{{%account}}', 'preferred_currency',Schema::TYPE_INTEGER  . ' NOT NULL');
    	$this->addForeignKey('account_currency_idx', 
    			'{{%account}}', 
    			'preferred_currency', 
    			'{{%currency}}',
    			'id',
    			'CASCADE', 'CASCADE');
    }

    public function down()
    {
        return true;
    }
}
