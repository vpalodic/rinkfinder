<?php

/**
 * This is the model class for table "recurrence".
 *
 * The followings are the available columns in table 'recurrence':
 * @property integer $id
 * @property string $start_date
 * @property integer $type
 * @property integer $interval
 * @property integer $relative_interval
 * @property integer $factor
 * @property integer $occurrences
 * @property string $end_date
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property Event[] $events
 * @property User $createdBy
 * @property User $updatedBy
 */
class Recurrence extends RinkfinderActiveRecord
{
    const TYPE_SINGLE = 1;
    const TYPE_DAILY = 4;
    const TYPE_WEEKLY = 8;
    const TYPE_MONTHLY = 16;
    const TYPE_MONTHLY_RELATIVE = 32;
    const TYPE_YEARLY = 64;

    const INTERVAL_WEEKLY_SUNDAY = 1;
    const INTERVAL_WEEKLY_MONDAY = 2;
    const INTERVAL_WEEKLY_TUESDAY = 4;
    const INTERVAL_WEEKLY_WEDNESDAY = 8;
    const INTERVAL_WEEKLY_THURSDAY = 16;
    const INTERVAL_WEEKLY_FRIDAY = 32;
    const INTERVAL_WEEKLY_SATURDAY = 64;

    const INTERVAL_MONTHLY_SUNDAY = 1;
    const INTERVAL_MONTHLY_MONDAY = 2;
    const INTERVAL_MONTHLY_TUESDAY = 3;
    const INTERVAL_MONTHLY_WEDNESDAY = 4;
    const INTERVAL_MONTHLY_THURSDAY = 5;
    const INTERVAL_MONTHLY_FRIDAY = 6;
    const INTERVAL_MONTHLY_SATURDAY = 7;
    const INTERVAL_MONTHLY_DAY = 8;
    const INTERVAL_MONTHLY_WEEKDAY = 9;
    const INTERVAL_MONTHLY_WEEKEND = 10;
    
    const INTERVAL_RELATIVE_FIRST = 1;
    const INTERVAL_RELATIVE_SECOND = 2;
    const INTERVAL_RELATIVE_THIRD = 4;
    const INTERVAL_RELATIVE_FOURTH = 8;
    const INTERVAL_RELATIVE_LAST = 16;
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'recurrence';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
	// will receive user inputs.
        return array(
            array('start_date, created_on, updated_on', 'required'),
            array('type, interval, relative_interval, factor, occurrences, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
            array('end_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, start_date, type, interval, relative_interval, factor, occurrences, end_date, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
            'events' => array(self::HAS_MANY, 'Event', 'recurrence_id'),
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
            'start_date' => 'Start Date',
            'type' => 'Type',
            'interval' => 'Interval',
            'relative_interval' => 'Relative Interval',
            'factor' => 'Factor',
            'occurrences' => 'Occurrences',
            'end_date' => 'End Date',
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
        $criteria->compare('start_date',$this->start_date,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('interval',$this->interval);
        $criteria->compare('relative_interval',$this->relative_interval);
        $criteria->compare('factor',$this->factor);
        $criteria->compare('occurrences',$this->occurrences);
        $criteria->compare('end_date',$this->end_date,true);
        $criteria->compare('lock_version',$this->lock_version);
        $criteria->compare('created_by_id',$this->created_by_id);
        $criteria->compare('created_on',$this->created_on,true);
        $criteria->compare('updated_by_id',$this->updated_by_id);
        $criteria->compare('updated_on',$this->updated_on,true);

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
     * @return Recurrence the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
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
            'EventType' => array(
                self::TYPE_SINGLE => 'Single',
                self::TYPE_DAILY => 'Daily',
                self::TYPE_MONTHLY => 'Monthly',
                self::TYPE_MONTHLY_RELATIVE => 'Monthly Relative',
                self::TYPE_YEARLY => 'Yearly',
            ),
            'IntervalWeekly' => array(
                self::INTERVAL_WEEKLY_SUNDAY => 'Sunday',
                self::INTERVAL_WEEKLY_MONDAY => 'Monday',
                self::INTERVAL_WEEKLY_TUESDAY => 'Tuesday',
                self::INTERVAL_WEEKLY_WEDNESDAY => 'Wednesday',
                self::INTERVAL_WEEKLY_THURSDAY => 'Thursday',
                self::INTERVAL_WEEKLY_FRIDAY => 'Friday',
                self::INTERVAL_WEEKLY_SATURDAY => 'Saturday',
            ),
            'IntervalMonthly' => array(
                self::INTERVAL_MONTHLY_SUNDAY => 'Sunday',
                self::INTERVAL_MONTHLY_MONDAY => 'Monday',
                self::INTERVAL_MONTHLY_TUESDAY => 'Tuesday',
                self::INTERVAL_MONTHLY_WEDNESDAY => 'Wednesday',
                self::INTERVAL_MONTHLY_THURSDAY => 'Thursday',
                self::INTERVAL_MONTHLY_FRIDAY => 'Friday',
                self::INTERVAL_MONTHLY_SATURDAY => 'Saturday',
                self::INTERVAL_MONTHLY_DAY => 'Day',
                self::INTERVAL_MONTHLY_WEEKDAY => 'Weekday',
                self::INTERVAL_MONTHLY_WEEKEND => 'Weekend',
            ),
            'IntervalRelative' => array(
                self::INTERVAL_RELATIVE_FIRST => 'First',
                self::INTERVAL_RELATIVE_SECOND => 'Second',
                self::INTERVAL_RELATIVE_THIRD => 'Third',
                self::INTERVAL_RELATIVE_FOURTH => 'Fourth',
                self::INTERVAL_RELATIVE_LAST => 'Last',
            ),
        );

        if(isset($code)) {
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        } else {
            return isset($_items[$type]) ? $_items[$type] : false;
        }
    }

}
