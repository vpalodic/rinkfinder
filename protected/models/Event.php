<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property integer $id
 * @property integer $ice_sheet_id
 * @property string $external_id
 * @property string $name
 * @property string $description
 * @property string $tags
 * @property integer $all_day
 * @property string $start_date
 * @property string $start_time
 * @property string $duration
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
 * @property IceSheet $iceSheet
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
			array('ice_sheet_id, name, description, start_date, start_time, duration, end_date, end_time, created_on, updated_on', 'required'),
			array('ice_sheet_id, all_day, type_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('external_id', 'length', 'max'=>32),
			array('name', 'length', 'max'=>128),
			array('tags', 'length', 'max'=>1024),
			array('price', 'length', 'max'=>10),
			array('notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ice_sheet_id, external_id, name, description, tags, all_day, start_date, start_time, duration, end_date, end_time, price, notes, type_id, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'iceSheet' => array(self::BELONGS_TO, 'IceSheet', 'ice_sheet_id'),
			'type' => array(self::BELONGS_TO, 'EventType', 'type_id'),
			'status' => array(self::BELONGS_TO, 'EventStatus', 'status_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
			'reservations' => array(self::HAS_MANY, 'Reservation', 'event_id'),
			'reservationRequests' => array(self::HAS_MANY, 'ReservationRequest', 'event_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ice_sheet_id' => 'Ice Sheet',
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
		$criteria->compare('ice_sheet_id',$this->ice_sheet_id);
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
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Event the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
