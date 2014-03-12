<?php

class m140307_024522_create_file_import_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createFileImportTable();
    }

    public function safeDown()
    {
        $this->dropFileImportTable();
    }
    
    private function createFileImportTable()
    {
        // the file_upload_id field references file_upload.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('file_import', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'file_upload_id' => 'INT(11) NOT NULL',
                'table_count' => 'INT(3) NOT NULL',
                'tables' => 'VARCHAR(1024) NOT NULL',
                'total_records' => 'INT(11) NOT NULL',
                'total_created' => 'INT(11) NOT NULL',
                'total_updated' => 'INT(11) NOT NULL',
                'auto_tagged' => 'BOOLEAN NOT NULL DEFAULT 0',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY file_import_file_upload_id_fk (file_upload_id)',
                'KEY file_import_created_by_id_fk (created_by_id)',
                'KEY file_import_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT file_import_file_upload_id_fk FOREIGN KEY (file_upload_id) REFERENCES file_upload (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_import_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT file_import_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function dropFileImportTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('file_import_file_upload_id_fk', 'file_import');
        $this->dropForeignKey('file_import_created_by_id_fk', 'file_import');
        $this->dropForeignKey('file_import_updated_by_id_fk', 'file_import');
        
        // Now truncate and drop the table
        $this->truncateTable('file_import');
        $this->dropTable('file_import');
    }
}