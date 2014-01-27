<?php

/**
 * This is the model class for table "tag".
 *
 * The followings are the available columns in table 'tag':
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property User $createdBy
 * @property User $updatedBy
 */
class Tag extends RinkfinderActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tag';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('frequency', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 128),
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
                'frequency' => 'Frequency',
        	'lock_version' => 'Lock Version',
                'created_by_id' => 'Created By',
                'created_on' => 'Created On',
                'updated_by_id' => 'Updated By',
                'updated_on' => 'Updated On',
        );
    }

    /**
     * Returns tag names and their corresponding weights.
     * Only the tags with the top weights will be returned.
     * @param integer the maximum number of tags that should be returned
     * @return array weights indexed by tag names.
     */
    public function findTagWeights($limit = 20)
    {
        $models = $this->findAll(array(
			'order' => 'frequency DESC',
			'limit' => $limit,
        ));

    	$total = 0;
                
        foreach($models as $model) {
            $total += $model->frequency;
        }

        $tags = array();
                
        if($total > 0) {
            foreach($models as $model) {
                $tags[$model->name] = 8 + (int)(16 * $model->frequency / ($total + 10));
            }
                        
            ksort($tags);
        }
                
        return $tags;
    }

    /**
     * Suggests a list of existing tags matching the specified keyword.
     * @param string the keyword to be matched
     * @param integer maximum number of tags to be returned
     * @return array list of matching tag names
     */
    public function suggestTags($keyword, $limit = 20)
    {
        $tags = $this->findAll(array(
            'condition' => 'name LIKE :keyword',
            'order' => 'frequency DESC, Name',
            'limit' => $limit,
            'params' => array(
                ':keyword' => '%' . strtr($keyword, array('%' => '\%', '_' => '\_', '\\' => '\\\\')) . '%',
            ),
        ));
		
        $names = array();
        
        foreach($tags as $tag) {
            $names[] = $tag->name;
        }
        
        return $names;
    }

    /**
     * Converts a string
     * @param string $tags The tags to be converted to an array
     * @return array the converted string
     */
    public static function string2array($tags)
    {
        return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Converts a string
     * @param array $tags The tags to be converted to a string
     * @return string the converted comma seperated string
     */
    public static function array2string($tags)
    {
        return implode(', ', $tags);
    }

    /**
     * Updates the frequency count for $oldTags and $newTags
     * New tags are added to the database and unreferenced tags are removed
     * @param array $olTags The previous set of tags
     * @param array $newTags The new set of tags
     */
    public function updateFrequency($oldTags, $newTags)
    {
        $oldTags = self::string2array($oldTags);
    	$newTags = self::string2array($newTags);
        $this->addTags(array_values(array_diff($newTags, $oldTags)));
    	$this->removeTags(array_values(array_diff($oldTags, $newTags)));
    }

    /**
     * Increments the frequency count for the $tags and add those that
     * are new to the database
     * @param array $tags The tags to be added or updated
     * @return integer the number of tags added
     */
    public function addTags($tags)
    {
        $criteria = new CDbCriteria;
    	$criteria->addInCondition('name', $tags);
        $this->updateCounters(array('frequency' => 1), $criteria);
    
        $count = 0;
        
        foreach($tags as $name) {
            if(!$this->exists('name = :name', array(':name' => $name))) {
                $tag = new Tag;
    		$tag->name = $name;
                $tag->frequency = 1;
    		
                if($tag->save()) {
                    $count += 1;
                }
            }
        }
        
        return $count;
    }

    /**
     * Decrements the frequency count for the $tags and removes from the
     * database those that have a frequency less than or equal to zero
     * @param array $tags The tags to be removed
     * @return integer the number of tags deleted
     */
    public function removeTags($tags)
    {
        if(empty($tags)) {
            return 0;
        }
		
        $criteria = new CDbCriteria;
        $criteria->addInCondition('name', $tags);
        $this->updateCounters(array('frequency' => -1), $criteria);
        return $this->deleteAll('frequency <= 0');
    }
        
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Tag the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
