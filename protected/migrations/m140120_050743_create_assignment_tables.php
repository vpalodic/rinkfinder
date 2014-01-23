<?php

class m140120_050743_create_assignment_tables extends CDbMigration
{
    public function safeUp()
    {
        $this->createArenaUserAssignmentTable();
        $this->createArenaContactAssignmentTable();
    }

    public function safeDown()
    {
        $this->dropArenaContactAssignmentTable();
        $this->dropArenaUserAssignmentTable();
    }
    
    private function createArenaUserAssignmentTable()
    {
        // the arena_id field references arena.id and
        // the user_id field references user.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('arena_user_assignment', array(
                'arena_id' => 'INT(11) NOT NULL',
                'user_id' => 'INT(11) NOT NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY arena_id_user_id (arena_id, user_id)',
                'KEY arena_user_assignment_arena_id_fk (arena_id)',
                'KEY arena_user_assignment_user_id_fk (user_id)',
                'KEY arena_user_assignment_created_by_id_fk (created_by_id)',
                'KEY arena_user_assignment_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_user_assignment_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_user_assignment_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_user_assignment_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_user_assignment_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function createArenaContactAssignmentTable()
    {
        // the arena_id field references arena.id and
        // the contact_id field references contact.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('arena_contact_assignment', array(
                'arena_id' => 'INT(11) NOT NULL',
                'contact_id' => 'INT(11) NOT NULL',
                'primary_contact' => 'BOOLEAN NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY arena_id_contact_id (arena_id, contact_id)',
                'KEY primary_contact (primary_contact)',
                'KEY arena_contact_assignment_arena_id_fk (arena_id)',
                'KEY arena_contact_assignment_contact_id_fk (contact_id)',
                'KEY arena_contact_assignment_created_by_id_fk (created_by_id)',
                'KEY arena_contact_assignment_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_contact_assignment_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_contact_assignment_contact_id_fk FOREIGN KEY (contact_id) REFERENCES contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_contact_assignment_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_contact_assignment_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function dropArenaContactAssignmentTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('arena_contact_assignment_arena_id_fk', 'arena_contact_assignment');
        $this->dropForeignKey('arena_contact_assignment_contact_id_fk', 'arena_contact_assignment');
        $this->dropForeignKey('arena_contact_assignment_created_by_id_fk', 'arena_contact_assignment');
        $this->dropForeignKey('arena_contact_assignment_updated_by_id_fk', 'arena_contact_assignment');
        
        // Now truncate and drop the table
        $this->truncateTable('arena_contact_assignment');
        $this->dropTable('arena_contact_assignment');
    }
    
    private function dropArenaUserAssignmentTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('arena_user_assignment_arena_id_fk', 'arena_user_assignment');
        $this->dropForeignKey('arena_user_assignment_user_id_fk', 'arena_user_assignment');
        $this->dropForeignKey('arena_user_assignment_created_by_id_fk', 'arena_user_assignment');
        $this->dropForeignKey('arena_user_assignment_updated_by_id_fk', 'arena_user_assignment');
        
        // Now truncate and drop the table
        $this->truncateTable('arena_user_assignment');
        $this->dropTable('arena_user_assignment');
    }
}