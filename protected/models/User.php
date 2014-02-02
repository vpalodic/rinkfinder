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
     * Holds the captcha code!
     * @var string
     */
    public $verifyCode;
    
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
        $rules = array(
            array(
                'username',
                'length',
                'max' => 32,
                'min' => 3,
                'message' => "Invalid username (length between 3 and 32 characters).",
            ),
            array(
                'username',
                'unique',
                'message' => "Username already exists.",
            ),
            array(
                'username',
                'match',
                'pattern' => '/^[A-Za-z0-9_\.]+$/u',
                'message' => "Invalid character(s) (A-z, 0-9).",
            ),
            array(
                'passwordSave, passwordRepeat',
                'required',
                'on' => 'insert changePassword registration',
            ),
            array(
                'passwordSave, passwordRepeat',
                'length',
                'max' => 48,
                'min' => 8,
                'message' => "Invalid password (length between 8 and 48 characters).",
                'on' => 'insert changePassword registration',
            ),
            array(
                'passwordSave, passwordRepeat',
                'match',
                'pattern' => '/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/u',
                'message' => "Password must contain at least one from each set (a-z, A-Z, 0-9, !@#$%^&*).",
                'on' => 'insert changePassword registration',
            ),
            array(
                'passwordRepeat',
                'compare',
                'compareAttribute' => 'passwordSave',
                'message' => "Passwords do not match!",
                'on' => 'insert changePassword registration'
            ),
            array(
                'email',
                'length',
                'max' => 128,
                'min' => 6,
                'message' => "Invalid email (length between 6 and 128 characters).",
            ),
            array(
                'email',
                'unique',
                'message' => "Email address already exists.",
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
                ),
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
            array(
                'last_visited_on, activated_on, verifyCode',
                'safe',
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, username, email, status_id, failed_logins, last_visited_on, last_visited_from, activated_on, created_by_id, created_on, updated_by_id, updated_on',
                'safe',
                'on'=>'search'
            ),
        );
        
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'registration-form') {
            return $rules;
        } else {
            array_push(
                    $rules,
                    array(
                        'verifyCode',
                        'captcha',
                        'allowEmpty' => !Yii::app()->doCaptcha('registration'),
                        'on' => 'registration'
                    )
            );
        }

        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'arenas' => array(
                self::MANY_MANY,
                'Arena',
                'arena_user_assignment(user_id, arena_id)',
            ),
            'eventRequestsCreated' => array(
                self::HAS_MANY,
                'EventRequest',
                'requester_id',
            ),
            'eventRequestsAcknowledged' => array(
                self::HAS_MANY,
                'EventRequest',
                'acknowledger_id',
            ),
            'eventRequestsAccepted' => array(
                self::HAS_MANY,
                'EventRequest',
                'accepter_id',
            ),
            'eventRequestsRejected' => array(
                self::HAS_MANY,
                'EventRequest',
                'rejector_id',
            ),
            'profile' => array(
                self::HAS_ONE,
                'Profile',
                'user_id',
            ),
            'reservations' => array(
                self::HAS_MANY,
                'Reservation',
                'for_id',
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
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'E-mail Address',
            'password' => 'Password',
            'passwordSave' => 'Password',
            'passwordRepeat' => 'Confirm Password',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('failed_logins', $this->failed_logins);
        $criteria->compare('last_visited_on', $this->last_visited_on, true);
        $criteria->compare('last_visited_from', $this->last_visited_from, true);
        $criteria->compare('activated_on', $this->activated_on, true);
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
     * @return User the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
        
    /**
     * Apply a hash on the password before we store it in the database
     */
    protected function beforeSave()
    {
        if(($this->scenario === 'insert' ||
            $this->scenario === 'changePassword' ||
            $this->scenario === 'registration') &&
           (!empty($this->passwordSave) && 
            !empty($this->passwordRepeat) && 
            ($this->passwordSave === $this->passwordRepeat))) {
            $this->setPassword($this->passwordSave);
        }
        return parent::beforeSave();;
    }
    
    /**
     * Retrieves the user's first name
     * @return string
     */
    public function getFirstName()
    {
        return (isset($this->profile) && isset($this->profile->first_name)) ? $this->profile->first_name : '';
    }

    /**
     * Retrieves the user's last name
     * @return string
     */
    public function getLastName()
    {
        return (isset($this->profile) && isset($this->profile->last_name)) ? $this->profile->last_name : '';
    }

    /**
     * Retrieves the user's full name
     * @return string
     */
    public function getFullName()
    {
        $fullName = $this->firstName;
        $fullName .= (!empty($fullName)) ? " " . $this->lastName : $this->lastName;
        return $fullName;
    }

    /**
     * Compares the passed in password to the hashed password
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }

    /**
     * Hashes the passed in $password using Blowfish
     * @param string $password
     * @param int $cost
     * @return string The 60 character hashed password
     */
    public function hashPassword($password, $cost = 11)
    {
        return CPasswordHelper::hashPassword($password, $cost);
    }

    /**
     * Assumes that the passed in password meets complexity
     * requirements before hashing and storing the password. This
     * function also generates a new user key.
     * @param string $password
     * @return bool
     */
    public function setPassword($password)
    {
        // We set the user key first
        $this->user_key = hash('sha256', microtime() . $password);
        $this->password = $this->hashPassword($password);
    }

    /**
     * Locks the account if it is active and exceeds the failedLoginsLimit
     * @return bool true if user account was locked
     */
    public function lockUser()
    {
        if($this->status_id == self::STATUS_ACTIVE &&
           $this->failed_logins > Yii::app()->params[failedLoginsLimit]) {
            // Lock the user account!
            $this->status_id = self::STATUS_LOCKED;
            return true;
        }
        return false;
    }

    /**
     * @desc Unlocks the account if it is locked and
     * resets the failed_logins to zero. If the account was previously
     * activated, the status is set to active, otherwise it is set to
     * not activated
     * @return bool true if the account was unlocked
     */
    public function unlockUser()
    {
        if($this->status_id == self::STATUS_LOCKED) {
            // Unlock the user account!
            $this->failed_logins = 0;

            if(!isset($this->activated_on)) {
                $this->status_id = self::STATUS_NOTACTIVATED;
            } else {
                $this->status_id = self::STATUS_ACTIVE;
            }
            return true;
        }

        return false;
    }

    /**
     * @desc Locks the account if it is active and
     * exceeds the failedLogins
     * @return bool
     */
	public function inactiveUser()
	{
		if(!isset($this->last_visited_on) || $this->last_visited_on == '0000-00-00 00:00:00') {
			return false;
		}

		$dtLastVisit = DateTime::createFromFormat('Y-m-d H:i:s', $this->last_visited_on);
		$dtCurrentTime = DateTime::createFromFormat('m-d-Y H:i:s', 'now');
		$dtiDays = $dtLastVisit->diff(dtCurrentTime, true);


		if($this->status_id == self::STATUS_ACTIVE && $dtiDays->days > Yii::app()->getModule('user')->daysSinceLastVisit) {
			// Mark the user account inactive!
			$this->status_id = self::STATUS_INACTIVE;

			return true;
		}

		return false;
	}

    /**
     * @desc Resets the account if it is inactive or locked
     * @return bool
     */
	public function resetUser()
	{
		if($this->status_id == self::STATUS_LOCKED ||
		   $this->status_id == self::STATUS_INACTIVE ||
		   $this->status_id == self::STATUS_ACTIVE) {
			// Reset the user account!
			$this->status_id = self::STATUS_RESET;

			return true;
		}

		return false;
	}

	/**
     * @desc Bans the account
     * @return bool
     */
	public function banUser()
	{
		// Ban the user account!
		$this->status_id = self::STATUS_BANNED;

		return true;
	}

    /**
     * @desc Marks the account as deleted
     * @return bool
     */
	public function deleteUser()
	{
		// Delete the user account!
		$this->status_id = self::STATUS_DELETED;

		return true;
	}

    /**
     * @desc Determines if the account is not activated
     * @return bool
     */
	public function isNotActivated()
	{
		if($this->status_id == self::STATUS_NOTACTIVATED) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is active
     * @return bool
     */
	public function isActive()
	{
		if($this->status_id == self::STATUS_ACTIVE) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is locked
     * @return bool
     */
	public function isLocked()
	{
		if($this->status_id == self::STATUS_LOCKED) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is reset
     * @return bool
     */
	public function isReset()
	{
		if($this->status_id == self::STATUS_RESET) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is inactive
     * @return bool
     */
	public function isInactive()
	{
		if($this->status_id == self::STATUS_INACTIVE) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is deleted
     * @return bool
     */
	public function isDeleted()
	{
		if($this->status_id == self::STATUS_DELETED) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Determines if the account is banned
     * @return bool
     */
	public function isBanned()
	{
		if($this->status_id == self::STATUS_BANNED) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * @desc Increments the failed_logins count and
     * calls lockUser
     * @param bool $save true to save the record
     * @return bool
     */
	public function loginFailed($save = false)
	{
		$this->failed_logins += 1;

		$this->lockUser();

		if($save) {
			return $this->save(true, array('status', 'failed_logins', 'updated_on', 'updated_by_id'));
		} else {
			return true;
		}
	}

    /**
     * @desc Updates last_visited_on to NOW() and resets failed_logins to 0
     * @param bool $save true to save the record
     * @return bool
     */
	public function loginSuccessful($save = false)
	{
		$this->failed_logins = 0;
		$this->last_visited_on = new CDbExpression('NOW()');

		if($save) {
			return $this->save(true, array('failed_logins', 'last_visited_on', 'updated_on', 'updated_by_id'));
		} else {
			return true;
		}
	}

    /**
     * @desc Activates the user's account!
     * @param string $activation_key
     * @param bool $save
     * @return bool
     */
	public function activateAccount($activation_key, $save = false)
	{
		if($this->status_id == self::STATUS_NOTACTIVATED && $this->activation_key == $activation_key) {
			// Reset the activation key so that it cannot be re-used!
			$this->activation_key = hash(Yii::app()->getModule('user')->hash, microtime());;
			$this->status_id = self::STATUS_ACTIVE;

			if($save) {
				return $this->save(true, array('activation_key', 'status', 'updated_on', 'updated_by_id'));
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}
