<?php

/**
 * This is the model class for table "location".
 *
 * The followings are the available columns in table 'location':
 * @property integer $id
 * @property integer $arena_id
 * @property string $arena_name
 * @property string $external_id
 * @property string $name
 * @property string $description
 * @property string $tags
 * @property double $length
 * @property double $width
 * @property double $radius
 * @property integer $seating
 * @property integer $base_id
 * @property integer $refrigeration_id
 * @property integer $resurfacer_id
 * @property string $notes
 * @property integer $type_id
 * @property string $type
 * @property integer $status_id
 * @property string $status
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Event[] $events
 * @property Arena $arena
 * @property LocationType $ltype
 * @property LocationStatus $lstatus
 * @property User $createdBy
 * @property User $updatedBy
 */
class Location extends RinkfinderActiveRecord
{
    public $type = '';
    public $status = '';
    public $arena_name = '';
    /**
     * @var string $oldTags
     */
    public $oldTags;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('arena_id, name,', 'required'),
			array('arena_id, seating, base_id, refrigeration_id, resurfacer_id, type_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('length, width, radius', 'numerical'),
			array('external_id', 'length', 'max'=>32),
			array('name', 'length', 'max'=>128),
			array('tags', 'length', 'max'=>1024),
			array('notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, arena_id, external_id, name, description, tags, length, width, radius, seating, base_id, refrigeration_id, resurfacer_id, notes, type_id, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'events' => array(self::HAS_MANY, 'Event', 'location_id'),
			'arena' => array(self::BELONGS_TO, 'Arena', 'arena_id'),
			'ltype' => array(self::BELONGS_TO, 'LocationType', 'type_id'),
			'lstatus' => array(self::BELONGS_TO, 'LocationStatus', 'status_id'),
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
			'arena_id' => 'Arena',
			'external_id' => 'External',
			'name' => 'Name',
			'description' => 'Description',
			'tags' => 'Tags',
			'length' => 'Length',
			'width' => 'Width',
			'radius' => 'Radius',
			'seating' => 'Seating',
			'base_id' => 'Base',
			'refrigeration_id' => 'Refrigeration',
			'resurfacer_id' => 'Resurfacer',
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
     * Returns an array of location types
     * @return array[] the array of location types
     * @throws CDbException
     */
    public static function getTypes()
    {
        $sql = 'SELECT * FROM location_type';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of location types
     * @return array[] the array of location types
     * @throws CDbException
     */
    public static function getTypesList()
    {
        $sql = 'SELECT id AS value, display_name AS text '
                . 'FROM location_type '
                . 'WHERE active = 1';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of location statuses
     * @return array[] the array of location statuses
     * @throws CDbException
     */
    public static function getStatuses()
    {
        $sql = 'SELECT * FROM location_status';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of location statuses
     * @return array[] the array of location statuses
     * @throws CDbException
     */
    public static function getStatusesList()
    {
        $sql = 'SELECT id AS value, display_name AS text '
                . 'FROM location_status '
                . 'WHERE active = 1';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of contacts not assigned to the passed in arena
     * @return array[] the array of contacts
     * @throws CDbException
     */
    public static function getAvailable($aid)
    {
        $sql = 'SELECT l.*, s.display_name AS status, t.display_name AS type '
                . 'FROM location l '
                . 'INNER JOIN location_status s '
                . 'ON l.status_id = s.id '
                . 'INNER JOIN location_type t '
                . 'ON l.type_id = t.id '
                . 'WHERE l.arena_id = :aid '
                . 'ORDER BY l.name ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindValue(':aid', (integer)$aid, PDO::PARAM_INT);
        
        $ret = $command->queryAll(true);
        
        return $ret;
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
            'length' => array(
                'name' => 'length',
                'display' => 'Length',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'width' => array(
                'name' => 'width',
                'display' => 'Width',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'radius' => array(
                'name' => 'radius',
                'display' => 'Radius',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'seating' => array(
                'name' => 'seating',
                'display' => 'Seating Capacity',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'tags' => array(
                'name' => 'tags',
                'display' => 'Tags',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'description' => array(
                'name' => 'description',
                'display' => 'Description',
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
		$criteria->compare('external_id',$this->external_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('length',$this->length);
		$criteria->compare('width',$this->width);
		$criteria->compare('radius',$this->radius);
		$criteria->compare('seating',$this->seating);
		$criteria->compare('base_id',$this->base_id);
		$criteria->compare('refrigeration_id',$this->refrigeration_id);
		$criteria->compare('resurfacer_id',$this->resurfacer_id);
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
	 * @return Location the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
    /**
     * @return array a list of links that point to the arena list filtered by every tag of this arena
     */
    public function getTagLinks()
    {
        $links = array();
        foreach(Tag::string2array($this->tags) as $tag)
            $links[] = CHtml::link(CHtml::encode($tag), array('location/search', 'tag' => $tag), array('class' => 'btn btn-small'));
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
     * Tags the record with the Arena's name, location type, and location name.
     * @throws CDbException
     */
    public function autoTag()
    {
        $tags = Tag::string2array($this->tags);
        
        if(isset($this->name) && !empty($this->name)) {
            $tags[] = $this->name;
        }
        
        $tags[] = $this->arena->name;
        $tags[] = $this->ltype->display_name;
        
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
     * Returns the location counts for arenas assigned to user.
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $aid = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Location',
        );

        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, '
                . 'IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM location_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(l.id) AS count '
                . ' FROM location l '
                . ' INNER JOIN arena a '
                . ' ON l.arena_id = a.id '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN event_status s1 '
                . ' ON l.status_id = s1.id '
                . ' WHERE u.id = :uid  ';
        
        if($aid !== null) {
            $sql .= "AND e.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($tid !== null) {
            $sql .= "AND l.type_id = :tid ";
            $parms['tid'] = $tid;
        } else {
            $sql .= "AND l.type_id = :ltype ";
        }
        
        if($sid !== null) {
            $sql .= "AND l.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= ' GROUP BY s1.id) AS sc '
                . ' ON s.id = sc.id '
                . ' ORDER BY s.display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $ltypeId = 0;
        $countTotal = 0;
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($aid !== null) {
            $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        }
        
        if($tid !== null) {
            $command->bindParam(':tid', $tid, PDO::PARAM_INT);
            $types = array();
            $types[] = LocationType::model()->findByPk($tid);
        } else {
            $command->bindParam(':ltype', $ltypeId, PDO::PARAM_INT);
            $types = LocationType::model()->findAll();
        }
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        // Start with each type and then go for each status within each type
        foreach($types as $type) {
            $ltypeId = $type->id;

            $typeCountTotal = 0;

            $statuses = $command->queryAll(true);
            
            $statusCount = count($statuses);
            
            for($i = 0; $i < $statusCount; $i++) {
                $typeCountTotal += (integer)$statuses[$i]['count'];
                
                $temp = $parms;
                $temp['sid'] = $statuses[$i]['id'];
                $temp['tid'] = $ltypeId;
                
                $statuses[$i]['endpoint'] = CHtml::normalizeUrl($temp);
            }
            
            $countTotal += $typeCountTotal;
            
            $temp = $parms;
            $temp['tid'] = $ltypeId;
            
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
        
        $ret['total'] = $countTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        return $ret;
    }
    
    /**
     * Returns a summary record for each location for arenas assigned to user.
     * The results can be further restricted by passing in a status code,
     * type code, from date, to date, and arena id, 
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $tid The optional type code id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The event summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedSummary($uid, $aid = null, $tid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Location',
        );

        $sql = "SELECT l.id, "
                . "l.external_id, "
                . "l.name, "
                . "l.description, "
                . "l.tags, "
                . "a.name AS arena, "
                . "a.id AS arena_id, "
                . "l.length, "
                . "l.width, "
                . "l.radius, "
                . "l.seating, "
                . "l.notes, "
                . "(SELECT t.display_name FROM location_type t WHERE t.id = l.type_id) AS type, "
                . "(SELECT s.display_name FROM location_status s WHERE s.id = l.status_id) AS status "
                . "FROM location l "
                . "    INNER JOIN arena a "
                . "    ON l.arena_id = a.id "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";

        if($aid !== null) {
            $sql .= "AND l.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($tid !== null) {
            $sql .= "AND l.type_id = :tid ";
            $parms['tid'] = $tid;
        }
        
        if($sid !== null) {
            $sql .= "AND l.status_id = :sid ";
            $parms['sid'] = $sid;
        }
        
        $sql .= "ORDER BY a.name, l.name ASC";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($aid !== null) {
            $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        }
        
        if($tid !== null) {
            $command->bindParam(':tid', $tid, PDO::PARAM_INT);
        }
        
        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $ret['items'] = $command->queryAll(true);

        $count = count($ret['items']);
        
        for($i = 0; $i < $count; $i++) {
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl(array(
                    'management/view',
                    'model' => 'Location',
                    'id' => $ret['items'][$i]['id'],
                    'aid' => $ret['items'][$i]['arena_id']
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
        }
        
        $ret['count'] = $count;
        $ret['model'] = 'location';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        $ret['statuses'] = CHtml::listData(Location::getStatuses(), 'name', 'display_name');
        $ret['types'] = CHtml::listData(Location::getTypes(), 'name', 'display_name');
        
        // Ok, lets return this stuff!!
        return $ret;
    }
}
