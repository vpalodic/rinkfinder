<?php

class m140119_210354_create_recurrence_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createRecurrenceTable();
    }

    public function safeDown()
    {
        $this->dropRecurrenceTable();
    }
    
    private function createRecurrenceTable()
    {
        // the arena_id field references location.id and
        // the location_id field references location.id and
        // the type_id field references recurrence_type.id and
        // the status_id field references recurrence_status.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('recurrence', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'start_date' => 'DATE NOT NULL',
                'type' => 'INT(3) NOT NULL DEFAULT 1',
                'interval' => 'INT(3) NOT NULL DEFAULT 0',
                'relative_interval' => 'INT(3) NOT NULL DEFAULT 0',
                'factor' => 'INT(3) NOT NULL DEFAULT 0',
                'occurrences' => 'INT(3) NULL',
                'end_date' => 'DATE NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY start_date (start_date)',
                'KEY end_date (end_date)',
                'KEY recurrence_created_by_id_fk (created_by_id)',
                'KEY recurrence_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT recurrence_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT recurrence_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }
    
    private function dropRecurrenceTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('recurrence_created_by_id_fk', 'recurrence');
        $this->dropForeignKey('recurrence_updated_by_id_fk', 'recurrence');

        // Now truncate and drop the table
        $this->truncateTable('recurrence');
        $this->dropTable('recurrence');
    }
}