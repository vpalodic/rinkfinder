<?php

class m140119_210454_create_event_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createEventTypeTable();
        $this->createEventStatusTable();
        $this->createEventTable();
        
        $this->insertEventTypeValues();
        $this->insertEventStatusValues();
    }

    public function safeDown()
    {
        $this->dropEventTable();
        $this->dropEventStatusTable();
        $this->dropEventTypeTable();
    }
    
    private function createEventTypeTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('event_type', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(32) NOT NULL',
                'display_name' => 'VARCHAR(32) NOT NULL',
                'display_order' => 'INT(3) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'active' => 'BOOLEAN NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY name (name)',
                'UNIQUE KEY display_name (display_name)',
                'KEY active (active)',
                'KEY event_type_created_by_id_fk (created_by_id)',
                'KEY event_type_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_type_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_type_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 9'
        );
    }

    private function createEventStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('event_status', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(32) NOT NULL',
                'display_name' => 'VARCHAR(32) NOT NULL',
                'display_order' => 'INT(3) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'active' => 'BOOLEAN NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY name (name)',
                'UNIQUE KEY display_name (display_name)',
                'KEY active (active)',
                'KEY event_status_created_by_id_fk (created_by_id)',
                'KEY event_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 4'
        );
    }

    private function createEventTable()
    {
        // the arena_id field references location.id and
        // the location_id field references location.id and
        // the type_id field references event_type.id and
        // the status_id field references event_status.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('event', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'arena_id' => 'INT(11) NOT NULL',
                'location_id' => 'INT(11) NULL',
                'external_id' => 'VARCHAR(32) NULL',
                'recurrence_id' => 'INT(11) NULL',
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
                'UNIQUE KEY arena_id_external_id (arena_id, external_id)',
                'KEY external_id (external_id)',
                'KEY arena_id (arena_id)',
                'KEY location_id (location_id)',
                'KEY arena_id_location_id (arena_id, location_id)',
                'KEY recurrence_id (recurrence_id)',
                'KEY name (name)',
                'KEY tags (tags)',
                'KEY start_date (start_date)',
                'KEY start_time (start_time)',
                'KEY all_day (all_day)',
                'KEY end_date (end_date)',
                'KEY end_time (end_time)',
                'KEY price (price)',
                'KEY event_arena_id_fk (arena_id)',
                'KEY event_location_id_fk (location_id)',
                'KEY event_recurrence_id_fk (recurrence_id)',
                'KEY event_type_id_fk (type_id)',
                'KEY event_status_id_fk (status_id)',
                'KEY event_created_by_id_fk (created_by_id)',
                'KEY event_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT event_location_id_fk FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE CASCADE ON DELETE SET NULL',
                'CONSTRAINT event_recurrence_id_fk FOREIGN KEY (recurrence_id) REFERENCES recurrence (id) ON UPDATE CASCADE ON DELETE SET NULL',
                'CONSTRAINT event_type_id_fk FOREIGN KEY (type_id) REFERENCES event_type (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT event_status_id_fk FOREIGN KEY (status_id) REFERENCES event_status (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT event_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT event_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function insertEventTypeValues()
    {
        // insert the seven default status values
        $this->insert('event_type', array(
                'id' => 1,
                'name' => 'FOR_SALE',
                'display_name' => 'Ice For Sale',
                'display_order' => 1,
                'description' => 'Ice time available for purchase by the public for the entire ice sheet.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 2,
                'name' => 'OPEN_SKATE',
                'display_name' => 'Open Skate',
                'display_order' => 2,
                'description' => 'Ice time available for pleasure skating that is open to the public.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 3,
                'name' => 'OPEN_HOCKEY',
                'display_name' => 'Open Hockey',
                'display_order' => 3,
                'description' => 'Ice time available for playing pick-up hockey that is open to the public.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 4,
                'name' => 'OPEN_FREESTYLE',
                'display_name' => 'Open Freestyle',
                'display_order' => 4,
                'description' => 'Ice time available for freestyle skating that is open to the public.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 5,
                'name' => 'OPEN_EVENT',
                'display_name' => 'Open Event',
                'display_order' => 5,
                'description' => 'An event being held that charges an admission fee.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 6,
                'name' => 'GAME',
                'display_name' => 'Game',
                'display_order' => 6,
                'description' => 'A game that charges an admission fee.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 7,
                'name' => 'TOURNAMENT',
                'display_name' => 'Tournament',
                'display_order' => 7,
                'description' => 'A tournament that charges an admission fee.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_type', array(
                'id' => 8,
                'name' => 'RESERVED',
                'display_name' => 'Reserved',
                'display_order' => 8,
                'description' => 'Ice time that has already been reserved prior to being listed and is therefore not available for sale.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertEventStatusValues()
    {
        // insert the seven default status values
        $this->insert('event_status', array(
                'id' => 1,
                'name' => 'OPEN',
                'display_name' => 'Open',
                'display_order' => 1,
                'description' => 'The event is for sale or for attending.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_status', array(
                'id' => 2,
                'name' => 'CLOSED',
                'display_name' => 'Closed',
                'display_order' => 2,
                'description' => 'The event is not for sale or for attending.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_status', array(
                'id' => 3,
                'name' => 'EXPIRED',
                'display_name' => 'Expired',
                'display_order' => 3,
                'description' => 'The event end time, if set, has past or else.'
                    . 'it is one hour past the event start time',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_status', array(
                'id' => 4,
                'name' => 'DELETED',
                'display_name' => 'Deleted',
                'display_order' => 4,
                'description' => 'The event has been removed from the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }
    
    private function dropEventTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_arena_id_fk', 'event');
        $this->dropForeignKey('event_location_id_fk', 'event');
        $this->dropForeignKey('event_type_id_fk', 'event');
        $this->dropForeignKey('event_status_id_fk', 'event');
        $this->dropForeignKey('event_created_by_id_fk', 'event');
        $this->dropForeignKey('event_updated_by_id_fk', 'event');

        // Now truncate and drop the table
        $this->truncateTable('event');
        $this->dropTable('event');
    }
    
    private function dropEventStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_status_created_by_id_fk', 'event_status');
        $this->dropForeignKey('event_status_updated_by_id_fk', 'event_status');

        // Now truncate and drop the table
        $this->truncateTable('event_status');
        $this->dropTable('event_status');
    }
    
    private function dropEventTypeTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_type_created_by_id_fk', 'event_type');
        $this->dropForeignKey('event_type_updated_by_id_fk', 'event_type');

        // Now truncate and drop the table
        $this->truncateTable('event_type');
        $this->dropTable('event_type');
    }
}