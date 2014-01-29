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
 * @property string $user_key
 * @property string $activated_on
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Arena[] $arenas
 * @property EventRequest[] $eventRequestsCreated
 * @property EventRequest[] $eventRequestsAcknowledged
 * @property EventRequest[] $eventRequestsAccepted
 * @property EventRequest[] $eventRequestsRejected
 * @property Profile $profile
 * @property Reservation[] $reservations
 * @property User $createdBy
 * @property User $updatedBy
 */
class User extends RinkfinderActiveRecord
{
    const STATUS_NOTACTIVATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_LOCKED = 2;
    const STATUS_RESET = 3;
    const STATUS_INACTIVE = 4;
    const STATUS_DELETED = 5;
    const STATUS_BANNED = -1;

    /**
     * Holds the new unecrypted password
     * @var string
     */
    public $passwordSave;
    
    /**
     * Holds the new unecrypted password and must match $passwordSave
     * @var string
     */
    public $passwordRepeat;
    
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
        return array(
                array(
                    'username',
                    'length',
                    'max' => 32,
                    'min' => 3,
                    'message' => Yii::t("Invalid username (length between 3 and 32 characters).")
                ),
                array(
                    'username',
                    'unique',
                    'message' => Yii::t("Username already exists.")
                ),
                array(
                    'username',
                    'match',
                    'pattern' => '/^[A-Za-z0-9_\.]+$/u',
                    'message' => Yii::t("Invalid character(s) (A-z, 0-9).")
                ),
		array(
                    'passwordSave, passwordRepeat',
                    'required',
                    'on' => 'insert, changePassword'
                ),
		array(
                    'passwordSave, passwordRepeat',
                    'length',
                    'max' => 48,
                    'min' => 8,
                    'message' => Yii::t("Invalid password (length between 8 and 48 characters)."),
                    'on' => 'insert, changePassword'
                ),
		array(
                    'passwordSave, passwordRepeat',
                    'match',
                    'pattern' => '/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/u',
                    'message' => Yii::t("Password must contain at least one from each set (a-z, A-Z, 0-9, !@#$%^&*)."),
                    'on' => 'insert, changePassword'
                ),
                array(
                    'repeatPassword',
                    'compare',
                    'compareAttribute' => 'passwordSave',
                    'on' => 'insert, changePassword'
                ),
                array(
                    'email',
                    'length',
                    'max' => 128,
                    'min' => 6,
                    'message' => Yii::t("Invalid email (length between 6 and 128 characters).")
                ),
		array(
                    'email',
                    'unique',
                    'message' => Yii::t("Email address already exists.")
                ),
		array(
                    'email',
                    'email'
                ),
		array(
                    'status_id',
                    'in',
                    'range' => array(
                        self::STATUS_BANNED,
                        self::STATUS_NOTACTIVATED,
                        self::STATUS_ACTIVE,
                        self::STATUS_LOCKED,
                        self::STATUS_RESET,
                        self::STATUS_INACTIVE,
                        self::STATUS_DELETED
                    )
                ),
                array(
                    'status_id',
                    'numerical',
                    'integerOnly' => true
                ),
                array(
                    'username, email, status_id',
                    'required'
                ),
        );
		return array(
			array('username, email, password', 'required'),
			array('status_id, failed_logins', 'numerical', 'integerOnly' => true),
			array('username, last_visited_from', 'length', 'max' => 32),
			array('email', 'length', 'max'=>128),
			array('password, user_key', 'length', 'max'=>64),
			array('last_visited_on, activated_on', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, email, status_id, failed_logins, last_visited_on, last_visited_from, user_key, activated_on, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'arenas' => array(self::MANY_MANY, 'Arena', 'arena_user_assignment(user_id, arena_id)'),
			'eventRequestsCreated' => array(self::HAS_MANY, 'EventRequest', 'requester_id'),
			'eventRequestsAcknowledged' => array(self::HAS_MANY, 'EventRequest', 'acknowledger_id'),
			'eventRequestsAccepted' => array(self::HAS_MANY, 'EventRequest', 'accepter_id'),
			'eventRequestsRejected' => array(self::HAS_MANY, 'EventRequest', 'rejector_id'),
			'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
			'reservations' => array(self::HAS_MANY, 'Reservation', 'for_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id', 'select' => array('id', 'username', 'status_id')),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id', 'select' => array('id', 'username', 'status_id')),
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
			'user_key' => 'User Key',
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
		$criteria->compare('user_key',$this->user_key,true);
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
