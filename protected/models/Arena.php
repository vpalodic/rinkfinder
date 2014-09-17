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
    public $oldTags;
    
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
                'status_id, ext, fax_ext, lock_version, created_by_id, updated_by_id',
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
                'max' => 10,
                'min' => 10
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
                'arena_contact_assignment(arena_id, contact_id)',
                'condition' => 'contacts.active = 1'
            ),
            'primaryContact' => array(
                self::MANY_MANY,
                'Contact',
                'arena_contact_assignment(arena_id, contact_id)',
                'condition' => 'primaryContact.active = 1 AND primaryContact_primaryContact.primary_contact = 1'
            ),
            'secondaryContacts' => array(
                self::MANY_MANY,
                'Contact',
                'arena_contact_assignment(arena_id, contact_id)',
                'condition' => 'secondaryContacts.active = 1 AND secondaryContacts_secondaryContacts.primary_contact = 0'
            ),
            'contactsCount' => array(
                self::STAT,
                'Contact',
                'arena_contact_assignment(arena_id, contact_id)',
                'condition' => 't.active = 1'
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
            'managersCount' => array(
                self::STAT,
                'User',
                'arena_user_assignment(arena_id, user_id)'
            ),
            'fileUploads' => array(
                self::HAS_MANY,
                'FileUpload',
                'arena_id'
            ),
            'locations' => array(
                self::HAS_MANY,
                'Location',
                'arena_id'
            ),
            'locationsCount' => array(
                self::STAT,
                'Location',
                'arena_id',
                'condition' => 't.status_id = (SELECT ls.id FROM location_status ls WHERE ls.name = "OPEN")',
            ),
            'events' => array(
                self::HAS_MANY,
                'Event',
                'arena_id'
            ),
            'eventsCount' => array(
                self::STAT,
                'Event',
                'arena_id',
                'condition' => 't.status_id = (SELECT es.id FROM event_status es WHERE es.name = "OPEN")'
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
            'url' => 'Home Page',
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
    
    public function scopes() {

        return array(
            'active' => array(
                'condition' => 't.status_id = (SELECT ass.id FROM arena_status ass WHERE ass.name = "OPEN")',
                'order' => 't.name',
            ),
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
     * Returns an array of open arenas for use in a select list
     * @return array[] the array of arenas
     * @throws CDbException
     */
    public static function getOpenList()
    {
        $sql = 'SELECT a.id, a.name, a.city '
                . 'FROM arena a '
                . 'WHERE a.status_id = (SELECT ass.id FROM arena_status ass WHERE ass.name = "OPEN") '
                . 'ORDER BY a.name ASC, a.city';
        
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of arena statuses
     * @return array[] the array of arena statuses
     * @throws CDbException
     */
    public static function getStatuses()
    {
        $sql = 'SELECT * FROM arena_status';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns an array of arena statuses
     * @return array[] the array of arena statuses
     * @throws CDbException
     */
    public static function getActiveStatusList()
    {
        $sql = 'SELECT id AS value, display_name AS text FROM arena_status WHERE active = 1 ORDER BY display_order';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryAll(true);
    }
    
    /**
     * Returns the display_name of the Arena status
     * @return string
     * @throws CDbException
     */
    public function getStatusAlias()
    {
        $sql = 'SELECT display_name FROM arena_status WHERE id = :sid';
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':sid', (integer)$this->status_id, PDO::PARAM_INT);
        return $command->queryScalar();
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
                'linkText' => 'Select'
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
                'display' => 'Events With Open Requests',
                'type' => 'numeric',
                'hide' => 'all'
            ),
            'outstanding_reservations' => array(
                'name' => 'outstanding_reservations',
                'display' => 'Events with Open Reservations',
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
    public function normalizeTags($attribute = null, $params = null)
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
     * Returns the arena count for each arena that is assigned to the user
     * @param integer $uid The user to get the arenas for.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The arena counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Arena',
        );

        $sql = 'SELECT s.id, s.name, s.description, s.display_name, '
                . 's.display_order, IF(sc.count IS NULL, 0, sc.count) AS count '
                . 'FROM arena_status s '
                . 'LEFT JOIN '
                . '(SELECT s1.id, COUNT(a.id) AS count '
                . ' FROM arena a '
                . ' INNER JOIN arena_user_assignment aua '
                . ' ON a.id = aua.arena_id '
                . ' INNER JOIN user u '
                . ' ON u.id = aua.user_id '
                . ' INNER JOIN arena_status s1 '
                . ' ON a.status_id = s1.id '
                . ' WHERE u.id = :uid ';
        
        if($sid !== null) {
            $sql .= "AND a.status_id = :sid ";
            $parms['aid'] = $sid;
        }
        
        $sql .= ' GROUP BY s1.id) AS sc '
                . 'ON s.id = sc.id '
                . 'ORDER BY s.display_order ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($sid !== null) {
            $command->bindParam(':sid', $sid, PDO::PARAM_INT);
        }
        
        $arenaCountTotal = 0;
        
        $ret['status'] = $command->queryAll(true);

        $arenaCount = count($ret['status']);
        
        for($i = 0; $i < $arenaCount; $i++) {
            $arenaCountTotal += (integer)$ret['status'][$i]['count'];
            
            $temp = $parms;
            $temp['sid'] = $ret['status'][$i]['id'];
            
            $ret['status'][$i]['endpoint'] = CHtml::normalizeUrl($temp);
        }
        
        $ret['total'] = $arenaCountTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        
        return $ret;
    }

    /**
     * Returns a summary record for each arena assigned to user.
     * The results can be further restricted by passing in a status code.
     * @param integer $uid The user to get the arenas for.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The arena summeries or an empty array.
     * @throws CDbException
     */
    public static function getAssignedSummary($uid, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Arena',
        );

        $sql = "SELECT a.id, "
                . "a.external_id, "
                . "a.name, "
                . "a.description, "
                . "a.tags, "
                . "a.address_line1, "
                . "a.address_line2, "
                . "a.city, "
                . "a.state, "
                . "a.zip, "
                . "IF(a.lat IS NULL OR a.lat = 0 OR a.lng IS NULL OR a.lng = 0, 'No', 'Yes') AS geocoded, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url, "
                . "a.notes, "
                . "(SELECT s.display_name FROM arena_status s WHERE s.id = a.status_id) AS status, "
                . "(SELECT COUNT(DISTINCT aua.user_id) FROM arena_user_assignment aua INNER JOIN auth_assignment aa ON aua.user_id = aa.userid AND aa.itemname IN ('Manager', 'RestrictedManager') WHERE aua.arena_id = a.id) AS managers, "
                . "(SELECT COUNT(DISTINCT l.id) FROM location l WHERE l.arena_id = a.id) AS locations, "
                . "(SELECT COUNT(DISTINCT aca.contact_id) FROM arena_contact_assignment aca WHERE aca.arena_id = a.id) AS contacts, "
                . "(SELECT COUNT(DISTINCT arp.id) FROM arena_reservation_policy arp WHERE arp.arena_id = a.id) AS reservation_policies, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN "
                . "    (SELECT er.event_id FROM event_request er WHERE e.id = er.event_id AND er.status_id IN "
                . "        (SELECT ers.id FROM event_request_status ers WHERE ers.name IN ('PENDING', 'ACKNOWLEDGED')))) AS outstanding_event_requests, "
                . "(SELECT COUNT(DISTINCT e.id) FROM event e WHERE e.arena_id = a.id AND e.id IN "
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
            $parms['sid'] = $sid;
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
                    'management/view',
                    'model' => 'Arena',
                    'id' => $ret['items'][$i]['id'],
                )
            );
        }
        
        $ret['count'] = $arenaCount;
        $ret['model'] = 'arena';
        $ret['action'] = 'index';
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        $ret['statuses'] = CHtml::listData(Arena::getStatuses(), 'name', 'display_name');
        
        // Ok, lets return this stuff!!
        return $ret;
    }
    
    /**
     * Returns a record for use with Google Maps.
     * @param float $lat The lattitude of the center point.
     * @param float $lng The longitude of the center point.
     * @param float $radius The radius of the search circle.
     * @param mixed[] $params An array of the optional parameters below.
     * @param integer $offset The offset to start returning records from.
     * @param integer $limit The maximum records to return. Returns all if 0.
     * @param boolean $open If true, limits the search to open facilities only.
     * @param float $price The maximum price to search on.
     * @param string $start_date The date of events to start search on.
     * @param string $end_date The last date of events to search to.
     * @param string $start_time The start time of the event to search from.
     * @param string $end_time The start time of the event to search to.
     * @param integer[] $types The event types to search for.
     * @return mixed[] The arena markers or an empty array.
     * @throws CDbException
     */
    public static function getMarkersWithContactsEvents($lat, $lng, $radius, $params = array())
    {
        // Set the default values before we go snooping through the passed in
        // parameters...
        $offset = (isset($params['offset']) && is_numeric($params['offset'])) ? (integer)$params['offset'] : 0;
        $limit = (isset($params['limit']) && is_numeric($params['limit'])) ? (integer)$params['limit'] : 0;
        $open = (isset($params['open']) && is_bool($params['open'])) ? (bool)$params['open'] : true;
        $price = (isset($params['price']) && is_numeric($params['price'])) ? (float)floatval(filter_var($params['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) : 0;
        $start_date = (isset($params['start_date']) && is_string($params['start_date']) && !empty($params['start_date'])) ? (string)$params['start_date'] : null;
        $end_date = (isset($params['end_date']) && is_string($params['end_date']) && !empty($params['end_date'])) ? (string)$params['end_date'] : null;
        $start_time = (isset($params['start_time']) && is_string($params['start_time']) && !empty($params['start_time'])) ? (string)$params['start_time'] : null;
        $end_time = (isset($params['end_time']) && is_string($params['end_time']) && !empty($params['end_time'])) ? (string)$params['end_time'] : null;
        $types = (isset($params['types']) && is_array($params['types'])) ? $params['types'] : array();
        
        // Let's start by building up our query
        $url = Yii::app()->createUrl('arena/view');
        $eventsUrlParams = array();
        
        $where = '';
        $incEvents = false;
        $typeCount = count($types);
        
        if($open === true) {
            $where .= "WHERE a.status_id = (SELECT s.id FROM arena_status s WHERE "
                    . "s.name = 'OPEN') ";
        }
        
        if($price > 0 || $start_date != null || $start_time != null || $end_date != null ||
                $end_time != null || $typeCount > 0) {
            $incEvents = true;
            
            $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS view_url, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url AS home_url, "
                . "a.tags, "
                . "a.lat, "
                . "a.lng, "
                . "( 3959 * ACOS( COS( RADIANS( :lat ) ) * COS( RADIANS( a.lat ) "
                . ") * COS( RADIANS( a.lng ) - RADIANS( :lng ) ) + SIN( RADIANS( "
                . ":lat ) ) * SIN( RADIANS( a.lat ) ) ) ) AS distance, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email, "
                . "et.id AS event_type_id, "
                . "et.display_name AS event_type_name, "
                . "ec.count AS event_count, "
                . "DATE_FORMAT(ec.start_date_time, '%c/%e/%Y %l:%i %p') AS start_date_time "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id"
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 ";
        } else {
            $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS view_url, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url AS home_url, "
                . "a.tags, "
                . "a.lat, "
                . "a.lng, "
                . "( 3959 * ACOS( COS( RADIANS( :lat ) ) * COS( RADIANS( a.lat ) "
                . ") * COS( RADIANS( a.lng ) - RADIANS( :lng ) ) + SIN( RADIANS( "
                . ":lat ) ) * SIN( RADIANS( a.lat ) ) ) ) AS distance, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id"
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 ";
        }
        
        $eventSql = "    INNER JOIN (SELECT e.arena_id, "
                . "        e.type_id, "
                . "        COUNT(e.id) AS count, "
                . "        MIN(CAST(CONCAT(e.start_date, ' ', e.start_time) AS DATETIME)) AS start_date_time "
                . "        FROM event e "
                . "        WHERE e.status_id = (SELECT es.id FROM event_status es WHERE es.name = 'OPEN') ";

        if($price > 0) {
            $eventSql .= 'AND e.price <= :price ';
            $eventsUrlParams['price'] = $price;
        }
        
        if($start_date != null && $end_date != null) {
            $today = strtotime(date("Y-m-d", time()));
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            
            if($start < $today) {
                $start_date = date("Y-m-d", $today);
                $start = $today;
            }
            
            if($end < $start) {
                $end_date = $start_date;
                $end = $start;
            }
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CAST(:end_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
            $eventsUrlParams['end_date'] = $end_date;
        } elseif($start_date != null && $end_date == null) {
            $today = strtotime(date("Y-m-d", time()));
            $start = strtotime($start_date);
            
            if($start < $today) {
                $start_date = date("Y-m-d", $today);
                $start = $today;
            }
            
            $eventSql .= 'AND e.start_date = CAST(:start_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
        } elseif($start_date == null && $end_date != null) {
            $start_date = date("Y-m-d", time());
            $today = strtotime($start_date);
            $end = strtotime($end_date);
            
            if($end < $today) {
                $end_date = $start_date;
                $end = $today;
            }
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CASE(:end_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
            $eventsUrlParams['end_date'] = $end_date;
        } else {
            $start_date = date("Y-m-d", time());
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) ';
        }
        
        if($start_time != null && $end_time != null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) '
                    . 'AND e.start_time <= CAST(:end_time AS TIME) ';
            $eventsUrlParams['start_time'] = $start_time;
            $eventsUrlParams['end_time'] = $end_time;
        } elseif($start_time != null && $end_time == null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) ';
            $eventsUrlParams['start_time'] = $start_time;
        } elseif($start_time == null && $end_time != null) {
            $eventSql .= 'AND e.start_time <= CAST(:end_time AS TIME) ';
            $eventsUrlParams['end_time'] = $end_time;
        }
        
        if($typeCount > 0) {
            $eventSql .= 'AND e.type_id IN (';
            
            for($i = 0; $i < $typeCount; $i++) {
                if($i + 1 == $typeCount) {
                    $eventSql .= ':eventType' . $i . ') ';
                } else {
                    $eventSql .= ':eventType' . $i . ', ';
                }
            }
        }
        
        $eventSql .= "        GROUP BY e.arena_id, e.type_id) ec "
                . "    ON a.id = ec.arena_id "
                . "    INNER JOIN event_type et "
                . "    ON ec.type_id = et.id AND et.active = 1 ";
        
        
        if($incEvents == true) {
            $sql .= $eventSql . $where;
        } else {
            $sql .= $where;
        }

        if($radius != null && $radius > 0) {
            $sql .= "HAVING distance <= :radius "
                    . "ORDER BY distance ASC, arena_name ASC, contact_type ASC, contact_name ASC ";
        } else {
            $sql .= "ORDER BY distance ASC, arena_name ASC, contact_type ASC, contact_name ASC ";
        }
        
        if($incEvents == true) {
            $sql .= ", et.display_order ASC ";
        }

        if($limit > 0) {
            $sql .= "LIMIT :offset, :limit";
        }
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':lat', $lat, PDO::PARAM_STR);
        $command->bindParam(':lng', $lng, PDO::PARAM_STR);
        
        if($radius != null && $radius > 0) {
            $command->bindParam(':radius', $radius, PDO::PARAM_STR);
        }
        
        if($limit > 0) {
            $command->bindValue(':offset', (integer)$offset, PDO::PARAM_INT);
            $command->bindValue(':limit', (integer)$limit, PDO::PARAM_INT);
        }
        
        if($price > 0) {
            $command->bindParam(':price', $price, PDO::PARAM_STR);
        }
        
        if($start_date != null) {
            $command->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        }
        
        if($end_date != null) {
            $command->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        }
        
        if($start_time != null) {
            $command->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        }
            
        if($end_time != null) {
            $command->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        }
            
        if($typeCount > 0) {
            for($i = 0; $i < $typeCount; $i++) {
                $command->bindValue(':eventType' . $i, (integer)$types[$i], PDO::PARAM_INT);
            }
        }
        
        $ret = $command->queryAll(true);
        
        return Arena::buildAddressMarkersResults($ret, $eventsUrlParams, $typeCount, $types);
    }

    /**
     * Returns a record for use with Index action of the Arena Controler.
     * @param mixed[] $params An array of the optional parameters below.
     * @param integer $offset The offset to start returning records from.
     * @param integer $limit The maximum records to return. Returns all if 0.
     * @param boolean $open If true, limits the search to open facilities only.
     * @param float $price The maximum price to search on.
     * @param string $start_date The date of events to start search on.
     * @param string $end_date The last date of events to search to.
     * @param string $start_time The start time of the event to search from.
     * @param string $end_time The start time of the event to search to.
     * @param integer[] $types The event types to search for.
     * @return mixed[] The arena markers or an empty array.
     * @throws CDbException
     */
    public static function getIndexWithContactsEvents($params = array())
    {
        // Set the default values before we go snooping through the passed in
        // parameters...
        $offset = (isset($params['offset']) && is_numeric($params['offset'])) ? (integer)$params['offset'] : 0;
        $limit = (isset($params['limit']) && is_numeric($params['limit'])) ? (integer)$params['limit'] : 0;
        $open = (isset($params['open']) && is_bool($params['open'])) ? (bool)$params['open'] : true;
        $price = (isset($params['price']) && is_numeric($params['price'])) ? (float)floatval(filter_var($params['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) : 0;
        $start_date = (isset($params['start_date']) && is_string($params['start_date']) && !empty($params['start_date'])) ? (string)$params['start_date'] : null;
        $end_date = (isset($params['end_date']) && is_string($params['end_date']) && !empty($params['end_date'])) ? (string)$params['end_date'] : null;
        $start_time = (isset($params['start_time']) && is_string($params['start_time']) && !empty($params['start_time'])) ? (string)$params['start_time'] : null;
        $end_time = (isset($params['end_time']) && is_string($params['end_time']) && !empty($params['end_time'])) ? (string)$params['end_time'] : null;
        $types = (isset($params['types']) && is_array($params['types'])) ? $params['types'] : array();
        
        // Let's start by building up our query
        $url = Yii::app()->createUrl('arena/view');
        $eventsUrlParams = array();
        
        $where = '';
        $incEvents = false;
        $typeCount = count($types);
        
        if($open === true) {
            $where .= "WHERE a.status_id = (SELECT s.id FROM arena_status s WHERE "
                    . "s.name = 'OPEN') ";
        }
        
        if($price > 0 || $start_date != null || $start_time != null || $end_date != null ||
                $end_time != null || $typeCount > 0) {
            $incEvents = true;
            
            $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS view_url, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url AS home_url, "
                . "a.tags, "
                . "a.lat, "
                . "a.lng, "
                . "CASE WHEN l.count IS NULL THEN 0 ELSE l.count END AS location_count, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email, "
                . "et.id AS event_type_id, "
                . "et.display_name AS event_type_name, "
                . "ec.count AS event_count, "
                . "DATE_FORMAT(ec.start_date_time, '%c/%e/%Y %l:%i %p') AS start_date_time "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id"
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 "
                . "    LEFT OUTER JOIN (SELECT al.arena_id, COUNT(al.id) AS count "
                . "        FROM location al "
                . "        WHERE al.status_id = (SELECT ls.id "
                . "            FROM location_status ls "
                . "            WHERE ls.name = 'OPEN') "
                . "        GROUP BY al.arena_id) l "
                . "    ON l.arena_id = a.id ";
        } else {
            $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS view_url, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url AS home_url, "
                . "a.tags, "
                . "a.lat, "
                . "a.lng, "
                . "CASE WHEN l.count IS NULL THEN 0 ELSE l.count END AS location_count, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id"
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 "
                . "    LEFT OUTER JOIN (SELECT al.arena_id, COUNT(al.id) AS count "
                . "        FROM location al "
                . "        WHERE al.status_id = (SELECT ls.id "
                . "            FROM location_status ls "
                . "            WHERE ls.name = 'OPEN') "
                . "        GROUP BY al.arena_id) l "
                . "    ON l.arena_id = a.id ";
        }
        
        $eventSql = "    LEFT OUTER JOIN (SELECT e.arena_id, "
                . "        e.type_id, "
                . "        COUNT(e.id) AS count, "
                . "        MIN(CAST(CONCAT(e.start_date, ' ', e.start_time) AS DATETIME)) AS start_date_time "
                . "        FROM event e "
                . "        WHERE e.status_id = (SELECT es.id FROM event_status es WHERE es.name = 'OPEN') ";

        if($price > 0) {
            $eventSql .= 'AND e.price <= :price ';
            $eventsUrlParams['price'] = $price;
        }
        
        if($start_date != null && $end_date != null) {
            $today = strtotime(date("Y-m-d", time()));
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            
            if($start < $today) {
                $start_date = date("Y-m-d", $today);
                $start = $today;
            }
            
            if($end < $start) {
                $end_date = $start_date;
                $end = $start;
            }
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CAST(:end_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
            $eventsUrlParams['end_date'] = $end_date;
        } elseif($start_date != null && $end_date == null) {
            $today = strtotime(date("Y-m-d", time()));
            $start = strtotime($start_date);
            
            if($start < $today) {
                $start_date = date("Y-m-d", $today);
                $start = $today;
            }
            
            $eventSql .= 'AND e.start_date = CAST(:start_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
        } elseif($start_date == null && $end_date != null) {
            $start_date = date("Y-m-d", time());
            $today = strtotime($start_date);
            $end = strtotime($end_date);
            
            if($end < $today) {
                $end_date = $start_date;
                $end = $today;
            }
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CASE(:end_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
            $eventsUrlParams['end_date'] = $end_date;
        } else {
            $start_date = date("Y-m-d", time());
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) ';
            $eventsUrlParams['start_date'] = $start_date;
        }
        
        if($start_time != null && $end_time != null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) '
                    . 'AND e.start_time <= CAST(:end_time AS TIME) ';
            $eventsUrlParams['start_time'] = $start_time;
            $eventsUrlParams['end_time'] = $end_time;
        } elseif($start_time != null && $end_time == null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) ';
            $eventsUrlParams['start_time'] = $start_time;
        } elseif($start_time == null && $end_time != null) {
            $eventSql .= 'AND e.start_time <= CAST(:end_time AS TIME) ';
            $eventsUrlParams['end_time'] = $end_time;
        }
        
        if($typeCount > 0) {
            $eventSql .= 'AND e.type_id IN (';
            
            for($i = 0; $i < $typeCount; $i++) {
                if($i + 1 == $typeCount) {
                    $eventSql .= ':eventType' . $i . ') ';
                } else {
                    $eventSql .= ':eventType' . $i . ', ';
                }
            }
        }
        
        $eventSql .= "        GROUP BY e.arena_id, e.type_id) ec "
                . "    ON a.id = ec.arena_id "
                . "    LEFT OUTER JOIN event_type et "
                . "    ON ec.type_id = et.id AND et.active = 1 ";
        
        
        if($incEvents == true) {
            $sql .= $eventSql . $where;
        } else {
            $sql .= $where;
        }

        $sql .= "ORDER BY arena_name ASC, contact_type ASC, contact_name ASC ";
        
        if($incEvents == true) {
            $sql .= ", et.display_order ASC ";
        }

        if($limit > 0) {
            $sql .= "LIMIT :offset, :limit";
        }
        
        $command = Yii::app()->db->createCommand($sql);
        
        if($limit > 0) {
            $command->bindValue(':offset', (integer)$offset, PDO::PARAM_INT);
            $command->bindValue(':limit', (integer)$limit, PDO::PARAM_INT);
        }
        
        if($price > 0) {
            $command->bindParam(':price', $price, PDO::PARAM_STR);
        }
        
        if($start_date != null) {
            $command->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        }
        
        if($end_date != null) {
            $command->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        }
        
        if($start_time != null) {
            $command->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        }
            
        if($end_time != null) {
            $command->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        }
            
        if($typeCount > 0) {
            for($i = 0; $i < $typeCount; $i++) {
                $command->bindValue(':eventType' . $i, (integer)$types[$i], PDO::PARAM_INT);
            }
        }
        
        $ret = $command->queryAll(true);
        
        return Arena::buildAddressMarkersResults($ret, $eventsUrlParams, $typeCount, $types);
    }

    /**
     * Returns a record for use with Index action of the Arena Controler.
     * @param integer $id The Arena ID that we are retrieving
     * @param mixed[] $params An array of the optional parameters below.
     * @param integer $offset The offset to start returning records from.
     * @param integer $limit The maximum records to return. Returns all if 0.
     * @param boolean $open If true, limits the search to open facilities only.
     * @param float $price The maximum price to search on.
     * @param string $start_date The date of events to start search on.
     * @param string $end_date The last date of events to search to.
     * @param string $start_time The start time of the event to search from.
     * @param string $end_time The start time of the event to search to.
     * @param integer[] $types The event types to search for.
     * @return mixed[] The arena markers or an empty array.
     * @throws CDbException
     */
    public static function getViewWithContactsLocations($aid, $params = array())
    {
        // Set the default values before we go snooping through the passed in
        // parameters...
        $offset = (isset($params['offset']) && is_numeric($params['offset'])) ? (integer)$params['offset'] : 0;
        $limit = (isset($params['limit']) && is_numeric($params['limit'])) ? (integer)$params['limit'] : 0;
        $open = (isset($params['open']) && is_bool($params['open'])) ? (bool)$params['open'] : true;
        
        // Let's start by building up our query
        $url = Yii::app()->createUrl('arena/view');
        $where = '';
        
        if($open === true) {
            $where .= "WHERE a.status_id = (SELECT s.id FROM arena_status s WHERE "
                    . "s.name = 'OPEN') AND a.id = :aid ";
        } else {
            $where .= "WHERE a.id = :aid ";
        }
        
        $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS view_url, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.ext, "
                . "a.fax, "
                . "a.fax_ext, "
                . "a.logo, "
                . "a.url AS home_url, "
                . "a.tags, "
                . "a.lat, "
                . "a.lng, "
                . "a.description, "
                . "a.notes, "
                . "CASE WHEN aca.primary_contact IS NULL THEN NULL WHEN "
                . "aca.primary_contact = 1 THEN 'Primary' ELSE 'Secondary' END "
                . "AS contact_type, "
                . "c.id AS contact_id, "
                . "CONCAT(c.first_name, ' ', c.last_name) AS contact_name, "
                . "c.phone AS contact_phone, "
                . "c.ext AS contact_ext, "
                . "c.fax AS contact_fax, "
                . "c.fax_ext AS contact_fax_ext, "
                . "c.email AS contact_email, "
                . "lt.id AS location_type_id, "
                . "lt.name AS location_type_name, "
                . "lt.display_name AS location_type_display_name, "
                . "l.id AS location_id, "
                . "l.name AS location_name, "
                . "l.description AS location_description, "
                . "l.tags AS location_tags, "
                . "l.length AS location_length, "
                . "l.width AS location_width, "
                . "l.radius AS location_radius, "
                . "l.seating AS location_seating, "
                . "l.notes AS location_notes "
                . "FROM arena a "
                . "    LEFT OUTER JOIN arena_contact_assignment aca "
                . "    ON a.id = aca.arena_id"
                . "    LEFT OUTER JOIN contact c "
                . "    ON c.id = aca.contact_id AND c.active = 1 "
                . "    LEFT OUTER JOIN location l "
                . "    ON l.arena_id = a.id AND l.status_id = (SELECT ls.id "
                . "        FROM location_status ls "
                . "        WHERE ls.name = 'OPEN') "
                . "    LEFT OUTER JOIN location_type lt "
                . "    ON l.type_id = lt.id AND lt.active = 1 ";

        $sql .= $where;
            
        $sql .= "ORDER BY arena_name ASC, city_state_zip ASC, a.id ASC, "
                . "contact_type ASC, contact_name ASC, contact_id ASC, "
                . "lt.display_order ASC, location_name ASC, location_id ASC ";
        
        if($limit > 0) {
            $sql .= "LIMIT :offset, :limit";
        }
        
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':aid', (integer)$aid, PDO::PARAM_INT);
        
        if($limit > 0) {
            $command->bindValue(':offset', (integer)$offset, PDO::PARAM_INT);
            $command->bindValue(':limit', (integer)$limit, PDO::PARAM_INT);
        }
        
        $ret = $command->queryAll(true);
        
        return Arena::buildViewResults($ret);
    }

    /**
     * Returns a record for use with Google Maps.
     * @param float $lat The lattitude of the center point.
     * @param float $lng The longitude of the center point.
     * @param float $radius The radius of the search circle.
     * @param integer $offset The offset to start returning records from.
     * @param integer $limit The maximum records to return. Returns all if 0.
     * @param boolean $open If true, limits the search to open facilities only.
     * @param string $start_date The date of events to start search on.
     * @param string $end_date The last date of events to search to.
     * @param string $start_time The start time of the event to search from.
     * @param string $end_time The start time of the event to search to.
     * @param integer[] $types The event types to search for.
     * @return mixed[] The arena markers or an empty array.
     * @throws CDbException
     */
    public static function getAddressWithEventsMarkers($lat, $lng, $radius, $offset = 0, $limit = 0, $open = true, $start_date = null, $end_date = null, $start_time = null, $end_time = null, $types = array())
    {
        // Let's start by building up our query
        // Let's start by building up our query
        $url = Yii::app()->createUrl('arena/view');
        
        $sql = "SELECT CONCAT('" . $url . "?id=', a.id) AS viewUrl, "
                . "a.id, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.lat, "
                . "a.lng, "
                . "( 3959 * ACOS( COS( RADIANS( :lat ) ) * COS( RADIANS( a.lat ) "
                . ") * COS( RADIANS( a.lng ) - RADIANS( :lng ) ) + SIN( RADIANS( "
                . ":lat ) ) * SIN( RADIANS( a.lat ) ) ) ) AS distance "
                . "FROM arena a ";
        
        $where = '';
        $incEvents = false;
        $typeCount = count($types);
        
        if($open === true) {
            $where .= "WHERE a.status_id = (SELECT s.id FROM arena_status s WHERE "
                    . "s.name = 'OPEN') ";
        }
        
        if($start_date != null || $start_time != null || $end_date != null ||
                $end_time != null || $typeCount > 0) {
            $incEvents = true;
        }
        
        $eventSql = "(SELECT DISTINCT e.arena_id "
                . "FROM event e "
                . "WHERE e.status_id = (SELECT es.id FROM event_status es WHERE "
                . "es.name = 'OPEN') ";
        
        if($start_date != null && $end_date != null) {
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CAST(:end_date AS DATE) ';
        } elseif($start_date != null && $end_date == null) {
            $eventSql .= 'AND e.start_date = CAST(:start_date AS DATE) ';
        } elseif($start_date == null && $end_date != null) {
            $newDate = new DateTime();
            $start_date = $newDate->format('Y-m-d');
            
            $eventSql .= 'AND e.start_date >= CAST(:start_date AS DATE) '
                    . 'AND e.start_date <= CASE(:end_date AS DATE) ';
        }
        
        if($start_time != null && $end_time != null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) '
                    . 'AND e.start_time <= CAST(:end_time AS TIME) ';
        } elseif($start_time != null && $end_time == null) {
            $eventSql .= 'AND e.start_time >= CAST(:start_time AS TIME) ';
        } elseif($start_time == null && $end_time != null) {
            $eventSql .= 'AND e.start_time <= CAST(:end_time AS TIME) ';
        }
        
        if($typeCount > 0) {
            $eventSql .= 'AND e.type_id IN (';
            
            for($i = 0; $i < $typeCount; $i++) {
                if($i + 1 == $typeCount) {
                    $eventSql .= ':eventType' . $i . ') ';
                } else {
                    $eventSql .= ':eventType' . $i . ', ';
                }
            }
        }
        
        $eventSql .= ' ) ee ON a.id = ee.arena_id ';
        
        if($incEvents == true) {
            $sql .= ' INNER JOIN ' . $eventSql . $where;
        } else {
            $sql .= $where;
        }

        if($radius != null && $radius > 0) {
            $sql .= "HAVING distance < :radius "
                    . "ORDER BY distance ASC ";
        } else {
            $sql .= "ORDER BY distance ASC ";
        }
        
        if($limit > 0) {
            $sql .= "LIMIT :offset, :limit";
        }
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':lat', $lat, PDO::PARAM_STR);
        $command->bindParam(':lng', $lng, PDO::PARAM_STR);
        $command->bindParam(':radius', $radius, PDO::PARAM_STR);
        $command->bindValue(':offset', (integer)$offset, PDO::PARAM_INT);
        
        if($limit > 0) {
            $command->bindValue(':limit', (integer)$limit, PDO::PARAM_INT);
        }
        
        if($start_date != null) {
            $command->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        }
        
        if($end_date != null) {
            $command->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        }
        
        if($start_time != null) {
            $command->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        }
            
        if($end_time != null) {
            $command->bindParam(':end_time', $end_time, PDO::PARAM_STR);
        }
            
        if($typeCount > 0) {
            for($i = 0; $i < $typeCount; $i++) {
                $command->bindValue(':eventType' . $i, (integer)$types[$i], PDO::PARAM_INT);
            }
        }
        
        $ret = $command->queryAll(true);
        
        return $ret;
    }

    /**
     * Returns a record for use with Google Maps.
     * @param float $lat The lattitude of the center point.
     * @param float $lng The longitude of the center point.
     * @param float $radius The radius of the search circle.
     * @param integer $limit The maximum records to return. Returns all if 0.
     * @param boolean $open If true, limits the search to open facilities only.
     * @return mixed[] The arena markers or an empty array.
     * @throws CDbException
     */
    public static function getAddressMarkers($lat, $lng, $radius, $limit = 0, $open = true)
    {
        // Let's start by building up our query
        $ret = array();
        
        $sql = "SELECT a.id AS aid, "
                . "a.name AS arena_name, "
                . "a.address_line1, "
                . "CASE WHEN a.address_line2 IS NULL OR a.address_line2 = '' THEN "
                . "NULL ELSE a.address_line2 END AS address_line2, "
                . "CONCAT(a.city, ', ', a.state, ' ', a.zip) AS city_state_zip, "
                . "a.phone, "
                . "a.lat, "
                . "a.lng, "
                . "( 3959 * ACOS( COS( RADIANS( :lat ) ) * COS( RADIANS( a.lat ) "
                . ") * COS( RADIANS( a.lng ) - RADIANS( :lng ) ) + SIN( RADIANS( "
                . ":lat ) ) * SIN( RADIANS( a.lat ) ) ) ) AS distance "
                . "FROM arena a ";
        
        if($open === true) {
            $sql .= "WHERE a.status_id = (SELECT s.id FROM arena_status s WHERE s.name = 'OPEN') ";
        }
        
        $sql .= "HAVING distance < :radius "
                . "ORDER BY distance ASC ";
        
        if($limit > 0) {
            $sql .= "LIMIT 0 , :limit";
        }
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':lat', $lat, PDO::PARAM_STR);
        $command->bindParam(':lng', $lng, PDO::PARAM_STR);
        $command->bindParam(':radius', $radius, PDO::PARAM_STR);

        if($limit > 0) {
            $command->bindValue(':limit', (integer)$limit, PDO::PARAM_INT);
        }
        
        $ret = $command->queryAll(true);
        
        return $ret;
    }
    
    /**
     * Returns a filtered record set for use with Google Maps or the Index.
     * @param mixed[] $input The record set to filter.
     * @param mixed[] $urlParams The parameters used to build the record set.
     * @param integer $typeCount The number of event types.
     * @param integer[] $types The array of type ids.
     * @return mixed[] The filtered arena markers or an empty array.
     */
    public static function buildAddressMarkersResults($input, $urlParams, $typeCount, $types)
    {
        $results = array();
        
        $arenaKeys = array(
            'view_url',
            'id',
            'arena_name',
            'address_line1',
            'address_line2',
            'city_state_zip',
            'phone',
            'ext',
            'fax',
            'fax_ext',
            'logo',
            'home_url',
            'tags',
            'lat',
            'lng',
            'distance',
            'location_count'
        );
        
        $contactKeys = array(
            'contact_id',
            'contact_type',
            'contact_name',
            'contact_phone',
            'contact_ext',
            'contact_fax',
            'contact_fax_ext',
            'contact_email'
        );
        
        $eventKeys = array(
            'event_type_id',
            'event_type_name',
            'start_date_time',
            'event_count'
        );

        $arenaIndex = 0;
        $contactIndex = 0;
        $eventIndex = 0;

        foreach($input as $record) {
            $recordParams = $urlParams;
            $arena = array_intersect_key($record, array_flip($arenaKeys));
            $contact = array_intersect_key($record, array_flip($contactKeys));
            $event = array_intersect_key($record, array_flip($eventKeys));
            $recordParams['aid'] = $arena['id'];
            // First see if we should add the arena
            if(!isset($results[$arenaIndex]) || $results[$arenaIndex]['id'] != $arena['id'] ) {
                $arenaIndex = array_push($results, $arena) - 1;
                $results[$arenaIndex]['contacts'] = array();
                $results[$arenaIndex]['events'] = array();
                $contactIndex = 0;
                $eventIndex = 0;
                
                $recordParams['aid'] = $arena['id'];
                $arenaParams = $recordParams;

                if($typeCount > 0) {
                    $arenaParams['types'] = $types;
                }
                
                $results[$arenaIndex]['events_url'] = Yii::app()->createUrl('event/index', $arenaParams);
            } 
            
            // Only process the contact if one exists!
            if(isset($contact['contact_id']) && is_numeric($contact['contact_id'])) {
                // We have a contact, so check if it has been added
                if(!isset($results[$arenaIndex]['contacts'][$contactIndex]) || 
                    $results[$arenaIndex]['contacts'][$contactIndex]['contact_id'] != $contact['contact_id']) {
                    $contactIndex = array_push($results[$arenaIndex]['contacts'], $contact) - 1;
                    $eventIndex = 0;
                    //$results[$arena['id']]['contacts'][$contact['contact_id']] = $contact;
                } else {
                    $eventIndex += 1;
                }
            }
            
            // Only process the event information if one exists!
            if(isset($event['event_type_id']) && is_numeric($event['event_type_id'])) {
                // We have an event, so check if it has been added
                if(!isset($results[$arenaIndex]['events'][$eventIndex]) || 
                    $results[$arenaIndex]['events'][$eventIndex]['event_type_id'] != $event['event_type_id']) {
                    $eventIndex = array_push($results[$arenaIndex]['events'], $event) - 1;
                    //$results[$arena['id']]['events'][$event['event_type_id']] = $event;
                    $results[$arenaIndex]['tags'] = $results[$arenaIndex]['tags'] . ', ' . $event['event_type_name'];
                    $eventParams = $recordParams;
                    
                    $eventParams['types'] = array($event['event_type_id']);
                
                    $results[$arenaIndex]['events'][$eventIndex]['event_view_url'] = Yii::app()->createUrl('event/index', $eventParams);
                }
            }
            
            unset($record);
            unset($arena);
            unset($contact);
            unset($event);
         }
         
         return $results;
    }
    
    /**
     * Returns a filtered record set for use with the View.
     * @param mixed[] $input The record set to filter.
     * @param mixed[] $urlParams The parameters used to build the record set.
     * @param integer $typeCount The number of event types.
     * @param integer[] $types The array of type ids.
     * @return mixed[] The filtered arena marker or an empty array.
     */
    public static function buildViewResults($input)
    {
        $results = array();
        
        $arenaKeys = array(
            'view_url',
            'id',
            'arena_name',
            'address_line1',
            'address_line2',
            'city_state_zip',
            'phone',
            'ext',
            'fax',
            'fax_ext',
            'logo',
            'home_url',
            'tags',
            'lat',
            'lng',
            'description',
            'notes'
        );
        
        $contactKeys = array(
            'contact_id',
            'contact_type',
            'contact_name',
            'contact_phone',
            'contact_ext',
            'contact_fax',
            'contact_fax_ext',
            'contact_email'
        );
        
        $locationKeys = array(
            'location_type_id',
            'location_type_name',
            'location_type_display_name',
            'location_id',
            'location_name',
            'location_description',
            'location_tags',
            'location_length',
            'location_width',
            'location_radius',
            'location_seating',
            'location_notes'
        );
        
        $contactIndex = 0;
        $locationIndex = 0;

        foreach($input as $record) {
            $recordParams = array();
            $arena = array_intersect_key($record, array_flip($arenaKeys));
            $contact = array_intersect_key($record, array_flip($contactKeys));
            $location = array_intersect_key($record, array_flip($locationKeys));
            
            // First see if we should add the arena
            if(!isset($results['id']) || $results['id'] != $arena['id']) {
                $results = $arena;
                $results['contacts'] = array();
                $results['locations'] = array();
                $contactIndex = 0;
                $locationIndex = 0;
                
                $recordParams['aid'] = $arena['id'];
                $arenaParams = $recordParams;

                $results['events_url'] = Yii::app()->createUrl('event/index', $arenaParams);
                //$arenaParams['output'] = 'json';
                $results['events_json_url'] = Yii::app()->createUrl('event/getMonth', $arenaParams);
            } 
            
            // Only process the contact if one exists!
            if(isset($contact['contact_id']) && is_numeric($contact['contact_id'])) {
                // We have a contact, so check if it has been added
                if(!isset($results['contacts'][$contactIndex]) || 
                    $results['contacts'][$contactIndex]['contact_id'] != $contact['contact_id']) {
                    $contactIndex = array_push($results['contacts'], $contact) - 1;
                    $locationIndex = 0;
                }
            }
            
            // Only process the location if one exists!
            if(isset($location['location_id']) && is_numeric($location['location_id'])) {
                // We have a location, so check if it has been added
                if(!isset($results['locations'][$locationIndex]) || 
                    $results['locations'][$locationIndex]['location_id'] != $location['location_id']) {
                    $locationIndex = array_push($results['locations'], $location) - 1;
                    
                    $locationParams = $recordParams;
                    $locationParams['lid'] = $location['location_id'];
                    $locationParams['aid'] = $arena['id'];
                    
                    $results['locations'][$locationIndex]['events_url'] = 
                             Yii::app()->createUrl('event/index', $locationParams);
                } else {
                    $locationIndex += 1;
                }
            }
            
            unset($record);
            unset($arena);
            unset($contact);
            unset($location);
         }
         
         return $results;
    }
    
    /**
     * Returns an array of manager and contact e-mail addresses for the specified arena
     * Please note that unlike access checks, the user must be explicitly assigned to
     * either the Manager or RestrictedManager roles in order for them to be pulled
     * @param integer $aid The Arena we are retrieving the e-mails for.
     * @return integer[] The arena manager e-mail addresses.
     * @throws CDbException
     */
    public static function getRealManagerContactEmails($aid)
    {
        // Let's start by building up our query
        $emails = array();
        
        $sql = "SELECT DISTINCT u.email "
                . "FROM user u "
                . "INNER JOIN arena_user_assignment aua "
                . "ON u.id = aua.user_id "
                . "INNER JOIN arena a "
                . "ON a.id = aua.arena_id AND a.id = :aid "
                . "INNER JOIN " . Yii::app()->authManager->assignmentTable . " auth "
                . "ON u.id = auth.userid AND auth.itemname IN ('Manager', 'RestrictedManager') "
                . "UNION DISTINCT "
                . "SELECT DISTINCT c.email "
                . "FROM contact c "
                . "INNER JOIN arena_contact_assignment aca "
                . "ON c.id = aca.contact_id AND c.active = 1 "
                . "INNER JOIN arena a "
                . "ON a.id = aca.arena_id AND a.id = :aid";
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':aid', $aid, PDO::PARAM_INT);

        $emails = $command->queryAll(true);
        
        $ret = array();
        
        foreach($emails as $email) {
            $ret[] = $email['email'];
        }
        
        return $ret;
    }
    
    /**
     * Returns true if the contacts were assigned and false if not
     * @param integer $uid The user making the update.
     * @param integer[] $cids The array of contacts to assign.
     * @return boolean true if the assignment succeeded.
     * @throws CDbException
     */
    public function assignContacts($uid, $cids)
    {
        $count = count($cids);
        
        if($count <= 0) {
            return false;
        }
        
        if(!is_array($cids)) {
            $cids = array($cids);
        }
        
        $sql = 'INSERT INTO arena_contact_assignment '
                . '(arena_id, contact_id, primary_contact, created_by_id, created_on, '
                . 'updated_by_id, updated_on) '
                . 'VALUES ';
        
        for($i = 0; $i < $count; $i++) {
            if($i + 1 == $count) {
                $sql .= '(:aid, :cid' . $i . ', 0, :uid, NOW(), :uid, NOW())';
            } else {
                $sql .= '(:aid, :cid' . $i . ', 0, :uid, NOW(), :uid, NOW()), ';
            }
        }
        
        // We always do this in a transaction!
        $transaction = Yii::app()->db->beginTransaction();
        
        try
        {
            $command = Yii::app()->db->createCommand($sql);
        
            $command->bindValue(':aid', (integer)$this->id, PDO::PARAM_INT);
            $command->bindValue(':uid', (integer)$uid, PDO::PARAM_INT);
        
            for($i = 0; $i < $count; $i++) {
                $command->bindValue(':cid' . $i, (integer)$cids[$i], PDO::PARAM_INT);
            }
        
            $ret = $command->execute();
        
            if($ret != $count) {
                // Something bad happened so we will roll back the transaction
                $transaction->rollback();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (Exception $e)
        {
            if($transaction->active == true) {
                $transaction->rollback();
            }

            if($e instanceof CDbException) {
                throw $e;
            }

            $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
            $message = $e->getMessage();
            throw new CDbException(
                    'Failed to execute the SQL statement: ' . $message,
                    (int)$e->getCode(),
                    $errorInfo
            );
        }
    }
    
    /**
     * Returns true if the contacts were unassigned and false if not
     * @param integer $uid The user making the update.
     * @param integer[] $cids The array of contacts to assign.
     * @return boolean true if the unassignment succeeded.
     * @throws CDbException
     */
    public function unassignContacts($uid, $cids)
    {
        $count = count($cids);
        
        if($count <= 0) {
            return false;
        }
        
        if(!is_array($cids)) {
            $cids = array($cids);
        }
        
        $sql = 'DELETE FROM arena_contact_assignment '
                . 'WHERE arena_id = :aid '
                . 'AND contact_id IN ( ';
        
        for($i = 0; $i < $count; $i++) {
            if($i + 1 == $count) {
                $sql .= ':cid' . $i . ')';
            } else {
                $sql .= ':cid' . $i . ', ';
            }
        }
        
        // We always do this in a transaction!
        $transaction = Yii::app()->db->beginTransaction();
        
        try
        {
            $command = Yii::app()->db->createCommand($sql);
        
            $command->bindValue(':aid', (integer)$this->id, PDO::PARAM_INT);
        
            for($i = 0; $i < $count; $i++) {
                $command->bindValue(':cid' . $i, (integer)$cids[$i], PDO::PARAM_INT);
            }
        
            $ret = $command->execute();
        
            if($ret != $count) {
                // Something bad happened so we will roll back the transaction
                $transaction->rollback();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (Exception $e)
        {
            if($transaction->active == true) {
                $transaction->rollback();
            }

            if($e instanceof CDbException) {
                throw $e;
            }

            $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
            $message = $e->getMessage();
            throw new CDbException(
                    'Failed to execute the SQL statement: ' . $message,
                    (int)$e->getCode(),
                    $errorInfo
            );
        }
    }
    
    /**
     * Returns true if the contacts were assigned and false if not
     * @param integer $uid The user making the update.
     * @param integer[] $uids The array of contacts to assign.
     * @return boolean true if the assignment succeeded.
     * @throws CDbException
     */
    public function assignUsers($uid, $uids)
    {
        $count = count($uids);
        
        if($count <= 0) {
            return false;
        }
        
        if(!is_array($uids)) {
            $uids = array($uids);
        }
        
        $sql = 'INSERT INTO arena_user_assignment '
                . '(arena_id, user_id, created_by_id, created_on, '
                . 'updated_by_id, updated_on) '
                . 'VALUES ';
        
        for($i = 0; $i < $count; $i++) {
            if($i + 1 == $count) {
                $sql .= '(:aid, :uids' . $i . ', :uid, NOW(), :uid, NOW())';
            } else {
                $sql .= '(:aid, :uids' . $i . ', :uid, NOW(), :uid, NOW()), ';
            }
        }
        
        // We always do this in a transaction!
        $transaction = Yii::app()->db->beginTransaction();
        
        try
        {
            $command = Yii::app()->db->createCommand($sql);
        
            $command->bindValue(':aid', (integer)$this->id, PDO::PARAM_INT);
            $command->bindValue(':uid', (integer)$uid, PDO::PARAM_INT);
        
            for($i = 0; $i < $count; $i++) {
                $command->bindValue(':uids' . $i, (integer)$uids[$i], PDO::PARAM_INT);
            }
        
            $ret = $command->execute();
        
            if($ret != $count) {
                // Something bad happened so we will roll back the transaction
                $transaction->rollback();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (Exception $e)
        {
            if($transaction->active == true) {
                $transaction->rollback();
            }

            if($e instanceof CDbException) {
                throw $e;
            }

            $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
            $message = $e->getMessage();
            throw new CDbException(
                    'Failed to execute the SQL statement: ' . $message,
                    (int)$e->getCode(),
                    $errorInfo
            );
        }
    }
    
    /**
     * Returns true if the contacts were unassigned and false if not
     * @param integer $uid The user making the update.
     * @param integer[] $cids The array of contacts to assign.
     * @return boolean true if the unassignment succeeded.
     * @throws CDbException
     */
    public function unassignUsers($uid, $uids)
    {
        $count = count($uids);
        
        if($count <= 0) {
            return false;
        }
        
        if(!is_array($uids)) {
            $uids = array($uids);
        }
        
        $sql = 'DELETE FROM arena_user_assignment '
                . 'WHERE arena_id = :aid '
                . 'AND user_id IN ( ';
        
        for($i = 0; $i < $count; $i++) {
            if($i + 1 == $count) {
                $sql .= ':uids' . $i . ')';
            } else {
                $sql .= ':uids' . $i . ', ';
            }
        }
        
        // We always do this in a transaction!
        $transaction = Yii::app()->db->beginTransaction();
        
        try
        {
            $command = Yii::app()->db->createCommand($sql);
        
            $command->bindValue(':aid', (integer)$this->id, PDO::PARAM_INT);
        
            for($i = 0; $i < $count; $i++) {
                $command->bindValue(':uids' . $i, (integer)$uids[$i], PDO::PARAM_INT);
            }
        
            $ret = $command->execute();
        
            if($ret != $count) {
                // Something bad happened so we will roll back the transaction
                $transaction->rollback();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (Exception $e)
        {
            if($transaction->active == true) {
                $transaction->rollback();
            }

            if($e instanceof CDbException) {
                throw $e;
            }

            $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
            $message = $e->getMessage();
            throw new CDbException(
                    'Failed to execute the SQL statement: ' . $message,
                    (int)$e->getCode(),
                    $errorInfo
            );
        }
    }
    
    /**
     * Returns an array of arenas not assigned to the passed in contact
     * @param integer $cid The Contact ID to get the list for
     * @param integer $$uid The User to restrict the results by
     * @return array[] the array of contacts
     * @throws CDbException
     */
    public static function getAvailableAssignedForContact($cid, $uid)
    {
        $sql = 'SELECT a.id, a.name, a.city, a.state, s.display_name AS status '
                . 'FROM arena a '
                . 'INNER JOIN arena_status s '
                . 'ON a.status_id = s.id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id AND aua.user_id = :uid '
                . 'WHERE a.id NOT IN (SELECT DISTINCT aca.arena_id '
                . '                   FROM arena_contact_assignment aca '
                . '                   WHERE aca.contact_id = :cid) ';
        
        $sql .= 'ORDER BY a.name ASC, a.city ASC, a.state ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindValue(':cid', (integer)$cid, PDO::PARAM_INT);
        $command->bindValue(':uid', (integer)$uid, PDO::PARAM_INT);
        
        $ret = $command->queryAll(true);
        
        return $ret;
    }
    
    /**
     * Returns an array of arenas assigned to the passed in contact
     * @param integer $cid The Contact ID to get the list for
     * @param integer $$uid The User to restrict the results by
     * @return array[] the array of contacts
     * @throws CDbException
     */
    public static function getAssignedAssignedForContact($cid, $uid)
    {
        $sql = 'SELECT a.id, a.name, a.city, a.state, s.display_name AS status '
                . 'FROM arena a '
                . 'INNER JOIN arena_status s '
                . 'ON a.status_id = s.id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id AND aua.user_id = :uid '
                . 'WHERE a.id IN (SELECT DISTINCT aca.arena_id '
                . '               FROM arena_contact_assignment aca '
                . '               WHERE aca.contact_id = :cid) ';
        
        $sql .= 'ORDER BY a.name ASC, a.city ASC, a.state ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindValue(':cid', (integer)$cid, PDO::PARAM_INT);
        $command->bindValue(':uid', (integer)$uid, PDO::PARAM_INT);
        
        $ret = $command->queryAll(true);
        
        return $ret;
    }
    
    /**
     * Returns an array of locations for the arena
     * @return array[] the array of locations
     * @throws CDbException
     */
    public static function getLocationsList($aid)
    {
        $sql = 'SELECT l.id AS value, '
                . 'CONCAT(l.name, " (", s.display_name, ")") AS text '
                . 'FROM location l '
                . 'INNER JOIN location_status s '
                . 'ON l.status_id = s.id AND l.arena_id = :aid '
                . 'ORDER BY text ASC';
        
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':aid', (integer)$aid, PDO::PARAM_INT);
        $ret = array(array(
            'value' => null,
            'text' => ''
        ));
        
        return array_merge($ret, $command->queryAll(true));
    }    
}