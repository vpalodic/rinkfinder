<?php

/**
 * This is the model class for table "reservation_request".
 *
 * The followings are the available columns in table 'reservation_request':
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
 * @property Reservation[] $reservations
 * @property Event $event
 * @property User $requester
 * @property User $acknowledger
 * @property User $accepter
 * @property User $rejector
 * @property ReservationRequestType $type
 * @property ReservationRequestStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 */
class ReservationRequest extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reservation_request';
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
			'reservations' => array(self::HAS_MANY, 'Reservation', 'source_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'requester' => array(self::BELONGS_TO, 'User', 'requester_id'),
			'acknowledger' => array(self::BELONGS_TO, 'User', 'acknowledger_id'),
			'accepter' => array(self::BELONGS_TO, 'User', 'accepter_id'),
			'rejector' => array(self::BELONGS_TO, 'User', 'rejector_id'),
			'type' => array(self::BELONGS_TO, 'ReservationRequestType', 'type_id'),
			'status' => array(self::BELONGS_TO, 'ReservationRequestStatus', 'status_id'),
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
	 * @return ReservationRequest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
