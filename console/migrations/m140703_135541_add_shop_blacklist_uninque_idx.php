<?php

use yii\db\Schema;
use yii\db\Migration;

class m140703_135541_add_shop_blacklist_uninque_idx extends Migration
{
    public function up()
    {
        $this->createIndex('user_idx_shop_idx_unique', '{{%shop_blacklist}}', ['user_id', 'shop_id'], true);
    }

    public function down()
    {
        echo "m140703_135541_add_shop_blacklist_uninque_idx cannot be reverted.\n";

        return false;
    }
}
