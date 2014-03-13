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

}
