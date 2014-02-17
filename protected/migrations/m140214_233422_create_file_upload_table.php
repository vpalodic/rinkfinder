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
        // the arena_id field references arena.id and
        // the event_type_id field references event_type.id
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('file_upload', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'user_id' => 'INT(11) NOT NULL',
                'arena_id' => 'INT(11) NULL',
                'ice_sheet_id' => 'INT(11) NULL',
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
                'KEY file_upload_ice_sheet_id_fk (ice_sheet_id)',
                'KEY file_upload_created_by_id_fk (created_by_id)',
                'KEY file_upload_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT file_upload_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_upload_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_upload_ice_sheet_id_fk FOREIGN KEY (ice_sheet_id) REFERENCES ice_sheet (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_upload_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_upload_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function dropFileUploadTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('file_upload_user_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_arena_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_ice_sheet_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_created_by_id_fk', 'file_upload');
        $this->dropForeignKey('file_upload_updated_by_id_fk', 'file_upload');
        
        // Now truncate and drop the table
        $this->truncateTable('file_upload');
        $this->dropTable('file_upload');
    }
}