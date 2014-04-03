<?php

/**
 * This is the model class for table "contact".
 *
 * The followings are the available columns in table 'contact':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $phone
 * @property string $ext
 * @property string $fax
 * @property string $fax_ext
 * @property string $email
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
class Contact extends RinkfinderActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('first_name, last_name, address_line1, city, state, zip, phone, email, created_on, updated_on', 'required'),
			array('active, lock_version, created_by_id, updated_by_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, address_line1, address_line2, city, email', 'length', 'max'=>128),
			array('state', 'length', 'max'=>2),
			array('zip', 'length', 'max'=>5),
			array('phone, ext, fax, fax_ext', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, first_name, last_name, address_line1, address_line2, city, state, zip, phone, ext, fax, fax_ext, email, active, lock_version, created_by_id, created_on, updated_by_id, updated_on', 'safe', 'on'=>'search'),
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
			'arenas' => array(self::MANY_MANY, 'Arena', 'arena_contact_assignment(contact_id, arena_id)'),
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
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'address_line1' => 'Address Line1',
			'address_line2' => 'Address Line2',
			'city' => 'City',
			'state' => 'State',
			'zip' => 'Zip',
			'phone' => 'Phone',
			'ext' => 'Ext',
			'fax' => 'Fax',
			'fax_ext' => 'Fax Ext',
			'email' => 'Email',
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
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('address_line1',$this->address_line1,true);
		$criteria->compare('address_line2',$this->address_line2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('ext',$this->ext,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('fax_ext',$this->fax_ext,true);
		$criteria->compare('email',$this->email,true);
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
	 * @return Contact the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Returns the contact count for each arena that is assigned to the user
     * @param integer $uid The user to get the arenas for.
     * @param integer $aid The optional arena id to limit results.
     * @param integer $sid The optional status code id to limit results.
     * @return mixed[] The contact counts or an empty array.
     * @throws CDbException
     */
    public static function getAssignedCounts($uid, $aid = null, $sid = null)
    {
        // Let's start by building up our query
        $ret = array();
        $parms = array(
            'management/index',
            'model' => 'Contact',
        );

        $sql = 'SELECT 0 AS id, '
                . '"INACTIVE" AS name, ' 
                . '"Contact may be assigned to an Arena but will not show under contacts" AS description, '
                . '"Inactive" AS display_name, '
                . '2 AS display_order, '
                . 'IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count '
                . 'FROM contact c '
                . 'INNER JOIN arena_contact_assignment aca '
                . 'ON c.id = aca.contact_id '
                . 'INNER JOIN arena a '
                . 'ON a.id = aca.arena_id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id '
                . 'INNER JOIN user u '
                . 'ON u.id = aua.user_id '
                . 'WHERE c.active = 0 '
                . 'AND u.id = :uid ';
        
        $sql2 = 'SELECT 1 AS id, '
                . '"ACTIVE" AS name, '
                . '"Contact may be assigned to an Arena and will show under contacts" AS description, '
                . '"Active" AS display_name, '
                . '1 AS display_order, '
                . 'IF(COUNT(c.id) IS NULL, 0, COUNT(c.id)) AS count '
                . 'FROM contact c '
                . 'INNER JOIN arena_contact_assignment aca '
                . 'ON c.id = aca.contact_id '
                . 'INNER JOIN arena a '
                . 'ON a.id = aca.arena_id '
                . 'INNER JOIN arena_user_assignment aua '
                . 'ON a.id = aua.arena_id '
                . 'INNER JOIN user u '
                . 'ON u.id = aua.user_id '
                . 'WHERE c.active = 1 '
                . 'AND u.id = :uid ';


        if($aid !== null) {
            $sql .= "AND a.arena_id = :aid ";
            $sql2 .= "AND a.arena_id = :aid ";
            $parms['aid'] = $aid;
        }
        
        if($sid !== null) {
            if($sid > 0) {
                $sql = $sql2;
            }

            $parms['sid'] = $sid;
        } else {
            $sql .= ' UNION ' . $sql2;
        }
        
        $sql .= ' ORDER BY display_order ASC ';
        
        $command = Yii::app()->db->createCommand($sql);
        
        $command->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($aid !== null) {
            $command->bindParam(':aid', $aid, PDO::PARAM_INT);
        }
        
        $contactCountTotal = 0;
        
        $ret['status'] = $command->queryAll(true);

        $contactCount = count($ret['status']);
        
        for($i = 0; $i < $contactCount; $i++) {
            $contactCountTotal += (integer)$ret['status'][$i]['count'];
            
            $temp = $parms;
            $temp['sid'] = $ret['status'][$i]['id'];
            
            $ret['status'][$i]['endpoint'] = CHtml::normalizeUrl($temp);
        }
        
        $ret['total'] = $contactCountTotal;
        $ret['endpoint'] = CHtml::normalizeUrl($parms);
        
        return $ret;
    }

}
