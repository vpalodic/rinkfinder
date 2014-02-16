<?php

class m140120_025114_create_contact_and_event_request_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createContactTable();
        $this->createEventRequestTypeTable();
        $this->createEventRequestStatusTable();
        $this->createEventRequestTable();
        
        $this->insertEventRequestTypeValues();
        $this->insertEventRequestStatusValues();
    }

    public function safeDown()
    {
        $this->dropEventRequestTable();
        $this->dropEventRequestStatusTable();
        $this->dropEventRequestTypeTable();
        $this->dropContactTable();
    }
    
    private function createContactTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('contact', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'first_name' => 'VARCHAR(128) NOT NULL',
                'last_name' => 'VARCHAR(128) NOT NULL',
                'address_line1' => 'VARCHAR(128) NOT NULL',
                'address_line2' => 'VARCHAR(128) NULL',
                'city' => 'VARCHAR(128) NOT NULL',
                'state' => 'VARCHAR(2) NOT NULL',
                'zip' => 'VARCHAR(5) NOT NULL',
                'phone' => 'VARCHAR(10) NOT NULL',
                'ext' => 'VARCHAR(10) NULL',
                'fax' => 'VARCHAR(10) NULL',
                'fax_ext' => 'VARCHAR(10) NULL',
                'email' => 'VARCHAR(128) NOT NULL',
                'active' => 'BOOLEAN NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY active (active)',
                'KEY contact_created_by_id_fk (created_by_id)',
                'KEY contact_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT contact_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT contact_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function createEventRequestTypeTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('event_request_type', array(
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
                'KEY event_request_type_created_by_id_fk (created_by_id)',
                'KEY event_request_type_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_request_type_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_type_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 4'
        );
    }

    private function createEventRequestStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('event_request_status', array(
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
                'KEY event_request_status_created_by_id_fk (created_by_id)',
                'KEY event_request_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_request_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createEventRequestTable()
    {
        // the event_id field references the event.id and
        // the requester_id field references user.id and
        // the acknowledger_id field references user.id and
        // the accpeter_id field references user.id and
        // the rejector_id field references user.id and
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // when we create the table
        $this->createTable('event_request', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'event_id' => 'INT(11) NOT NULL',
                'requester_id' => 'INT(11) NOT NULL',
                'acknowledger_id' => 'INT(11) NULL',
                'acknowledged_on' => 'DATETIME NULL',
                'accepter_id' => 'INT(11) NULL',
                'accepted_on' => 'DATETIME NULL',
                'rejector_id' => 'INT(11) NULL',
                'rejected_on' => 'DATETIME NULL',
                'rejected_reason' => 'VARCHAR(511) NULL',
                'notes' => 'TEXT NULL',
                'type_id' => 'INT(3) NOT NULL',
                'status_id' => 'INT(3) NOT NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY event_request_event_id_fk (event_id)',
                'KEY event_request_requester_id_fk (requester_id)',
                'KEY event_request_acknowledger_id_fk (acknowledger_id)',
                'KEY event_request_accepter_id_fk (accepter_id)',
                'KEY event_request_rejector_id_fk (rejector_id)',
                'KEY event_request_type_id_fk (type_id)',
                'KEY event_request_status_id_fk (status_id)',
                'KEY event_request_created_by_id_fk (created_by_id)',
                'KEY event_request_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT event_request_event_id_fk FOREIGN KEY (event_id) REFERENCES event (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_requester_id_fk FOREIGN KEY (requester_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_acknowledger_id_fk FOREIGN KEY (acknowledger_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_accepter_id_fk FOREIGN KEY (accepter_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_rejector_id_fk FOREIGN KEY (rejector_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_type_id_fk FOREIGN KEY (type_id) REFERENCES event_request_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_status_id_fk FOREIGN KEY (status_id) REFERENCES event_request_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT event_request_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function insertEventRequestTypeValues()
    {
        // insert the seven default status values
        $this->insert('event_request_type', array(
                'id' => 1,
                'name' => 'PURCHASE',
                'display_name' => 'Purchase',
                'display_order' => 1,
                'description' => 'A request to purchase the event.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_type', array(
                'id' => 2,
                'name' => 'INFORMATION',
                'display_name' => 'Information',
                'display_order' => 2,
                'description' => 'A request for information about the event.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_type', array(
                'id' => 3,
                'name' => 'CANCEL',
                'display_name' => 'Cancel Reservation',
                'display_order' => 3,
                'description' => 'A request to cancel the reservation for this event.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }
    
    private function insertEventRequestStatusValues()
    {
        // insert the seven default status values
        $this->insert('event_request_status', array(
                'id' => 1,
                'name' => 'PENDING',
                'display_name' => 'Pending',
                'display_order' => 1,
                'description' => 'The request has been successfully sent and is waiting to be acknowledged.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_status', array(
                'id' => 2,
                'name' => 'ACKNOWLEDGED',
                'display_name' => 'Acknowledged',
                'display_order' => 2,
                'description' => 'The request has been successfully acknowledged and is waiting to be approved or rejected.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_status', array(
                'id' => 3,
                'name' => 'ACCEPTED',
                'display_name' => 'Accepted',
                'display_order' => 3,
                'description' => 'The reservation requested has been accepted and the reservation has been added to the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_status', array(
                'id' => 4,
                'name' => 'CANCELED',
                'display_name' => 'Canceled Request',
                'display_order' => 4,
                'description' => 'The request has been canceled by the requester.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('event_request_status', array(
                'id' => 5,
                'name' => 'REJECTED',
                'display_name' => 'Rejected',
                'display_order' => 5,
                'description' => 'The request has been rejected. Check the rejected reason for more details.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
    }
    
    private function dropEventRequestTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_request_event_id_fk', 'event_request');
        $this->dropForeignKey('event_request_requester_id_fk', 'event_request');
        $this->dropForeignKey('event_request_acknowledger_id_fk', 'event_request');
        $this->dropForeignKey('event_request_accepter_id_fk', 'event_request');
        $this->dropForeignKey('event_request_rejector_id_fk', 'event_request');
        $this->dropForeignKey('event_request_type_id_fk', 'event_request');
        $this->dropForeignKey('event_request_status_id_fk', 'event_request');
        $this->dropForeignKey('event_request_created_by_id_fk', 'event_request');
        $this->dropForeignKey('event_request_updated_by_id_fk', 'event_request');
        
        // Now truncate and drop the table
        $this->truncateTable('event_request');
        $this->dropTable('event_request');
    }
    
    private function dropEventRequestStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_request_status_created_by_id_fk', 'event_request_status');
        $this->dropForeignKey('event_request_status_updated_by_id_fk', 'event_request_status');
        
        // Now truncate and drop the table
        $this->truncateTable('event_request_status');
        $this->dropTable('event_request_status');
    }
    
    private function dropEventRequestTypeTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('event_request_type_created_by_id_fk', 'event_request_type');
        $this->dropForeignKey('event_request_type_updated_by_id_fk', 'event_request_type');
        
        // Now truncate and drop the table
        $this->truncateTable('event_request_type');
        $this->dropTable('event_request_type');
    }
    
    private function dropContactTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('contact_created_by_id_fk', 'contact');
        $this->dropForeignKey('contact_updated_by_id_fk', 'contact');
        
        // Now truncate and drop the table
        $this->truncateTable('contact');
        $this->dropTable('contact');
    }
}