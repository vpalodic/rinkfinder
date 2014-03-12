<?php

class m140120_033811_create_reservation_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createReservationStatusTable();
        $this->createReservationTable();
        
        $this->insertReservationStatusValues();
    }

    public function safeDown()
    {
        $this->dropReservationTable();
        $this->dropReservationStatusTable();
    }
    
    private function createReservationStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('reservation_status', array(
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
                'KEY reservation_status_created_by_id_fk (created_by_id)',
                'KEY reservation_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT reservation_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createReservationTable()
    {
        // the source_id field references event_request.id and
        // the arena_id field references arena.id and
        // the event_id field references event.id and
        // the for_id field references user.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('reservation', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'source_id' => 'INT(11) NULL',
                'arena_id' => 'INT(11) NOT NULL',
                'event_id' => 'INT(11) NOT NULL',
                'for_id' => 'INT(11) NOT NULL',
                'notes' => 'TEXT NULL',
                'status_id' => 'INT(3) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY reservation_source_id_fk (source_id)',
                'KEY reservation_arena_id_fk (arena_id)',
                'KEY reservation_event_id_fk (event_id)',
                'KEY reservation_for_id_fk (for_id)',
                'KEY reservation_status_id_fk (status_id)',
                'KEY reservation_created_by_id_fk (created_by_id)',
                'KEY reservation_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT reservation_source_id_fk FOREIGN KEY (source_id) REFERENCES event_request (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_event_id_fk FOREIGN KEY (event_id) REFERENCES event (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_for_id_fk FOREIGN KEY (for_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_status_id_fk FOREIGN KEY (status_id) REFERENCES reservation_status (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT reservation_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }
    
    private function insertReservationStatusValues()
    {
        // insert the seven default status values
        $this->insert('reservation_status', array(
                'id' => 1,
                'name' => 'BOOKED',
                'display_name' => 'Booked',
                'display_order' => 1,
                'description' => 'The reservation has been booked but still needs to be paid for.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_status', array(
                'id' => 2,
                'name' => 'COMPLETED',
                'display_name' => 'Completed',
                'display_order' => 2,
                'description' => 'The user arrived for the event and the event is paid in full.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_status', array(
                'id' => 3,
                'name' => 'NOSHOW',
                'display_name' => 'Did not show',
                'display_order' => 3,
                'description' => 'The user did not show up for the event and did not cancel.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );        
        
        $this->insert('reservation_status', array(
                'id' => 4,
                'name' => 'CANCELED',
                'display_name' => 'Canceled',
                'display_order' => 4,
                'description' => 'The user canceled the reservation and the event may be re-booked.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );        
    }

    private function dropReservationTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('reservation_source_id_fk', 'reservation');
        $this->dropForeignKey('reservation_arena_id_fk', 'reservation');
        $this->dropForeignKey('reservation_event_id_fk', 'reservation');
        $this->dropForeignKey('reservation_for_id_fk', 'reservation');
        $this->dropForeignKey('reservation_status_id_fk', 'reservation');
        $this->dropForeignKey('reservation_created_by_id_fk', 'reservation');
        $this->dropForeignKey('reservation_updated_by_id_fk', 'reservation');
        
        // Now truncate and drop the table
        $this->truncateTable('reservation');
        $this->dropTable('reservation');
    }

    private function dropReservationStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('reservation_status_created_by_id_fk', 'reservation_status');
        $this->dropForeignKey('reservation_status_updated_by_id_fk', 'reservation_status');
        
        // Now truncate and drop the table
        $this->truncateTable('reservation_status');
        $this->dropTable('reservation_status');
    }
}