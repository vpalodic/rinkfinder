<?php

class m140117_005726_create_users_and_profile_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createUsersTable();
        $this->createProfilesTable();
        
        $this->insertSuperAdmin();
        $this->insertGuestUser();
    }

    public function safeDown()
    {
        $this->deleteGuestUser();
        $this->deleteSuperAdmin();
            
        $this->dropProfilesTable();
        $this->dropUsersTable();
    }
        
    private function createUsersTable()
    {
        $this->createTable('user', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'username' => 'VARCHAR(32) NOT NULL',
                'email' => 'VARCHAR(128) NOT NULL',
                'password' => 'VARCHAR(64) NOT NULL',
                'status' => 'INT(3) NOT NULL DEFAULT \'0\'',
                'failed_logins' => 'INT(3) NOT NULL DEFAULT \'0\'',
                'last_visited_on' => 'DATETIME NULL',
                'last_visited_from' => 'VARCHAR(32) NULL',
                'activation_key' => 'VARCHAR(64) NOT NULL',
                'activated_on' => 'DATETIME NULL',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT \'1\'',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT \'1\'',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY username (username)',
                'UNIQUE KEY email (email)',
                'KEY status (status)',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
        );
    }

    private function createProfilesTable()
    {
        // The user_id field references user.id and 
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('profile', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'user_id' => 'INT(11) NOT NULL',
                'first_name' => 'VARCHAR(80) NOT NULL',
                'last_name' => 'VARCHAR(80) NOT NULL',
                'address_line1' => 'VARCHAR(128) NOT NULL',
                'address_line2' => 'VARCHAR(128) NULL',
                'city' => 'VARCHAR(128) NOT NULL',
                'state' => 'VARCHAR(2) NOT NULL',
                'zip' => 'VARCHAR(5) NOT NULL',
                'lat' => 'FLOAT(12, 8) NULL',
                'lng' => 'FLOAT(12, 8) NULL',
                'phone' => 'VARCHAR(10) NULL',
                'ext' => 'VARCHAR(10) NULL',
                'birthday' => 'DATE NULL',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT \'1\'',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT \'1\'',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY profile_user_id_fk (user_id)',
                'KEY profile_created_by_id_fk (created_by_id)',
                'KEY profile_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT profile_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT profile_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT profile_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 3'
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
                'status' => 1,
                'failed_logins' => 0,
                'activation_key' => '$2a$13$ZgDFkwtY9f57GeMbbV35Sui3umePC8Q2qLprpfuNFHLStRc3yuY.y',
                'activated_on' => new CDbExpression('NOW()'),
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        // Now we can safely add the Foreign Keys to the user's table
        // created_by_id references user.id
        $this->addForeignKey('user_created_by_id_fk', 'user', 'created_by_id', 'user', 'id', 'RESTRICT', 'RESTRICT');
        
        // updated_by_id references user.id
        $this->addForeignKey('user_updated_by_id_fk', 'user', 'updated_by_id', 'user', 'id', 'RESTRICT', 'RESTRICT');

        // Now insert the account profile
        $this->insert('profile', array(
                'id' => 1,
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
                'status' => 1,
                'failed_logins' => 0,
                'activation_key' => '',
                'activated_on' => new CDbExpression('NOW()'),
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );

        // Now insert the account profile
        $this->insert('profile', array(
                'id' => 2,
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
        $this->delete('profile', 'id = :id', array(':id' => 2));
        $this->delete('user', 'id = :id', array(':id' => 2));
    }
    
    private function deleteSuperAdmin()
    {
        $this->delete('profile', 'id = :id', array(':id' => 1));
        
        // Must drop the Foreign Key constraints on the users table!!!
        $this->dropForeignKey('user_created_by_id_fk', 'user');
        $this->dropForeignKey('user_updated_by_id_fk', 'user');
        
        $this->delete('user', 'id = :id', array(':id' => 1));
    }
    
    private function dropProfilesTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('profile_user_id_fk', 'profile');
        $this->dropForeignKey('profile_created_by_id_fk', 'profile');
        $this->dropForeignKey('profile_updated_by_id_fk', 'profile');
        
        // Now truncate and drop the table
        $this->truncateTable('profile');
        $this->dropTable('profile');
    }
    
    private function dropUsersTable()
    {
        // No Foreign Keys so just truncate and drop the table
        $this->truncateTable('user');
        $this->dropTable('user');
    }    
}