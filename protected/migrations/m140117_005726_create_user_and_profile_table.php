<?php

class m140117_005726_create_user_and_profile_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createUserStatusTable();
        $this->createUserTable();
        $this->createProfileTable();
        
        $this->insertUserStatusValues();
        $this->insertSuperAdmin();
        //$this->insertGuestUser();
    }

    public function safeDown()
    {
        //$this->deleteGuestUser();
        $this->deleteSuperAdmin();
            
        $this->dropProfileTable();
        $this->dropUserTable();
        $this->dropUserStatusTable();
    }
        
    private function createUserStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('user_status', array(
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
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 8'
        );
    }

    private function createUserTable()
    {
        // the status_id field references user_status.id and
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
                'KEY user_status_id_fk (status_id)',
                'KEY user_created_by_id_fk (created_by_id)',
                'KEY user_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT user_status_id_fk FOREIGN KEY (status_id) REFERENCES user_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
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

    private function insertUserStatusValues()
    {
        // insert the seven default status values
        $this->insert('user_status', array(
                'id' => 1,
                'name' => 'ACTIVE',
                'display_name' => 'Active',
                'display_order' => 1,
                'description' => 'Normal account status.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 2,
                'name' => 'NOTACTIVATED',
                'display_name' => 'Activation Required',
                'display_order' => 2,
                'description' => 'Similar to \'Active\' but the user can not submit reservation requests until they activate their account.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 3,
                'name' => 'LOCKED',
                'display_name' => 'Locked',
                'display_order' => 3,
                'description' => 'The user will need to \'Reset\' their account before they will be able to login.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 4,
                'name' => 'RESET',
                'display_name' => 'Reset',
                'display_order' => 4,
                'description' => 'The user must change their password the next time they login.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 5,
                'name' => 'INACTIVE',
                'display_name' => 'Inactive',
                'display_order' => 5,
                'description' => 'The user will need to \'Reset\' their account before they will be able to login.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 6,
                'name' => 'DELETED',
                'display_name' => 'Deleted',
                'display_order' => 6,
                'description' => 'Similar to \'Banned\' except that the user chose this.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('user_status', array(
                'id' => 7,
                'name' => 'BANNED',
                'display_name' => 'Banned',
                'display_order' => 7,
                'description' => 'Similar to \'Deleted\' except that and administrator or manager made the change login.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }

    private function insertSuperAdmin()
    {
        // First insert the user account
        $this->insert('user', array(
                'id' => 1,
                'username' => 'sysadmin',
                'email' => 'webmaster@rinkfinder.com',
                'password' => '$2a$13$XbvEz28oJHUt8CMd7ExY3ONPSPMdub5gG7/J09jvYhxQJ4kjpp0cq',
                'status_id' => 1,
                'failed_logins' => 0,
                'user_key' => '$2a$13$ZgDFkwtY9f57GeMbbV35Sui3umePC8Q2qLprpfuNFHLStRc3yuY.y',
                'activated_on' => new CDbExpression('NOW()'),
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        // Now we can safely add the Foreign Keys for the user_status' table
        // created_by_id references user.id
        $this->addForeignKey('user_status_created_by_id_fk', 'user_status', 'created_by_id', 'user', 'id', 'RESTRICT', 'RESTRICT');
        
        // updated_by_id references user.id
        $this->addForeignKey('user_status_updated_by_id_fk', 'user_status', 'updated_by_id', 'user', 'id', 'RESTRICT', 'RESTRICT');

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
    
    private function insertGuestUser()
    {
        // First insert the user account
        $this->insert('user', array(
                'id' => 2,
                'username' => 'guest',
                'email' => 'guestuser@rinkfinder.com',
                'password' => '',
                'status_id' => 1,
                'failed_logins' => 0,
                'user_key' => '',
                'activated_on' => new CDbExpression('NOW()'),
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );

        // Now insert the account profile
        $this->insert('profile', array(
                'user_id' => 2,
                'first_name' => 'Rinkfinder',
                'last_name' => 'Guest',
                'address_line1' => '123 Main St.',
                'city' => 'Saint Paul',
                'state' => 'MN',
                'zip' => '55122',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }
    
    private function deleteGuestUser()
    {
        $this->delete('profile', 'user_id = :id', array(':id' => 2));
        $this->delete('user', 'id = :id', array(':id' => 2));
    }
    
    private function deleteSuperAdmin()
    {
        $this->delete('profile', 'user_id = :id', array(':id' => 1));
        
        // Must drop the Foreign Key constraints on the user and user_status tables!!!
        $this->dropForeignKey('user_status_created_by_id_fk', 'user_status');
        $this->dropForeignKey('user_status_updated_by_id_fk', 'user_status');
        $this->dropForeignKey('user_created_by_id_fk', 'user');
        $this->dropForeignKey('user_updated_by_id_fk', 'user');
        
        $this->delete('user', 'id = :id', array(':id' => 1));
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
    
    private function dropUserStatusTable()
    {
        // At this point their should be no Foreign Keys
        // So truncate and drop the table
        $this->truncateTable('user_status');
        $this->dropTable('user_status');
    }    
}