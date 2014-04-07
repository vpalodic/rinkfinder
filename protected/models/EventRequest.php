<?php

/**
 * This is the model class for table "event_request".
 *
 * The followings are the available columns in table 'event_request':
 * @property integer $id
 * @property integer $event_id
 * @property integer $requester_id
 * @property string $requester_name
 * @property string $requester_email
 * @property string $requester_phone
 * @property integer $acknowledger_id
 * @property string $acknowledged_on
 * @property integer $accepter_id
 * @property string $accepted_on
 * @property integer $rejector_id
 * @property string $rejected_on
 * @property string $rejected_reason
 * @property string $notes
 * @property integer $type_id
 * @property integer $status_id
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property User $requester
 * @property User $acknowledger
 * @property User $accepter
 * @property User $rejector
 * @property EventRequestType $type
 * @property EventRequestStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 * @property Reservation[] $reservations
 */
class EventRequest extends RinkfinderActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'event_id, requester_id, requester_name, requester_email, requester_phone, type_id',
                'required'
            ),
            array(
                'acknowledged_by, acknowledged_on',
                'required',
                'on' => 'acknowledging'
            ),
            array(
                'accepted_by, accepted_on',
                'required',
                'on' => 'accepting'
            ),
            array(
                'rejected_by, rejected_on, rejected_reason',
                'required',
                'on' => 'rejecting'
            ),
            array(
                'event_id, requester_id, acknowledger_id, accepter_id, rejector_id, type_id, status_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'rejected_reason',
                'length',
                'max' => 255
            ),
            array(
                'requester_name',
                'length',
                'max' => 256
            ),
            array(
                'requester_email',
                'length',
                'max' => 128
            ),
            array(
                'requester_phone',
                'length',
                'max' => 10,
                'min' => 10
            ),
            array(
                'acknowledged_on, accepted_on, rejected_on, created_on, updated_on, notes',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, event_id, requester_id, requester_name, requester_email, requester_pone, acknowledger_id, acknowledged_on, accepter_id, accepted_on, rejector_id, rejected_on, rejected_reason, notes, type_id, status_id, created_by_id, created_on, updated_by_id, updated_on',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'requester' => array(self::BELONGS_TO, 'User', 'requester_id', 'select' => array('id', 'username', 'status_id')),
            'acknowledger' => array(self::BELONGS_TO, 'User', 'acknowledger_id', 'select' => array('id', 'username', 'status_id')),
            'accepter' => array(self::BELONGS_TO, 'User', 'accepter_id', 'select' => array('id', 'username', 'status_id')),
            'rejector' => array(self::BELONGS_TO, 'User', 'rejector_id', 'select' => array('id', 'username', 'status_id')),
            'type' => array(self::BELONGS_TO, 'EventRequestType', 'type_id'),
            'status' => array(self::BELONGS_TO, 'EventRequestStatus', 'status_id'),
            'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id', 'select' => array('id', 'username', 'status_id')),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id', 'select' => array('id', 'username', 'status_id')),
            'reservations' => array(self::HAS_MANY, 'Reservation', 'source_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'requester_id' => 'Requester',
            'requester_name' => 'Requester Name',
            'requester_email' => 'Requester Email',
            'requester_phone' => 'Requester Phone',
            'acknowledger_id' => 'Acknowledger',
            'acknowledged_on' => 'Acknowledged On',
            'accepter_id' => 'Accepter',
            'accepted_on' => 'Accepted On',
            'rejector_id' => 'Rejector',
            'rejected_on' => 'Rejected On',
            'rejected_reason' => 'Rejected Reason',
            'notes' => 'Notes',
            'type_id' => 'Type',
            'status_id' => 'Status',
            'lock_version' => 'Lock Version',
            'created_by_id' => 'Created By',
            'created_on' => 'Created On',
            'updated_by_id' => 'Updated By',
            'updated_on' => 'Updated On',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id);
        $criteria->compare('requester_id', $this->requester_id);
        $criteria->compare('requester_name', $this->requester_name);
        $criteria->compare('requester_email', $this->requester_email);
        $criteria->compare('requester_phone', $this->requester_phone);
        $criteria->compare('acknowledger_id', $this->acknowledger_id);
        $criteria->compare('acknowledged_on', $this->acknowledged_on, true);
        $criteria->compare('accepter_id', $this->accepter_id);
        $criteria->compare('accepted_on', $this->accepted_on, true);
        $criteria->compare('rejector_id', $this->rejector_id);
        $criteria->compare('rejected_on', $this->rejected_on, true);
        $criteria->compare('rejected_reason', $this->rejected_reason, true);
        $criteria->compare('notes', $this->notes, true);
        $criteria->compare('type_id', $this->type_id);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('lock_version', $this->lock_version);
        $criteria->compare('created_by_id', $this->created_by_id);
        $criteria->compare('created_on', $this->created_on, true);
        $criteria->compare('updated_by_id', $this->updated_by_id);
        $criteria->compare('updated_on', $this->updated_on, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventRequest the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
        
    /**
     * Returns an array of event request types
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return array[] the array of event request types
     * @throws CDbException
     */
    public static function getTypes($activeOnly = false)
    {
        $sql = 'SELECT * FROM event_request_type ';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1 ';
        }
        
        $sql .= ' ORDER BY display_order ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of event request statuses
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return array[] the array of event request statuses
     * @throws CDbException
     */
    public static function getStatuses($activeOnly = false)
    {
        $sql = 'SELECT * FROM event_request_status ';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1 ';
        }
        
        $sql .= ' ORDER BY display_order ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Acknowledges a request by updating the record and by sending an e-mail to
     * the requester that their request was acknowledged.
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return boolean true if the request was successfully acknowleged
     * @throws CDbException
     */
    public function acknowledgeRequest()
    {
        $sql = 'SELECT * FROM event_request_type';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1';
        }
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Accepts a request by updating the record, by sending an e-mail to
     * the requester that their request was accepted, and it creates a
     * reservation in the system on the requester's behalf for the event. All
     * other requests will automatically be rejected with the reason that the
     * event has already been reserved.
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return boolean true if the request was successfully acknowleged
     * @throws CDbException
     */
    public function acceptRequest()
    {
        $sql = 'SELECT * FROM event_request_type';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1';
        }
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Rejects a request by updating the record, by sending an e-mail to
     * the requester that their request was not accepted, and it creates a
     * reservation in the system on the requester's behalf for the event. All
     * other requests will automatically be rejected with the reason that the
     * event has already been reserved.
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return boolean true if the request was successfully acknowleged
     * @throws CDbException
     */
    public function rejectRequest()
    {
        $sql = 'SELECT * FROM event_request_type';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1 ';
        }
        
        $sql .= 'ORDER BY display_order ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of attributes that are in the summary view
     * @return string[] the array of attributes
     */
    public static function getSummaryAttributes()
    {
        return array(
            'id' => array(
                'name' => 'id',
                'display' => 'ID',
                'type' => 'numeric',
                'link' => 'endpoint',
            ),
            'event_id' => array(
                'name' => 'event_id',
                'display' => 'Event ID',
                'type' => 'numeric',
                'hide' => 'all',
                'link' => 'endpoint2',
            ),
            'event_start' => array(
                'name' => 'event_start',
                'display' => 'Event Start',
                'type' => 'numeric',
            ),
            'arena' => array(
                'name' => 'arena',
                'display' => 'Arena',
                'type' => 'alpha',
                'hide' => 'phone,tablet',
            ),
            'arena_id' => array(
                'name' => 'arena_id',
                'display' => 'Arena ID',
                'type' => 'numeric',
                'hide' => 'all',
                'link' => 'endpoint3',
            ),
            'location' => array(
                'name' => 'location',
                'display' => 'Location',
                'type' => 'alpha',
                'hide' => 'phone,tablet',
            ),
            'location_id' => array(
                'name' => 'location_id',
                'display' => 'Location ID',
                'type' => 'numeric',
                'hide' => 'all',
                'link' => 'endpoint4',
            ),
            'requester_name' => array(
                'name' => 'requester_name',
                'display' => 'Requested By',
                'type' => 'alpha',
                'hide' => 'phone',
            ),
            'created_on' => array(
                'name' => 'created_on',
                'display' => 'Requested On',
                'type' => 'alpha',
                'hide' => 'phone',
            ),
            'acknowledged_by' => array(
                'name' => 'acknowledged_by',
                'display' => 'Acknowledged By',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'acknowledged_on' => array(
                'name' => 'acknowledged_on',
                'display' => 'Acknowledged On',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'accepted_by' => array(
                'name' => 'accepted_by',
                'display' => 'Accepted By',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'accepted_on' => array(
                'name' => 'accepted_on',
                'display' => 'Accepted On',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'rejected_by' => array(
                'name' => 'rejected_by',
                'display' => 'Rejected By',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'rejected_on' => array(
                'name' => 'rejected_on',
                'display' => 'Rejected On',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'rejected_reason' => array(
                'name' => 'rejected_reason',
                'display' => 'Rejection Reason',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'notes' => array(
                'name' => 'notes',
                'display' => 'Notes',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'type' => array(
                'name' => 'type',
                'display' => 'Type',
                'type' => 'alpha',
            ),
            'status' => array(
                'name' => 'status',
                'display' => 'Status',
                'type' => 'alpha',
            ),
        );
    }

    /**
     * Retrieve either a field or indexed field list
     * @param string $field If null, returns the entire list
     * @return mixed If no field is provided, it is an array indexed by the
     * field values. If a valid field is provided, then it is the
     * details for only that field.
     */
    public static function fieldDetails($field = null)
    {
        $_items = array(
            'id' => array(
                'name' => 'id',
                'label' => 'ID',
                'controlType' => 'text',
                'type' => 'numeric',
                'primary' => true,
                'editable' => false,
                'hidden' => false,
            ),
            'event_id' => array(
                'name' => 'event_id',
                'label' => 'Event ID',
                'controlType' => 'text',
                'type' => 'numeric',
                'editable' => false,
                'hidden' => false,
            ),
            'event_start' => array(
                'name' => 'event_start',
                'label' => 'Event Start',
                'controlType' => 'datetime',
                'type' => 'datetime',
                'format' => 'yyyy-mm-dd hh:ii',
                'viewformat' => 'mm/dd/yyyy HH:ii P',
                'editable' => false,
                'hidden' => false,
            ),
            'arena' => array(
                'name' => 'arena',
                'label' => 'Arena',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'arena_id' => array(
                'name' => 'arena_id',
                'label' => 'Arena ID',
                'controlType' => 'text',
                'type' => 'numeric',
                'editable' => false,
                'hidden' => true,
            ),
            'location' => array(
                'name' => 'location',
                'label' => 'Location',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'location_id' => array(
                'name' => 'location_id',
                'label' => 'Location ID',
                'controlType' => 'text',
                'type' => 'numeric',
                'editable' => false,
                'hidden' => true,
            ),
            'created_on' => array(
                'name' => 'created_on',
                'label' => 'Requested On',
                'controlType' => 'datetime',
                'type' => 'datetime',
                'format' => 'yyyy-mm-dd hh:ii',
                'viewformat' => 'mm/dd/yyyy HH:ii P',
                'editable' => false,
                'hidden' => false,
            ),
            'requester_name' => array(
                'name' => 'requester_name',
                'label' => 'Requested By',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'requester_email' => array(
                'name' => 'requester_email',
                'label' => 'Requester E-mail',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'requester_phone' => array(
                'name' => 'requester_phone',
                'label' => 'Requester Phone',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
                'inputmask' => array(
                    'mask' => '(999) 999-9999'
                )
            ),
            'acknowledger' => array(
                'name' => 'acknowledger',
                'label' => 'Acknowledged By',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'acknowledged_on' => array(
                'name' => 'acknowledged_on',
                'label' => 'Acknowledged On',
                'controlType' => 'datetime',
                'type' => 'datetime',
                'format' => 'yyyy-mm-dd hh:ii',
                'viewformat' => 'mm/dd/yyyy HH:ii P',
                'editable' => false,
                'hidden' => false,
            ),
            'accepter' => array(
                'name' => 'accepter',
                'label' => 'Accepted By',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'accepted_on' => array(
                'name' => 'accepted_on',
                'label' => 'Accepted On',
                'controlType' => 'datetime',
                'type' => 'datetime',
                'format' => 'yyyy-mm-dd hh:ii',
                'viewformat' => 'mm/dd/yyyy HH:ii P',
                'editable' => false,
                'hidden' => false,
            ),
            'rejector' => array(
                'name' => 'rejector',
                'label' => 'Rejected By',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'rejected_on' => array(
                'name' => 'rejected_on',
                'label' => 'Rejected On',
                'controlType' => 'datetime',
                'type' => 'datetime',
                'format' => 'yyyy-mm-dd hh:ii',
                'viewformat' => 'mm/dd/yyyy HH:ii P',
                'editable' => false,
                'hidden' => false,
            ),
            'rejected_reason' => array(
                'name' => 'rejected_reason',
                'label' => 'Rejection Reason',
                'controlType' => 'text',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'notes' => array(
                'name' => 'notes',
                'display' => 'Notes',
                'controlType' => 'textarea',
                'type' => 'alpha',
                'editable' => true,
                'hidden' => false,
            ),
            'type_id' => array(
                'name' => 'type_id',
                'label' => 'Type',
                'type' => 'alpha',
                'controlType' => 'select',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
            'status_id' => array(
                'name' => 'status_id',
                'label' => 'Status',
                'type' => 'alpha',
                'controlType' => 'select',
                'type' => 'alpha',
                'editable' => false,
                'hidden' => false,
            ),
        );
        
        if(isset($field)) {
            return isset($_items[$field]) ? $_items[$field] : false;
        } else {
            return $_items;
        }
    }
    
    /**
     * Returns the event request count for each type and status 
     * plus the total count for each type and the total count for all.
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event request counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'EventRequest',
        );

        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, '
                . 'IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM event_request_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(e.id) AS count '
                . ' FROM event_request er '
                . ' INNER JOIN event e '
                . ' ON e.id = er.event_id '
                . ' INNER JOIN arena a '
                . ' ON a.id = e.arena_id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN event_request_status s1 '
                . ' ON s1.id = er.status_id '
                . ' WHERE u.id = :uid  ';
        
        if($aid !== null) {
            $sql .= "AND e.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($from !== null) {
            $sql .= "AND e.start_date >= :from ";
            $parms['from'] = $from;
        }
        
        if($to !== null) {
            $sql .= "AND e.start_date <= :to ";
            $parms['to'] = $to;
        }
        
        if($tid !== null) {
            $sql .= "AND er.type_id = :tid ";
            $parms['tid'] = $tid;
        } else {
            $sql .= "AND er.type_id = :ertype ";
        }
        
        if($sid !== null) {
            $sql .= "AND er.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= ' GROUP BY s1.id) AS sc '
                . ' ON s.id = sc.id '
                . ' ORDER BY s.display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $ertypeId = 0;
        $eventRequestCountTotal = 0;
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($aid !== null) {
            $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        }
        
        if($from !== null) {
            $command->bindParam(':from', $from, PDO::PARAM_STR);
        }
        
        if($to !== null) {
            $command->bindParam(':to', $to, PDO::PARAM_STR);
        }
        
        if($tid !== null) {
            $ertypes = array();
            $ertypes[] = EventRequestType::model()->findByPk($tid);
            
            $command->bindParam(':tid', $tid, PDO::PARAM_INT);
        } else {
            $ertypes = EventRequestType::model()->findAll();
        
            $command->bindParam(':ertype', $ertypeId, PDO::PARAM_INT);
        }
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        // Start with each type and then go for each status within each type
        foreach($ertypes as $ertype) {
            $ertypeId = $ertype->id;
            
            $erTypeCountTotal = 0;
            
            $erstatuses = $command->queryAll(true);
            
            $statusCount = count($erstatuses);
            
            for($i = 0; $i < $statusCount; $i++) {
                $erTypeCountTotal += (integer)$erstatuses[$i]['count'];
                
                $temp = $parms;
                $temp['sid'] = $erstatuses[$i]['id'];
                $temp['tid'] = $ertypeId;
                
                $erstatuses[$i]['endpoint'] = CHtml::normalizeUrl($temp);
            }
            
            $eventRequestCountTotal += $erTypeCountTotal;

            $temp = $parms;
            $temp['tid'] = $ertypeId;
            
            $ret['type'][] = array(
                'id' => $ertype->id,
                'name' => $ertype->name,
                'description' => $ertype->description,
                'display_name' => $ertype->display_name,
                'display_order' => $ertype->display_order,
                'count' => (integer)$erTypeCountTotal,
                'status' => $erstatuses,
                'endpoint' => CHtml::normalizeUrl($temp)
            );
        }
        
        $ret['total'] = $eventRequestCountTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        return $ret;
    }
    
    /**
     * Returns a summary record for each event request for an arena assigned to user.
     * The results can be further restricted by passing in a status code,
     * type code, from date, to date, and arena id, 
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event request summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedSummary($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'EventRequest',
        );

        $sql = "SELECT er.id, "
                . "e.id AS event_id, "
                . "CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id, "
                . "er.requester_name, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.acknowledger_id = p.user_id) AS acknowledged_by, "
                . "CASE WHEN er.acknowledged_on IS NULL OR er.acknowledged_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.acknowledged_on, '%m/%d/%Y %h:%i %p') END AS acknowledged_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.accepter_id = p.user_id) AS accepted_by, "
                . "CASE WHEN er.accepted_on IS NULL OR er.accepted_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.accepted_on, '%m/%d/%Y %h:%i %p') END AS accepted_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.rejector_id = p.user_id) AS rejected_by, "
                . "CASE WHEN er.rejected_on IS NULL OR er.rejected_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.rejected_on, '%m/%d/%Y %h:%i %p') END AS rejected_on, "
                . "er.rejected_reason, "
                . "er.notes, "
                . "(SELECT t.display_name FROM event_request_type t WHERE t.id = er.type_id) AS type, "
                . "(SELECT s.display_name FROM event_request_status s WHERE s.id = er.status_id) AS status, "
                . "CASE WHEN er.created_on IS NULL OR er.created_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.created_on, '%m/%d/%Y %h:%i %p') END AS created_on "
                . "FROM event_request er "
                . "    INNER JOIN event e "
                . "    ON er.event_id = e.id "
                . "    INNER JOIN arena a "
                . "    ON e.arena_id = a.id "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";

        if($aid !== null) {
            $sql .= "AND e.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($from !== null) {
            $sql .= "AND e.start_date >= :from ";
            $parms['from'] = $from;
        }
        
        if($to !== null) {
            $sql .= "AND e.start_date <= :to ";
            $parms['to'] = $to;
        }
        
        if($tid !== null) {
            $sql .= "AND er.type_id = :tid ";
            $parms['tid'] = $tid;
        }
        
        if($sid !== null) {
            $sql .= "AND er.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= "ORDER BY e.start_date ASC";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($aid !== null) {
            $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        }
        
        if($from !== null) {
            $command->bindParam(':from', $from, PDO::PARAM_STR);
        }
        
        if($to !== null) {
            $command->bindParam(':to', $to, PDO::PARAM_STR);
        }
        
        if($tid !== null) {
            $command->bindParam(':tid', $tid, PDO::PARAM_INT);
        }
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $ret['items'] = $command->queryAll(true);

        $eventRequestCount = count($ret['items']);
        
        for($i = 0; $i < $eventRequestCount; $i++) {
            $localErid = $ret['items'][$i]['id'];
            $localEid = null;
            $localAid = null;
            $localLid = null;
            
            $localErParms = array(
                'management/view',
                'model' => 'EventRequest',
                'id' => $localErid
            );
            
            $localEParms = array(
                'management/view',
                'model' => 'Event'
            );
            
            $localAParms = array(
                'management/view',
                'model' => 'Arena'
            );
            
            $localLParms = array(
                'management/view',
                'model' => 'Location'
            );
            
            if(is_numeric($ret['items'][$i]['event_id'])) {
                $localEid = $ret['items'][$i]['event_id'];
                $localErParms['eid'] = $localEid;
                $localEParms['id'] = $localEid;
            }
            if(is_numeric($ret['items'][$i]['location_id'])) {
                $localLid = $ret['items'][$i]['location_id'];
                $localErParms['lid'] = $localLid;
                $localEParms['lid'] = $localLid;
                $localLParms['id'] = $localLid;
            }
            if(is_numeric($ret['items'][$i]['arena_id'])) {
                $localAid = $ret['items'][$i]['arena_id'];
                $localErParms['aid'] = $localAid;
                $localEParms['aid'] = $localAid;
                $localLParms['aid'] = $localAid;
                $localAParms['id'] = $localAid;
            }
            
            if(isset($ret['items'][$i]['event_start']) && 
                     strtotime($ret['items'][$i]['event_start']) !== false) {
                $ret['items'][$i]['dataConvert']['event_start'] = 
                        strtotime($ret['items'][$i]['event_start']);
            }
            if(isset($ret['items'][$i]['acknowledged_on']) && 
                     strtotime($ret['items'][$i]['acknowledged_on']) !== false) {
                $ret['items'][$i]['dataConvert']['acknowledged_on'] = 
                        strtotime($ret['items'][$i]['acknowledged_on']);
            }
            if(isset($ret['items'][$i]['accepted_on']) && 
                     strtotime($ret['items'][$i]['accepted_on']) !== false) {
                $ret['items'][$i]['dataConvert']['accepted_on'] = 
                        strtotime($ret['items'][$i]['accepted_on']);
            }
            if(isset($ret['items'][$i]['rejected_on']) && 
                     strtotime($ret['items'][$i]['rejected_on']) !== false) {
                $ret['items'][$i]['dataConvert']['rejected_on'] = 
                        strtotime($ret['items'][$i]['rejected_on']);
            }
            
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl($localErParms);
            
            if(is_numeric($ret['items'][$i]['event_id'])) {
                $ret['items'][$i]['endpoint2'] = CHtml::normalizeUrl($localEParms);
            }            
            if(is_numeric($ret['items'][$i]['location_id'])) {
                $ret['items'][$i]['endpoint4'] = CHtml::normalizeUrl($localLParms);
            }
            if(is_numeric($ret['items'][$i]['arena_id'])) {
                $ret['items'][$i]['endpoint3'] = CHtml::normalizeUrl($localAParms);
            }            
        }
        
        $ret['count'] = $eventRequestCount;
        $ret['model'] = 'eventRequest';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        $ret['statuses'] = CHtml::listData(EventRequest::getStatuses(), 'name', 'display_name');
        $ret['types'] = CHtml::listData(EventRequest::getTypes(), 'name', 'display_name');
        
        // Ok, lets return this stuff!!
        return $ret;
    }
    
    /**
     * Returns a summary record for each event request for an arena assigned to user.
     * The results can be further restricted by passing in a status code,
     * type code, from date, to date, and arena id, 
     * @param integer $id The id of the Event Request.
     * @param integer $uid The user to get the arenas for.
     * @param integer $eid The event id the request was generated from.
     * @param integer $aid The arena id the event was genereated from.
     * @param integer $lid The optional location id to event was generated from.
     * @return mixed[] The event request details or an empty array.
     * @throws CDbException
     */
    public static function getAssignedRecord($id, $uid, $eid, $aid, $lid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/view',
            'model' => 'EventRequest',
            'id' => $id,
            'eid' => $eid,
            'aid' => $aid,
        );
        
        $recordParms = array(
            'id' => $id,
            'eid' => $eid,
            'aid' => $aid
        );
        
        $updateParms = array(
            'eventRequest/update',
        );
        
        $typeParms = array(
            'eventRequest/type',
        );
        
        $statusParms = array(
            'eventRequest/status',
            'output' => 'json'
        );
        
        $sql = "SELECT er.id, "
                . "e.id AS event_id, "
                . "CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id AND l.arena_id = a.id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id AND l.arena_id = a.id) AS location_id, "
                . "er.requester_id, "
                . "er.requester_name, "
                . "er.requester_email, "
                . "er.requester_phone, "
                . "er.acknowledger_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.acknowledger_id = p.user_id) AS acknowledger, "
                . "CASE WHEN er.acknowledged_on IS NULL OR er.acknowledged_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.acknowledged_on, '%m/%d/%Y %h:%i %p') END AS acknowledged_on, "
                . "er.accepter_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.accepter_id = p.user_id) AS accepter, "
                . "CASE WHEN er.accepted_on IS NULL OR er.accepted_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.accepted_on, '%m/%d/%Y %h:%i %p') END AS accepted_on, "
                . "er.rejector_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.rejector_id = p.user_id) AS rejector, "
                . "CASE WHEN er.rejected_on IS NULL OR er.rejected_on = '0000-00-00 00:00:00' THEN NULL ELSE DATE_FORMAT(er.rejected_on, '%m/%d/%Y %h:%i %p') END AS rejected_on, "
                . "er.rejected_reason, "
                . "er.notes, "
                . "er.type_id, "
                . "(SELECT t.display_name FROM event_request_type t WHERE t.id = er.type_id) AS type, "
                . "er.status_id, "
                . "(SELECT s.display_name FROM event_request_status s WHERE s.id = er.status_id) AS status, "
                . "DATE_FORMAT(er.created_on, '%m/%d/%Y %h:%i %p') AS created_on "
                . "FROM event_request er "
                . "    INNER JOIN event e "
                . "    ON er.event_id = e.id "
                . "    INNER JOIN arena a "
                . "    ON e.arena_id = a.id "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id ";

        if($lid !== null) {
            $sql .= " INNER JOIN location l "
                    . " ON l.arena_id = a.id ";

            $parms['lid'] = $lid;
            $recordParms['lid'] = $lid;
            
            $sql .= " AND e.location_id = :lid ";
        }
        
        $sql .= "WHERE er.id = :id "
                . " AND u.id = :uid "
                . " AND er.event_id = :eid "
                . " AND e.id = :eid "
                . " AND e.arena_id = :aid "
                . " AND a.id = :aid ";

        
        if($lid !== null) {
            $sql .= " AND e.location_id = :lid "
                    . " AND l.id = :lid ";
        }
        
        $sql .= " ORDER BY e.start_date ASC";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':id', $id, PDO::PARAM_INT);
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);
        $command->bindParam(':eid', $eid, PDO::PARAM_INT);
        $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        
        if($lid !== null) {
            $command->bindParam(':lid', $lid, PDO::PARAM_INT);
        }
        
        $row = $command->queryRow(true);
        
        $ret['item'] = false;
        $ret['count'] = is_array($row) ? 1 : 0;
        $ret['model'] = 'EventRequest';
        $ret['endpoint']['view'] = CHtml::normalizeUrl($parms);

        if($ret['count'] == 0) {
            return $ret;
        }

        $fields = array();
        
        foreach($row as $field => $value) {
            $fieldData = EventRequest::fieldDetails($field);
            
            if(is_array($fieldData)) {
                if($field == 'acknowledger' || $field == 'accepter' ||
                        $field == 'rejector') {
                    if(empty($value)) {
                        $fieldData['button']['enabled'] = true;
                        $fieldData['button']['name'] = $field . '_id';
                    }
                }
                
                if($field == 'requester_phone') {
                    if(!empty($value) && is_numeric($value)) {
                        $fieldData['value'] = RinkfinderActiveRecord::format_telephone($value);
                    }
                } elseif($field == 'type_id') {
                    $fieldData['value'] = $row['type'];
                    $fieldData['source'] = CHtml::normalizeUrl($typeParms);
                } elseif($field == 'status_id') {
                    $fieldData['value'] = $row['status'];
                    $fieldData['source'] = CHtml::normalizeUrl($statusParms);
                } else {
                    $fieldData['value'] = $value;
                }
                
                $fields[$field] = $fieldData;
            }
        }
        
        $ret['item']['fields'] = $fields;
        $ret['endpoint']['update'] = CHtml::normalizeUrl($updateParms);
        $ret['endpoint']['type'] = CHtml::normalizeUrl($typeParms);
        $ret['endpoint']['status'] = CHtml::normalizeUrl($statusParms);
        $ret['pk']['name'] = 'id';
        $ret['pk']['value'] = isset($ret['item']['fields']['id']['value']) ? 
                $ret['item']['fields']['id']['value'] : 0;
        $ret['parms'] = $recordParms;
        
        // Ok, lets return this stuff!!
        return $ret;
    }
}
