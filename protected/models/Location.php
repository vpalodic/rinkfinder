<?php

/**
 * This is the model class for table "location".
 *
 * The followings are the available columns in table 'location':
 * @property integer $id
 * @property integer $arena_id
 * @property string $external_id
 * @property string $name
 * @property string $description
 * @property string $tags
 * @property double $length
 * @property double $width
 * @property double $radius
 * @property integer $seating
 * @property integer $base_id
 * @property integer $refrigeration_id
 * @property integer $resurfacer_id
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
 * @property Event[] $events
 * @property Arena $arena
 * @property LocationType $type
 * @property LocationStatus $status
 * @property User $createdBy
 * @property User $updatedBy
 */
class Location extends RinkfinderActiveRecord
{
    /**
     * @var string $oldTags
     */
    private $oldTags;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('arena_id, name,', 'required'),
			array('arena_id, seating, base_id, refrigeration_id, resurfacer_id, type_id, status_id, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('length, width, radius', 'numerical'),
			array('external_id', 'length', 'max'=>32),
			array('name', 'length', 'max'=>128),
			array('tags', 'length', 'max'=>1024),
			array('notes', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, arena_id, external_id, name, description, tags, length, width, radius, seating, base_id, refrigeration_id, resurfacer_id, notes, type_id, status_id, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'events' => array(self::HAS_MANY, 'Event', 'location_id'),
			'arena' => array(self::BELONGS_TO, 'Arena', 'arena_id'),
			'type' => array(self::BELONGS_TO, 'LocationType', 'type_id'),
			'status' => array(self::BELONGS_TO, 'LocationStatus', 'status_id'),
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
			'arena_id' => 'Arena',
			'external_id' => 'External',
			'name' => 'Name',
			'description' => 'Description',
			'tags' => 'Tags',
			'length' => 'Length',
			'width' => 'Width',
			'radius' => 'Radius',
			'seating' => 'Seating',
			'base_id' => 'Base',
			'refrigeration_id' => 'Refrigeration',
			'resurfacer_id' => 'Resurfacer',
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
		$criteria->compare('arena_id',$this->arena_id);
		$criteria->compare('external_id',$this->external_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('length',$this->length);
		$criteria->compare('width',$this->width);
		$criteria->compare('radius',$this->radius);
		$criteria->compare('seating',$this->seating);
		$criteria->compare('base_id',$this->base_id);
		$criteria->compare('refrigeration_id',$this->refrigeration_id);
		$criteria->compare('resurfacer_id',$this->resurfacer_id);
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
	 * @return Location the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
    /**
     * @return array a list of links that point to the arena list filtered by every tag of this arena
     */
    public function getTagLinks()
    {
        $links = array();
        foreach(Tag::string2array($this->tags) as $tag)
            $links[] = CHtml::link(CHtml::encode($tag), array('location/search', 'tag' => $tag), array('class' => 'btn btn-small'));
        return $links;
    }

    /**
     * Normalizes the user-entered tags.
     */
    public function normalizeTags($attribute, $params)
    {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->tags)));
    }

    /**
     * Tags the record with the Arena's name, event type, and event name.
     * @throws CDbException
     */
    public function autoTag()
    {
        $tags = Tag::string2array($this->tags);
        
        if(isset($this->name) && !empty($this->name)) {
            $tags[] = $this->name;
        }
        
        $tags[] = $this->arena->name;
        $tags[] = $this->type->display_name;
        
        $this->tags = Tag::array2string(array_unique($tags));
    }

    /**
     * This is invoked when a record is populated with data from a find() call.
     */
    protected function afterFind()
    {
        parent::afterFind();
        $this->oldTags = $this->tags;
    }

    /**
     * This is invoked after the record is saved.
     */
    protected function afterSave()
    {
        parent::afterSave();
        Tag::model()->updateFrequency($this->oldTags, $this->tags);
    }

    /**
     * This is invoked after the record is deleted.
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        Tag::model()->updateFrequency($this->tags, '');
    }

}
