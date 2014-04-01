<?php

/**
 * This is the model class for table "event_request".
 *
 * The followings are the available columns in table 'event_request':
 * @property integer $id
 * @property integer $event_id
 * @property integer $requester_id
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
			array('event_id, requester_id, type_id, status_id, created_on, updated_on', 'required'),
			array('event_id, requester_id, acknowledger_id, accepter_id, rejector_id, type_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('rejected_reason', 'length', 'max'=>255),
			array('acknowledged_on, accepted_on, rejected_on, notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, event_id, requester_id, acknowledger_id, acknowledged_on, accepter_id, accepted_on, rejector_id, rejected_on, rejected_reason, notes, type_id, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'requester' => array(self::BELONGS_TO, 'User', 'requester_id'),
			'acknowledger' => array(self::BELONGS_TO, 'User', 'acknowledger_id'),
			'accepter' => array(self::BELONGS_TO, 'User', 'accepter_id'),
			'rejector' => array(self::BELONGS_TO, 'User', 'rejector_id'),
			'type' => array(self::BELONGS_TO, 'EventRequestType', 'type_id'),
			'status' => array(self::BELONGS_TO, 'EventRequestStatus', 'status_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('event_id',$this->event_id);
		$criteria->compare('requester_id',$this->requester_id);
		$criteria->compare('acknowledger_id',$this->acknowledger_id);
		$criteria->compare('acknowledged_on',$this->acknowledged_on,true);
		$criteria->compare('accepter_id',$this->accepter_id);
		$criteria->compare('accepted_on',$this->accepted_on,true);
		$criteria->compare('rejector_id',$this->rejector_id);
		$criteria->compare('rejected_on',$this->rejected_on,true);
		$criteria->compare('rejected_reason',$this->rejected_reason,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('lock_version',$this->lock_version);
		$criteria->compare('created_by_id',$this->created_by_id);
		$criteria->compare('created_on',$this->created_on,true);
		$criteria->compare('updated_by_id',$this->updated_by_id);
		$criteria->compare('updated_on',$this->updated_on,true);

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
            'requested_by' => array(
                'name' => 'requested_by',
                'display' => 'Requested By',
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
                'hide' => 'phone'
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
                'hide' => 'phone'
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
                'hide' => 'phone'
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
     * Returns a summary record for each event request for an arena assigned to user.
     * The results can be further restricted by passing in a status code,
     * type code, from date, to date, and arena id, 
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedEventRequestsSummary($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
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
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.requester_id = p.user_id) AS requested_by, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.acknowledger_id = p.user_id) AS acknowledged_by, "
                . "CASE WHEN er.acknowledged_on IS NULL THEN NULL ELSE DATE_FORMAT(er.acknowledged_on, '%m/%d/%Y %h:%i %p') END AS acknowledged_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.accepter_id = p.user_id) AS accepted_by, "
                . "CASE WHEN er.accepted_on IS NULL THEN NULL ELSE DATE_FORMAT(er.accepted_on, '%m/%d/%Y %h:%i %p') END AS accepted_on, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE er.rejector_id = p.user_id) AS rejected_by, "
                . "CASE WHEN er.rejected_on IS NULL THEN NULL ELSE DATE_FORMAT(er.rejected_on, '%m/%d/%Y %h:%i %p') END AS rejected_on, "
                . "er.rejected_reason, "
                . "er.notes, "
                . "(SELECT t.display_name FROM event_request_type t WHERE t.id = er.type_id) AS type, "
                . "(SELECT s.display_name FROM event_request_status s WHERE s.id = er.status_id) AS status "
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
            $sql .= "AND e.type_id = :tid ";
            $parms['tid'] = $tid;
        }
        
        if($sid !== null) {
            $sql .= "AND e.status_id = :sid ";
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
            
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl(array(
                    'management/update',
                    'model' => 'EventRequest',
                    'eid' => $ret['items'][$i]['id'],
                )
            );
            
            if(is_numeric($ret['items'][$i]['event_id'])) {
                $ret['items'][$i]['endpoint2'] = CHtml::normalizeUrl(array(
                        'management/update',
                        'model' => 'Event',
                        'eid' => $ret['items'][$i]['event_id'],
                    )
                );
            }            
            if(is_numeric($ret['items'][$i]['arena_id'])) {
                $ret['items'][$i]['endpoint3'] = CHtml::normalizeUrl(array(
                        'management/update',
                        'model' => 'Arena',
                        'aid' => $ret['items'][$i]['arena_id'],
                    )
                );
            }            
            if(is_numeric($ret['items'][$i]['location_id'])) {
                $ret['items'][$i]['endpoint4'] = CHtml::normalizeUrl(array(
                        'management/update',
                        'model' => 'Location',
                        'lid' => $ret['items'][$i]['location_id'],
                    )
                );
            }
        }
        
        $ret['count'] = $eventRequestCount;
        $ret['model'] = 'eventRequest';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        
        // Ok, lets return this stuff!!
        return $ret;
    }
}
