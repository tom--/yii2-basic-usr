<?php

use yii\db\Schema;
use yii\db\Migration;

class m150207_202323_table_user extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%user}}',
            [
                'id' => 'pk',
                'email' => 'string NOT NULL',
                'password' => 'string NOT NULL',
                'created' => 'datetime',
                'updated' => 'datetime',
                'last_visit' => 'datetime',
                'password_set' => 'datetime',
            ]
        );
        $prefix = $this->db->tablePrefix;
        $this->createIndex($prefix . 'user_email_idx', '{{%user}}', 'email', true);

        $this->insert('{{%user}}', [
            'email' => 'the@user.org',
            // Password1
            'password' => '$2y$10$x30tRFhifFO6QDeZnuGz9OVZpchE2wlezkIH.u8ZO1z0LSdIwdhKu',
            'created' => gmdate('Y-m-d H:i:s'),
            'password_set' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
