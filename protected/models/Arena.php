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
 * @property IceSheet[] $iceSheets
 * @property Reservation[] $reservations
 */
class Arena extends RinkfinderActiveRecord
{
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
			array('name, description, address_line1, city, state, zip, created_on, updated_on', 'required'),
			array('status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('lat, lng', 'numerical'),
			array('external_id', 'length', 'max'=>32),
			array('name, address_line1, address_line2, city', 'length', 'max'=>128),
			array('tags', 'length', 'max'=>255),
			array('state', 'length', 'max'=>2),
			array('zip', 'length', 'max'=>5),
			array('phone, ext, fax, fax_ext', 'length', 'max'=>10),
			array('logo, url', 'length', 'max'=>511),
			array('notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, external_id, name, description, tags, address_line1, address_line2, city, state, zip, lat, lng, phone, ext, fax, fax_ext, logo, url, notes, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'status' => array(self::BELONGS_TO, 'ArenaStatus', 'status_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
			'contacts' => array(self::MANY_MANY, 'Contact', 'arena_contact_assignment(arena_id, contact_id)'),
			'arenaReservationPolicies' => array(self::HAS_MANY, 'ArenaReservationPolicy', 'arena_id'),
			'users' => array(self::MANY_MANY, 'User', 'arena_user_assignment(arena_id, user_id)'),
			'iceSheets' => array(self::HAS_MANY, 'IceSheet', 'arena_id'),
			'reservations' => array(self::HAS_MANY, 'Reservation', 'arena_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'external_id' => 'External',
			'name' => 'Name',
			'description' => 'Description',
			'tags' => 'Tags',
			'address_line1' => 'Address Line1',
			'address_line2' => 'Address Line2',
			'city' => 'City',
			'state' => 'State',
			'zip' => 'Zip',
			'lat' => 'Lat',
			'lng' => 'Lng',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('external_id',$this->external_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('address_line1',$this->address_line1,true);
		$criteria->compare('address_line2',$this->address_line2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('ext',$this->ext,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('fax_ext',$this->fax_ext,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('url',$this->url,true);
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
	 * @return Arena the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
