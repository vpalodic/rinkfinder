<?php

/**
 * This is the model class for table "profile".
 *
 * The followings are the available columns in table 'profile':
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property double $lat
 * @property double $lng
 * @property string $phone
 * @property string $ext
 * @property string $avatar
 * @property string $url
 * @property integer $lock_version
 * @property integer $created_by_id
 * @property string $created_on
 * @property integer $updated_by_id
 * @property string $updated_on
 *
 * The followings are the available model relations:
 * @property User $user
 * @property User $createdBy
 * @property User $updatedBy
 */
class Profile extends RinkfinderActiveRecord
{
    public $regMode = false;
    
    private $_model;
    private $_modelReg;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'profile';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $required = array();
        $numerical = array();		
        $rules = array();
		
        $model = $this->getFields();

        foreach($model as $field) {
            $field_rule = array();

            if($field->required == ProfileField::REQUIRED_YES_NOT_SHOW_REG ||
                $field->required == ProfileField::REQUIRED_YES_SHOW_REG) {
                array_push($required, $field->varname);
            }
                
            if($field->field_type == 'FLOAT' || $field->field_type == 'INTEGER') {
                array_push($numerical, $field->varname);
            }
            if($field->field_type == 'VARCHAR' || $field->field_type == 'TEXT') {
                $field_rule = array(
                    $field->varname,
                    'length',
                    'max' => $field->field_size,
                    'min' => $field->field_size_min,
                );

                if($field->varname == 'phone') {
                    $field_rule['max'] = 14;
                }
                
                if($field->error_message) {
                    $field_rule['message'] = $field->error_message;
                }
				
                array_push($rules, $field_rule);
            }
            if($field->other_validator) {
                if(strpos($field->other_validator, '{') === 0) {
                    $validator = (array)CJavaScript::jsonDecode($field->other_validator);
					
                    foreach($validator as $name => $val) {
                        $field_rule = array($field->varname, $name);
                        $field_rule = array_merge($field_rule, (array)$validator[$name]);
						
                        if($field->error_message) {
                            $field_rule['message'] = $field->error_message;
                        }
						
                        array_push($rules, $field_rule);
                    }
                } else {
                    $field_rule = array($field->varname, $field->other_validator);
					
                    if($field->error_message) {
                        $field_rule['message'] = $field->error_message;
                    }
					
                    array_push($rules, $field_rule);
                }
            } elseif ($field->field_type == 'DATE') {
                $field_rule = array(
                    $field->varname,
                    'type',
                    'type' => 'date',
                    'dateFormat' => 'yyyy-MM-dd',
                    'allowEmpty' => true,
                );
				
                if($field->error_message) {
                    $field_rule['message'] = $field->error_message;
                }
				
                array_push($rules, $field_rule);
            }
            if($field->match) {
                $field_rule = array($field->varname, 'match', 'pattern' => $field->match);
				
                if($field->error_message) {
                    $field_rule['message'] = $field->error_message;
                }
				
                array_push($rules, $field_rule);
            }
            if($field->range) {
                $field_rule = array($field->varname, 'in', 'range' => self::rangeRules($field->range));
				
                if($field->error_message) {
                    $field_rule['message'] = $field->error_message;
                }
				
                array_push($rules, $field_rule);
            }
        }
		
        array_push($rules, array(implode(',', $required), 'required'));
        array_push($rules, array(implode(',', $numerical), 'numerical', 'integerOnly' => true));
		
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
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id'
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
/*        return array(
            'user_id' => 'User',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'phone' => 'Phone',
            'ext' => 'Ext',
            'avatar' => 'Avatar',
            'url' => 'Url',
            'lock_version' => 'Lock Version',
            'created_by_id' => 'Created By',
            'created_on' => 'Created On',
            'updated_by_id' => 'Updated By',
            'updated_on' => 'Updated On',
        );
*/
        $labels = array(
            'user_id' => 'User ID',
        );
        
        $model = $this->getFields();
        
        foreach($model as $field) {
            $labels[$field->varname] = $field->title;
        }
    
        return $labels;
    }
	
    private function rangeRules($str)
    {
        $rules = explode(';', $str);
		
        for($i = 0; $i < count($rules); $i++) {
            $rules[$i] = current(explode("==", $rules[$i]));
        }
		
        return $rules;
    }
	
    static public function range($str, $fieldValue = NULL)
    {
        $rules = explode(';', $str);
        $array = array();
		
        for($i = 0; $i < count($rules); $i++) {
            $item = explode("==", $rules[$i]);
            
            if(isset($item[0])) {
                $array[$item[0]] = ((isset($item[1])) ? $item[1] : $item[0]);
            }
        }

        if(isset($fieldValue)) {
            if(isset($array[$fieldValue])) {
                return $array[$fieldValue];
            }
            else {
                return '';
            }
        } else {
            return $array;
        }
    }

    public function widgetAttributes()
    {
        $data = array();
        $model = $this->getFields();

        foreach($model as $field) {
            if ($field->widget) {
                $data[$field->varname] = $field->widget;
            }
        }
        
        return $data;
    }
	
    public function widgetParams($fieldName)
    {
        $data = array();
        $model = $this->getFields();
		
        foreach($model as $field) {
            if($field->widget) {
                $data[$field->varname] = $field->widgetparams;
            }
        }
    
        return $data[$fieldName];
    }
	
    public function getFields()
    {
        if($this->regMode) {
            if(!$this->_modelReg) {
                $this->_modelReg = ProfileField::model()->forRegistration()->findAll();
            }
            
            return $this->_modelReg;
        } else {
            if(!$this->_model) {
                $this->_model = ProfileField::model()->forOwner()->findAll();
            }
            
            return $this->_model;
        }
    }
        
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Profile the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
        
    public function getId()
    {
        return $this->user_id;
    }
    
    protected function beforeSave()
    {
        // We ensure that the input mask for the phone number is removed!!!
        $this->phone = preg_replace('/[^0-9]/s', '', $this->phone);
        
        return parent::beforeSave();
    }
}
