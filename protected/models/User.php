<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property integer $status_id
 * @property integer $failed_logins
 * @property string $last_visited_on
 * @property string $last_visited_from
 * @property string $activation_key
 * @property string $activated_on
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Arena[] $arenas
 * @property Arena[] $arenas1
 * @property ArenaReservationPolicy[] $arenaReservationPolicies
 * @property ArenaReservationPolicy[] $arenaReservationPolicies1
 * @property ArenaStatus[] $arenaStatuses
 * @property ArenaStatus[] $arenaStatuses1
 * @property Arena[] $arenas2
 * @property AuthItem[] $authItems
 * @property Contact[] $contacts
 * @property Contact[] $contacts1
 * @property Event[] $events
 * @property Event[] $events1
 * @property EventStatus[] $eventStatuses
 * @property EventStatus[] $eventStatuses1
 * @property EventType[] $eventTypes
 * @property EventType[] $eventTypes1
 * @property IceSheet[] $iceSheets
 * @property IceSheet[] $iceSheets1
 * @property IceSheetBase[] $iceSheetBases
 * @property IceSheetBase[] $iceSheetBases1
 * @property IceSheetRefrigeration[] $iceSheetRefrigerations
 * @property IceSheetRefrigeration[] $iceSheetRefrigerations1
 * @property IceSheetResurfacer[] $iceSheetResurfacers
 * @property IceSheetResurfacer[] $iceSheetResurfacers1
 * @property IceSheetStatus[] $iceSheetStatuses
 * @property IceSheetStatus[] $iceSheetStatuses1
 * @property IceSheetType[] $iceSheetTypes
 * @property IceSheetType[] $iceSheetTypes1
 * @property Profile $profile
 * @property Profile[] $profiles
 * @property Profile[] $profiles1
 * @property Reservation[] $reservations
 * @property Reservation[] $reservations1
 * @property Reservation[] $reservations2
 * @property ReservationRequest[] $reservationRequests
 * @property ReservationRequest[] $reservationRequests1
 * @property ReservationRequest[] $reservationRequests2
 * @property ReservationRequest[] $reservationRequests3
 * @property ReservationRequest[] $reservationRequests4
 * @property ReservationRequest[] $reservationRequests5
 * @property ReservationRequestStatus[] $reservationRequestStatuses
 * @property ReservationRequestStatus[] $reservationRequestStatuses1
 * @property ReservationRequestType[] $reservationRequestTypes
 * @property ReservationRequestType[] $reservationRequestTypes1
 * @property ReservationStatus[] $reservationStatuses
 * @property ReservationStatus[] $reservationStatuses1
 * @property Tag[] $tags
 * @property Tag[] $tags1
 * @property UserStatus $status
 * @property User $createdBy
 * @property User[] $users
 * @property User $updatedBy
 * @property User[] $users1
 * @property UserStatus[] $userStatuses
 * @property UserStatus[] $userStatuses1
 */
class User extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, email, password, activation_key, created_on, updated_on', 'required'),
			array('status_id, failed_logins, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('username, last_visited_from', 'length', 'max'=>32),
			array('email', 'length', 'max'=>128),
			array('password, activation_key', 'length', 'max'=>64),
			array('last_visited_on, activated_on', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, email, password, status_id, failed_logins, last_visited_on, last_visited_from, activation_key, activated_on, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'arenas' => array(self::HAS_MANY, 'Arena', 'created_by_id'),
			'arenas1' => array(self::HAS_MANY, 'Arena', 'updated_by_id'),
			'arenaReservationPolicies' => array(self::HAS_MANY, 'ArenaReservationPolicy', 'created_by_id'),
			'arenaReservationPolicies1' => array(self::HAS_MANY, 'ArenaReservationPolicy', 'updated_by_id'),
			'arenaStatuses' => array(self::HAS_MANY, 'ArenaStatus', 'created_by_id'),
			'arenaStatuses1' => array(self::HAS_MANY, 'ArenaStatus', 'updated_by_id'),
			'arenas2' => array(self::MANY_MANY, 'Arena', 'arena_user_assignment(user_id, arena_id)'),
			'authItems' => array(self::MANY_MANY, 'AuthItem', 'auth_assignment(userid, itemname)'),
			'contacts' => array(self::HAS_MANY, 'Contact', 'created_by_id'),
			'contacts1' => array(self::HAS_MANY, 'Contact', 'updated_by_id'),
			'events' => array(self::HAS_MANY, 'Event', 'created_by_id'),
			'events1' => array(self::HAS_MANY, 'Event', 'updated_by_id'),
			'eventStatuses' => array(self::HAS_MANY, 'EventStatus', 'created_by_id'),
			'eventStatuses1' => array(self::HAS_MANY, 'EventStatus', 'updated_by_id'),
			'eventTypes' => array(self::HAS_MANY, 'EventType', 'created_by_id'),
			'eventTypes1' => array(self::HAS_MANY, 'EventType', 'updated_by_id'),
			'iceSheets' => array(self::HAS_MANY, 'IceSheet', 'created_by_id'),
			'iceSheets1' => array(self::HAS_MANY, 'IceSheet', 'updated_by_id'),
			'iceSheetBases' => array(self::HAS_MANY, 'IceSheetBase', 'created_by_id'),
			'iceSheetBases1' => array(self::HAS_MANY, 'IceSheetBase', 'updated_by_id'),
			'iceSheetRefrigerations' => array(self::HAS_MANY, 'IceSheetRefrigeration', 'created_by_id'),
			'iceSheetRefrigerations1' => array(self::HAS_MANY, 'IceSheetRefrigeration', 'updated_by_id'),
			'iceSheetResurfacers' => array(self::HAS_MANY, 'IceSheetResurfacer', 'created_by_id'),
			'iceSheetResurfacers1' => array(self::HAS_MANY, 'IceSheetResurfacer', 'updated_by_id'),
			'iceSheetStatuses' => array(self::HAS_MANY, 'IceSheetStatus', 'created_by_id'),
			'iceSheetStatuses1' => array(self::HAS_MANY, 'IceSheetStatus', 'updated_by_id'),
			'iceSheetTypes' => array(self::HAS_MANY, 'IceSheetType', 'created_by_id'),
			'iceSheetTypes1' => array(self::HAS_MANY, 'IceSheetType', 'updated_by_id'),
			'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
			'profiles' => array(self::HAS_MANY, 'Profile', 'created_by_id'),
			'profiles1' => array(self::HAS_MANY, 'Profile', 'updated_by_id'),
			'reservations' => array(self::HAS_MANY, 'Reservation', 'for_id'),
			'reservations1' => array(self::HAS_MANY, 'Reservation', 'created_by_id'),
			'reservations2' => array(self::HAS_MANY, 'Reservation', 'updated_by_id'),
			'reservationRequests' => array(self::HAS_MANY, 'ReservationRequest', 'requester_id'),
			'reservationRequests1' => array(self::HAS_MANY, 'ReservationRequest', 'acknowledger_id'),
			'reservationRequests2' => array(self::HAS_MANY, 'ReservationRequest', 'accepter_id'),
			'reservationRequests3' => array(self::HAS_MANY, 'ReservationRequest', 'rejector_id'),
			'reservationRequests4' => array(self::HAS_MANY, 'ReservationRequest', 'created_by_id'),
			'reservationRequests5' => array(self::HAS_MANY, 'ReservationRequest', 'updated_by_id'),
			'reservationRequestStatuses' => array(self::HAS_MANY, 'ReservationRequestStatus', 'created_by_id'),
			'reservationRequestStatuses1' => array(self::HAS_MANY, 'ReservationRequestStatus', 'updated_by_id'),
			'reservationRequestTypes' => array(self::HAS_MANY, 'ReservationRequestType', 'created_by_id'),
			'reservationRequestTypes1' => array(self::HAS_MANY, 'ReservationRequestType', 'updated_by_id'),
			'reservationStatuses' => array(self::HAS_MANY, 'ReservationStatus', 'created_by_id'),
			'reservationStatuses1' => array(self::HAS_MANY, 'ReservationStatus', 'updated_by_id'),
			'tags' => array(self::HAS_MANY, 'Tag', 'created_by_id'),
			'tags1' => array(self::HAS_MANY, 'Tag', 'updated_by_id'),
			'status' => array(self::BELONGS_TO, 'UserStatus', 'status_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'users' => array(self::HAS_MANY, 'User', 'created_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
			'users1' => array(self::HAS_MANY, 'User', 'updated_by_id'),
			'userStatuses' => array(self::HAS_MANY, 'UserStatus', 'updated_by_id'),
			'userStatuses1' => array(self::HAS_MANY, 'UserStatus', 'created_by_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'email' => 'Email',
			'password' => 'Password',
			'status_id' => 'Status',
			'failed_logins' => 'Failed Logins',
			'last_visited_on' => 'Last Visited On',
			'last_visited_from' => 'Last Visited From',
			'activation_key' => 'Activation Key',
			'activated_on' => 'Activated On',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('failed_logins',$this->failed_logins);
		$criteria->compare('last_visited_on',$this->last_visited_on,true);
		$criteria->compare('last_visited_from',$this->last_visited_from,true);
		$criteria->compare('activation_key',$this->activation_key,true);
		$criteria->compare('activated_on',$this->activated_on,true);
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
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
