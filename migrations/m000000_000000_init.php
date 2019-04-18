<?php

namespace sjaakp\pluto\migrations;

use yii\db\Migration;

class m000000_000000_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(60)->notNull()->unique(),
            'auth_key' => $this->string(32)->null(),
            'password_hash' => $this->string(128)->null(),
            'token' => $this->string(48)->null()->unique(),
            'email' => $this->string(128)->null()->unique(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(2),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
            'blocked_at' => $this->timestamp()->null(),
            'deleted_at' => $this->timestamp()->null(),
            'lastlogin_at' => $this->timestamp()->null(),
            'login_count' => $this->integer()->unsigned()->defaultValue(0)
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
