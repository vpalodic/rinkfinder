<?php

class m140120_065309_create_arena_reservation_policy_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createArenaReservationPolicyTable();
    }

    public function safeDown()
    {
        $this->dropArenaReservationPolicyTable();
    }
    
    private function createArenaReservationPolicyTable()
    {
        // the arena_id field references arena.id and
        // the event_type_id field references event_type.id
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('arena_reservation_policy', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'arena_id' => 'INT(11) NOT NULL',
                'days' => 'VARCHAR( 64 ) NOT NULL DEFAULT \'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday\'',
                'cutoff_time' => 'TIME NULL',
                'cutoff_day' => 'VARCHAR(16) NULL',
                'notes' => 'TEXT NULL',
                'event_type_id' => 'INT(3) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY arena_reservation_policy_arena_id_fk (arena_id)',
                'KEY arena_reservation_policy_event_type_id_fk (event_type_id)',
                'KEY arena_reservation_policy_created_by_id_fk (created_by_id)',
                'KEY arena_reservation_policy_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_reservation_policy_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT arena_reservation_policy_event_type_id_fk FOREIGN KEY (event_type_id) REFERENCES event_type (id) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT arena_reservation_policy_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL',
                'CONSTRAINT arena_reservation_policy_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE SET NULL',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function dropArenaReservationPolicyTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('arena_reservation_policy_arena_id_fk', 'arena_reservation_policy');
        $this->dropForeignKey('arena_reservation_policy_event_type_id_fk', 'arena_reservation_policy');
        $this->dropForeignKey('arena_reservation_policy_created_by_id_fk', 'arena_reservation_policy');
        $this->dropForeignKey('arena_reservation_policy_updated_by_id_fk', 'arena_reservation_policy');
        
        // Now truncate and drop the table
        $this->truncateTable('arena_reservation_policy');
        $this->dropTable('arena_reservation_policy');
    }
}