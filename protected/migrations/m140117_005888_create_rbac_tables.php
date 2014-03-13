<?php

class m140117_005888_create_rbac_tables extends CDbMigration
{
    public function safeUp()
    {
        $this->createAuthItemTable();
        $this->createAuthItemChildTable();
        $this->createAuthAssignmentTable();        
    }

    public function safeDown()
    {
        $this->dropAuthAssignmentTable();
        $this->dropAuthItemChildTable();
        $this->dropAuthItemTable();
    }
        
    private function createAuthItemTable()
    {
        $this->createTable('auth_item', array(
                'name' => 'VARCHAR(64) NOT NULL',
                'type' => 'INTEGER NOT NULL',
                'description' => 'TEXT',
                'bizrule' => 'TEXT',
                'data' => 'TEXT',
                'PRIMARY KEY name (name)',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function createAuthItemChildTable()
    {
        // parent references auth_item.name and 
        // child references auth_item.name and so
        // we will add foreign keys for these relations
        $this->createTable('auth_item_child', array(
                'parent' => 'VARCHAR(64) NOT NULL',
                'child' => 'VARCHAR(64) NOT NULL',
                'PRIMARY KEY parent_child (parent, child)',
                'KEY auth_item_child_parent_fk (parent)',
                'KEY auth_item_child_child_fk (child)',
                'CONSTRAINT auth_item_child_parent_fk FOREIGN KEY (parent) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT auth_item_child_child_fk FOREIGN KEY (child) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
        );
    }

    private function createAuthAssignmentTable()
    {
        // itemname references auth_item.name and 
        // userid references user.id and so
        // we will add foreign keys for these relations
        $this->createTable('auth_assignment', array(
                'itemname' => 'VARCHAR(64) NOT NULL',
                'userid' => 'INT(11) NOT NULL',
                'bizrule' => 'TEXT',
                'data' => 'TEXT',
                'PRIMARY KEY itemname_userid (itemname, userid)',
                'KEY auth_assignment_itemname_fk (itemname)',
                'KEY auth_assignment_userid_fk (userid)',
                'CONSTRAINT auth_assignment_itemname_fk FOREIGN KEY (itemname) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE',
                'CONSTRAINT auth_assignment_userid_fk FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
        );
    }

    private function dropAuthAssignmentTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('auth_assignment_userid_fk', 'auth_assignment');
        $this->dropForeignKey('auth_assignment_itemname_fk', 'auth_assignment');
        
        // Now truncate and drop the table
        $this->truncateTable('auth_assignment');
        $this->dropTable('auth_assignment');
    }
    
    private function dropAuthItemChildTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('auth_item_child_child_fk', 'auth_item_child');
        $this->dropForeignKey('auth_item_child_parent_fk', 'auth_item_child');
        
        // Now truncate and drop the table
        $this->truncateTable('auth_item_child');
        $this->dropTable('auth_item_child');
    }

    private function dropAuthItemTable()
    {
        // No Foreign Keys so just truncate and drop the table
        $this->truncateTable('auth_item');
        $this->dropTable('auth_item');
    }
}