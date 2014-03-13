<?php

class m140117_030122_create_tag_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createTagTable();
    }

    public function safeDown()
    {
        $this->dropTagTable();
    }
    
    private function createTagTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('tag', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(128) NOT NULL',
                'frequency' => 'INT(11) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY name (name)',
                'KEY tag_created_by_id_fk (created_by_id)',
                'KEY tag_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT tag_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT tag_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }
    
    private function dropTagTable()
    {
        $this->dropForeignKey('tag_created_by_id_fk', 'tag');
        $this->dropForeignKey('tag_updated_by_id_fk', 'tag');
        $this->truncateTable('tag');
        $this->dropTable('tag');
    }
}

