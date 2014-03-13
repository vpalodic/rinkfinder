<?php

/**
 * This is the model class for table "file_upload".
 *
 * The followings are the available columns in table 'file_upload':
 * @property integer $id
 * @property integer $user_id
 * @property integer $arena_id
 * @property integer $location_id
 * @property integer $upload_type_id
 * @property string $name
 * @property string $path
 * @property string $uri
 * @property string $extension
 * @property string $mime_type
 * @property integer $size
 * @property integer $error_code
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Arena $arena
 * @property Location $location
 * @property User $createdBy
 * @property User $updatedBy
 */
class FileUpload extends RinkfinderActiveRecord
{
    const TYPE_USER_CSV = 0;
    const TYPE_USER_AVATAR = 1;
    const TYPE_ARENA_CSV = 100;
    const TYPE_ARENA_LOGO = 101;
    const TYPE_ICE_SHEET_CSV = 200;
    const TYPE_ICE_SHEET_LOGO = 201;
    const TYPE_EVENT_CSV = 300;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'file_upload';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'user_id, upload_type_id, name, path, uri, extension, mime_type, size',
                'required'
            ),
            array(
                'user_id, arena_id, location_id, upload_type_id, size, error_code',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'name',
                'length',
                'max' => 255
            ),
            array(
                'path',
                'length',
                'max' => 511
            ),
            array(
                'uri',
                'length',
                'max' => 766
            ),
            array(
                'extension',
                'length',
                'max' => 32
            ),
            array(
                'mime_type',
                'length',
                'max' => 128
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, user_id, arena_id, location_id, upload_type_id, name, path, uri, extension, mime_type, size, error_code, lock_version, created_by_id, created_on, updated_by_id, updated_on',
                'safe',
                'on' => 'search'
            ),
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
            'user' => array(
                self::BELONGS_TO,
                'User',
                'created_by_id',
                'select' => array(
                    'id',
                    'username',
                    'status_id',
                ),
            ),
            'arena' => array(
                self::BELONGS_TO,
                'Arena',
                'arena_id'
            ),
            'location' => array(
                self::BELONGS_TO,
                'Location',
                'location_id'
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
            'id' => 'Upload ID',
            'user_id' => 'Uploaded By',
            'arena_id' => 'In Arena',
            'location_id' => 'In Ice Sheet',
            'upload_type_id' => 'Upload Type',
            'name' => 'File Name',
            'path' => 'File Path',
            'uri' => 'URI',
            'extension' => 'File Extension',
            'mime_type' => 'File Mime Type',
            'size' => 'File Size',
            'error_code' => 'Upload Error Code',
            'lock_version' => 'Lock Version',
            'created_by_id' => 'Created By',
            'created_on' => 'Created On',
            'updated_by_id' => 'Updated By',
            'updated_on' => 'Updated On',
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
            'UploadType' => array(
                self::TYPE_USER_CSV => 'User Accounts CSV Upload',
                self::TYPE_USER_AVATAR => 'User Account Avatar Image',
                self::TYPE_ARENA_CSV => 'Arena Records CSV Upload',
                self::TYPE_ARENA_LOGO => 'Arena Record Logo Image',
                self::TYPE_ICE_SHEET_CSV => 'Ice Sheet Records CSV Upload',
                self::TYPE_ICE_SHEET_LOGO => 'Ice Sheet Record Logo Image',
                self::TYPE_EVENT_CSV => 'Event Records CSV Upload',
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('arena_id', $this->arena_id);
        $criteria->compare('location_id', $this->location_id);
        $criteria->compare('upload_type_id', $this->upload_type_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('extension', $this->extension, true);
        $criteria->compare('mime_type', $this->mime_type, true);
        $criteria->compare('size', $this->size);
        $criteria->compare('error_code', $this->error_code);
        $criteria->compare('lock_version', $this->lock_version);
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
     * @return FileUpload the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
