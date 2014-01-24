<?php

/**
 * This is the model class for table "arena_user_assignment".
 *
 * The followings are the available columns in table 'arena_user_assignment':
 * @property integer $arena_id
 * @property integer $user_id
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 */
class ArenaUserAssignment extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'arena_user_assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('arena_id, user_id, created_on, updated_on', 'required'),
			array('arena_id, user_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('arena_id, user_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'arena_id' => 'Arena',
			'user_id' => 'User',
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

		$criteria->compare('arena_id',$this->arena_id);
		$criteria->compare('user_id',$this->user_id);
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
	 * @return ArenaUserAssignment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
