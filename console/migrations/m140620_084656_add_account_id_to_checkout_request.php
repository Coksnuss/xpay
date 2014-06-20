<?php

use yii\db\Schema;

class m140620_084656_add_account_id_to_checkout_request extends \yii\db\Migration
{
    public function up()
    {
        // A transaction_id is only given for real transactions that actually
        // were executed. A checkout is only expressing the intention to
        // generate a new transaction, i.e. a checkout_id is temporary while a
        // transaction_id is persistent.
        $this->renameColumn('{{%checkout_request}}', 'transaction_id', 'checkout_id');

        // The account_id is set to the customer which approved the checkout.
        $this->addColumn('{{%checkout_request}}', 'account_id', Schema::TYPE_INTEGER . ' AFTER checkout_id');
        $this->addForeignKey('checkout_request_account_idx',
            '{{%checkout_request}}', 'account_id',
            '{{%account}}', 'id',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m140620_084656_add_account_id_to_checkout_request cannot be reverted.\n";

        return false;
    }
}
