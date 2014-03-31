<?php

/**
 * This is the model class for table "arena".
 *
 * The followings are the available columns in table 'arena':
 * @property integer $id
 * @property string $external_id
 * @property string $name
 * @property string $description
 * @property string $tags
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property double $lat
 * @property double $lng
 * @property string $phone
 * @property string $ext
 * @property string $fax
 * @property string $fax_ext
 * @property string $logo
 * @property string $url
 * @property string $notes
 * @property integer $status_id
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property ArenaStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 * @property Contact[] $contacts
 * @property ArenaReservationPolicy[] $arenaReservationPolicies
 * @property User[] $users
 * @property FileUpload[] $fileUploads
 * @property Location[] $locations
 * @property Reservation[] $reservations
 */
class Arena extends RinkfinderActiveRecord
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
        return 'arena';
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
                'name, address_line1, city, state, zip',
                'required'
            ),
            array(
                'status_id, lock_version, created_by_id, updated_by_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'lat, lng', 'numerical'
            ),
            array(
                'external_id',
                'length',
                'max' => 32
            ),
            array(
                'name, address_line1, address_line2, city',
                'length',
                'max' => 128
            ),
            array(
                'tags',
                'length',
                'max' => 1024
            ),
            array(
                'state',
                'length',
                'max' => 2
            ),
            array(
                'zip',
                'length',
                'max' => 5
            ),
            array(
                'phone, ext, fax, fax_ext',
                'length',
                'max' => 10
            ),
            array(
                'logo, url',
                'length',
                'max' => 511
            ),
            array(
                'notes',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, external_id, name, description, tags, address_line1, address_line2, city, state, zip, lat, lng, phone, fax, url, notes, status_id, created_by_id, created_on, updated_by_id, updated_on',
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
            'status' => array(
                self::BELONGS_TO,
                'ArenaStatus',
                'status_id'
            ),
            'contacts' => array(
                self::MANY_MANY,
                'Contact',
                'arena_contact_assignment(arena_id, contact_id)'
            ),
            'arenaReservationPolicies' => array(
                self::HAS_MANY,
                'ArenaReservationPolicy',
                'arena_id'
            ),
            'users' => array(
                self::MANY_MANY,
                'User',
                'arena_user_assignment(arena_id, user_id)'
            ),
            'fileUploads' => array(
                self::HAS_MANY,
                'FileUpload',
                'arena_id'
            ),
            'locationss' => array(
                self::HAS_MANY,
                'Location',
                'arena_id'
            ),
            'reservations' => array(
                self::HAS_MANY,
                'Reservation',
                'arena_id'
            ),
            'createdBy' => array(
                self::BELONGS_TO,
                'User',
                'created_by_id',
                'select' => array(
                    'id',
                    'username',
                    'status_id',
                ),
            ),
            'updatedBy' => array(
                self::BELONGS_TO,
                'User',
                'updated_by_id',
                'select' => array(
                    'id',
                    'username',
                    'status_id',
                ),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'external_id' => 'External ID',
            'name' => 'Name',
            'description' => 'Description',
            'tags' => 'Tags',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'lat' => 'Lattitude',
            'lng' => 'Longitude',
            'phone' => 'Phone',
            'ext' => 'Ext',
            'fax' => 'Fax',
            'fax_ext' => 'Fax Ext',
            'logo' => 'Logo',
            'url' => 'Url',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('external_id', $this->external_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('tags', $this->tags, true);
        $criteria->compare('address_line1', $this->address_line1, true);
        $criteria->compare('address_line2', $this->address_line2, true);
        $criteria->compare('city', $this->city, true);
        $criteria->compare('state', $this->state, true);
        $criteria->compare('zip', $this->zip, true);
        $criteria->compare('lat', $this->lat);
        $criteria->compare('lng', $this->lng);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('fax', $this->fax, true);
        $criteria->compare('logo', $this->logo, true);
        $criteria->compare('url', $this->url, true);
        $criteria->compare('notes', $this->notes, true);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('created_by_id', $this->created_by_id);
        $criteria->compare('created_on', $this->created_on, true);
        $criteria->compare('updated_by_id', $this->updated_by_id);
        $criteria->compare('updated_on', $this->updated_on, true);

        return new CActiveDataProvider(
                $this,
                array(
                    'criteria' => $criteria,
                )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Arena the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
        
    /**
     * Returns an array of attributes that can be imported
     * @return string[] the array of attributes
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
                'tooltip' => 'The ID of the arena in your system. '
                . 'You can enter up to a 32 character ID.',
                'example' => '1234ABCD',
            ),
            array(
                'name' => 'name',
                'display' => 'Name',
                'type' => 'string',
                'size' => 128,
                'required' => true,
                'tooltip' => 'The name of the Arena. '
                . 'There is a 128 character limit for the name. This field is required.',
                'example' => 'My Home Arena',
            ),
            array(
                'name' => 'address_line1',
                'display' => 'Address Line 1',
                'type' => 'string',
                'size' => 128,
                'required' => true,
                'tooltip' => 'The first line of the Arena\'s address. '
                . 'There is a 128 character limit for the address line. This field is required.',
                'example' => '123 Main St',
            ),
            array(
                'name' => 'address_line2',
                'display' => 'Address Line 2',
                'type' => 'string',
                'size' => 128,
                'required' => false,
                'tooltip' => 'The second line of the Arena\'s address. '
                . 'There is a 128 character limit for the address line.',
                'example' => 'Suite #456',
            ),
            array(
                'name' => 'city',
                'display' => 'City',
                'type' => 'string',
                'size' => 128,
                'required' => true,
                'tooltip' => 'The city of the Arena\'s address. '
                . 'There is a 128 character limit for the city. This field is required.',
                'example' => 'Some City',
            ),
            array(
                'name' => 'state',
                'display' => 'State',
                'type' => 'string',
                'size' => 2,
                'required' => true,
                'tooltip' => 'The two character state abbreviation of the Arena\'s address. '
                . 'There is a 2 character limit for the state. This field is required.',
                'example' => 'MN',
            ),
            array(
                'name' => 'zip',
                'display' => 'Zip',
                'type' => 'string',
                'size' => 5,
                'required' => true,
                'tooltip' => 'The five digit zip code of the Arena\'s address. '
                . 'There is a 5 character limit for the zip code. This field is required.',
                'example' => '12345',
            ),
            array(
                'name' => 'lat',
                'display' => 'Lattitude',
                'type' => 'float',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The lattitude of the Arena. '
                . 'This field may be overwritten if the Arena is later geocoded.',
                'example' => '43.12345678',
            ),
            array(
                'name' => 'lng',
                'display' => 'Longitude',
                'type' => 'float',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The longitude of the Arena. '
                . 'This field may be overwritten if the Arena is later geocoded.',
                'example' => '-72.987654321',
            ),
            array(
                'name' => 'phone',
                'display' => 'Phone',
                'type' => 'phone',
                'size' => 10,
                'required' => false,
                'tooltip' => 'The ten digit phone number of the Arena. '
                . 'There is a 10 character limit for the phone number.',
                'example' => '0123456789',
            ),
            array(
                'name' => 'ext',
                'display' => 'Extension',
                'type' => 'phone',
                'size' => 10,
                'required' => false,
                'tooltip' => 'The ten digit extension for the phone number of the Arena. '
                . 'There is a 10 character limit for the extension.',
                'example' => '0123456789',
            ),
            array(
                'name' => 'fax',
                'display' => 'Fax',
                'type' => 'phone',
                'size' => 10,
                'required' => false,
                'tooltip' => 'The ten digit fax number of the Arena. '
                . 'There is a 10 character limit for the fax number.',
                'example' => '0123456789',
            ),
            array(
                'name' => 'fax_ext',
                'display' => 'Fax Extension',
                'type' => 'phone',
                'size' => 10,
                'required' => false,
                'tooltip' => 'The ten digit extension for the fax number of the Arena. ',
                'example' => '0123456789',
            ),
            array(
                'name' => 'url',
                'display' => 'Homepage URL',
                'type' => 'string',
                'size' => 511,
                'required' => false,
                'tooltip' => 'The hompage for the Arena. '
                . 'Don\'t forget to add http:// or https://. '
                . 'There is a 511 character limit for this field.',
                'example' => 'http://www.myarena.com',
            ),
            array(
                'name' => 'description',
                'display' => 'Description',
                'type' => 'text',
                'size' => 0,
                'required' => false,
                'tooltip' => 'The description for the Arena. '
                . 'The description may contain HTML markup. '
                . 'The description will appear at the top of the Arena\'s page '
                . 'on this site. '
                . 'There is no limit to the amount of text you may enter.',
                'example' => CHtml::encode('<h1>My Heading</h1><p>My Paragraph</p>'),
            ),
            array(
                'name' => 'tags',
                'display' => 'Tags',
                'type' => 'string',
                'size' => 1024,
                'required' => false,
                'tooltip' => 'The tags for the Arena. '
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
                'tooltip' => 'The notes for the Arena. '
                . 'The notes may contain HTML markup. '
                . 'The notes will appear at the bottom of the Arena\'s page '
                . 'on this site. '
                . 'There is no limit to the amount of text you may enter.',
                'example' => CHtml::encode('<h1>My Heading</h1><p>My Paragraph</p>'),
            ),
            array(
                'name' => 'status_id',
                'display' => 'Status ID',
                'type' => 'integer',
                'size' => 0,
                'required' => false,
                'tooltip' => 'Here you may enter the Status ID of the Arena. '
                . 'Please note that entering in an invalid Status ID will result '
                . 'with the import failing. It is best to not map this field '
                . 'unless you know what you are doing.',
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
                'type' => 'string',
                'hide' => 'all'
            ),
            'name' => array(
                'name' => 'name',
                'display' => 'Name',
                'type' => 'alpha',
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
            'address_line1' => array(
                'name' => 'address_line1',
                'display' => 'Address Line 1',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'address_line2' => array(
                'name' => 'address_line2',
                'display' => 'Address Line 2',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'city' => array(
                'name' => 'city',
                'display' => 'City',
                'type' => 'alpha',
                'hide' => 'phone,tablet'
            ),
            'state' => array(
                'name' => 'state',
                'display' => 'State',
                'type' => 'alpha',
                'hide' => 'phone,tablet'
            ),
            'zip' => array(
                'name' => 'zip',
                'display' => 'Zip',
                'type' => 'numeric',
                'hide' => 'phone,tablet'
            ),
            'geocoded' => array(
                'name' => 'geocoded',
                'display' => 'Geocoded',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'phone' => array(
                'name' => 'phone',
                'display' => 'Phone',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'ext' => array(
                'name' => 'ext',
                'display' => 'Extension',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'fax' => array(
                'name' => 'fax',
                'display' => 'Fax',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'fax_ext' => array(
                'name' => 'fax_ext',
                'display' => 'Fax Extension',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'logo' => array(
                'name' => 'logo',
                'display' => 'Logo',
                'type' => 'alpha',
                'hide' => 'all'
            ),
            'url' => array(
                'name' => 'url',
                'display' => 'Homepage URL',
                'type' => 'alpha',
                'hide' => 'all'
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
            'managers' => array(
                'name' => 'managers',
                'display' => 'Managers',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'locations' => array(
                'name' => 'locations',
                'display' => 'Locations',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'contacts' => array(
                'name' => 'contacts',
                'display' => 'Contacts',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'reservation_policies' => array(
                'name' => 'reservation_policies',
                'display' => 'Reservation Policies',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'outstanding_event_requests' => array(
                'name' => 'outstanding_event_requests',
                'display' => 'Outstanding Event Requests',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'outstanding_reservations' => array(
                'name' => 'outstanding_reservations',
                'display' => 'Outstanding Reservations',
                'type' => 'numeric',
                'hide' => 'all'
            ),
        );
    }

    /**
     * Checks if the passed in user is assigned to the arena.
     * @param integer $uid The id of the user to check.
     * @return boolean True if the user is already assigned to the arena.
     */
    public function isUserAssigned($uid)
    {
        Yii::trace(
                'isUserAssigned()',
                'application.models.Arena'
        );
        
        $sql = "SELECT user_id FROM arena_user_assignment ";
        $sql .= "WHERE arena_id = :arenaId AND user_id = :userId";

        $command = Yii::app()->db->createCommand($sql);
		
        $command->bindValue(
                ':arenaId',
                $this->id,
                PDO::PARAM_INT
        );

        $command->bindValue(
                ':userId',
                $uid,
                PDO::PARAM_INT
        );

        return $command->execute() == 1;
    }
    
    /**
     * Assigns the user to the arena.
     * @param integer $uid The id of the user to be assigned.
     * @return boolean True if the user has been assigned to the arena.
     * @throws CDbException
     */
    public function assignUser($uid)
    {
        Yii::trace(
                'assignUser()',
                'application.models.Arena'
        );
        
        $command = Yii::app()->db->createCommand();
        
        return $command->insert(
                'arena_user_assignment',
                array(
                    'user_id' => $uid,
                    'arena_id' => $this->id,
                    'created_by_id' => Yii::app()->user->id,
                    'updated_by_id' => Yii::app()->user->id,
                    'created_on' => new CDbExpression('NOW()'),
                    'updated_on' => new CDbExpression('NOW()'),
                )
        ) == 1;
    }
    
    /**
     * Removes the user from the arena.
     * @param integer $uid The id of the user to be removed.
     * @return boolean True if the user has been removed from the arena.
     */
    public function removeUser($uid)
    {
        Yii::trace(
                'removeUser()',
                'application.models.Arena'
        );
        
        $command = Yii::app()->db->createCommand();
        
        return $command->delete(
                'arena_user_assignment',
                'user_id = :userId AND arena_id = :arenaId',
                array(
                    ':userId' => $uid,
                    ':arenaId' => $this->id
                )
        ) == 1;
    }
    
    /**
     * @return array a list of links that point to the arena list filtered by every tag of this arena
     */
    public function getTagLinks()
    {
        $links = array();
        foreach(Tag::string2array($this->tags) as $tag)
            $links[] = CHtml::link(CHtml::encode($tag), array('arena/search', 'tag' => $tag), array('class' => 'btn btn-small'));
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
     * Tags the record with the Arena's name, city, and state, plus full state name.
     */
    public function autoTag()
    {
        $tags = Tag::string2array($this->tags);
        
        $tags[] = $this->name;
        $tags[] = $this->city;
        $tags[] = $this->state;
        $tags[] = UnitedStatesNames::getName($this->state);
        
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
     * Returns a summary record for each arena assigned to user.
     * The results can be further restricted by passing in a status code.
     * @param integer $uid The user to get the arenas for.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The arena summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedArenasSummary($uid, $sid = null) {
        // Let's start by building up our query
        $ret = array();

        $sql = "SELECT a.id, "
                . "CASE WHEN a.external_id IS NULL THEN 'Not set' ELSE a.external_id END AS external_id, "
                . "a.name, "
                . "CASE WHEN a.description IS NULL THEN 'Not set' ELSE 'Yes' END as description, "
                . "CASE WHEN a.tags IS NULL THEN 'Not set' ELSE 'Yes' END as tags, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL THEN 'Not set' ELSE a.address_line2 END AS address_line2, "
                . "a.city, "
                . "a.state, "
                . "a.zip, "
                . "IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded, "
                . "CASE WHEN a.phone IS NULL THEN 'Not set' ELSE a.phone END AS phone, "
                . "CASE WHEN a.ext IS NULL THEN 'Not set' ELSE a.ext END AS ext, "
                . "CASE WHEN a.fax IS NULL THEN 'Not set' ELSE a.fax END AS fax, "
                . "CASE WHEN a.fax_ext IS NULL THEN 'Not set' ELSE a.fax_ext END AS fax_ext, "
                . "CASE WHEN a.logo IS NULL THEN 'Not set' ELSE 'Yes' END AS logo, "
                . "CASE WHEN a.url IS NULL THEN 'Not set' ELSE 'Yes' END AS url, "
                . "CASE WHEN a.notes IS NULL THEN 'Not set' ELSE 'Yes' END AS notes, "
                . "(SELECT s.display_name FROM arena_status s WHERE s.id = a.status_id) AS status, "
                . "(SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua WHERE aua.arena_id = a.id) AS managers, "
                . "(SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations, "
                . "(SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts, "
                . "(SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN "
                . "    (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN "
                . "        (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.id = a.id AND e.id IN "
                . "    (SELECT r.event_id FROM reservation r WHERE e.id = r.event_id AND r.status_id IN "
                . "        (SELECT rs.id FROM reservation_status rs WHERE rs.name IN ('BOOKED')))) AS outstanding_reservations "
                . "FROM arena a "
                . "    INNER JOIN arena_user_assignment aua "
                . "    ON a.id = aua.arena_id "
                . "    INNER JOIN user u "
                . "    ON u.id = aua.user_id "
                . "WHERE u.id = :uid ";
        
        if($sid !== null) {
            $sql .= "AND a.status_id = :sid ";
        }
        
        $sql .= "ORDER BY a.name ASC";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $ret['items'] = $command->queryAll(true);

        $arenaCount = count($ret['items']);
        
        for($i = 0; $i < $arenaCount; $i++) {
            if(is_numeric($ret['items'][$i]['phone'])) {
                $ret['items'][$i]['phone'] = RinkfinderActiveRecord::format_telephone($ret['items'][$i]['phone']);
            }
            
            if(is_numeric($ret['items'][$i]['fax'])) {
                $ret['items'][$i]['fax'] = RinkfinderActiveRecord::format_telephone($ret['items'][$i]['fax']);
            }
            
            $ret['items'][$i]['endpoint'] = CHtml::normalizeUrl(array(
                    'management/update',
                    'model' => 'Arena',
                    'aid' => $ret['items'][$i]['id'],
                )
            );
        }
        
        $ret['count'] = $arenaCount;
        $ret['model'] = 'arena';
        $ret['action'] = 'index';
        
        if($sid === null) {
            $ret['endpoint'] = CHtml::normalizeUrl(array(
                    'management/index',
                    'model' => 'Arena'
                )
            );
        } else {
            $ret['endpoint'] = CHtml::normalizeUrl(array(
                    'management/index',
                    'model' => 'Arena',
                    'sid' => $sid
                )
            );
        }
        // Ok, lets return this stuff!!
        return $ret;
    }
}
