<?php

class m140117_005726_create_user_and_profile_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createUserTable();
        $this->createProfileTable();
        $this->createProfileFieldTable();
        
        $this->insertSiteAdministrator();
        $this->insertProfileFieldValues();
    }

    public function safeDown()
    {
        $this->dropProfileFieldTable();
        $this->deleteSiteAdministrator();
        $this->dropProfileTable();
        $this->dropUserTable();
    }
        
    private function createUserTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('user', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'username' => 'VARCHAR(32) NOT NULL',
                'email' => 'VARCHAR(128) NOT NULL',
                'password' => 'VARCHAR(64) NOT NULL',
                'status_id' => 'INT(3) NOT NULL DEFAULT 1',
                'failed_logins' => 'INT(3) NOT NULL DEFAULT 0',
                'last_visited_on' => 'DATETIME NULL',
                'last_visited_from' => 'VARCHAR(32) NULL',
                'user_key' => 'VARCHAR(64) NOT NULL',
                'activated_on' => 'DATETIME NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY username (username)',
                'UNIQUE KEY email (email)',
                'KEY status_id (status_id)',
                'KEY user_created_by_id_fk (created_by_id)',
                'KEY user_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT user_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT user_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
        );
    }

    private function createProfileTable()
    {
        // The user_id field references user.id and 
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('profile', array(
                'user_id' => 'INT(11) NOT NULL',
                'first_name' => 'VARCHAR(128) NOT NULL',
                'last_name' => 'VARCHAR(128) NOT NULL',
                'address_line1' => 'VARCHAR(128) NOT NULL',
                'address_line2' => 'VARCHAR(128) NULL',
                'city' => 'VARCHAR(128) NOT NULL',
                'state' => 'VARCHAR(2) NOT NULL',
                'zip' => 'VARCHAR(5) NOT NULL',
                'lat' => 'FLOAT(12, 8) NULL',
                'lng' => 'FLOAT(12, 8) NULL',
                'phone' => 'VARCHAR(10) NULL',
                'ext' => 'VARCHAR(10) NULL',
                'avatar' => 'VARCHAR(511) NULL',
                'url' => 'VARCHAR(511) NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY user_id (user_id)',
                'UNIQUE KEY profile_user_id_fk (user_id)',
                'KEY profile_created_by_id_fk (created_by_id)',
                'KEY profile_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT profile_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT profile_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT profile_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
        );
    }

    private function createProfileFieldTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('profile_field', array(
                'id' => 'INT(11) NOT NULL',
                'varname' => 'VARCHAR(50) NOT NULL',
                'title' => 'VARCHAR(255) NOT NULL',
                'field_type' => 'VARCHAR(50) NOT NULL',
                'field_size' => 'INT(4) NULL DEFAULT 0',
                'field_size_min' => 'INT(3) NOT NULL DEFAULT 0',
                'required' => 'INT(1) NOT NULL DEFAULT 0',
                'match' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'range' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'error_message' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'other_validator' => 'VARCHAR(5000) NOT NULL DEFAULT \'\'',
                'default' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'widget' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'widget_params' => 'VARCHAR(5000) NOT NULL DEFAULT \'\'',
                'position' => 'INT(3) NOT NULL DEFAULT 0',
                'visible' => 'INT(1) NOT NULL DEFAULT 0',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY varname (varname, widget, visible)',
                'KEY profile_field_created_by_id_fk (created_by_id)',
                'KEY profile_field_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT profile_field_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT profile_field_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function insertProfileFieldValues()
    {
        // insert the seven default status values
        $this->insert('profile_field', array(
                'id' => 1,
                'varname' => 'first_name',
                'title' => 'First Name',
                'field_type' => 'VARCHAR',
                'field_size' => 128,
                'field_size_min' => 3,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid First Name (length between 3 and 128 characters).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 0,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 2,
                'varname' => 'last_name',
                'title' => 'Last Name',
                'field_type' => 'VARCHAR',
                'field_size' => 128,
                'field_size_min' => 3,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Last Name (length between 3 and 128 characters).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 1,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 3,
                'varname' => 'address_line1',
                'title' => 'Address Line 1',
                'field_type' => 'VARCHAR',
                'field_size' => 128,
                'field_size_min' => 3,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Address Line 1 (length between 3 and 128 characters).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 2,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 4,
                'varname' => 'address_line2',
                'title' => 'Address Line 2',
                'field_type' => 'VARCHAR',
                'field_size' => 128,
                'field_size_min' => 0,
                'required' => 2,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Address Line 2 (length between 0 and 128 characters).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 3,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 5,
                'varname' => 'city',
                'title' => 'City',
                'field_type' => 'VARCHAR',
                'field_size' => 128,
                'field_size_min' => 3,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid City (length between 3 and 128 characters).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 4,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 6,
                'varname' => 'state',
                'title' => 'State',
                'field_type' => 'VARCHAR',
                'field_size' => 2,
                'field_size_min' => 2,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid State (length 2 character abbreviation).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 5,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 7,
                'varname' => 'zip',
                'title' => 'Zip Code',
                'field_type' => 'VARCHAR',
                'field_size' => 5,
                'field_size_min' => 5,
                'required' => 1,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Zip Code (length 5 digit zip code).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 6,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 8,
                'varname' => 'lat',
                'title' => 'Lattitude',
                'field_type' => 'FLOAT',
                'field_size' => '12,8',
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '0.00',
                'widget' => '',
                'widget_params' => '',
                'position' => 7,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 9,
                'varname' => 'lng',
                'title' => 'Longitude',
                'field_type' => 'FLOAT',
                'field_size' => '12,8',
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '0.00',
                'widget' => '',
                'widget_params' => '',
                'position' => 8,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 10,
                'varname' => 'phone',
                'title' => 'Phone Number',
                'field_type' => 'VARCHAR',
                'field_size' => 10,
                'field_size_min' => 0,
                'required' => 2,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Phone Number (length 10 digit phone number).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 9,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 11,
                'varname' => 'ext',
                'title' => 'Extension',
                'field_type' => 'VARCHAR',
                'field_size' => 10,
                'field_size_min' => 0,
                'required' => 2,
                'match' => '',
                'range' => '',
                'error_message' => 'Invalid Extension (length 0 to 10 digits).',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 10,
                'visible' => 1,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 12,
                'varname' => 'avatar',
                'title' => 'Profile Photo',
                'field_type' => 'VARCHAR',
                'field_size' => 511,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 11,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 13,
                'varname' => 'url',
                'title' => 'Personal Home Page',
                'field_type' => 'VARCHAR',
                'field_size' => 511,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 12,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 14,
                'varname' => 'lock_version',
                'title' => 'Lock Version',
                'field_type' => 'INT',
                'field_size' => 11,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 13,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 15,
                'varname' => 'created_by_id',
                'title' => 'Created By',
                'field_type' => 'INT',
                'field_size' => 11,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '1',
                'widget' => '',
                'widget_params' => '',
                'position' => 14,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 16,
                'varname' => 'created_on',
                'title' => 'Created On',
                'field_type' => 'DATETIME',
                'field_size' => 0,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 15,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 17,
                'varname' => 'updated_by_id',
                'title' => 'Updated By',
                'field_type' => 'INT',
                'field_size' => 11,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '1',
                'widget' => '',
                'widget_params' => '',
                'position' => 16,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('profile_field', array(
                'id' => 18,
                'varname' => 'updated_on',
                'title' => 'Updated On',
                'field_type' => 'DATETIME',
                'field_size' => 0,
                'field_size_min' => 0,
                'required' => 0,
                'match' => '',
                'range' => '',
                'error_message' => '',
                'other_validator' => '',
                'default' => '',
                'widget' => '',
                'widget_params' => '',
                'position' => 17,
                'visible' => 0,
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertSiteAdministrator()
    {
        // First insert the user account
        $this->insert('user', array(
                'id' => 1,
                'username' => 'sysadmin',
                'email' => 'webmaster@rinkfinder.com',
                'password' => '$2y$11$4SOqQpalypKG9tgx0076pOXwonA4gcPZ.skwwW3xXEgxBRl00ZzhK',
                'status_id' => 1,
                'failed_logins' => 0,
                'user_key' => '834198d9c507807b2b1ae6e0ecb5089abec8dd02744ea8c3e43cfb20fa399b20',
                'activated_on' => new CDbExpression('NOW()'),
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        // Now insert the account profile
        $this->insert('profile', array(
                'user_id' => 1,
                'first_name' => 'Rinkfinder',
                'last_name' => 'Administrator',
                'address_line1' => '123 Main St.',
                'city' => 'Saint Paul',
                'state' => 'MN',
                'zip' => '55122',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }
    
    private function deleteSiteAdministrator()
    {
        $this->delete('profile', 'user_id = :id', array(':id' => 1));
        
        // Must drop the Foreign Key constraints on the user and user_status tables!!!
        $this->dropForeignKey('user_created_by_id_fk', 'user');
        $this->dropForeignKey('user_updated_by_id_fk', 'user');
        
        $this->delete('user', 'id = :id', array(':id' => 1));
    }
    
    private function dropProfileFieldTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('profile_field_created_by_id_fk', 'profile_field');
        $this->dropForeignKey('profile_field_updated_by_id_fk', 'profile_field');
        
        // Now truncate and drop the table
        $this->truncateTable('profile_field');
        $this->dropTable('profile_field');
    }
    
    private function dropProfileTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('profile_user_id_fk', 'profile');
        $this->dropForeignKey('profile_created_by_id_fk', 'profile');
        $this->dropForeignKey('profile_updated_by_id_fk', 'profile');
        
        // Now truncate and drop the table
        $this->truncateTable('profile');
        $this->dropTable('profile');
    }
    
    private function dropUserTable()
    {
        // At this point their should be no Foreign Keys
        // So truncate and drop the table
        $this->truncateTable('user');
        $this->dropTable('user');
    }    
}