<?php

use yii\db\Schema;
use yii\db\Migration;

class m140715_030557_add_cas_logged_in extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'libreid_used', Schema::TYPE_BOOLEAN . ' AFTER updated_at');
        $this->addColumn('{{%user}}', 'secauth_used', Schema::TYPE_BOOLEAN . ' AFTER libreid_used');
    }

    public function down()
    {
        echo "m140715_030557_add_cas_logged_in cannot be reverted.\n";

        return false;
    }
}
