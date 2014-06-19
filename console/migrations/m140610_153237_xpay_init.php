<?php

use yii\db\Schema;

class m140610_153237_xpay_init extends \yii\db\Migration
{
    // CREATE DATABASE `xpay` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id'                   => Schema::TYPE_PK,
            'first_name'           => Schema::TYPE_STRING   . ' NOT NULL',
            'last_name'            => Schema::TYPE_STRING   . ' NOT NULL',
            'email'                => Schema::TYPE_STRING   . ' NOT NULL',
            'auth_key'             => Schema::TYPE_STRING   . '(32) NOT NULL',
            'password_hash'        => Schema::TYPE_STRING   . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'api_token'            => Schema::TYPE_STRING   . '(32) NOT NULL',
            'role'                 => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'status'               => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'last_login_time'      => Schema::TYPE_DATETIME,
            'last_login_ip'        => Schema::TYPE_INTEGER  . ' UNSIGNED',
            'created_at'           => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'           => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%account}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER  . ' NOT NULL',
            'number'      => Schema::TYPE_INTEGER  . ' UNSIGNED NOT NULL',
            'balance'     => Schema::TYPE_DECIMAL  . '(11,2) NOT NULL',
            'created_at'  => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'  => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%transaction}}', [
            'id'                        => Schema::TYPE_PK,
            'transaction_id'            => Schema::TYPE_STRING   . '(32) NOT NULL',
            'uuid'                      => Schema::TYPE_STRING   . '(32)',
            'account_id'                => Schema::TYPE_INTEGER  . ' NOT NULL',
            'associated_account_number' => Schema::TYPE_INTEGER  . ' UNSIGNED NOT NULL',
            'type'                      => Schema::TYPE_SMALLINT . ' NOT NULL',
            'amount'                    => Schema::TYPE_DECIMAL  . '(11,2) NOT NULL',
            'foreign_currency_amount'   => Schema::TYPE_DECIMAL,
            'foreign_currency_id'       => Schema::TYPE_INTEGER,
            'description'               => Schema::TYPE_STRING   . ' NOT NULL',
            'created_at'                => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'                => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%accountStatement}}', [
            'id'                      => Schema::TYPE_PK,
            'account_id'              => Schema::TYPE_INTEGER  . ' NOT NULL',
            'date'                    => Schema::TYPE_DATE     . ' NOT NULL',
            'email_notification_send' => Schema::TYPE_BOOLEAN  . ' NOT NULL',
            'created_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%shop}}', [
            'id'         => Schema::TYPE_PK,
            'name'       => Schema::TYPE_STRING   . ' NOT NULL',
            'url'        => Schema::TYPE_STRING   . ' NOT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%shopBlacklist}}', [
            'id'          => Schema::TYPE_PK,
            'user_id'     => Schema::TYPE_INTEGER  . ' NOT NULL',
            'shop_id'     => Schema::TYPE_INTEGER  . ' NOT NULL',
            'created_at'  => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'  => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%currency}}', [
            'id'                => Schema::TYPE_PK,
            'iso_4217_name'     => Schema::TYPE_STRING   . '(3) NOT NULL',
            'eur_exchange_rate' => Schema::TYPE_FLOAT    . '(8,5) UNSIGNED NOT NULL',
            'created_at'        => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'        => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%checkoutRequest}}', [
            'id'                      => Schema::TYPE_PK,
            'transaction_id'          => Schema::TYPE_STRING   . '(32) NOT NULL',
            'amount'                  => Schema::TYPE_DECIMAL  . '(11,2) NOT NULL',
            'currency'                => Schema::TYPE_STRING   . '(3) NOT NULL',
            'receiver_account_number' => Schema::TYPE_INTEGER  . ' UNSIGNED NOT NULL',
            'tax'                     => Schema::TYPE_FLOAT    . '(3,2)',
            'return_url'              => Schema::TYPE_STRING   . ' NOT NULL',
            'cancel_url'              => Schema::TYPE_STRING   . ' NOT NULL',
            'description'             => Schema::TYPE_STRING   . ' NOT NULL',
            'reference'               => Schema::TYPE_STRING,
            'type'                    => Schema::TYPE_SMALLINT . ' NOT NULL',
            'created_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%transactionRequest}}', [
            'id'                      => Schema::TYPE_PK,
            'uuid'                    => Schema::TYPE_STRING   . ' NOT NULL',
            'sender_account_number'   => Schema::TYPE_INTEGER  . ' UNSIGNED NOT NULL',
            'receiver_account_number' => Schema::TYPE_INTEGER  . ' UNSIGNED NOT NULL',
            'amount'                  => Schema::TYPE_DECIMAL  . '(11,2) NOT NULL',
            'currency'                => Schema::TYPE_STRING   . '(3) NOT NULL',
            'description'             => Schema::TYPE_STRING   . ' NOT NULL',
            'type'                    => Schema::TYPE_SMALLINT . ' NOT NULL',
            'transaction_id'          => Schema::TYPE_STRING,
            'created_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at'              => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);


        // Unique Idx
        $this->createIndex('email_unique', '{{%user}}', 'email', true);
        $this->createIndex('number_unique', '{{%account}}', 'number', true);
        $this->createIndex('transaction_id_unique', '{{%transaction}}', 'transaction_id', true);
        $this->createIndex('transaction_id_unique', '{{%checkoutRequest}}', 'transaction_id', true);
        $this->createIndex('iso_4217_name_unique', '{{%currency}}', 'iso_4217_name', true);
        $this->createIndex('uuid_unique', '{{%transaction}}', 'uuid', true);
        $this->createIndex('uuid_unique', '{{%transactionRequest}}', 'uuid', true);

        // Foreign key constraints
        $this->addForeignKey('account_user_idx',
            '{{%account}}', 'user_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE');
        $this->addForeignKey('transaction_account_idx',
            '{{%transaction}}', 'account_id',
            '{{%account}}', 'id',
            'RESTRICT', 'CASCADE');
        $this->addForeignKey('accountstatement_account_idx',
            '{{%accountStatement}}', 'account_id',
            '{{%account}}', 'id',
            'CASCADE', 'CASCADE');
        $this->addForeignKey('shopblacklist_user_idx',
            '{{%shopBlacklist}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE');
        $this->addForeignKey('shop_blacklist_shop_idx',
            '{{%shopBlacklist}}', 'shop_id',
            '{{%shop}}', 'id',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m140610_153237_xpay_init cannot be reverted.\n";

        return false;
    }
}
