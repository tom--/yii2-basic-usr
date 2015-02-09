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
                'created_on' => 'datetime',
                'updated_on' => 'datetime',
                'last_visit_on' => 'datetime',
                'password_set_on' => 'datetime',
            ]
        );
        $prefix = $this->db->tablePrefix;
        $this->createIndex($prefix . 'users_email_idx', '{{%user}}', 'email', true);

        $this->insert('{{%user}}', [
            'email' => 'the@user.org',
            'password' => '$2y$10$Uci808uOm5MfE/SHPSqHyurPB8kAST4hWc7gFj/QnkWQtzf.50TVe',
            'created_on' => gmdate('Y-m-d H:i:s'),
            'password_set_on' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
