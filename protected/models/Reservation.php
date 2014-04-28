<?php

/**
 * This is the model class for table "reservation".
 *
 * The followings are the available columns in table 'reservation':
 * @property integer $id
 * @property integer $source_id
 * @property integer $arena_id
 * @property integer $event_id
 * @property integer $for_id
 * @property integer $for_name
 * @property integer $for_email
 * @property integer $for_phone
 * @property string $notes
 * @property integer $status_id
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property EventRequest $source
 * @property Arena $arena
 * @property Event $event
 * @property User $for
 * @property ReservationStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 */
class Reservation extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reservation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('arena_id, event_id, for_id, created_on, updated_on', 'required'),
			array('source_id, arena_id, event_id, for_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, source_id, arena_id, event_id, for_id, for_name, for_email, for_phone, notes, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'source' => array(self::BELONGS_TO, 'EventRequest', 'source_id'),
			'arena' => array(self::BELONGS_TO, 'Arena', 'arena_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'for' => array(self::BELONGS_TO, 'User', 'for_id'),
			'status' => array(self::BELONGS_TO, 'ReservationStatus', 'status_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'source_id' => 'Source',
			'arena_id' => 'Arena',
			'event_id' => 'Event',
			'for_id' => 'For ID',
			'for_name' => 'For Name',
			'for_email' => 'For Email',
			'for_phone' => 'For Phone',
			'notes' => 'Notes',
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
		$criteria->compare('source_id',$this->source_id);
		$criteria->compare('arena_id',$this->arena_id);
		$criteria->compare('event_id',$this->event_id);
		$criteria->compare('for_id',$this->for_id);
		$criteria->compare('notes',$this->notes,true);
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
	 * @return Reservation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Returns an array of reservation statuses
     * @return array[] the array of reservation statuses
     * @throws CDbException
     */
    public static function getStatuses()
    {
        $sql = 'SELECT * FROM reservation_status';
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
            'source' => array(
                'name' => 'source',
                'display' => 'Event Request ID',
                'type' => 'alpha',
                'hide' => 'all',
                'link' => 'endpoint1',
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
                'hide' => 'phone',
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
            'party' => array(
                'name' => 'party',
                'display' => 'Party',
                'type' => 'alpha,tablet',
            ),
            'notes' => array(
                'name' => 'notes',
                'display' => 'Notes',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'status' => array(
                'name' => 'status',
                'display' => 'Status',
                'type' => 'alpha',
            ),
        );
    }
    
    /**
     * Returns the event reservation count for each status 
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event reservation counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Reservation',
        );

        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, s.active, IF(sc.count IS NULL, 0, sc.count) '
                . 'AS count '
                . 'FROM reservation_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(r.id) AS count '
                . ' FROM reservation r '
                . ' INNER JOIN event e '
                . ' ON r.event_id = e.id '
                . ' INNER JOIN arena a '
                . ' ON e.arena_id = a.id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN reservation_status s1 '
                . ' ON r.status_id = s1.id '
                . ' WHERE u.id = :uid ';
        
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
        
        if($sid !== null) {
            $sql .= "AND r.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= ' GROUP BY s1.id) AS sc '
                . 'ON s.id = sc.id '
                . 'ORDER BY s.display_order ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $reservationCountTotal = 0;
        
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
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $ret['status'] = $command->queryAll(true);

        $statusCount = count($ret['status']);
        
        for($i = 0; $i < $statusCount; $i++) {
            $reservationCountTotal += (integer)$ret['status'][$i]['count'];
            
            $temp = $parms;
            
            $temp['sid'] = $ret['status'][$i]['id'];
                
            $ret['status'][$i]['endpoint'] = CHtml::normalizeUrl($temp);
        }
        
        $ret['total'] = $reservationCountTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        return $ret;
    }
    
    /**
     * Returns a summary record for each reservation for an arena assigned to user.
     * The results can be further restricted by passing in a status code,
     * from date, to date, and arena id, 
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The reservation summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedSummary($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Reservation',
        );

        $sql = "SELECT r.id, "
                . "CASE WHEN r.source_id IS NULL THEN 'Manual' ELSE r.source_id END AS source, "
                . "e.id AS event_id, "
                . "CONCAT(DATE_FORMAT(e.start_date, '%m/%d/%Y'), ' ', DATE_FORMAT(e.start_time, '%h:%i %p')) AS event_start, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id, "
                . "(SELECT CONCAT(p.last_name, ', ', p.first_name) FROM profile p WHERE r.for_id = p.user_id) AS party, "
                . "r.notes, "
                . "(SELECT s.display_name FROM reservation_status s WHERE s.id = r.status_id) AS status "
                . "FROM reservation r "
                . "    INNER JOIN event e "
                . "    ON r.event_id = e.id "
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
        
        if($sid !== null) {
            $sql .= "AND r.status_id = :sid ";
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
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $ret['items'] = $command->queryAll(true);

        $reservationCount = count($ret['items']);
        
        for($i = 0; $i < $reservationCount; $i++) {
            if(isset($ret['items'][$i]['event_start']) && 
                     strtotime($ret['items'][$i]['event_start']) !== false) {
                $ret['items'][$i]['dataConvert']['event_start'] = 
                        strtotime($ret['items'][$i]['event_start']);
            }
            
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl(array(
                    'management/view',
                    'model' => 'Reservation',
                    'id' => $ret['items'][$i]['id'],
                )
            );
            
            if(is_numeric($ret['items'][$i]['source'])) {
                $ret['items'][$i]['endpoint1'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'EventRequest',
                        'id' => $ret['items'][$i]['source'],
                    )
                );
            }
            
            if(is_numeric($ret['items'][$i]['event_id'])) {
                $ret['items'][$i]['endpoint2'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Event',
                        'id' => $ret['items'][$i]['event_id'],
                    )
                );
            }
            
            if(is_numeric($ret['items'][$i]['arena_id'])) {
                $ret['items'][$i]['endpoint3'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Arena',
                        'id' => $ret['items'][$i]['arena_id'],
                    )
                );
            }
            
            if(is_numeric($ret['items'][$i]['location_id'])) {
                $ret['items'][$i]['endpoint4'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Location',
                        'id' => $ret['items'][$i]['location_id'],
                    )
                );
            }
        }
        
        $ret['count'] = $reservationCount;
        $ret['model'] = 'reservation';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        $ret['statuses'] = CHtml::listData(Reservation::getStatuses(), 'name', 'display_name');
        
        // Ok, lets return this stuff!!
        return $ret;
    }
}
