<?php

class m140120_025114_create_contact_and_reservation_request_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createContactTable();
        $this->createReservationRequestTypeTable();
        $this->createReservationRequestStatusTable();
        $this->createReservationRequestTable();
        
        $this->insertReservationRequestTypeValues();
        $this->insertReservationRequestStatusValues();
    }

    public function safeDown()
    {
        $this->dropReservationRequestTable();
        $this->dropReservationRequestStatusTable();
        $this->dropReservationRequestTypeTable();
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
                'active' => 'BIT(1) NOT NULL DEFAULT 1',
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

    private function createReservationRequestTypeTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('reservation_request_type', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(32) NOT NULL',
                'display_name' => 'VARCHAR(32) NOT NULL',
                'display_order' => 'INT(3) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'active' => 'BIT(1) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY name (name)',
                'UNIQUE KEY display_name (display_name)',
                'KEY active (active)',
                'KEY reservation_request_type_created_by_id_fk (created_by_id)',
                'KEY reservation_request_type_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT reservation_request_type_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_type_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 4'
        );
    }

    private function createReservationRequestStatusTable()
    {
        // the created_by_id field references user.id and
        // the updated_by_id field references user.id
        // so we add Foreign Keys for these fields
        // after we create the user table
        $this->createTable('reservation_request_status', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'name' => 'VARCHAR(32) NOT NULL',
                'display_name' => 'VARCHAR(32) NOT NULL',
                'display_order' => 'INT(3) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'active' => 'BIT(1) NOT NULL DEFAULT 1',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'UNIQUE KEY name (name)',
                'UNIQUE KEY display_name (display_name)',
                'KEY active (active)',
                'KEY reservation_request_status_created_by_id_fk (created_by_id)',
                'KEY reservation_request_status_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT reservation_request_status_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_status_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 6'
        );
    }

    private function createReservationRequestTable()
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
        $this->createTable('reservation_request', array(
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'event_id' => 'INT(11) NOT NULL',
                'requester_id' => 'INT(11) NOT NULL',
                'acknowledger_id' => 'INT(11) NULL',
                'acknowledged_on' => 'DATETIME NULL',
                'accepter_id' => 'INT(11) NULL',
                'accepted_on' => 'DATETIME NULL',
                'rejector_id' => 'INT(11) NULL',
                'rejected_on' => 'DATETIME NULL',
                'rejected_reason' => 'VARCHAR(255) NULL',
                'notes' => 'TEXT NULL',
                'type_id' => 'INT(3) NOT NULL',
                'status_id' => 'INT(3) NOT NULL',
                'lock_version' => 'INT(11) NOT NULL DEFAULT 0',
                'created_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'created_on' => 'DATETIME NOT NULL',
                'updated_by_id' => 'INT(11) NOT NULL DEFAULT 1',
                'updated_on' => 'DATETIME NOT NULL',
                'PRIMARY KEY id (id)',
                'KEY reservation_request_event_id_fk (event_id)',
                'KEY reservation_request_requester_id_fk (requester_id)',
                'KEY reservation_request_acknowledger_id_fk (acknowledger_id)',
                'KEY reservation_request_accepter_id_fk (accepter_id)',
                'KEY reservation_request_rejector_id_fk (rejector_id)',
                'KEY reservation_request_type_id_fk (type_id)',
                'KEY reservation_request_status_id_fk (status_id)',
                'KEY reservation_request_created_by_id_fk (created_by_id)',
                'KEY reservation_request_updated_by_id_fk (updated_by_id)',
                'CONSTRAINT reservation_request_event_id_fk FOREIGN KEY (event_id) REFERENCES event (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_requester_id_fk FOREIGN KEY (requester_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_acknowledger_id_fk FOREIGN KEY (acknowledger_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_accepter_id_fk FOREIGN KEY (accepter_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_rejector_id_fk FOREIGN KEY (rejector_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_type_id_fk FOREIGN KEY (type_id) REFERENCES reservation_request_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_status_id_fk FOREIGN KEY (status_id) REFERENCES reservation_request_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_created_by_id_fk FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
                'CONSTRAINT reservation_request_updated_by_id_fk FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE RESTRICT ON DELETE RESTRICT',
            ),
            'ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci'
        );
    }

    private function insertReservationRequestTypeValues()
    {
        // insert the seven default status values
        $this->insert('reservation_request_type', array(
                'id' => 1,
                'name' => 'PURCHASE',
                'display_name' => 'Purchase',
                'display_order' => 1,
                'description' => 'A request to purchase the event.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_type', array(
                'id' => 2,
                'name' => 'INFORMATION',
                'display_name' => 'Information',
                'display_order' => 2,
                'description' => 'A request for information about the event.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_type', array(
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
    
    private function insertReservationRequestStatusValues()
    {
        // insert the seven default status values
        $this->insert('reservation_request_status', array(
                'id' => 1,
                'name' => 'PENDING',
                'display_name' => 'Pending',
                'display_order' => 1,
                'description' => 'The request has been successfully sent and is waiting to be acknowledged.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_status', array(
                'id' => 2,
                'name' => 'ACKNOWLEDGED',
                'display_name' => 'Acknowledged',
                'display_order' => 2,
                'description' => 'The request has been successfully acknowledged and is waiting to be approved or rejected.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_status', array(
                'id' => 3,
                'name' => 'ACCEPTED',
                'display_name' => 'Accepted',
                'display_order' => 3,
                'description' => 'The reservation requested has been accepted and the reservation has been added to the system.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_status', array(
                'id' => 4,
                'name' => 'CANCELED',
                'display_name' => 'Canceled Request',
                'display_order' => 4,
                'description' => 'The request has been canceled by the requester.',
                'created_on' => new CDbExpression('NOW()'),
                'updated_on' => new CDbExpression('NOW()'),
            )
        );
        
        $this->insert('reservation_request_status', array(
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
    
    private function dropReservationRequestTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('reservation_request_event_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_requester_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_acknowledger_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_accepter_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_rejector_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_type_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_status_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_created_by_id_fk', 'reservation_request');
        $this->dropForeignKey('reservation_request_updated_by_id_fk', 'reservation_request');
        
        // Now truncate and drop the table
        $this->truncateTable('reservation_request');
        $this->dropTable('reservation_request');
    }
    
    private function dropReservationRequestStatusTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('reservation_request_status_created_by_id_fk', 'reservation_request_status');
        $this->dropForeignKey('reservation_request_status_updated_by_id_fk', 'reservation_request_status');
        
        // Now truncate and drop the table
        $this->truncateTable('reservation_request_status');
        $this->dropTable('reservation_request_status');
    }
    
    private function dropReservationRequestTypeTable()
    {
        // First drop the Foreign Keys!
        $this->dropForeignKey('reservation_request_type_created_by_id_fk', 'reservation_request_type');
        $this->dropForeignKey('reservation_request_type_updated_by_id_fk', 'reservation_request_type');
        
        // Now truncate and drop the table
        $this->truncateTable('reservation_request_type');
        $this->dropTable('reservation_request_type');
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