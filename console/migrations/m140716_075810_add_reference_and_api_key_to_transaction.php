<?php
use yii\db\Schema;
use yii\db\Migration;

class m140716_075810_add_reference_and_api_key_to_transaction extends Migration
{
    public function up()
    {
        $this->addColumn('{{%transaction}}', 'reference', Schema::TYPE_STRING . ' AFTER description');
        $this->addColumn('{{%transaction}}', 'transaction_user_id', Schema::TYPE_INTEGER  . ' AFTER reference');

        $this->addForeignKey('transaction_user_idx',
            '{{%transaction}}', 'transaction_user_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        echo "m140716_075810_add_reference_and_api_key_to_transaction cannot be reverted.\n";

        return false;
    }
}
