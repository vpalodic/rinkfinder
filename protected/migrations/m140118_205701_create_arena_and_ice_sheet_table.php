<?php

class m140118_205701_create_arena_and_ice_sheet_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createArenaStatusTable();
        $this->createArenaTable();
        $this->createIceSheetTypeTable();
        $this->createIceSheetBaseTable();
        $this->createIceSheetRefrigerationTable();
        $this->createIceSheetResurfacerTable();
        $this->createIceSheetStatusTable();
        $this->createIceSheetTable();
        
        $this->insertArenaStatusValues();
        $this->insertIceSheetTypeValues();
        $this->insertIceSheetBaseValues();
        $this->insertIceSheetRefrigerationValues();
        $this->insertIceSheetResurfacerValues();
        $this->insertIceSheetStatusValues();
    }

    public function safeDown()
    {
        $this->dropIceSheetTable();
        $this->dropIceSheetStatusTable();
        $this->dropIceSheetResurfacerTable();
        $this->dropIceSheetRefrigerationTable();
        $this->dropIceSheetBaseTable();
        $this->dropIceSheetTypeTable();
        $this->dropArenaTable();
        $this->dropArenaStatusTable();
    }
        
    private function createArenaStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('arena_status', array(
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
                'KEY arena_status_created_by_id_fk (created_by_id)',
                'KEY arena_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createArenaTable()
    {
        // the status_id field references arena_status.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('arena', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'external_id' => 'VARCHAR(32) NULL',
                'name' => 'VARCHAR(128) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'tags' => 'VARCHAR(255) NULL',
                'address_line1' => 'VARCHAR(128) NOT NULL',
                'address_line2' => 'VARCHAR(128) NULL',
                'city' => 'VARCHAR(128) NOT NULL',
                'state' => 'VARCHAR(2) NOT NULL',
                'zip' => 'VARCHAR(5) NOT NULL',
                'lat' => 'FLOAT(12, 8) NULL',
                'lng' => 'FLOAT(12, 8) NULL',
                'phone' => 'VARCHAR(10) NULL',
                'ext' => 'VARCHAR(10) NULL',
                'fax' => 'VARCHAR(10) NULL',
                'fax_ext' => 'VARCHAR(10) NULL',
                'logo' => 'VARCHAR(511) NULL',
                'url' => 'VARCHAR(511) NULL',
                'notes' => 'TEXT NULL',
                'status_id' => 'INT(3) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY external_id (external_id)',
                'KEY name (name)',
                'KEY tags (tags)',
                'KEY arena_status_id_fk (status_id)',
                'KEY arena_created_by_id_fk (created_by_id)',
                'KEY arena_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_status_id_fk FOREIGN KEY (status_id) REFERENCES arena_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT arena_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function createIceSheetTypeTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('ice_sheet_type', array(
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
                'KEY ice_sheet_type_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_type_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_type_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_type_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }
        
    private function createIceSheetBaseTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('ice_sheet_base', array(
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
                'KEY ice_sheet_base_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_base_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_base_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_base_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertIceSheetBaseValues()
         */
    }
        
    private function createIceSheetRefrigerationTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('ice_sheet_refrigeration', array(
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
                'KEY ice_sheet_refrigeration_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_refrigeration_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_refrigeration_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_refrigeration_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertIceSheetRefrigerationValues()
         */
    }
        
    private function createIceSheetResurfacerTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('ice_sheet_resurfacer', array(
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
                'KEY ice_sheet_resurfacer_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_resurfacer_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_resurfacer_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_resurfacer_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertIceSheetResurfacerValues()
         */
    }
        
    private function createIceSheetStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('ice_sheet_status', array(
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
                'KEY ice_sheet_status_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createIceSheetTable()
    {
        // the arena_id field references arena.id and
        // the base_id field references ice_sheet_base.id and
        // the refrigeration_id field references ice_sheet_refrigeration.id and
        // the resurfacer_id field references ice_sheet_resurfacer.id and
        // the type_id field references ice_sheet_type.id and
        // the status_id field references ice_sheet_status.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('ice_sheet', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'arena_id' => 'INT(11) NOT NULL',
                'external_id' => 'VARCHAR(32) NULL',
                'name' => 'VARCHAR(128) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'tags' => 'VARCHAR(1024) NULL',
                'length' => 'FLOAT(5, 2) NULL',
                'width' => 'FLOAT(5, 2) NULL',
                'radius' => 'FLOAT(5, 2) NULL',
                'seating' => 'INT(5) NULL',
                'base_id' => 'INT(3) NULL',
                'refrigeration_id' => 'INT(3) NULL',
                'resurfacer_id' => 'INT(3) NULL',
                'notes' => 'TEXT NULL',
                'type_id' => 'INT(3) NOT NULL DEFAULT 1',
                'status_id' => 'INT(3) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY external_id (external_id)',
                'KEY name (name)',
                'KEY tags (tags)',
                'KEY ice_sheet_arena_id_fk (arena_id)',
                'KEY ice_sheet_base_id_fk (base_id)',
                'KEY ice_sheet_refrigeration_id_fk (refrigeration_id)',
                'KEY ice_sheet_resurfacer_id_fk (resurfacer_id)',
                'KEY ice_sheet_type_id_fk (type_id)',
                'KEY ice_sheet_status_id_fk (status_id)',
                'KEY ice_sheet_created_by_id_fk (created_by_id)',
                'KEY ice_sheet_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT ice_sheet_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_base_id_fk FOREIGN KEY (type_id) REFERENCES ice_sheet_base (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_refrigeration_id_fk FOREIGN KEY (type_id) REFERENCES ice_sheet_refrigeration (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_resurfacer_id_fk FOREIGN KEY (type_id) REFERENCES ice_sheet_resurfacer (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_type_id_fk FOREIGN KEY (type_id) REFERENCES ice_sheet_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_status_id_fk FOREIGN KEY (status_id) REFERENCES ice_sheet_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT ice_sheet_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function insertArenaStatusValues()
    {
        // insert the seven default status values
        $this->insert('arena_status', array(
                'id' => 1,
                'name' => 'OPEN',
                'display_name' => 'Open',
                'display_order' => 1,
                'description' => 'The arena is open for business.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('arena_status', array(
                'id' => 2,
                'name' => 'CLOSED',
                'display_name' => 'Closed',
                'display_order' => 2,
                'description' => 'The arena is closed for business with no immediate plans to re-open.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('arena_status', array(
                'id' => 3,
                'name' => 'CLOSED_CONSTRUCTION',
                'display_name' => 'Under Construction',
                'display_order' => 3,
                'description' => 'The arena is temporarily closed due to being under construction.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('arena_status', array(
                'id' => 4,
                'name' => 'CLOSED_OTHER',
                'display_name' => 'Closed Other',
                'display_order' => 4,
                'description' => 'The arena is temporarily closed for unspecified reasons.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('arena_status', array(
                'id' => 5,
                'name' => 'DELETED',
                'display_name' => 'Deleted',
                'display_order' => 5,
                'description' => 'The arena has been removed from the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertIceSheetTypeValues()
    {
        // insert the seven default status values
        $this->insert('ice_sheet_type', array(
                'id' => 1,
                'name' => 'STANDARD',
                'display_name' => 'Standard / NHL',
                'display_order' => 1,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. Is a standard NHL sized ice sheet or very close to standard NHL size.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_type', array(
                'id' => 2,
                'name' => 'PRACTICE',
                'display_name' => 'Practice',
                'display_order' => 2,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. May be smaller or larger than a standard NHL sized ice sheet.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_type', array(
                'id' => 3,
                'name' => 'OLYMPIC',
                'display_name' => 'Olympic / International',
                'display_order' => 3,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. Is wider and shorter than a standard NHL sized ice sheet.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_type', array(
                'id' => 4,
                'name' => 'OUTDOORS',
                'display_name' => 'Outdoors',
                'display_order' => 4,
                'description' => 'Ice sheet is outdoors and may or may not have a warming house. In addition to being suitable for practice and pleasure skating, it may also be suitable for games and freestyle.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_type', array(
                'id' => 5,
                'name' => 'OVAL',
                'display_name' => 'Oval',
                'display_order' => 5,
                'description' => 'Suitable for speed skating and pleasure skating.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertIceSheetBaseValues()
    {
        /**
         * @todo Complete insertIceSheetBaseValues()
         */
    }
    
    private function insertIceSheetRefrigerationValues()
    {
        /**
         * @todo Complete insertIceSheetRefrigerationValues()
         */
    }
    
    private function insertIceSheetResurfacerValues()
    {
        /**
         * @todo Complete insertIceSheetResurfacerValues()
         */
    }
    
    private function insertIceSheetStatusValues()
    {
        // insert the seven default status values
        $this->insert('ice_sheet_status', array(
                'id' => 1,
                'name' => 'OPEN',
                'display_name' => 'Open',
                'display_order' => 1,
                'description' => 'The ice sheet is open.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_status', array(
                'id' => 2,
                'name' => 'CLOSED',
                'display_name' => 'Closed',
                'display_order' => 2,
                'description' => 'The ice sheet is closed for with no immediate plans to re-open.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_status', array(
                'id' => 3,
                'name' => 'CLOSED_CONSTRUCTION',
                'display_name' => 'Under Construction',
                'display_order' => 3,
                'description' => 'The ice sheet is temporarily closed due to being under construction.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_status', array(
                'id' => 4,
                'name' => 'CLOSED_OTHER',
                'display_name' => 'Closed Other',
                'display_order' => 4,
                'description' => 'The ice sheet is temporarily closed for unspecified reasons.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('ice_sheet_status', array(
                'id' => 5,
                'name' => 'DELETED',
                'display_name' => 'Deleted',
                'display_order' => 5,
                'description' => 'The ice sheet has been removed from the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function dropIceSheetTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_arena_id_fk', 'ice_sheet');
        $this->dropForeignKey('ice_sheet_type_id_fk', 'ice_sheet');
        $this->dropForeignKey('ice_sheet_status_id_fk', 'ice_sheet');
        $this->dropForeignKey('ice_sheet_created_by_id_fk', 'ice_sheet');
        $this->dropForeignKey('ice_sheet_updated_by_id_fk', 'ice_sheet');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet');
        $this->dropTable('ice_sheet');
    }
    
    private function dropIceSheetStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_status_created_by_id_fk', 'ice_sheet_status');
        $this->dropForeignKey('ice_sheet_status_updated_by_id_fk', 'ice_sheet_status');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet_status');
        $this->dropTable('ice_sheet_status');
    }
    
    private function dropIceSheetResurfacerTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_resurfacer_created_by_id_fk', 'ice_sheet_resurfacer');
        $this->dropForeignKey('ice_sheet_resurfacer_updated_by_id_fk', 'ice_sheet_resurfacer');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet_resurfacer');
        $this->dropTable('ice_sheet_resurfacer');
    }
    
    private function dropIceSheetRefrigerationTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_refrigeration_created_by_id_fk', 'ice_sheet_refrigeration');
        $this->dropForeignKey('ice_sheet_refrigeration_updated_by_id_fk', 'ice_sheet_refrigeration');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet_refrigeration');
        $this->dropTable('ice_sheet_refrigeration');
    }
    
    private function dropIceSheetBaseTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_base_created_by_id_fk', 'ice_sheet_base');
        $this->dropForeignKey('ice_sheet_base_updated_by_id_fk', 'ice_sheet_base');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet_base');
        $this->dropTable('ice_sheet_base');
    }
    
    private function dropIceSheetTypeTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('ice_sheet_type_created_by_id_fk', 'ice_sheet_type');
        $this->dropForeignKey('ice_sheet_type_updated_by_id_fk', 'ice_sheet_type');
        
        // Now truncate and drop the table
        $this->truncateTable('ice_sheet_type');
        $this->dropTable('ice_sheet_type');
    }
    
    private function dropArenaTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('arena_status_id_fk', 'arena');
        $this->dropForeignKey('arena_created_by_id_fk', 'arena');
        $this->dropForeignKey('arena_updated_by_id_fk', 'arena');
        
        // Now truncate and drop the table
        $this->truncateTable('arena');
        $this->dropTable('arena');
    }
    
    
    private function dropArenaStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('arena_status_created_by_id_fk', 'arena_status');
        $this->dropForeignKey('arena_status_updated_by_id_fk', 'arena_status');
        
        // Now truncate and drop the table
        $this->truncateTable('arena_status');
        $this->dropTable('arena_status');
    }
    
}