<?php

/**
 * This is the model base class RinkfinderActiveRecord.
 * All ActiveRecord based models should extend this class.
 * This class will automatically update the create and update audit fields
 * and implements optimistic record locking.
 * 
 * The optimistic record locking will prevent the model from being
 * updated or deleted if data has changed since it was retrieved from the
 * underlying database.
 *
 * Audit Fields Usage:
 * 1. Add created_by_id, created_on, updated_by_id, and updated_on fields
 * to the database table.
 * 2. To disable auto updating, set updateAttribute and createAttribute to null.
 * 
 * Optimistic Record Locking Usage:
 * 1. Add locking_version field to the database table
 * 2. Inherit model class from OptimisticLockingActiveRecord
 * 3. Add 'lock_version' hidden field to the edit form
 * 4. Handle StaleObjectError exception when saving record, for example
 *    try {
 *        $result = $model->save();
 *     } catch (StaleObjectError $e) {
 *        $model->addError('lock_version', $e->getMessage());
 *        return false;
 *     }
 *
 *
 * @author Vincent J Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */
abstract class RinkfinderActiveRecord extends CActiveRecord
{
    /**
     * Prepares created_by_id and updated_by_id attributes before
     * saving.
     */
    protected function beforeSave()
    {
        if(null !== Yii::app()->user && Yii::app()->user->id != 0) {
            $id = Yii::app()->user->id;
        } else {
            $id = 1;
        }
        
        if($this->isNewRecord) {
            $this->created_by_id = $id;
        }
        
        $this->updated_by_id = $id;
        
        return parent::beforeSave();
    }
    
    /**
     * Returns the list of behaviors to attach to this class.
     * This base class attaches the CTimestampBehavior
     * @return array an array of configured behaviors for the class.
     */
    public function behaviors()
    {
        return array('CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_on',
                'updateAttribute' => 'updated_on',
                'setUpdateOnCreate' => true,
                'timestampExpression' => new CDbExpression('NOW()'),
            ),
        );
    }

    /*
     * Can not implement the optimistic record locking as a behavior as
     * CActiveRecord::update() does not allow the SQL update criteria to
     * be changed and that is required to implement safe locking
     */

    /**
     * Returns the name of the attribute to store object version number.
     * Defaults to 'lock_version'
     * @return string locking attibute name
     */
    public function getLockingAttribute()
    {
        return 'lock_version';
    }

    /**
     * Overrides parent implementation to add object version check during update
     * @param mixed $pk primary key value(s). Use array for multiple primary keys.
     * For composite key, each key value must be an array (column name => column value).
     * @param array $attributes list of attributes (name=>$value) to be updated
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows being updated
     * @throws StaleObjectException
     */
    public function updateByPk($pk, $attributes, $condition = '', $params = array())
    {
        $this->applyLockingCondition($condition, $params);

        //increment object version
        $lockingAttribute = $this->getLockingAttribute();
        $attributes[$lockingAttribute] = $this->$lockingAttribute + 1;

        $affectedRows = parent::updateByPk($pk, $attributes, $condition, $params);
        
        if($affectedRows != 1) {
            throw new StaleObjectException(Yii::t('application.components.RinkfinderActiveRecord', 'Data has been updated by another user.'));
        }
        
        $this->$lockingAttribute = $this->$lockingAttribute + 1;
        
        return $affectedRows;
    }

    /**
     * Overrides parent implementation to add object version check during delete
     * @param mixed $pk primary key value(s). Use array for multiple primary keys.
     * For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows deleted
     * @throws StaleObjectException
     */
    public function deleteByPk($pk, $condition = '', $params = array())
    {
        $this->applyLockingCondition($condition, $params);
        
        $affectedRows = parent::deleteByPk($pk, $condition, $params);
        
        if($affectedRows != 1) {
            throw new StaleObjectException(Yii::t('application.components.RinkfinderActiveRecord', 'Data has been updated by another user'));
        }
        
        return $affectedRows;
    }

    /**
     * Adds check for object version to the specified condition
     * @param mixed $condition initial condition
     * @params array $params the parameters array for the query
     */
    private function applyLockingCondition(&$condition, &$params)
    {
        $lockingAttribute = $this->getLockingAttribute();
        $lockingAttributeValue = $this->$lockingAttribute;

        if(is_string($condition)) {
            if(!empty($condition)) {
                $condition .= ' and ';
            }
        
            $paramName = ':' . $lockingAttribute;
            $condition .= "$lockingAttribute = $paramName";
            $params[$paramName] = $lockingAttributeValue;
        } elseif($condition instanceof CDbCriteria) {
            $paramName = ':' . $lockingAttribute;
            $condition->addCondition("$lockingAttribute = $paramName");
            $params[$paramName] = $lockingAttributeValue;
        }
    }
}