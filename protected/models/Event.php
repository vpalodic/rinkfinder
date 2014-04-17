<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property integer $id
 * @property integer $arena_id
 * @property integer $location_id
 * @property string $external_id
 * @property string $name
 * @property string $description
 * @property string $tags
 * @property boolean $all_day
 * @property string $start_date
 * @property string $start_time
 * @property integer $duration
 * @property string $end_date
 * @property string $end_time
 * @property string $price
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
 * @property Arena $arena
 * @property Location $iceSheet
 * @property EventType $type
 * @property EventStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 * @property Reservation[] $reservations
 * @property ReservationRequest[] $reservationRequests
 */
class Event extends RinkfinderActiveRecord
{
    /**
     * @var string $oldTags
     */
    private $oldTags;
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('arena_id, location_id, start_date, start_time', 'required'),
            array('arena_id, location_id, duration, type_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly' => true),
            array('all_day', 'boolean'),
            array('external_id', 'length', 'max'=>32),
            array('name', 'length', 'max'=>128),
            array('tags', 'length', 'max'=>1024),
            array('price', 'length', 'max'=>10),
            array('duration, end_date, end_time, notes', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, arena_id, location_id, external_id, name, description, tags, all_day, start_date, start_time, duration, end_date, end_time, location, price, notes, type_id, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
            'arena' => array(self::BELONGS_TO, 'Arena', 'arena_id'),
            'location' => array(self::BELONGS_TO, 'Location', 'location_id'),
            'type' => array(self::BELONGS_TO, 'EventType', 'type_id'),
            'status' => array(self::BELONGS_TO, 'EventStatus', 'status_id'),
            'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
            'eventRequests' => array(self::HAS_MANY, 'EventRequest', 'event_id'),
            'reservations' => array(self::HAS_MANY, 'Reservation', 'event_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'arena_id' => 'Arena',
            'location_id' => 'Venue',
            'external_id' => 'External',
            'name' => 'Name',
            'description' => 'Description',
            'tags' => 'Tags',
            'all_day' => 'All Day',
            'start_date' => 'Start Date',
            'start_time' => 'Start Time',
            'duration' => 'Duration',
            'end_date' => 'End Date',
            'end_time' => 'End Time',
            'location' => 'Location',
            'price' => 'Price',
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
        $criteria->compare('arena_id',$this->arena_id);
        $criteria->compare('location_id',$this->location_id);
        $criteria->compare('external_id',$this->external_id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('tags',$this->tags,true);
        $criteria->compare('all_day',$this->all_day);
        $criteria->compare('start_date',$this->start_date,true);
        $criteria->compare('start_time',$this->start_time,true);
        $criteria->compare('duration',$this->duration,true);
        $criteria->compare('end_date',$this->end_date,true);
        $criteria->compare('end_time',$this->end_time,true);
        $criteria->compare('location',$this->location,true);
        $criteria->compare('price',$this->price,true);
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
     * Please note that you should have this exact method in all your
     * CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Event the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
        
    /**
     * Returns an array of event types
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return array[] the array of event types
     * @throws CDbException
     */
    public static function getTypes($activeOnly = false)
    {
        $sql = 'SELECT * FROM event_type ';

        if($activeOnly == true) {
            $sql .= ' WHERE active = 1 ';
        }
        
        $sql .= ' ORDER BY display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of event statuses
     * @param boolean $activeOnly If true, then only active values will be returned
     * @return array[] the array of event statuses
     * @throws CDbException
     */
    public static function getStatuses($activeOnly = false)
    {
        $sql = 'SELECT * FROM event_status ';
        
        if($activeOnly == true) {
            $sql .= ' WHERE active = 1 ';
        }
        
        $sql .= ' ORDER BY display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of attributes that can be imported
     * @return array[][] the array of attributes
     */
    public static function getImportAttributes()
    {
        return array(
            array(
                'name' => 'external_id',
                'display' => 'External ID',
                'type' => 'string',
                'size' => 32,
                'required' => false,
                'tooltip' => 'The ID of the event in your system. '
                . 'You can enter up to a 32 character ID. Must be unique for'
                . ' each event. This field is used to update existing events.',
                'example' => '1234ABCD',
            ),
            array(
                'name' => 'name',
                'display' => 'Name',
                'type' => 'string',
                'size' => 128,
                'required' => false,
                'tooltip' => 'The name of the Event. '
                . 'There is a 128 character limit for the name.',
                'example' => 'Holiday Tournament',
            ),
            array(
                'name' => 'description',
                'display' => 'Description',
                'type' => 'text',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The description for the Event. '
                . 'The description must be plain text. '
                . 'There is no limit to the amount of text you may enter.',
                'example' => 'Ice time that is available for purchase',
            ),
            array(
                'name' => 'location',
                'display' => 'Location',
                'type' => 'string',
                'size' => 128,
                'required' => true,
                'tooltip' => 'The location of the event. This should be the name '
                . 'of an existing location. If the location does not exist '
                . 'in the database, it will be created. Please be careful when '
                . 'specifying the value of this field!'
                . 'You can enter up to a 128 characters. This field is required.',
                'example' => 'Rink A',
            ),
            array(
                'name' => 'all_day',
                'display' => 'All Day',
                'type' => 'integer',
                'size' => 0,
                'required' => false,
                'tooltip' => 'Set to 1 to indicate this is an all day event. ',
                'example' => '0',
            ),
            array(
                'name' => 'start_date',
                'display' => 'Start Date',
                'type' => 'date',
                'size' => 0,
                'required' => true,
                'tooltip' => 'The start date for this event. This field is required.',
                'example' => '11/01/2014',
            ),
            array(
                'name' => 'start_time',
                'display' => 'Start Time',
                'type' => 'time',
                'size' => 0,
                'required' => true,
                'tooltip' => 'The start time for this event. This field is required.',
                'example' => '7:00 PM',
            ),
            array(
                'name' => 'end_date',
                'display' => 'End Date',
                'type' => 'date',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The end date for this event. If not provided or it'
                . ' is invalid and the event occurs in the future, the '
                . 'end_date will automatically be calculated from the duration. '
                . 'If duration is also not provided, a default duration of 60 '
                . 'minutes will be used.',
                'example' => '11/01/2014',
            ),
            array(
                'name' => 'end_time',
                'display' => 'End Time',
                'type' => 'time',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The end time for this event. If not provided or it'
                . ' is invalid and the event occurs in the future, the '
                . 'end_time will automatically be calculated from the duration. '
                . 'If duration is also not provided, a default duration of 60 '
                . 'minutes will be used.',
                'example' => '8:00 PM',
            ),
            array(
                'name' => 'duration',
                'display' => 'Duration',
                'type' => 'integer',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The duration of the event in minutes. For example '
                . 'an hour and a half must be specified as 90 and not 1:30. No '
                . 'conversion will be done for this field. If not provided and'
                . ' the event occurs in the future, the duration will be'
                . ' calcuated from the end_date and end_time. If '
                . 'those fields are also not provided, then duration will default '
                . 'to 60 minutes.',
                'example' => '90',
            ),
            array(
                'name' => 'price',
                'display' => 'Price',
                'type' => 'float',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The cost to purchase the event.',
                'example' => '150.00',
            ),
            array(
                'name' => 'tags',
                'display' => 'Tags',
                'type' => 'string',
                'size' => 1024,
                'required' => false,
                'tooltip' => 'The tags for the Event. '
                . 'Multiple tags must be seperated by a comma (,). '
                . 'There is a 1024 character limit for this field',
                'example' => 'tag1, tag2, tag3, tag4',
            ),
            array(
                'name' => 'notes',
                'display' => 'Notes',
                'type' => 'text',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The notes for the Event. '
                . 'The notes must be plain text. '
                . 'There is no limit to the amount of text you may enter.',
                'example' => 'The location of the event may be moved.',
            ),
            array(
                'name' => 'status_id',
                'display' => 'Status ID',
                'type' => 'integer',
                'size' => 0,
                'required' => false,
                'tooltip' => 'Here you may enter the Status ID of the Event. '
                . 'Please note that entering in an invalid Status ID will result '
                . 'with the import failing. The default status is Open but, if '
                . 'the event occurs in the past, the status will be updated to '
                . 'Expired. It is best to not map this field unless you know'
                . ' what you are doing.',
                'example' => '1',
            ),
        );
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
            'external_id' => array(
                'name' => 'external_id',
                'display' => 'External ID',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'name' => array(
                'name' => 'name',
                'display' => 'Name',
                'type' => 'alpha',
                'hide' => 'phone,tablet',
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
                'link' => 'endpoint2',
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
                'link' => 'endpoint3',
            ),
            'recurrence_id' => array(
                'name' => 'recurrence_id',
                'display' => 'Recurring ID',
                'type' => 'numeric',
                'hide' => 'all',
                'link' => 'endpoint4',
            ),
            'start_date' => array(
                'name' => 'start_date',
                'display' => 'Start Date',
                'type' => 'numeric',
            ),
            'start_time' => array(
                'name' => 'start_time',
                'display' => 'Start Time',
                'type' => 'numeric',
            ),
            'all_day' => array(
                'name' => 'all_day',
                'display' => 'All Day',
                'type' => 'alpha',
                'hide' => 'phone,tablet'
            ),
            'duration' => array(
                'name' => 'duration',
                'display' => 'Duration',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'end_date' => array(
                'name' => 'end_date',
                'display' => 'End Date',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'end_time' => array(
                'name' => 'end_time',
                'display' => 'End Time',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'price' => array(
                'name' => 'price',
                'display' => 'Price',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'description' => array(
                'name' => 'description',
                'display' => 'Description',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'tags' => array(
                'name' => 'tags',
                'display' => 'Tags',
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
            'outstanding_event_requests' => array(
                'name' => 'outstanding_event_requests',
                'display' => 'Open Event Requests',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'outstanding_event_request_ids' => array(
                'name' => 'outstanding_event_request_ids',
                'display' => 'Open Event Request IDs',
                'type' => 'alpha',
                'hide' => 'all',
                'link' => 'endpoint5',
                'linkArray' => true
            ),
            'outstanding_reservations' => array(
                'name' => 'outstanding_reservations',
                'display' => 'Open Reservations',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'outstanding_reservation_ids' => array(
                'name' => 'outstanding_reservation_ids',
                'display' => 'Open Reservation IDs',
                'type' => 'alpha',
                'hide' => 'all',
                'link' => 'endpoint6',
                'linkArray' => true
            ),
        );
    }

    /**
     * @return array a list of links that point to the arena list filtered by every tag of this arena
     */
    public function getTagLinks()
    {
        $links = array();
        foreach(Tag::string2array($this->tags) as $tag)
            $links[] = CHtml::link(CHtml::encode($tag), array('event/search', 'tag' => $tag), array('class' => 'btn btn-small'));
        return $links;
    }

    /**
     * Normalizes the user-entered tags.
     */
    public function normalizeTags($attribute, $params)
    {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->tags)));
    }

    /**
     * Tags the record with the Arena's name, event type, and event name.
     * @throws CDbException
     */
    public function autoTag()
    {
        // First do some date calculations as we only want to tag future
        // dates and remove tags for dates and times that occur in the past!
        $dtCurrentTime = new DateTime();
        $dtEventTime = new DateTime($this->start_date . ' ' . $this->start_time);
        
        if($dtCurrentTime == $dtEventTime || $dtCurrentTime > $dtEventTime) {
            $this->tags = '';
            return;
        }
        
        $tags = Tag::string2array($this->tags);
        
        if(isset($this->name) && !empty($this->name)) {
            $tags[] = $this->name;
        }
        
        $tags[] = $this->arena->name;
        $tags[] = $this->type->display_name;
        
        $this->tags = Tag::array2string(array_unique($tags));
    }

    /**
     * This is invoked when a record is populated with data from a find() call.
     */
    protected function afterFind()
    {
        parent::afterFind();
        $this->oldTags = $this->tags;
    }

    /**
     * This is invoked after the record is saved.
     */
    protected function afterSave()
    {
        parent::afterSave();
        Tag::model()->updateFrequency($this->oldTags, $this->tags);
    }

    /**
     * This is invoked after the record is deleted.
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        Tag::model()->updateFrequency($this->tags, '');
    }

    /**
     * Corrects the the record's status, duration, end_date, and end_time fields.
     * @throws CDbException
     */
    public function autoDurationEndDateTimeStatus()
    {
        // First do some date calculations as we only want to update events that
        // have not happened yet!!!
        $dtCurrentTime = new DateTime();
        $dtEventStartDateTime = new DateTime($this->start_date . ' ' . $this->start_time);
        
        $dtEventEndDateTime = new DateTime($this->end_date . ' ' . $this->end_time);
        $bAllDay = $this->all_day;
        
        $this->duration = !empty($this->duration) ? abs($this->duration) : 60;
        
        $intvalDuration = new DateInterval('PT' . $this->duration . 'M');
        
        if($dtEventEndDateTime == $dtEventStartDateTime ||
                $dtEventEndDateTime < $dtEventStartDateTime) {
            // End date and time isn't set correctly so we calculate it
            if($bAllDay) {
                $this->end_date = $this->start_date;
                $this->end_time = '23:59:59';
                $dtEventEndDateTime = new DateTime($this->end_date . ' ' . $this->end_time);
                $intvalDuration = $dtEventStartDateTime->diff($dtEventEndDateTime);
                $this->duration = (integer)(
                        ($intvalDuration->y * 525949) + 
                        ($intvalDuration->m * 43829.1) + 
                        ($intvalDuration->d * 1440) + 
                        ($intvalDuration->h * 60) +
                        $intvalDuration->i
                );
           } else {
                $dtEventEndDateTime = $dtEventStartDateTime->add($intvalDuration);
                
                $this->end_date = $dtEventEndDateTime->format('Y-m-d');
                $this->end_time = $dtEventEndDateTime->format('H:i:s');
            }
        } else {
            // End date and time are valid so calculate the duration
            $intvalDuration = $dtEventStartDateTime->diff($dtEventEndDateTime);
            
            $this->duration = (integer)(
                    ($intvalDuration->y * 525949) + 
                    ($intvalDuration->m * 43829.1) + 
                    ($intvalDuration->d * 1440) + 
                    ($intvalDuration->h * 60) +
                    $intvalDuration->i
            );
        }
        
        if($dtCurrentTime > $dtEventEndDateTime || $dtCurrentTime > ($dtEventStartDateTime->add($intvalDuration))) {
            // Expire the event!!!
            if($this->status->name == 'OPEN') {
                $sql = 'SELECT id FROM event_status WHERE name = :name';
                $command = Yii::app()->db->createCommand($sql);
                $tempid = $command->queryScalar(array(':name' => 'EXPIRED'));
            
                if($tempid > 0) {
                    $this->status_id = $tempid;
                }
            }
            return;
        }
    }
    
    /**
     * Returns the event counts for arenas assigned to user.
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $from The optional from date to limit results.
     * @param integer $to The optional to date to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Event',
        );

        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, '
                . 'IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM event_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(e.id) AS count '
                . ' FROM event e '
                . ' INNER JOIN arena a '
                . ' ON e.arena_id = a.id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN event_status s1 '
                . ' ON e.status_id = s1.id '
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
            $sql .= "AND e.type_id = :tid ";
            $parms['tid'] = $tid;
        } else {
            $sql .= "AND e.type_id = :etype ";
        }
        
        if($sid !== null) {
            $sql .= "AND e.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= ' GROUP BY s1.id) AS sc '
                . ' ON s.id = sc.id '
                . ' ORDER BY s.display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $etypeId = 0;
        $eventCountTotal = 0;
        
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
            $types = array();
            $types[] = EventType::model()->findByPk($tid);
        } else {
            $command->bindParam(':etype', $etypeId, PDO::PARAM_INT);
            $types = EventType::model()->findAll();
        }
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        // Start with each type and then go for each status within each type
        foreach($types as $type) {
            $etypeId = $type->id;

            $typeCountTotal = 0;

            $statuses = $command->queryAll(true);
            
            $statusCount = count($statuses);
            
            for($i = 0; $i < $statusCount; $i++) {
                $typeCountTotal += (integer)$statuses[$i]['count'];
                
                $temp = $parms;
                $temp['sid'] = $statuses[$i]['id'];
                $temp['tid'] = $etypeId;
                
                $statuses[$i]['endpoint'] = CHtml::normalizeUrl($temp);
            }
            
            $eventCountTotal += $typeCountTotal;
            
            $temp = $parms;
            $temp['tid'] = $etypeId;
            
            $ret['type'][] = array(
                'id' => $type->id,
                'name' => $type->name,
                'description' => $type->description,
                'display_name' => $type->display_name,
                'display_order' => $type->display_order,
                'count' => (integer)$typeCountTotal,
                'status' => $statuses,
                'endpoint' => CHtml::normalizeUrl($temp)
            );
        }
        
        $ret['total'] = $eventCountTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        return $ret;
    }
    
    /**
     * Returns a summary record for each event for arenas assigned to user.
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
    public static function getAssignedSummary($uid, $aid = null, $from = null, $to = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Event',
        );

        $sql = "SELECT e.id, "
                . "e.external_id, "
                . "e.name, "
                . "e.description, "
                . "e.tags, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "(SELECT l.name FROM location l WHERE l.id = e.location_id) AS location, "
                . "(SELECT l.id FROM location l WHERE l.id = e.location_id) AS location_id, "
                . "CASE WHEN e.recurrence_id IS NULL OR e.recurrence_id = 0 THEN NULL ELSE recurrence_id END AS recurrence_id, "
                . "CASE WHEN e.all_day = 0 THEN 'No' ELSE 'Yes' END AS all_day, "
                . "DATE_FORMAT(e.start_date, '%m/%d/%Y') AS start_date, "
                . "DATE_FORMAT(e.start_time, '%h:%i %p') AS start_time, "
                . "CASE WHEN e.duration = 1 THEN CONCAT(e.duration, ' minute') ELSE CONCAT(e.duration, ' minutes') END AS duration, "
                . "CASE WHEN e.end_date = '0000-00-00' THEN NULL ELSE DATE_FORMAT(e.end_date, '%m/%d/%Y') END AS end_date, "
                . "CASE WHEN e.end_time = '00:00:00' THEN NULL ELSE DATE_FORMAT(e.end_time, '%h:%i %p') END AS end_time, "
                . "CONCAT('$', FORMAT(e.price, 2)) AS price, "
                . "e.notes, "
                . "(SELECT t.display_name FROM event_type t WHERE t.id = e.type_id) AS type, "
                . "(SELECT s.display_name FROM event_status s WHERE s.id = e.status_id) AS status, "
                . "(SELECT COUNT(DISTINCT er.id) FROM event_request er WHERE er.event_id = e.id AND er.status_id IN "
                . "    (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED'))) AS outstanding_event_requests, "
                . "(SELECT GROUP_CONCAT(DISTINCT er.id) FROM event_request er WHERE er.event_id = e.id AND er.status_id IN "
                . "    (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED'))) AS outstanding_event_request_ids, "
                . "(SELECT COUNT(DISTINCT r.id) FROM reservation r WHERE r.event_id = e.id AND r.status_id IN "
                . "    (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED'))) AS outstanding_reservations, "
                . "(SELECT GROUP_CONCAT(DISTINCT r.id) FROM reservation r WHERE r.event_id = e.id AND r.status_id IN "
                . "    (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED'))) AS outstanding_reservation_ids "
                . "FROM event e "
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

        $eventCount = count($ret['items']);
        
        for($i = 0; $i < $eventCount; $i++) {
            if(isset($ret['items'][$i]['start_date']) && 
                    isset($ret['items'][$i]['start_time']) && 
                     strtotime($ret['items'][$i]['start_date'] . 
                             ' ' . 
                             $ret['items'][$i]['start_time']) !== false) {
                $ret['items'][$i]['dataConvert']['start_date'] = 
                        strtotime($ret['items'][$i]['start_date'] . 
                                ' ' . 
                                $ret['items'][$i]['start_time']);
                
                $ret['items'][$i]['dataConvert']['start_time'] = 
                    strtotime($ret['items'][$i]['start_time']);
            }
            if(isset($ret['items'][$i]['end_date']) && 
                    isset($ret['items'][$i]['end_time']) && 
                     strtotime($ret['items'][$i]['end_date'] . 
                             ' ' . 
                             $ret['items'][$i]['end_time']) !== false) {
                $ret['items'][$i]['dataConvert']['end_date'] = 
                        strtotime($ret['items'][$i]['end_date'] . 
                                ' ' . 
                                $ret['items'][$i]['end_time']);
                
                $ret['items'][$i]['dataConvert']['end_time'] = 
                    strtotime($ret['items'][$i]['end_time']);
            }
            
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl(array(
                    'management/view',
                    'model' => 'Event',
                    'id' => $ret['items'][$i]['id'],
                )
            );
            
            if(is_numeric($ret['items'][$i]['arena_id'])) {
                $ret['items'][$i]['endpoint2'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Arena',
                        'id' => $ret['items'][$i]['arena_id'],
                    )
                );
            }            
            if(is_numeric($ret['items'][$i]['location_id'])) {
                $ret['items'][$i]['endpoint3'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Location',
                        'id' => $ret['items'][$i]['location_id'],
                    )
                );
            }            
            if(is_numeric($ret['items'][$i]['recurrence_id'])) {
                $ret['items'][$i]['endpoint4'] = CHtml::normalizeUrl(array(
                        'management/view',
                        'model' => 'Recurrence',
                        'id' => $ret['items'][$i]['recurrence_id'],
                    )
                );
            }
            if(is_string($ret['items'][$i]['outstanding_event_request_ids'])) {
                $temp = explode(',', $ret['items'][$i]['outstanding_event_request_ids']);
                $ret['items'][$i]['outstanding_event_request_ids'] = $temp;
                $tempLength = count($temp);
                
                for($j = 0; $j < $tempLength; $j++) {
                        $ret['items'][$i]['endpoint5'][] = CHtml::normalizeUrl(array(
                            'management/view',
                            'model' => 'EventRequest',
                            'id' => $temp[$j],
                        )
                    );
                }
            }
            if(is_string($ret['items'][$i]['outstanding_reservation_ids'])) {
                $temp = explode(',', $ret['items'][$i]['outstanding_reservation_ids']);
                $ret['items'][$i]['outstanding_reservation_ids'] = $temp;
                $tempLength = count($temp);
                
                for($j = 0; $j < $tempLength; $j++) {
                        $ret['items'][$i]['endpoint6'][] = CHtml::normalizeUrl(array(
                            'management/view',
                            'model' => 'Reservation',
                            'id' => $temp[$j],
                        )
                    );
                }
            }
        }
        
        $ret['count'] = $eventCount;
        $ret['model'] = 'event';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        $ret['statuses'] = CHtml::listData(Event::getStatuses(), 'name', 'display_name');
        $ret['types'] = CHtml::listData(Event::getTypes(), 'name', 'display_name');
        
        // Ok, lets return this stuff!!
        return $ret;
    }
}
