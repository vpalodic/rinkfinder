<?php

/**
 * The followings are the available columns in table 'profile_field':
 * @property integer $id
 * @property string $varname
 * @property string $title
 * @property string $field_type
 * @property integer $field_size
 * @property integer $field_size_mix
 * @property integer $required
 * @property integer $match
 * @property string $range
 * @property string $error_message
 * @property string $other_validator
 * @property string $default
 * @property string $widget
 * @property string $widget_params
 * @property integer $position
 * @property integer $visible
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
class ProfileField extends RinkfinderActiveRecord
{
    const VISIBLE_ALL = 3;
    const VISIBLE_REGISTER_USER = 2;
    const VISIBLE_ONLY_OWNER = 1;
    const VISIBLE_NO = 0;

    const REQUIRED_NO = 0;
    const REQUIRED_YES_SHOW_REG = 1;
    const REQUIRED_NO_SHOW_REG = 2;
    const REQUIRED_YES_NOT_SHOW_REG = 3;

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'profile_field';
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
                'varname, title, field_type',
                'required',
            ),
            array(
                'varname',
                'match',
                'pattern' => '/^[A-Za-z_0-9]+$/u',
                'message' => "Variable name may only consist of A-z, 0-9, underscores, and must begin with a letter.",
            ),
            array(
                'varname',
                'unique',
                'message' => "Field already exists.",
            ),
            array(
                'varname, field_type',
                'length',
                'max' => 50,
            ),
            array(
                'field_size, field_size_min, required, position, visible',
                'numerical',
                'integerOnly' => true,
            ),
            array(
                'title, match, error_message, other_validator, default, widget',
                'length',
                'max' => 255,
            ),
            array(
                'range, widget_params',
                'length',
                'max' => 5000,
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
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'varname' => 'Variable Name',
            'title' => 'Title',
            'field_type' => 'Field Type',
            'field_size' => 'Field Size',
            'field_size_min' => 'Field Size Min',
            'required' => 'Required',
            'match' => 'Match',
            'range' => 'Range',
            'error_message' => 'Error Message',
            'other_validator' => 'Other Validator',
            'default' => 'Default',
            'widget' => 'Widget',
            'widget_params' => 'Widget Parameters',
            'position' => 'Position',
            'visible' => 'Visible',
        );
    }

    public function scopes()
    {
        return array(
            'forAll' => array(
                'condition' => 'visible = ' . self::VISIBLE_ALL,
                'order' => 'position',
            ),
            'forUser' => array(
                'condition' => 'visible >= ' . self::VISIBLE_REGISTER_USER,
                'order' => 'position',
            ),
            'forOwner' => array(
                'condition' => 'visible >= ' . self::VISIBLE_ONLY_OWNER,
                'order' => 'position',
            ),
            'forRegistration' => array(
                'condition' => 'required = ' . self::REQUIRED_NO_SHOW_REG . ' OR required = ' . self::REQUIRED_YES_SHOW_REG,
                'order' => 'position',
            ),
            'sort' => array(
                'order' => 'position',
            ),
        );
    }

    /**
     * @param $value
     * @return formated value (string)
     */
    public function widgetView($model)
    {
        if($this->widget && class_exists($this->widget)) {
            $widgetClass = new $this->widget;

            $arr = $this->widget_params;

            if($arr) {
                $newParams = $widgetClass->params;
                $arr = (array)CJavaScript::jsonDecode($arr);

                foreach($arr as $p => $v) {
                    if(isset($newParams[$p])) {
                        $newParams[$p] = $v;
                    }
                }

                $widgetClass->params = $newParams;
            }
			
            if(method_exists($widgetClass, 'viewAttribute')) {
                return $widgetClass->viewAttribute($model, $this);
            }
        }

        return false;
    }

    public function widgetEdit($model, $params = array())
    {
        if($this->widget && class_exists($this->widget)) {
            $widgetClass = new $this->widget;
            
            $arr = $this->widget_params;
            
            if($arr) {
                $newParams = $widgetClass->params;
                $arr = (array)CJavaScript::jsonDecode($arr);

                foreach($arr as $p => $v) {
                    if(isset($newParams[$p])) {
                        $newParams[$p] = $v;
                    }
                }

                $widgetClass->params = $newParams;
            }

            if(method_exists($widgetClass, 'editAttribute')) {
                return $widgetClass->editAttribute($model, $this, $params);
            }
        }

        return false;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = array(
            'field_type' => array(
                'INTEGER' => 'INTEGER',
                'VARCHAR' => 'VARCHAR',
                'TEXT'=> 'TEXT',
                'DATE'=> 'DATE',
                'FLOAT'=> 'FLOAT',
                'BOOL'=> 'BOOL',
                'BLOB'=> 'BLOB',
                'BINARY'=> 'BINARY',
            ),
            'required' => array(
                self::REQUIRED_NO => 'No',
                self::REQUIRED_NO_SHOW_REG => 'No, but show on registration form',
                self::REQUIRED_YES_SHOW_REG => 'Yes and show on registration form',
                self::REQUIRED_YES_NOT_SHOW_REG => 'Yes',
            ),
            'visible' => array(
                self::VISIBLE_ALL => 'For all',
                self::VISIBLE_REGISTER_USER => 'Registered users',
                self::VISIBLE_ONLY_OWNER => 'Only owner',
                self::VISIBLE_NO => 'Hidden',
            ),
        );
        
        if(isset($code)) {
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        } else {
            return isset($_items[$type]) ? $_items[$type] : false;
        }
    }
}
