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
                'arena_id' => 'INT(11) NOT NULL',
                'location_id' => 'INT(11) NULL',
                'name' => 'VARCHAR(128) NOT NULL DEFAULT \'\'',
                'description' => 'TEXT NOT NULL DEFAULT \'\'',
                'tags' => 'VARCHAR(1024) NULL',
                'all_day' => 'BOOLEAN NOT NULL DEFAULT 0',
                'start_date' => 'DATE NOT NULL',
                'start_time' => 'TIME NOT NULL',
                'duration' => 'INT(11) NOT NULL DEFAULT 0',
                'end_date' => 'DATE NOT NULL DEFAULT \'0000-00-00\'',
                'end_time' => 'TIME NOT NULL DEFAULT \'00:00:00\'',
                'price' => 'NUMERIC( 10 , 2 ) NOT NULL DEFAULT 0.00',
                'notes' => 'TEXT NULL',
                'type_id' => 'INT(3) NOT NULL DEFAULT 1',
                'status_id' => 'INT(3) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY arena_id (arena_id)',
                'KEY location_id (location_id)',
                'KEY arena_id_location_id (arena_id, location_id)',
                'KEY name (name)',
                'KEY tags (tags)',
                'KEY start_date (start_date)',
                'KEY start_time (start_time)',
                'KEY all_day (all_day)',
                'KEY end_date (end_date)',
                'KEY end_time (end_time)',
                'KEY price (price)',
                'KEY recurrence_arena_id_fk (arena_id)',
                'KEY recurrence_location_id_fk (location_id)',
                'KEY recurrence_type_id_fk (type_id)',
                'KEY recurrence_status_id_fk (status_id)',
                'KEY recurrence_created_by_id_fk (created_by_id)',
                'KEY recurrence_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT recurrence_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT recurrence_location_id_fk FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT recurrence_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT recurrence_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }
    
    private function dropRecurrenceTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('recurrence_arena_id_fk', 'event');
        $this->dropForeignKey('recurrence_location_id_fk', 'event');
        $this->dropForeignKey('recurrence_created_by_id_fk', 'event');
        $this->dropForeignKey('recurrence_updated_by_id_fk', 'event');

        // Now truncate and drop the table
        $this->truncateTable('event');
        $this->dropTable('event');
    }
}