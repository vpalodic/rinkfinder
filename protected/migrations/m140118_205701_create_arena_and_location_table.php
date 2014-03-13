<?php

class m140118_205701_create_arena_and_location_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createArenaStatusTable();
        $this->createArenaTable();
        $this->createLocationTypeTable();
        $this->createLocationBaseTable();
        $this->createLocationRefrigerationTable();
        $this->createLocationResurfacerTable();
        $this->createLocationStatusTable();
        $this->createLocationTable();
        
        $this->insertArenaStatusValues();
        $this->insertLocationTypeValues();
        $this->insertLocationBaseValues();
        $this->insertLocationRefrigerationValues();
        $this->insertLocationResurfacerValues();
        $this->insertLocationStatusValues();
    }

    public function safeDown()
    {
        $this->dropLocationTable();
        $this->dropLocationStatusTable();
        $this->dropLocationResurfacerTable();
        $this->dropLocationRefrigerationTable();
        $this->dropLocationBaseTable();
        $this->dropLocationTypeTable();
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
                'CONSTRAINT arena_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT arena_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
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
                'description' => 'TEXT NULL',
                'tags' => 'VARCHAR(1024) NULL',
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
                'UNIQUE KEY name_city_state (name, city, state)',
                'KEY name (name)',
                'KEY tags (tags)',
                'KEY arena_status_id_fk (status_id)',
                'KEY arena_created_by_id_fk (created_by_id)',
                'KEY arena_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT arena_status_id_fk FOREIGN KEY (status_id) REFERENCES arena_status (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT arena_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT arena_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function createLocationTypeTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('location_type', array(
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
                'KEY location_type_created_by_id_fk (created_by_id)',
                'KEY location_type_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_type_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_type_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }
        
    private function createLocationBaseTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('location_base', array(
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
                'KEY location_base_created_by_id_fk (created_by_id)',
                'KEY location_base_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_base_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_base_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertLocationBaseValues()
         */
    }
        
    private function createLocationRefrigerationTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('location_refrigeration', array(
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
                'KEY location_refrigeration_created_by_id_fk (created_by_id)',
                'KEY location_refrigeration_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_refrigeration_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_refrigeration_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertLocationRefrigerationValues()
         */
    }
        
    private function createLocationResurfacerTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('location_resurfacer', array(
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
                'KEY location_resurfacer_created_by_id_fk (created_by_id)',
                'KEY location_resurfacer_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_resurfacer_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_resurfacer_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
        /**
         * @todo Update the AUTO_INCREMENT value after finishing insertLocationResurfacerValues()
         */
    }
        
    private function createLocationStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('location_status', array(
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
                'KEY location_status_created_by_id_fk (created_by_id)',
                'KEY location_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createLocationTable()
    {
        // the arena_id field references arena.id and
        // the base_id field references location_base.id and
        // the refrigeration_id field references location_refrigeration.id and
        // the resurfacer_id field references location_resurfacer.id and
        // the type_id field references location_type.id and
        // the status_id field references location_status.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('location', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'arena_id' => 'INT(11) NOT NULL',
                'external_id' => 'VARCHAR(32) NULL',
                'name' => 'VARCHAR(128) NOT NULL',
                'description' => 'TEXT NOT NULL DEFAULT \'\'',
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
                'UNIQUE KEY arena_id_name (arena_id, name)',
                'KEY tags (tags)',
                'KEY location_arena_id_fk (arena_id)',
                'KEY location_base_id_fk (base_id)',
                'KEY location_refrigeration_id_fk (refrigeration_id)',
                'KEY location_resurfacer_id_fk (resurfacer_id)',
                'KEY location_type_id_fk (type_id)',
                'KEY location_status_id_fk (status_id)',
                'KEY location_created_by_id_fk (created_by_id)',
                'KEY location_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT location_arena_id_fk FOREIGN KEY (arena_id) REFERENCES arena (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_base_id_fk FOREIGN KEY (base_id) REFERENCES location_base (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_refrigeration_id_fk FOREIGN KEY (refrigeration_id) REFERENCES location_refrigeration (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_resurfacer_id_fk FOREIGN KEY (resurfacer_id) REFERENCES location_resurfacer (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_type_id_fk FOREIGN KEY (type_id) REFERENCES location_type (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_status_id_fk FOREIGN KEY (status_id) REFERENCES location_status (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
                'CONSTRAINT location_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE RESTRICT',
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

    private function insertLocationTypeValues()
    {
        // insert the seven default status values
        $this->insert('location_type', array(
                'id' => 1,
                'name' => 'STANDARD_RINK',
                'display_name' => 'Standard / NHL Rink',
                'display_order' => 1,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. Is a standard NHL sized location or very close to standard NHL size.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 2,
                'name' => 'PRACTICE_RINK',
                'display_name' => 'Practice Rink',
                'display_order' => 2,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. May be smaller or larger than a standard NHL sized location.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 3,
                'name' => 'OLYMPIC_RINK',
                'display_name' => 'Olympic / International Rink',
                'display_order' => 3,
                'description' => 'Suitable for practice and game sessions as well as pleasure skating. Is wider and shorter than a standard NHL sized location.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 4,
                'name' => 'OUTDOOR_RINK',
                'display_name' => 'Outdoor Rink',
                'display_order' => 4,
                'description' => 'Ice sheet is outdoors and may or may not have a warming house. In addition to being suitable for practice and pleasure skating, it may also be suitable for games and freestyle.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 5,
                'name' => 'OVAL_RINK',
                'display_name' => 'Oval Rink',
                'display_order' => 5,
                'description' => 'Suitable for speed skating and pleasure skating.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 6,
                'name' => 'FIELD_HOUSE',
                'display_name' => 'Field House',
                'display_order' => 6,
                'description' => 'A facility that is not a rink and is used for various activities.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_type', array(
                'id' => 7,
                'name' => 'ACTIVITY_ROOM',
                'display_name' => 'Activity Room',
                'display_order' => 7,
                'description' => 'A facility that is not a rink and is used mainly for parties and meetings.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertLocationBaseValues()
    {
        /**
         * @todo Complete insertLocationBaseValues()
         */
    }
    
    private function insertLocationRefrigerationValues()
    {
        /**
         * @todo Complete insertLocationRefrigerationValues()
         */
    }
    
    private function insertLocationResurfacerValues()
    {
        /**
         * @todo Complete insertLocationResurfacerValues()
         */
    }
    
    private function insertLocationStatusValues()
    {
        // insert the seven default status values
        $this->insert('location_status', array(
                'id' => 1,
                'name' => 'OPEN',
                'display_name' => 'Open',
                'display_order' => 1,
                'description' => 'The location is open.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_status', array(
                'id' => 2,
                'name' => 'CLOSED',
                'display_name' => 'Closed',
                'display_order' => 2,
                'description' => 'The location is closed for with no immediate plans to re-open.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_status', array(
                'id' => 3,
                'name' => 'CLOSED_CONSTRUCTION',
                'display_name' => 'Under Construction',
                'display_order' => 3,
                'description' => 'The location is temporarily closed due to being under construction.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_status', array(
                'id' => 4,
                'name' => 'CLOSED_OTHER',
                'display_name' => 'Closed Other',
                'display_order' => 4,
                'description' => 'The location is temporarily closed for unspecified reasons.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('location_status', array(
                'id' => 5,
                'name' => 'DELETED',
                'display_name' => 'Deleted',
                'display_order' => 5,
                'description' => 'The location has been removed from the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function dropLocationTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_arena_id_fk', 'location');
        $this->dropForeignKey('location_type_id_fk', 'location');
        $this->dropForeignKey('location_status_id_fk', 'location');
        $this->dropForeignKey('location_created_by_id_fk', 'location');
        $this->dropForeignKey('location_updated_by_id_fk', 'location');
        
        // Now truncate and drop the table
        $this->truncateTable('location');
        $this->dropTable('location');
    }
    
    private function dropLocationStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_status_created_by_id_fk', 'location_status');
        $this->dropForeignKey('location_status_updated_by_id_fk', 'location_status');
        
        // Now truncate and drop the table
        $this->truncateTable('location_status');
        $this->dropTable('location_status');
    }
    
    private function dropLocationResurfacerTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_resurfacer_created_by_id_fk', 'location_resurfacer');
        $this->dropForeignKey('location_resurfacer_updated_by_id_fk', 'location_resurfacer');
        
        // Now truncate and drop the table
        $this->truncateTable('location_resurfacer');
        $this->dropTable('location_resurfacer');
    }
    
    private function dropLocationRefrigerationTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_refrigeration_created_by_id_fk', 'location_refrigeration');
        $this->dropForeignKey('location_refrigeration_updated_by_id_fk', 'location_refrigeration');
        
        // Now truncate and drop the table
        $this->truncateTable('location_refrigeration');
        $this->dropTable('location_refrigeration');
    }
    
    private function dropLocationBaseTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_base_created_by_id_fk', 'location_base');
        $this->dropForeignKey('location_base_updated_by_id_fk', 'location_base');
        
        // Now truncate and drop the table
        $this->truncateTable('location_base');
        $this->dropTable('location_base');
    }
    
    private function dropLocationTypeTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('location_type_created_by_id_fk', 'location_type');
        $this->dropForeignKey('location_type_updated_by_id_fk', 'location_type');
        
        // Now truncate and drop the table
        $this->truncateTable('location_type');
        $this->dropTable('location_type');
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