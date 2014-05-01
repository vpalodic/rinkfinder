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
 * @property integer $arenaCount
 * @property EventRequest[] $eventRequestsCreated
 * @property EventRequest[] $eventRequestsAcknowledged
 * @property EventRequest[] $eventRequestsAccepted
 * @property EventRequest[] $eventRequestsRejected
 * @property FileUpload[] $fileUploads
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
     * Holds the user's acceptance of TOU, PP, and Over 13!
     * @var string
     */
    public $acceptTerms;
    
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
            array(
                'acceptTerms',
                'required',
                'message' => 'You must agree to the Terms of Use, Privacy Policy, and that you are thirteen (13) years of age or older.',
                'on' => 'registration'
            ),
            array(
                'acceptTerms',
                'boolean',
                'on' => 'registration'
            ),
            array(
                'acceptTerms',
                'compare',
                'compareValue' => true,
                'message' => 'You must agree to the Terms of Use, Privacy Policy, and that you are thirteen (13) years of age or older.',
                'on' => 'registration'
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
            'arenaCount' => array(
                self::STAT,
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
            'fileUploads' => array(
                self::HAS_MANY,
                'FileUpload',
                'user_id'
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
            'created_by_id' => 'Registered By',
            'created_on' => 'Registered On',
            'updated_by_id' => 'Updated By',
            'updated_on' => 'Updated On',
            'acceptTerms' => 'You have read and agree to both the ' . CHtml::link(
                                    'Terms of Use',
                                    '#terms-of-use',
                                    array(
                                        'class' => 'open-popup-link'
                                    )
                            ) . ' and the ' . CHtml::link(
                                    'Privacy Policy',
                                    '#privacy-policy',
                                    array(
                                        'class' => 'open-popup-link'
                                    )
                            ) . ' of this site and that you are thirteen (13) years of age or older.'
        );
    }

    /**
     * Returns the declaration of named scopes.
     * A named scope represents a query criteria that can be chained together with
     * other named scopes and applied to a query. This method should be overridden
     * by child classes to declare named scopes for the particular AR classes.
     * For example, the following code declares two named scopes: 'recently' and
     * 'published'.
     * <pre>
     * return array(
     *     'published'=>array(
     *           'condition'=>'status=1',
     *     ),
     *     'recently'=>array(
     *           'order'=>'create_time DESC',
     *           'limit'=>5,
     *     ),
     * );
     * </pre>
     * If the above scopes are declared in a 'Post' model, we can perform the following
     * queries:
     * <pre>
     * $posts=Post::model()->published()->findAll();
     * $posts=Post::model()->published()->recently()->findAll();
     * $posts=Post::model()->published()->with('comments')->findAll();
     * </pre>
     * Note that the last query is a relational query.
     *
     * @return array the scope definition. The array keys are scope names; the array
     * values are the corresponding scope definitions. Each scope definition is represented
     * as an array whose keys must be properties of {@link CDbCriteria}.
     */
    public function scopes()
    {
        return array(
            'forLogin' => array(
//                'select' => 'id, username, email, password, failed_logins, status_id, lock_version',
                'select' => 'password',
            ),
            'forRecovery' => array(
                'select' => 'user_key',
            ),
            'forActivation' => array(
                'select' => 'user_key',
            ),
            'sort' => array(
                'order' => 'created_on DESC 1',
            ),
        );
    }

    /**
     * Returns the default named scope that should be implicitly applied to all queries for this model.
     * Note, default scope only applies to SELECT queries. It is ignored for INSERT, UPDATE and DELETE queries.
     * The default implementation simply returns an empty array. You may override this method
     * if the model needs to be queried with some default criteria (e.g. only active records should be returned).
     * @return array the query criteria. This will be used as the parameter to the constructor
     * of {@link CDbCriteria}.
     */
    public function defaultScope()
    {
        return array(
            'select' => 'id, username, email, failed_logins, status_id, activated_on, last_visited_on, last_visited_from, lock_version, created_on, created_by_id, updated_on, updated_by_id',
            'order' => $this->getTableAlias(false, false) . '.created_on DESC',
        );
    }

    /**
     * Retrieve either a lable or indexed list
     * @param string $type The type of the list you wish to access
     * @param integer $code If null, returns the list that is usable in
     * a Drop-Down list, otherwise, it is the numeric code for the text label
     * @return mixed If no code is provided, it is an array indexed by the
     * code values. If a valid type and code are provided, then it is the
     * label for the code.
     */
    public static function itemAlias($type, $code = NULL)
    {
        $_items = array(
            'UserStatus' => array(
                self::STATUS_NOTACTIVATED => 'Not Activated',
                self::STATUS_ACTIVE => 'Active',
                self::STATUS_LOCKED => 'Locked',
                self::STATUS_RESET => 'Reset',
                self::STATUS_INACTIVE => 'Inactive',
                self::STATUS_DELETED => 'Deleted',
                self::STATUS_BANNED => 'Banned',
            ),
        );

        if(isset($code)) {
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        } else {
            return isset($_items[$type]) ? $_items[$type] : false;
        }
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
            $this->setPassword($this->passwordSave, false);
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
     * Retrieves the user's phone number
     * @return string
     */
    public function getPhone()
    {
        return (isset($this->profile) && isset($this->profile->phone)) ? $this->profile->phone : '';
    }

    /**
     * Retrieves the user's roles
     * @return string[]
     */
    public function getRoles()
    {
        $sql = 'SELECT itemName FROM auth_assignment WHERE userid = :uid AND '
                . 'itemName IN (SELECT name FROM auth_item WHERE type = :type)';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindValue(':uid', $this->id, PDO::PARAM_INT);
        $command->bindValue(':type', CAuthItem::TYPE_ROLE, PDO::PARAM_INT);
        
        $rows = $command->queryAll(false);
        $roles = array();
        
        foreach($rows as $row) {
            $roles[] = $row[0];
        }
        
        return $roles;
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
     * @param boolean $save
     * @return bool True if the password was saved to the database
     */
    public function setPassword($password, $save = true)
    {
        // We set the user key first
        $this->user_key = hash('sha256', microtime() . $password);
        $this->password = $this->hashPassword($password);
        
        if($save == true) {
            return $this->saveAttributes(array('user_key', 'password'));
        } else {
            return false;
        }
    }

    /**
     * Locks the account if it is active, not activated, inactive, or reset
     * and {@link $failed_logins} exceeds the failedLoginsLimit
     * @return bool true if user account was locked
     */
    public function lockUser()
    {
        if(($this->status_id == self::STATUS_NOTACTIVATED ||
            $this->status_id == self::STATUS_ACTIVE ||
            $this->status_id == self::STATUS_RESET ||
            $this->status_id == self::STATUS_INACTIVE) &&
           $this->failed_logins > Yii::app()->params['user']['failedLoginsLimit']) {
            // Lock the user account!
            $this->status_id = self::STATUS_LOCKED;
            return true;
        }
        return false;
    }

    /**
     * Unlocks the account if it is locked and resets the
     * failed_logins to zero. If the account was previously
     * activated, the status is set to active. If the account
     * was not previously activated, the status is set to not activated.
     * @return bool true if the account was unlocked
     */
    public function unlockUser()
    {
        if($this->status_id == self::STATUS_LOCKED) {
            // Unlock the user account!
            $this->failed_logins = 0;

            if(!isset($this->activated_on) || $this->activated_on == '0000-00-00 00:00:00') {
                $this->status_id = self::STATUS_NOTACTIVATED;
            } else {
                $this->status_id = self::STATUS_ACTIVE;
            }
            return true;
        }
        return false;
    }

    /**
     * Sets the account as inactive if the user's last visit has been
     * more than daysSinceLastVisitLimit or if the user has never
     * visited, if the creation date has been more than
     * daysSinceLastVisitLimit
     * @return bool true if the user has been set to inactive
     */
    public function inactiveUser()
    {
        if(!isset($this->last_visited_on) || $this->last_visited_on == '0000-00-00 00:00:00') {
            $dtLastVisit = DateTime::createFromFormat('Y-m-d H:i:s', $this->created_on);
        } else {
            $dtLastVisit = DateTime::createFromFormat('Y-m-d H:i:s', $this->last_visited_on);
        }
        
        $dtCurrentTime = new DateTime();
        $dtiDays = $dtLastVisit->diff($dtCurrentTime, true);

        if(($this->status_id == self::STATUS_NOTACTIVATED ||
            $this->status_id == self::STATUS_ACTIVE ||
            $this->status_id == self::STATUS_LOCKED ||
            $this->status_id == self::STATUS_RESET ||
            $this->status_id == self::STATUS_INACTIVE) &&
           $dtiDays->days > Yii::app()->params['user']['daysSinceLastVisitLimit']) {
            // Mark the user account inactive!
            $this->status_id = self::STATUS_INACTIVE;
            return true;
        }
        return false;
    }

    /**
     * Sets the account status as reset and resets the failed_logins
     * to zero if the account has a valid status.
     * @return bool true if the account was reset
     */
    public function resetUser()
    {
        if($this->status_id == self::STATUS_NOTACTIVATED ||
           $this->status_id == self::STATUS_ACTIVE ||
           $this->status_id == self::STATUS_LOCKED ||
           $this->status_id == self::STATUS_RESET ||
           $this->status_id == self::STATUS_INACTIVE) {
            // Reset the user account!
            $this->status_id = self::STATUS_RESET;

            if($this->failed_logins > 0) {
                $neg = 0 - $this->failed_logins;
                $this->saveCounters(array('failed_logins' => $neg));
            }

            return $this->saveAttributes(array('status_id'));
        }
        return false;
    }

    /**
     * Sets the account to banned, even if it has been deleted
     * @return bool always returns true
     */
    public function banUser()
    {
        // Ban the user account!
        $this->status_id = self::STATUS_BANNED;

        return true;
    }

    /**
     * Sets the account as deleted, even if it has been banned
     * @return bool always returns true
     */
    public function deleteUser()
    {
        // Delete the user account!
        $this->status_id = self::STATUS_DELETED;

        return true;
    }

    /**
     * Determines if the account has not been activated.
     * Please note that this function may return true even if
     * the account is active.
     * @return bool true if the account has not been activated
     */
    public function isNotActivated()
    {
        if($this->status_id == self::STATUS_NOTACTIVATED ||
           !isset($this->activated_on) ||
           $this->activated_on == '0000-00-00 00:00:00') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determines if the account status_id is active
     * Please note that this function may return true even if
     * the account has never been activated.
     * @return bool true if the account is active
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
     * Determines if the account status_id is locked.
     * Please note that this function does not determine if the
     * account should be locked.
     * @return bool true if the account is set to locked
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
     * Determines if the account status_id is reset.
     * @return bool true if the account is set to reset
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
     * Determines if the account status_id is inactive
     * Please note that this function does not determine if the
     * account should be set to inactive.
     * @return bool true if the account is set to inactive
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
     * Determines if the account status_id is deleted
     * @return bool true if the account is set to deleted
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
     * Determines if the account status_id is banned
     * @return bool true if the account is set to banned
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
     * Increments the failed_logins count and calls lockUser.
     * @return bool true if the account has been locked
     */
    public function loginFailed()
    {
        $this->saveCounters(array('failed_logins' => 1));

        if($this->lockUser()) {
            return $this->saveAttributes(array('status_id'));
        } else {
            return false;
        }
    }

    /**
     * Updates last_visited_on to NOW(), last_visited_from to the
     * IP Address the login request came from and resets
     * failed_logins to 0 if it is currently non 0. It will not
     * unlock the account if it is locked.
     * Please note that regardless of status_id, the above is performed.
     * @return bool true if login was processed successfully!
     */
    public function loginSuccessful()
    {
        if($this->failed_logins > 0) {
            $neg = 0 - $this->failed_logins;
            $this->saveCounters(array('failed_logins' => $neg));
        }
        
        $this->last_visited_on = new CDbExpression('NOW()');
        $this->last_visited_from = Yii::app()->request->userHostAddress;

        return $this->saveAttributes(array('last_visited_on', 'last_visited_from'));
    }

    /**
     * Activates the user's account if it has not been activated and
     * sets the status_id to active if it is currently set to
     * not activated or reset.
     * @param string $user_key
     * @return bool true if the account was activated
     */
    public function activateAccount($user_key)
    {
        if($this->user_key == $user_key &&
           ($this->status_id == self::STATUS_NOTACTIVATED ||
            $this->status_id == self::STATUS_ACTIVE ||
            $this->status_id == self::STATUS_RESET)) {
            // Reset the user key so that it cannot be re-used!
            $this->user_key = hash('sha256', microtime());
            $this->status_id = self::STATUS_ACTIVE;

            $attributes = array('user_key', 'status_id');
            // Check if we should set an activated_on date
            if(!isset($this->activated_on) ||
               $this->activated_on == '0000-00-00 00:00:00') {
                $this->activated_on = date('Y-m-d H:i:s');
                $attributes[] = 'activated_on';
            }
            return $this->saveAttributes($attributes);
        } else {
            return false;
        }
    }

    /**
     * Marks the user's account as deleted if it isn't banned or deleted
     * @return bool true if the account was deleted
     */
    public function deleteAccount()
    {
        if(($this->status_id == self::STATUS_NOTACTIVATED ||
            $this->status_id == self::STATUS_ACTIVE ||
            $this->status_id == self::STATUS_LOCKED ||
            $this->status_id == self::STATUS_RESET ||
            $this->status_id == self::STATUS_INACTIVE)) {
            // Mark the user as deleted
            $this->status_id = self::STATUS_DELETED;

            $attributes = array('status_id', 'updated_by_id', 'updated_on');

            // We call the before save function to set the updated attributes
            $this->beforeSave();
            
            return $this->saveAttributes($attributes);
        } else {
            return false;
        }
    }

    /**
     * Really deletes the account!!!
     * @return bool true if the account was deleted
     */
    public function adminDeleteAccount()
    {
        return $this->delete();
    }

    /**
     * Prepares a new user account from self-registration prior to them
     * being added to the system.
     * Sets the status_id to not activated and records the user's
     * ip address.
     * @param integer $status_id The status to assign to the user.
     * @return bool true if the user was setup successfully
     */
    public function preRegisterNewUser($status_id = self::STATUS_NOTACTIVATED)
    {
        $this->status_id = $status_id;
        $this->last_visited_from = Yii::app()->request->userHostAddress;
        if($status_id == self::STATUS_ACTIVE) {
            $this->activated_on = date('Y-m-d H:i:s');
        }
        
        return true;
    }

    /**
     * Prepares a new user account from self-registration after they have
     * been added to the system. Add thems to the "User" role.
     * Sets the status_id to not activated and records the user's
     * ip address.
     * @param string $role The role to be assigned to the user.
     * @return bool true if the user was setup successfully
     */
    public function postRegisterNewUser($role = 'User')
    {
        // First add them to the User role
        Yii::app()->authManager->assign($role, $this->id);
        
        // Next, update the created_by_id and updated_by_id
        if(Yii::app()->user->isGuest) {
            $this->created_by_id = $this->id;
            $this->updated_by_id = $this->id;
        } else {
            $this->created_by_id = Yii::app()->user->id;
            $this->updated_by_id = Yii::app()->user->id;
        }
        
        $attributes = array('created_by_id', 'updated_by_id');
        
        return $this->saveAttributes($attributes);
    }
    
    /**
     * Removes the user from all arenas.
     * @return integer The number of arenas the user has been removed from.
     * @throws CDbException
     */
    public function removeUserFromAllArenas()
    {
        Yii::trace(
                'removeUserFromAllArenas()',
                'application.models.User'
        );
        
        $command = Yii::app()->db->createCommand();
        
        return $command->delete(
                'arena_user_assignment',
                'user_id = :userId',
                array(
                    ':userId' => $this->id,
                )
        );
    }
    
    /**
     * Assigns the user to multiple arenas.
     * @param integer[] An array of Arena ids
     * @return boolean The number of arenas the user has been assigned to.
     * @throws CDbException
     */
    public function assignUserToMultipleArenas($arenaIds)
    {
        Yii::trace(
                'assignUserToMultipleArenas()',
                'application.models.User'
        );
        
        $insertArray = array();
        
        foreach($array as $value) {
            $insertArray[] = array(
                'user_id' => $this->id,
                'arena_id' => $value
            );
        }
        
        $builder = Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand(
                'arena_user_assignment',
                $insertArray
        );
        
        return $command->execute();
    }
    
    public static function assignAllAdminsToAllArenas($userId = 1)
    {
        Yii::trace(
                'assignAllAdminsToAllArenas()',
                'application.models.User'
        );
        
        $ret = 0;
        
        // Setup the SQL
        $sql = 'INSERT INTO arena_user_assignment '
                . '(arena_id, user_id, created_by_id, created_on, '
                . 'updated_by_id, updated_on) '
                . 'SELECT a.id, :user_id, :cuser_id, NOW(), '
                . ':cuser_id, NOW() '
                . 'FROM arena a '
                . 'WHERE (a.id, :user_id) NOT IN '
                . '(SELECT au.arena_id, au.user_id '
                . ' FROM arena_user_assignment au'
                . ' WHERE au.user_id = :user_id)';
        
        // Get the admin IDs
        $ids = User::getAllAdminIds();
        
        $command = Yii::app()->db->createCommand($sql);
        
        $uid = 0;
        
        $command->bindParam(':user_id', $uid, PDO::PARAM_INT);
        $command->bindParam(':cuser_id', $userId, PDO::PARAM_INT);
        
        foreach($ids as $id) {
            $uid = $id;
            
            $ret += $command->execute();
        }
        
        return $ret;
    }
    
    public static function getAllAdminIds()
    {
        Yii::trace(
                'getAllAdminIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2)',
                array(
                    ':itemname1' => 'Administrator',
                    ':itemname2' => 'ApplicationAdministrator',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getSiteAdministratorIds()
    {
        Yii::trace(
                'getSiteAdministratorIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1)',
                array(
                    ':itemname1' => 'Administrator',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getApplicationAdministratorIds()
    {
        Yii::trace(
                'getApplicationAdministratorIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname2)',
                array(
                    ':itemname2' => 'ApplicationAdministrator',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getAllMangerIds()
    {
        Yii::trace(
                'getAllMangerIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1, :itemname2)',
                array(
                    ':itemname1' => 'Manager',
                    ':itemname2' => 'RestrictedManager',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getArenaManagerIds()
    {
        Yii::trace(
                'getArenaManagerIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname1)',
                array(
                    ':itemname1' => 'Manager',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getRestrictedArenaManagerIds()
    {
        Yii::trace(
                'getRestrictedArenaManagerIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname2)',
                array(
                    ':itemname2' => 'RestrictedManager',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    public function getSiteUserIds()
    {
        Yii::trace(
                'getSiteUserIds()',
                'application.models.User'
        );
        
        $users =  Yii::app()->db->createCommand()
        ->selectDistinct('userid')
        ->from(Yii::app()->authManager->assignmentTable)
        ->where('itemname IN (:itemname2)',
                array(
                    ':itemname2' => 'User',
                )
        )
        ->queryAll(true);
        
        $ret = array();
        
        foreach($users as $user) {
            $ret[] = $user['userid'];
        }
        
        return $ret;
    }
    
    /**
     * Retrieves all the management information for a user. Uses DAO instead
     * of ActiveRecords as speed is of great concern.
     * @param string[] $for The counts to retrieve.
     * @param string $from The start of the date range.
     * @param string $to The end of the date range.
     * @param integer $uid The user to retrieve information for. If null then
     * information is pulled for the current user.
     * @return mixed[] An indexd array of information for the user.
     * @throws CDbException
     */
    public function getManagementDashboardCounts($for, $from, $to, $uid = null)
    {
        // We need to retrieve a lot of information and will need to 
        // perform a number of querys in order to pull all of the information
        // Now since this is a dashboard view, we don't need the details yet.
        $dashData = array();
        
        if($uid === null) {
            $uid = $this->id;
        }
        
        // Go through each of the items to retrieve counts for
        foreach($for as $request) {
            switch(strtolower($request)) {
                case 'arenas':
                    $dashData['arenas'] = Arena::getAssignedCounts($uid);
                    break;
                case 'contacts':
                    $dashData['contacts'] = Contact::getAssignedCounts($uid);
                    break;
                case 'locations':
                    $dashData['locations'] = Location::getAssignedCounts($uid);
                    break;
                case 'events':
                    $dashData['events'] = Event::getAssignedCounts($uid, null, $from, $to);
                    break;
                case 'requests':
                    $dashData['requests'] = EventRequest::getAssignedCounts($uid, null, $from, $to);
                    break;
                case 'reservations':
                    $dashData['reservations'] = Reservation::getAssignedCounts($uid, null, $from, $to);
                    break;
            }
        }
        
        return $dashData;
    }
    
    /**
     * Retrieves all the administration information for a user. Uses DAO instead
     * of ActiveRecords as speed is of great concern.
     * @param integer $uid The user to retrieve information for. If null then
     * information is pulled for the current user.
     * @return mixed[] An indexd array of information for the user.
     * @throws CDbException
     */
    public function getAdministrationDashboardCounts($uid = null)
    {
        // We need to retrieve a lot of information and will need to 
        // perform a number of querys in order to pull all of the information.
        // Now since this is a dashboard view, we don't need the details yet.
        
        
    }
    
    /**
     * Returns the manager count for each arena that is assigned to the user
     * @param integer $uid The user to get the arenas for.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The arena counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedManagerCounts($uid, $sid = null)
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
     * Returns an array of arenas for the user
     * @return array[] the array of arenas
     * @throws CDbException
     */
    public static function getArenasList($uid)
    {
        $sql = 'SELECT a.id AS value, '
                . 'CONCAT(a.name, " (", s.display_name, ")") AS text '
                . 'FROM arena a '
                . 'INNER JOIN arena_status s '
                . 'ON a.status_id = s.id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id AND aua.user_id = :uid '
                . 'ORDER BY text ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':uid', (integer)$uid, PDO::PARAM_INT);
        
        return $command->queryAll(true);
    }    
}
