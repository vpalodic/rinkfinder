<?php

class m140214_233422_create_file_upload_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createFileUploadTable();
    }

    public function safeDown()
    {
        $this->dropFileUploadTable();
    }
    
    private function createFileUploadTable()
    {
        // the user_id field references user.id and
        // the arena_id field references arena.id and
        // the location_id field references location.id
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('file_upload', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'user_id' => 'INT(11) NOT NULL',
                'arena_id' => 'INT(11) NULL',
                'location_id' => 'INT(11) NULL',
                'upload_type_id' => 'INT(3) NOT NULL',
                'name' => 'VARCHAR(255) NOT NULL',
                'path' => 'VARCHAR(511) NOT NULL',
                'uri' => 'VARCHAR(766) NOT NULL',
                'extension' => 'VARCHAR(32) NOT NULL',
                'mime_type' => 'VARCHAR(128) NOT NULL',
                'size' => 'INT(11) NOT NULL',
                'error_code' => 'INT(3) NOT NULL DEFAULT 0',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY file_upload_user_id_fk (user_id)',
                'KEY file_upload_arena_id_fk (arena_id)',
                'KEY file_upload_location_id_fk (location_id)',
                'KEY file_upload_created_by_id_fk (created_by_id)',
                'KEY file_upload_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT file_upload_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT file_upload_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT file_upload_location_id_fk FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT file_upload_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT file_upload_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function dropFileUploadTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('file_upload_user_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_arena_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_location_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_created_by_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_updated_by_id_fk', 'file_upload');
        
        // Now truncate and drop the table
        $this->truncateTable('file_upload');
        $this->dropTable('file_upload');
    }
}