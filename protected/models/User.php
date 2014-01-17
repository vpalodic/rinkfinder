<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property integer $status
 * @property integer $failed_logins
 * @property string $last_visited_on
 * @property string $last_visited_from
 * @property string $activation_key
 * @property string $activated_on
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property AuthItem[] $authItems
 * @property Profile[] $profiles
 * @property Profile[] $profiles1
 * @property Profile[] $profiles2
 * @property User $updatedBy
 * @property User[] $users
 * @property User $createdBy
 * @property User[] $users1
 */
class User extends CActiveRecord
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
			array('status, failed_logins, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('username, last_visited_from', 'length', 'max'=>32),
			array('email', 'length', 'max'=>128),
			array('password, activation_key', 'length', 'max'=>64),
			array('last_visited_on, activated_on', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, email, password, status, failed_logins, last_visited_on, last_visited_from, activation_key, activated_on, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'authItems' => array(self::MANY_MANY, 'AuthItem', 'auth_assignment(userid, itemname)'),
			'profiles' => array(self::HAS_MANY, 'Profile', 'user_id'),
			'profiles1' => array(self::HAS_MANY, 'Profile', 'created_by_id'),
			'profiles2' => array(self::HAS_MANY, 'Profile', 'updated_by_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by_id'),
			'users' => array(self::HAS_MANY, 'User', 'updated_by_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by_id'),
			'users1' => array(self::HAS_MANY, 'User', 'created_by_id'),
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
			'status' => 'Status',
			'failed_logins' => 'Failed Logins',
			'last_visited_on' => 'Last Visited On',
			'last_visited_from' => 'Last Visited From',
			'activation_key' => 'Activation Key',
			'activated_on' => 'Activated On',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('failed_logins',$this->failed_logins);
		$criteria->compare('last_visited_on',$this->last_visited_on,true);
		$criteria->compare('last_visited_from',$this->last_visited_from,true);
		$criteria->compare('activation_key',$this->activation_key,true);
		$criteria->compare('activated_on',$this->activated_on,true);
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
