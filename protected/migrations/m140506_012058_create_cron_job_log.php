<?php

class m140506_012058_create_cron_job_log extends CDbMigration
{
    public function safeUp()
    {
        $this->createCronJobLogTable();
    }

    public function safeDown()
    {
        $this->dropCronJobLogTable();
    }
    
    protected function createCronJobLogTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('cron_job_log', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(1024) NOT NULL',
                'parameters' => 'TEXT NULL',
                'succeeded' => 'BOOLEAN NOT NULL DEFAULT 0',
                'output' => 'TEXT NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY cron_job_log_created_by_id_fk (created_by_id)',
                'KEY cron_job_log_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT cron_job_log_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL',
                'CONSTRAINT cron_job_log_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }
    
    protected function dropCronJobLogTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('cron_job_log_created_by_id_fk', 'file_import');
        $this->dropForeignKey('cron_job_log_updated_by_id_fk', 'file_import');
        
        // Now truncate and drop the table
        $this->truncateTable('cron_job_log');
        $this->dropTable('cron_job_log');
    }
}