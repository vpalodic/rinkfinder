<?php

/**
 * This is the model class for table "arena_status".
 *
 * The followings are the available columns in table 'arena_status':
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property integer $display_order
 * @property string $description
 * @property integer $active
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Arena[] $arenas
 * @property User $createdBy
 * @property User $updatedBy
 */
class ArenaStatus extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'arena_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, display_name, display_order, description, created_on, updated_on', 'required'),
			array('display_order, active, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('name, display_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, display_name, display_order, description, active, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'arenas' => array(self::HAS_MANY, 'Arena', 'status_id'),
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
			'name' => 'Name',
			'display_name' => 'Display Name',
			'display_order' => 'Display Order',
			'description' => 'Description',
			'active' => 'Active',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('display_name',$this->display_name,true);
		$criteria->compare('display_order',$this->display_order);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('active',$this->active);
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
	 * @return ArenaStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
