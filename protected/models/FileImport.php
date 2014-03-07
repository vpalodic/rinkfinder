<?php

/**
 * This is the model class for table "file_import".
 *
 * The followings are the available columns in table 'file_import':
 * @property integer $id
 * @property integer $file_upload_id
 * @property integer $table_count
 * @property string $tables
 * @property integer $total_records
 * @property integer $total_created
 * @property integer $total_updated
 * @property integer $auto_tagged
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property FileUpload $fileUpload
 * @property User $createdBy
 * @property User $updatedBy
 */
class FileImport extends RinkfinderActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'file_import';
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
                'file_upload_id, table_count, tables, total_records, total_created, total_updated',
                'required'
            ),
            array(
                'file_upload_id, table_count, total_records, total_created, total_updated',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'auto_tagged',
                'boolean'
            ),
            array(
                'tables',
                'length',
                'max' => 1024
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, file_upload_id, table_count, tables, total_records, total_created, total_updated, auto_tagged, lock_version, created_by_id, created_on, updated_by_id, updated_on',
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
            'fileUpload' => array(
                self::BELONGS_TO,
                'FileUpload',
                'file_upload_id'
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
            'file_upload_id' => 'File Upload',
            'table_count' => 'Table Count',
            'tables' => 'Tables',
            'total_records' => 'Total Records',
            'total_created' => 'Total Created',
            'total_updated' => 'Total Updated',
            'auto_tagged' => 'Auto Tagged',
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
        $criteria->compare('file_upload_id', $this->file_upload_id);
        $criteria->compare('table_count', $this->table_count);
        $criteria->compare('tables', $this->tables, true);
        $criteria->compare('total_records', $this->total_records);
        $criteria->compare('total_created', $this->total_created);
        $criteria->compare('total_updated', $this->total_updated);
        $criteria->compare('auto_tagged', $this->auto_tagged);
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
