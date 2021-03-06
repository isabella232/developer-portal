<?php

class m160603_143202_further_update_user_table extends CDbMigration
{
    public function safeUp()
    {
        $this->alterColumn('{{user}}', 'status', 'tinyint(1) NOT NULL DEFAULT 1');
        $this->alterColumn('{{user}}', 'created', 'datetime NOT NULL');
        
        // Added 2018-04-18 to prevent error from setting `updated` to NOT NULL
        // when many records are NULL.
        $this->execute('UPDATE {{user}} SET `updated` = `created` WHERE `updated` IS NULL');
        
        $this->alterColumn('{{user}}', 'updated', 'datetime NOT NULL');
        $this->createIndex(
            'uq_user_auth_provider_user_identifier',
            '{{user}}',
            'auth_provider_user_identifier',
            true
        );
    }

    public function safeDown()
    {
        $this->dropIndex('uq_user_auth_provider_user_identifier', '{{user}}');
        $this->alterColumn('{{user}}', 'updated', 'datetime DEFAULT NULL');
        $this->alterColumn('{{user}}', 'created', 'datetime DEFAULT NULL');
        $this->alterColumn('{{user}}', 'status', 'tinyint(1) NOT NULL');
    }
}
